<?php

namespace Library\ProductCenter;

use App;
use Illuminate\Http\Request;
use \Exception;
use Enum\EnumKeys;
use Illuminate\Support\Facades\Cache;
use \Helper\CFunctionHelper as help;
use Enum\EnumLang;
use \DB;
use \App\Models\OrderGoods;
use \App\Models\OrderInfo;

class Order extends \Library\CBase
{
	protected $request;
	protected $userCMd;

	public function __construct(Request $request)
	{
		$this->request = $request;
		$this->OrderMd = new App\Repositories\UserOrderRepository();
		$this->GoodsMd = new App\Repositories\GoodsRepository();
		parent::__construct(__CLASS__);
	}

	/**
	 * 提交订单
	 * @author: colin
	 * @date: 2019/1/24 16:23
	 * @return bool|\Library\type
	 */
	public function subOrder()
	{
		try {
			\DB::beginTransaction();
			$time = time();
			$uid = $this->request->input('uid');
			$message = json_decode($this->request->input("message"), true);//用户信息
			if (empty($message))
				throw new \Exception('没有提交用户信息！');

			$dispatch = !empty($message['dispatch']) ? intval($message['dispatch']) : 0;//是否配送 1是 0否
			$shopCartArr = $this->setCartData();
			help::throwError($shopCartArr, $this->getUserMsg());
			$order = $this->initOrder($message);
			help::throwError($order, $this->getUserMsg());
			$stoData = $this->OrderMd->StoresUser(['gs_id' => $order['order_pick_stores'], 'gs_stats' => 1]);//获取门店信息
			help::throwError($stoData, $this->getUserMsg());

			if ($stoData['pickup_mode'] == 1 || $dispatch < 1) //自提
				unset($order['address']);

			if (!empty($stoData['open_time'])) {
				$openTime = strtotime(date('Y-m-d') . ' ' . trim($stoData['open_time']));
				if ($openTime > $time)
					throw new Exception("还未到开店时间,暂时不能提交订单");
			}
			$order['stores_type'] = $stoData['gs_type'];
			$shipp = $this->OrderMd->Shipp(['shipping_id' => $order['shipping_id']]);//获取取货信息
			help::throwError($shipp, '请选择取货方式');
			$cardData = $this->setCard();
			help::throwError($cardData, $this->getUserMsg());
			$total = help::orderFee($order, $shopCartArr);//获得订单中的费用信息
			$order['goods_amount'] = $total['goods_price'];
			if ($dispatch == 1 && $order['goods_amount'] < $stoData['free_post_money']) {//商家配送
				$order['shipping_fee'] = $stoData['post_fee'];
			}
			$order['surplus'] = $total['goods_price'] + $order['shipping_fee'];
			$bonusUsedId = [];
			if (!empty($cardData)) {
				$tmpOrderSn = md5(json_encode($order) . time());
				$goodsAmount = $order['goods_amount'] + $order['shipping_fee'];
				$usedTotalMoney = 0;
				foreach ($cardData as $val) {
					if ($val['balance'] >= $goodsAmount) { //卡全部结算
						$balance = $val['balance'] - $goodsAmount;
						$umoney = $goodsAmount;
						$goodsAmount = 0;
					} else {
						$balance = 0;
						$goodsAmount = $goodsAmount - $val['balance'];
						$umoney = $val['balance'];
					}
					$usedTotalMoney += $umoney;
					$create = \App\Models\OrderBonusUser::create([
						'order_sn' => $tmpOrderSn,
						'bonus_id' => $val['bonus_id'],
						'used_money' => $umoney,
					]);
					$bonusUsedId[] = $create->id;
				}
				$order['surplus'] = $goodsAmount;//卡不足，由其它方式付款金额
				$order['bonus'] = $usedTotalMoney;//使用卡的总金额
				if ($order['surplus'] == 0) {
					$order['pay_id'] = 1;//默认余额支付
				}
			}
			/* 支付方式 */
			if ($order['pay_id'] > 0) {
				$payment = help::paymentInfo($order['pay_id']);
				$order['pay_name'] = strip_tags($payment['pay_name']);
			}
			$order['pay_fee'] = $total['pay_fee'];
			$order['cod_fee'] = $total['cod_fee'];
			if ($order['pay_id'] == 1 && $order['surplus'] > 0) {
				$useInfo = $this->OrderMd->userInfo(['user_id' => $uid]);
				if ($order['surplus'] > $useInfo['user_money']) {
					throw new Exception('余额不足，请充值或其它支付方式');
				}
			}

			$order['order_amount'] = number_format($total['amount'], 2, '.', '');
			$order['order_amount_zy'] = number_format($total['amount_zy'], 2, '.', '');
			$configData = EnumLang::loadConfig();
			if (in_array($order['pay_id'], [4, 5, 6])) {
				$zhekoulv = isset($configData['zhekou']) ? (float)$configData['zhekou'] : 0;
				if ($zhekoulv > 0) {
					$order['zhekou'] = $zhekoulv;       //将折扣率 存入订单表
					$order['order_amount_all'] = $order['order_amount'];   //将没计算折扣总金额 存入订单表
					$order['order_amount'] = $order['order_amount'] * $zhekoulv;   //根据后台折扣率 计算 折扣金额
					$total['amount_formated'] = help::priceFormat($order['order_amount'], false);
				} else {
					$order['order_amount_all'] = $order['order_amount'];   //将没计算折扣总金额 存入订单表
					$total['amount_formated'] = help::priceFormat($order['order_amount'], false);
				}
			} elseif ($order['pay_id'] == 1) {
				$total['amount_formated'] = help::priceFormat($order['surplus'], false);
				$order['order_amount_all'] = $order['order_amount'];
			}
			$order['integral_money'] = $total['integral_money'];
			$order['integral'] = $total['integral'];

			if ($order['extension_code'] == 'exchange_goods') {
				$order['integral_money'] = 0;
				$order['integral'] = $total['exchange_integral'];
			}
			$create = 1;
			$num = 0;
			do {
				$order['order_sn'] = help::getOrderSn(); //获取新订单号
				$create = $this->OrderMd->create($order);
				$num++;
			} while ($create === false && $num < 10); //如果是订单号重复则重新提交数据
			help::throwError($create, '生成订单失败！');
			$newOrderId = $create->order_id;
			if (!empty($bonusUsedId)) {
				\App\Models\OrderBonusUser::whereIn('id', $bonusUsedId)->update(['order_sn' => $order['order_sn']]);
			}
			$order['order_id'] = $newOrderId;
			$goodInfo = [];


			$StoreSaleAmount = 0;
			/* 插入订单商品 */
			foreach ($shopCartArr as $v) {
				$goodInfo[] = [
					'order_id' => $newOrderId,
					'goods_id' => $v['goods_id'],
					'goods_name' => $v['goods_name'],
					'goods_sn' => $v['goods_sn'],
					'product_id' => 0,
					'goods_number' => $v['goods_number'],
					'market_price' => $v['market_price'],
					'goods_price' => $v['goods_price'],
					'goods_attr' => empty($v['goods_attr']) ? '' : $v['goods_attr'],
					'is_real' => 1,
					'extension_code' => 1,
					'parent_id' => 0,
					'is_gift' => 0,
					'goods_attr_id' => empty($v['goods_attr_id']) ? '' : $v['goods_attr_id'],
					'exceed_promote_num' => 0,
					'exceed_promote_price' => $v['exceed_promote_price'],
				];
				/* 插入商品表的salesnum 字段，统计销量排行 */
				$this->GoodsMd->salesNum($v['goods_id'], $v['goods_number']);
				$StoreSaleAmount = $StoreSaleAmount + $v['goods_number'];
			}
			OrderGoods::insert($goodInfo);
			$this->GoodsMd->storeNum($order['order_pick_stores'], $StoreSaleAmount);
			/* 如果需要，发短信 */
			if ($configData['sms_order_placed'] == '1' && $configData['sms_shop_mobile'] != '') {
				$msg = $order['pay_status'] == EnumKeys::PS_UNPAYED ?
					$configData['order_placed_sms'] : $configData['order_placed_sms'] . '[' . $configData['sms_paid'] . ']';
				help::send($configData['sms_shop_mobile'], sprintf($msg, $order['consignee'], $order['tel']), $uid, '下单');
			}
		} catch (\Exception $e) {
			\DB::rollBack();
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "Order upOrder:" . json_encode(1) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		\DB::commit();
		$result = ['orderSn' => $order['order_sn']];
		return $result;

	}

	/**
	 * 获取幸福卡信息
	 * @author: colin
	 * @date: 2019/1/28 14:42
	 * @return array|\Library\type
	 */
	public function setCard()
	{
		try {
			$uid = $this->request->input('uid');
			$message = json_decode($this->request->input("message"), true);//用户信息
			if (empty($message['card_ids']))
				return [];
			$cardIds = explode(',', $message['card_ids']);
			sort($cardIds, SORT_NUMERIC);
			$bonusInfo = $this->OrderMd->bonusInfo($cardIds, $uid);
			if (empty($bonusInfo))
				throw new Exception('选取的幸福券不存在，请检查！');

			$bonusId = array_column($bonusInfo, 'bonus_id');
			sort($bonusId, SORT_NUMERIC);
			if ($bonusId != $cardIds)
				throw new Exception('选取的幸福券不存在,请检查！');

			$cardData = [];
			foreach ($bonusInfo as $item) {
				if ($item['bonus_status'] > 1 || $item['balance'] <= 0) {
					throw new Exception('选取的幸福券异常，请检查');
				}
				$cardSort[] = $item['bonus_end_date'];//以到期时间排序
				$cardData[] = [
					'bonus_id' => $item['bonus_id'],
					'balance' => $item['balance'],
					'bonus_end_date' => $item['bonus_end_date'],
				];
			}
			array_multisort($cardSort, SORT_ASC, $cardData);

		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "Order setCard:" . json_encode(1) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return $cardData;
	}

	/**
	 * 初始化订单信息
	 * @author: colin
	 * @date: 2019/1/25 14:35
	 * @param $shopCartArr
	 * @param $message
	 * @return \Library\type
	 */
	public function initOrder($message)
	{
		try {
			$config = EnumLang::loadConfig();
			$param = $this->request->all();
			$userId = $this->request->input('uid');
			if (empty($message['sellerid'])) {
				throw new Exception('门店错误，请重新选购！');
			}
			$consignee = help::getConsignee($userId);
			$payId = help::getRealPamentId($message['pays']);
			if (empty($payId)) {
				throw new Exception('支付方式暂不支持！');
			}
			$order = [
				'shipping_id' => 8,
				'pay_id' => $payId,
				'pack_id' => isset($param['pack']) ? intval($param['pack']) : 0,
				'card_message' => isset($param['card_message']) ? $param['card_message'] : '',
				'surplus' => isset($param['surplus']) ? floatval($param['surplus']) : 0.00,
				'integral' => isset($param['integral']) ? intval($param['integral']) : 0,
				'need_inv' => empty($param['need_inv']) ? 0 : 1,
				'inv_type' => isset($param['inv_type']) ? $param['inv_type'] : '',
				'inv_payee' => isset($param['inv_payee']) ? $param['inv_payee'] : '',
				'inv_content' => isset($param['inv_content']) ? $param['inv_content'] : '',
				'order_note' => $message['keyword'],
				'order_lxr' => $message['user_name'],
				'order_tel' => $message['user_phone'],
				'address' => $message['person_adr'],
				'order_pick_time' => $message['TheDate'] . ' ' . $message['time'],
				'order_pick_stores' => $message['sellerid'],
				'postscript' => !empty($param['postscript']) ? $param['postscript'] : '',
				'how_oos' => isset($param['how_oos']) && isset($config['oos'][$param['how_oos']]) ? addslashes($config['oos'][$param['how_oos']]) : '',
				'need_insure' => isset($param['need_insure']) ? intval($param['need_insure']) : 0,
				'user_id' => $userId,
				'add_time' => time(),
				'order_status' => EnumKeys::OS_UNCONFIRMED,
				'shipping_status' => EnumKeys::SS_UNSHIPPED,
				'pay_status' => EnumKeys::PS_UNPAYED,
				'agency_id' => help::getAgencyByRegions([$consignee['country'], $consignee['province'], $consignee['city'], $consignee['district']]),
				'dfrom' => 1,
				'shipping_fee' => 0,//配送费
				'extension_code' => '',
				'extension_id' => 0,
				'from_ad' => 0,
				'referer' => '',
				'parent_id' => 0,
			];

		} catch (\Exception $e) {
			echo $e->getMessage();
			die;
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "Order upOrder:" . json_encode(1) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return $order;
	}

	/**
	 * 购物车信息
	 * @author: colin
	 * @date: 2019/1/25 14:31
	 * @return array|\Library\type
	 */
	public function setCartData()
	{
		try {
			$shopCartArr = [];
			$shopCart = json_decode($this->request->input("shopCart"));//购物车信息
			$message = json_decode($this->request->input("message"));//用户信息
			if (empty($shopCart) || empty($message)) {
				throw new \Exception('没有提交订单！');
			}
			foreach ($shopCart as $cartshop) {
				$goodsId = isset($cartshop->goods_id) ? intval($cartshop->goods_id) : 0;
				$row = $this->GoodsMd->ById($goodsId);
				if (empty($row)) {
					throw new Exception('无此商品');
				}
				$sizeId = isset($cartshop->sizeid) ? intval($cartshop->sizeid) : 0;
				$tastesId = isset($cartshop->tastesid) ? intval($cartshop->tastesid) : 0;
				$cart["goods_name"] = $cartshop->name;
				$cart["goods_id"] = $goodsId;

				//判断商品状态
				if ($row['is_on_sale'] < 1 || $row['is_delete'] > 0)
					throw new Exception('商品<' . $row['goods_name'] . '>已下架，请重新选择~');

				$gprice = ($row['is_promote'] == 1 && time() >= $row['promote_start_date'] && time() <= $row['promote_end_date']) ? $row["promote_price"] : $row["shop_price"];
				$attrIds = [];
				if (!empty($sizeId)) {
					$attrIds[] = $sizeId;
				}
				if (!empty($tastesId)) {
					$attrIds[] = $tastesId;
				}
				//根据商品id和规格属性取商品的价格，防止传过来的价格有误
				if (!empty($attrIds)) {
					$comparePrice = help::getFinalPrice($goodsId, intval($cartshop->foodcount), true, $attrIds);
					$specPrice = help::specPrice($attrIds);
					$normalPrice = $row['market_price'] + $specPrice;
					if ($cartshop->price != $comparePrice) {
						throw new Exception('商品价格出现异常...');
					}
				} else {
					if ($cartshop->price != $gprice) {
						throw new Exception('商品价格出现异常.');
					}
					$normalPrice = $cartshop->price;
				}
				$cart["goods_price"] = $cartshop->price;
				$cart["exceed_promote_price"] = $normalPrice;
				$cart["market_price"] = $normalPrice;
				$cart["user_id"] = $this->request->input('uid');
				$cart["goods_sn"] = $cartshop->goods_sn;
				$cart["goods_number"] = $cartshop->foodcount;
				$cart["is_check"] = 1;
				$cart["is_real]"] = 1;
				$cart["cart_stores_id"] = $message->sellerid;

				if (!empty($sizeId) && !empty($tastesId))
					$cart["goods_attr_id"] = $sizeId . "," . $tastesId;
				elseif (empty($sizeId) && !empty($tastesId))
					$cart["goods_attr_id"] = $tastesId;
				elseif (!empty($sizeId) && empty($tastesId))
					$cart["goods_attr_id"] = $sizeId;

				$cart["subtotal"] = $cartshop->price * $cartshop->foodcount;
				$cart["goods_thumb"] = $cartshop->icon;
				$cart["formated_goods_price"] = help::priceFormat($cartshop->price);
				$cart["formated_subtotal"] = help::priceFormat($cartshop->price * $cartshop->foodcount);

				array_push($shopCartArr, $cart);

			}
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "Order setCartData:" . json_encode($shopCart) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return $shopCartArr;
	}

	/**
	 * 订单详情
	 * @author: colin
	 * @date: 2019/1/29 15:31
	 * @return \Library\type
	 */
	public function detail()
	{
		try {
			$orderSn = $this->request->input('order_sn');
			$uid = $this->request->input('uid');
			$data = $this->OrderMd->Orderdetail($orderSn, $uid);
			help::throwError($data, '订单不存在！');
			$expireTime = 60 * 10;
			$now = time();
			$gsMobile = $data['gs_mobile'];
			$gsMobileArr = explode('|', $gsMobile);
			$data['gs_mobile'] = $gsMobileArr[0];
			$data['stores_mess'] = [
				'name'    => $data['gs_name'],
				'mobile'  => $gsMobileArr[0],
				'address' => $data['gs_address'],
				'gs_lat' => $data['gs_lat'],
				'gs_lng' => $data['gs_lng'],
			];
			unset($data['gs_name'], $data['gs_address'], $data['gs_mobile']);
			$diffTime = $now - $data['order_time'];
			if ($diffTime >= $expireTime) {
				$litime = 0;
			} else {
				$litime = $expireTime - $diffTime;
			}
			$data['litime'] = $litime;
			$charge_back = false;//是否可退货
			if ($data['pay_status'] == EnumKeys::PS_PAYED && $data['shipping_status'] == EnumKeys::SS_SHIPPED && $data['order_status'] == EnumKeys::OS_SPLITED) {
				$charge_back = true;
			}
			$data['charge_back'] = $charge_back;
			$data['pay_status_text'] = EnumKeys::$payArr[$data['pay_status']];
			$data['pay_name'] = strip_tags($data['pay_name']);
			$data['exp_data'] = $this->OrderMd->expData($orderSn);
			$data['goods'] = $this->OrderMd->goodData($data['order_id']);
			}catch(\Exception $e) {
				return $this->setErrorAndReturn([
					'return' => false,
					'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
					'errorMsg' => "Order detail:reason:" . $e->getMessage(),
					'userMsg' => $e->getMessage(),
					'line' => __LINE__,
				]);
			}
			return $data;
	}
	/**
	 * 申请取消订单
	 * @author: colin
	 * @date: 2019/5/23 14:35
	 * @return bool|\Library\type
	 */
	public function cancelOrder()
	{
		try{
			\DB::beginTransaction();
			$orderSn = $this->request->input('order_sn');
			$uid = $this->request->input('uid');
			$data = $this->OrderMd->Orderdetail($orderSn, $uid);
			help::throwError($data, '订单不存在！');
			if($data['order_status'] == EnumKeys::OS_CANCELED){
				throw new \Exception('已取消订单,无需反复提交！');
			}
			$dataInfo = [
				'order_status' => EnumKeys::OS_CANCELED,
				'confirm_time' => '',
				'shipping_status' => EnumKeys::SS_UNSHIPPED,
				'pay_status' => EnumKeys::PS_UNPAYED,
				'order_sn' => $orderSn,
			];
			$result = $this->OrderMd->update($dataInfo, ['order_sn' => $orderSn]);
			help::throwError($result, '取消订单失败！');
			//修改商户定义的库存
			$goodsList = $this->OrderMd->orderGoodData($data['order_id']);
			help::throwError($goodsList, '取消订单获取商品失败！');
			foreach ($goodsList as $item) {
				$isPromote = help::isGoodsPromote($item['goods_id']);
				$attrIdsArr = explode(',', $item['goods_attr_id']);
				$sizeId = isset($attrIdsArr[0]) ? $attrIdsArr[0] : '';
				$tastesId = isset($attrIdsArr[1]) ? $attrIdsArr[1] : '';
				$res = help::updateGoodsStock($data['order_pick_stores'], $item['goods_id'], $item['goods_number'], $isPromote, $sizeId, $tastesId);
				help::throwError($res, '取消订单更新库存失败！');
			}

		}catch(\Exception $e){
			\DB::rollBack();
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "Order cancelOrder:reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		\DB::commit();
		return true;
	}
	/**
	 * 申请退款
	 * @author: colin
	 * @date: 2019/5/23 14:35
	 * @return bool|\Library\type
	 */
	public function chargeBack()
	{
		try{
			\DB::beginTransaction();
			$orderSn = $this->request->input('order_sn');
			$uid = $this->request->input('uid');
			$returnReason = $this->request->input('reason');//原因
			if(empty($returnReason))
				throw new \Exception('请填写退货原因！');
			$data = $this->OrderMd->Orderdetail($orderSn, $uid);
			help::throwError($data, '订单不存在！');
			$refund = $this->applyRefund($data['order_id'], 1, $uid, $returnReason);
			if($refund === false)
				return false;
		}catch(\Exception $e){
			\DB::rollBack();
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "Order cancelOrder:reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		\DB::commit();
		return true;
	}

	/**
	 * 退货操作
	 * @author: colin
	 * @date: 2019/5/24 14:53
	 * @param $orderId
	 * @param $applyUser
	 * @param $applyUserType
	 * @param int $userId
	 * @param string $returnReason
	 * @return bool|\Library\type
	 */
	public function applyRefund($orderId,$applyUserType, $userId, $returnReason = '')
	{
		try{
			$order = OrderInfo::selectRaw("user_id, order_sn , order_status, shipping_status, pay_status")->where('order_id',$orderId)->firstOrFail()->toArray();
			// 如果用户ID大于 0 。检查订单是否属于该用户
			if ($userId > 0 && $order['user_id'] != $userId)
				throw new \Exception('用户有误,非法操作！');
			if($order['shipping_status'] == EnumKeys::SS_PREPARING)
				throw new \Exception('该订单已经提交了退货申请！');
			if ($order['order_status'] == EnumKeys::OS_SPLITED && $order['shipping_status'] == 2)
				throw new \Exception('该订单已经完成收货，不能申请退货。');
			$applyUser = $this->OrderMd->userName(['user_id' => $userId]);
			$userSafe = str_replace("'", "''", $applyUser);
			$upInfo = [
				'shipping_status' => EnumKeys::SS_PREPARING,
				'return_reason' => $returnReason,
			];
			$this->OrderMd->update($upInfo,['order_id'=>$orderId]);
			$dataInfo = [
				'order_id' => $orderId,
				'apply_user' => $userSafe,
				'apply_user_type' => $applyUserType,
				'apply_time' => time(),
				'apply_status' => 0,
				'return_reason' => $returnReason,
			];
			\App\Models\RefundApply::create($dataInfo);

		}catch(\Exception $e){
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "Order applyRefund:reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return true;
	}

}