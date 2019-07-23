<?php

namespace Library;

use DB;
use App;
use Cookie;
use Enum\EnumKeys;
use Helper\CFunctionHelper as help;
use Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use \Exception;

class MerchantUserCart extends CBase
{
	protected $request;
	protected $token;
	protected $gsId;

	public function __construct(Request $request)
	{
		$this->request = $request;
		parent::__construct(__CLASS__);

	}

	/**
	 * 添加购物车
	 * @param $param
	 * @return mixed
	 * @author colin
	 */
	public function addToCart()
	{
		$param = $this->request->all();
		$num = $param['number'];
		try {
			$param['spec'] = $param['spec'] ?? [];
			$param['parent'] = $param['parent'] ?? 0;
			$goodInfo = self::goodInfo($param['goodsId'], $num, 0, $this->getToken());
			if (empty($goodInfo)) {
				return false;
			}
			/* 如果商品有规格则取规格商品信息 配件除外 */
			$productsInfo = self::productsInfo($param['goodsId'], $param['spec']);
			/* 取规格的货品库存 */
			if ($productsInfo['product_number'] > 0 && $param['number'] > $productsInfo['product_number']) {
				throw new \Mockery\Exception('添加购物车商品数量不能大于该规格最大数量！');
			}
			/* 总库存 */
			if ($param['number'] > $goodInfo['goods_number']) {
				throw new \Mockery\Exception('添加购物车商品数量不能大于库存数量！');
			}
			/* 计算商品的规格价格 */
			$specPrice = self::specPrice($param['spec']);
			$goodsPrice = $this->getFinalPrice($param['goodsId'], $num, true, $param['spec']);
			$goodInfo['market_price'] += $specPrice;
			$goodsAttr = self::goodsAttrInfo($param['spec']);
			$goodsAttrId = implode(',', $param['spec']);
			/* 初始化要插入购物车的基本件数据 */
			$parent = [
				'user_id' => 0,
				'session_id' => $this->getToken(),
				'goods_id' => $param['goodsId'],
				'goods_sn' => addslashes($goodInfo['goods_sn']),
				'product_id' => $productsInfo['product_id'],
				'goods_name' => addslashes($goodInfo['goods_name']),
				'market_price' => $goodInfo['market_price'],
				'goods_attr' => addslashes($goodsAttr),
				'goods_attr_id' => $goodsAttrId,
				'is_real' => $goodInfo['is_real'],
				'extension_code' => $goodInfo['extension_code'],
				'is_gift' => 0,
				'is_shipping' => $goodInfo['is_shipping'],
				'rec_type' => EnumKeys::CART_GENERAL_GOODS,
				'goods_brand_id' => $goodInfo['brand_id'],
				'stores_user_id' => $this->getgsId(),
				'cart_stores_id' => $this->getgsId(),
				'is_check' => 1,
			];
			/* 检查该商品是否已经存在在购物车中 */
			$goodNum = \App\Models\Cart::where(['session_id' => $this->getToken(), 'goods_id' => $param['goodsId'], 'parent_id' => 0, 'rec_type' => EnumKeys::CART_GENERAL_GOODS, 'goods_attr' => $goodsAttr])
				->where('extension_code', '<>', 'package_buy')->value('goods_number');
			if (!empty($goodNum)) //如果购物车已经有此物品，则更新
			{
				$num += $goodNum;
				$goodsStorage = !empty($productsInfo['product_number']) ? $productsInfo['product_number'] : $goodInfo['goods_number'];
				if ($num > $goodsStorage) {
					throw new Exception('添加购物车商品数量大于库存量！');
				}
				$dataInfo = [
					'goods_number' => $num,
					'goods_price' => $goodsPrice,
				];
				\App\Models\Cart::where(['session_id' => $this->getToken(), 'parent_id' => 0, 'goods_id' => $param['goodsId'], 'goods_attr' => $goodsAttr, 'rec_type' => EnumKeys::CART_GENERAL_GOODS])->where('extension_code', '<>', 'package_buy')->update($dataInfo);
			} else //购物车没有此物品，则插入
			{
				$parent['goods_price'] = max($goodsPrice, 0);
				$parent['goods_number'] = $num;
				$parent['exceed_promote_price'] = $goodsPrice;
				$parent['parent_id'] = 0;
				\App\Models\Cart::create($parent);
			}
			/* 把赠品删除 */
			\App\Models\Cart::where('session_id', $this->getToken())->where('is_gift', '<>', 0)->delete();
			//添加商户操作日志
			\Helper\CFunctionHelper::setStoreLogs($this->getgsId(), \Enum\EnumLang::BUSINESS_ADD_CART, 'ecs_cart', '1', '添加购物车');
			$result = $this->cart(1);
			return $result;
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
	}

	/***
	 * 购物车列表
	 * @author: colin
	 * @date: 2018/11/27 17:28
	 * @return array|type
	 */
	public function cart($isToal = '')
	{
		$total = [
			'goodsPrice' => 0, // 本店售价合计（有格式）
			'goodsAmount' => 0, // 本店售价合计（无格式）
			'count' => 0,
			'checkedGoods' => '',
		];
		$goodsList = [];
		try {
			/* 循环、统计 */
			$res = \App\Models\Cart::where(['session_id' => $this->getToken(), 'rec_type' => EnumKeys::CART_GENERAL_GOODS, 'is_real' => '1'])->orderBy('parent_id', 'desc')->orderBy('goods_id', 'desc')->get()->toArray();
			if (empty($res)) {
				return [];
			}
			foreach ($res as $k => $row) {
				$total['goodsPrice'] += $row['goods_price'] * ($row['goods_number'] - $row['exceed_promote_num']) + $row['exceed_promote_num'] * $row['exceed_promote_price'];
				$total['checkedGoods'] .= $row['rec_id'] . ',';
				/* 统计商品的个数 */
				$total['count'] += intval($row['goods_number']);
				if ($isToal) {//只获取统计数据，返回
					continue;
				}
				$row['subtotal'] = \Helper\CFunctionHelper::priceFormat($row['goods_price'] * ($row['goods_number'] - $row['exceed_promote_num']) + $row['exceed_promote_num'] * $row['exceed_promote_price'], false);
				$row['goods_price'] = \Helper\CFunctionHelper::priceFormat($row['goods_price'], false);
				$row['market_price'] = \Helper\CFunctionHelper::priceFormat($row['market_price'], false);
				$row['storesName'] = ($row['cart_stores_id'] > 0) ? \App\Models\StoresUser::where('gs_id', $row['cart_stores_id'])->value('gs_name') : '选取门店';

				$goodsThumb = \App\Models\Goods::where('goods_id', $row['goods_id'])->value('goods_thumb');
				$row['goods_thumb'] = $goodsThumb ? config('merchant.static_host') . $goodsThumb : '';
				if ($row['extension_code'] == 'package_buy') {
					$row['package_goods_list'] = self::getPackageGoods($row['goods_id']);
				}
				$goodsList[] = self::setCartGoods($row);

			}
			$total['checkedGoods'] = trim($total['checkedGoods'], ',');
			$total['goodsAmount'] = help::priceFormat($total['goodsPrice'], false);
			$total['goodsPrice'] = help::priceFormat($total['goodsPrice']);

		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		if (!empty($isToal)) {
			return ['total' => $total];
		}
		return ['goodsList' => $goodsList, 'total' => $total];
	}


	/**
	 * 更新购物车
	 * @author: colin
	 * @date: 2018/11/28 9:17
	 * @return type
	 */
	public function updateGroupCart()
	{
		$parm = $this->request->all();
		try {
			$goodNumber = \App\Models\Goods::where('goods_id', $parm['goodsId'])->value('goods_number');
			if ($goodNumber < $parm['number']) {
				throw new Exception('对不起,您选择的数量超出库存您最多可购买' . $goodNumber . '件');
			}
			\App\Models\Cart::where('rec_id', $parm['recId'])->update(['goods_number' => $parm['number']]);
			$cart = $this->cart();
			$subtotal = \App\Models\Cart::selectRaw("goods_price * goods_number AS subtotal")->where('rec_id', $parm['recId'])->firstOrFail();
			$result['subtotal'] = $subtotal->subtotal;
			$result['cartAmountDesc'] = $cart['total']['goodsPrice'];
//			$result['marketAmountDesc'] = sprintf('比市场价 %s 节省了 %s (%s)', $cart['total']['marketPrice'], $cart['total']['saving'], $cart['total']['saveRate']);
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return $result;
	}

	/***
	 * 清除购物车指定商品
	 * @author: colin
	 * @date: 2018/11/28 10:58
	 * @return type
	 */
	public function dropGoods()
	{
		$parm = $this->request->all();
		$parm['recIds'] = explode(',', $parm['recIds']);
		\DB::beginTransaction();
		try {
			\App\Models\Cart::where('session_id', $this->getToken())->whereIn('rec_id', $parm['recIds'])->delete();
			$this->clearCartAlone();//删除不可以独立销售的商品
			//添加商户操作日志
			\Helper\CFunctionHelper::setStoreLogs($this->getgsId(), \Enum\EnumLang::BUSINESS_ADD_CART, 'ecs_cart', '3', '清除购物车指定商品');
		} catch (\Exception $e) {

			\DB::rollBack();
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => $e->getMessage(),
				'userMsg' => '删除失败！',
				'line' => __LINE__,
			]);
		}
		\DB::commit();
		return true;
	}

	/**
	 * 删除不可以独立销售的商品
	 * @author: colin
	 * @date: 2018/11/28 10:47
	 */
	public function clearCartAlone()
	{
		/* 查询：购物车中所有不可以单独销售的配件 */
		$row = \App\Models\Cart::leftJoin('ecs_group_goods as gg', 'ecs_cart.goods_id', '=', 'gg.goods_id')->leftJoin('ecs_goods as g', 'ecs_cart.goods_id', '=', 'g.goods_id')
			->where('ecs_cart.session_id', $this->getToken())
			->where('ecs_cart.extension_code', '<>', 'package_buy')
			->where('gg.parent_id', '>', 0)
			->where('g.is_alone_sale', 0)
			->pluck('gg.parent_id');
		if (empty($row))
			return;
		$resIds = [];
		foreach ($row as $val) {
			$resIds[$val['rec_id']][] = $val['parent_id'];
		}
		/* 查询：购物车中所有商品 */
		$row = \App\Models\Cart::selectRaw("DISTINCT goods_id")->where('session_id', $this->getToken())->where('extension_code', '<>', 'package_buy')->get()->toArray();
		if (empty($row))
			return;
		$cartGood = [];
		foreach ($row as $v) {
			$cartGood[] = $v['goods_id'];
		}
		/* 如果购物车中不可以单独销售配件的基本件不存在则删除该配件 */
		$delRecId = [];
		foreach ($resIds as $key => $value) {
			foreach ($value as $v) {
				if (in_array($v, $cartGood)) {
					continue 2;
				}
			}
			$delRecId[] = $key;
		}
		if (empty($delRecId))
			return;
		/* 删除 */
		\App\Models\Cart::where('session_id', $this->getToken())->whereIn('rec_id', $delRecId)->delete();

	}

	/**
	 * 更新选中购物车商品
	 * @author: colin
	 * @date: 2018/11/28 14:28
	 * @return array|type
	 */
	public function upCheckCart()
	{
		$param = $this->request->all();
		try {
			\App\Models\Cart::where('session_id', $this->getToken())->update(['is_check' => 0]);
			if (!empty($param['recId'])) {
				$param['recId'] = explode(',', $param['recId']);
				\App\Models\Cart::where('session_id', $this->getToken())->whereIn('rec_id', $param['recId'])->update(['is_check' => 1]);
			}
			$cart = $this->cart();
			$money = $cart['total']['goodsPrice'];
			$result = ['shopPrice' => $money];
		} catch (\Exception $e) {

			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => $e->getMessage(),
				'userMsg' => '更新选中失败！',
				'line' => __LINE__,
			]);
		}
		return $result;
	}

	/***
	 * 结算列表
	 * @author: colin
	 * @date: 2018/11/27 17:28
	 * @return array|type
	 */
	public function checkout($isToal = '')
	{
		$total = [
			'goodsPrice' => 0, // 本店售价合计（有格式）
			'goodsAmount' => 0, // 本店售价合计（无格式）
			'count' => 0,
			'checkedGoods' => '',
		];
		$goodsList = [];
		try {
			/* 循环、统计 */
			$res = \App\Models\Cart::where(['session_id' => $this->getToken(), 'rec_type' => EnumKeys::CART_GENERAL_GOODS, 'is_real' => '1', 'is_check' => 1])->orderBy('rec_id', 'asc')->orderBy('goods_id', 'desc')->get()->toArray();
			if (empty($res)) {
				return [];
			}
			foreach ($res as $k => $row) {
				$total['goodsPrice'] += $row['goods_price'] * ($row['goods_number'] - $row['exceed_promote_num']) + $row['exceed_promote_num'] * $row['exceed_promote_price'];
				$total['checkedGoods'] .= $row['rec_id'] . ',';
				/* 统计商品的个数 */
				$total['count'] += intval($row['goods_number']);
				if ($isToal) {
					continue;
				}
				$row['subtotal'] = \Helper\CFunctionHelper::priceFormat($row['goods_price'] * ($row['goods_number'] - $row['exceed_promote_num']) + $row['exceed_promote_num'] * $row['exceed_promote_price'], false);
				$row['goods_price'] = \Helper\CFunctionHelper::priceFormat($row['goods_price'], false);
				$row['market_price'] = \Helper\CFunctionHelper::priceFormat($row['market_price'], false);
				$row['storesName'] = ($row['cart_stores_id'] > 0) ? \App\Models\StoresUser::where('gs_id', $row['cart_stores_id'])->value('gs_name') : '选取门店';

				$goodsThumb = \App\Models\Goods::where('goods_id', $row['goods_id'])->value('goods_thumb');
				$row['goods_thumb'] = $goodsThumb ? config('merchant.static_host') . $goodsThumb : '';
				if ($row['extension_code'] == 'package_buy') {
					$row['package_goods_list'] = self::getPackageGoods($row['goods_id']);
				}
				$goodsList[] = self::setCartGoods($row);

			}
			$total['checkedGoods'] = trim($total['checkedGoods'], ',');
			$total['goodsAmount'] = help::priceFormat($total['goodsPrice'], false);
			$total['goodsPrice'] = help::priceFormat($total['goodsPrice']);

		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		if (!empty($isToal)) {
			return ['total' => $total];
		}
		return ['goodsList' => $goodsList, 'total' => $total];
	}

	/***
	 * 订单支付
	 * @author: colin
	 * @date: 2018/11/30 14:44
	 * @return bool|type
	 */
	public function checkoutAct()
	{
		\DB::beginTransaction();
		try {
			$userName = $this->request->input('user_login_name');
			$payCode = $this->request->input('pycode');
			$code = $this->request->input('code');
			if ((empty($payCode) && strlen($payCode) == 16) && empty($userName)) {
				throw new  Exception('提交数据有误!');
			}
			if (!empty($payCode) && strlen($payCode) == 16) {//付款码
				$uData = \App\Models\UsersPaymentCode::select(['pyid', 'user_id', 'status', 'create_time_int'])->where('pcode', $payCode)->first();
				if (empty($uData)) {
					throw new Exception('请输入有效的付款码!');
				}
				$uData = $uData->toArray();
				if ($uData['status'] > 0 || $uData['create_time_int'] + 50 < time()) {
					throw new  Exception('该付款码已过期!');
				}
				$userId = $uData['user_id'];
				$pyid = $uData['pyid'];
				\App\Models\UsersPaymentCode::where('pyid', $pyid)->update(['status' => 1, 'change_time' => date('Y-m-d H:i:s')]);
				$row = \App\Models\Users::select('user_id', 'user_name', 'user_money', 'user_money_date', 'nickname', 'bonus_company')
					->where('user_id', $userId)->first();
			} else {
				//短信验证
				$key = 'mobileCode_' . $userName;
				$cacheCode = Cache::get($key);
				if (empty($cacheCode) || $cacheCode != $code) {
					throw new Exception('验证码错误！');
				}
				if ($cacheCode == $code) {
					Cache::put($key, '', 1);
				}
				$row = \App\Models\Users::select('user_id', 'user_name', 'user_money', 'user_money_date', 'nickname', 'bonus_company')
					->where('user_name', $userName)->first();
			}
			if (empty($row)) {
				throw new Exception('不存在用户或者付款码！');
			} else {
				$row = $row->toArray();
			}
			/* 取得购物类型 */
			$flowType = EnumKeys::CART_GENERAL_GOODS;
			$consignee = help::getConsignee(1);
			$consignee['user_id'] = 1;
			$cartGoods = $this->cartGoodsCheck();
			if (empty($cartGoods)) {
				throw new \Mockery\Exception('没有选中的支付商品！');
			}
			$order = $this->flowOrderInfo($row['user_id']);
			//取得门店类型
			$order['stores_type'] = \App\Models\StoresUser::where('gs_id', $this->getgsId())->value('gs_type');
			$total = $this->orderFee($order, $cartGoods, $consignee);//获得订单中的费用信息
			$order['goods_amount'] = $total['goods_price'];
			$order['user_id'] = $row['user_id'];
			$order['surplus'] = $total['amount'];
			$order['integral'] = $total['amount'];
			$order['order_amount'] = number_format($total['amount'], 2, '.', '');
			$order['order_amount_zy'] = number_format($total['amount_zy'], 2, '.', '');
			$order['add_time'] = time();
			$order['order_status'] = EnumKeys::SS_SHIPPED_ING;
			$order['shipping_status'] = EnumKeys::SS_SHIPPED;
			$order['pay_status'] = EnumKeys::PS_PAYED;
			$order['pay_id'] = 1;
			$order['pay_name'] = '余额支付';
			$order['pay_time'] = time();
			$order['extension_code'] = !empty($payCode) ? $payCode : '';
			$order['extension_id'] = 0;
			$order['order_pick_stores'] = $this->getgsId();
			$orderLxr = $this->request->input('order_lxr');
			if ($payCode) {
				//联系人 从收货地址取
				$consignee = \App\Models\UsersAddress::where('user_id', $row['user_id'])->orderBy('is_default', 'desc')->value('consignee');
				if (empty($consignee)) {
					$consignee = \App\Models\UsersAddressTmp::where('user_id', $row['user_id'])->value('uname');
				}
				$orderLxr = !empty($consignee) ? $consignee : $row['nickname'];
			}
			$order['order_lxr'] = $orderLxr;
			$order['order_tel'] = $row['user_name'];
			$order['bonus_company'] = $row['bonus_company'];
			if (!empty($this->request->input('order_pick_time'))) {
				$order['order_pick_time'] = $this->request->input('order_pick_time');
			}
			$userMoneyTotal = $row['user_money'];
			$balanceTotal = \App\Models\UserBonus::where(['user_id' => $row['user_id'], 'bonus_status' => 1])->sum('balance');
			if ($balanceTotal > 0) {
				$userMoneyTotal += $balanceTotal;
			}
			if ($total['amount'] > $userMoneyTotal) {
				throw new Exception('余额不足！请在用户端充值后操作！');
			}
			if ($balanceTotal > $total['amount']) {
				$order['bonus'] = $total['amount'];//全部用卡支付
				$order['surplus'] = 0;
			} else {
				$order['bonus'] = $balanceTotal;//卡 + 余额
				$order['surplus'] = $total['amount'] - $balanceTotal;
			};
			/* 插入订单表 */
			$orderSn = '';
			do {
				$order['order_sn'] = help::getOrderSn(); //获取新订单号
				$orderSn = \App\Models\OrderInfo::where('order_sn', $order['order_sn'])->value('order_sn');
			} while (!empty($orderSn)); //如果是订单号重复则重新提交数据
			$insertOrder = \App\Models\OrderInfo::create($order);
			$order['order_id'] = $insertOrder->order_id;
			$orderId = $insertOrder->order_id;
			/* 插入订单商品 */
			$goodInfo = \App\Models\Cart::selectRaw("{$orderId} as order_id, goods_id, goods_name, goods_sn, product_id, goods_number, market_price, goods_price, goods_attr, is_real, extension_code, parent_id, is_gift, goods_attr_id,exceed_promote_num,exceed_promote_price")
				->where(['session_id' => $this->getToken(), 'is_check' => 1, 'rec_type' => $flowType])
				->get()
				->toArray();
			\App\Models\OrderGoods::insert($goodInfo);
			foreach($goodInfo as $item){
				$goodsNow = \App\Models\Goods::where('goods_id',$item['goods_id'])->first();
				$goodsNow->saleqt += $item['goods_number'];
				$goodsNow->save();
			}
			$usedTotalMoney = 0;
			$userBonusData = \App\Models\UserBonus::selectRaw("bonus_id,balance,used_money,bonus_company")->where('user_id', $row['user_id'])->where('bonus_status', 1)->orderBy('bonus_end_date', 'desc')->get()->toArray();
			$bonusCompanyArr = [];
			foreach ($userBonusData as $bonusInfo) {
				if ($usedTotalMoney >= $total['amount']) {
					break;
				}

				$umoney = $total['amount'] - $usedTotalMoney;
				if ($bonusInfo['balance'] >= $umoney) {//够扣
					$balance = $bonusInfo['balance'] - $umoney;
				} else {//不够
					$umoney = $bonusInfo['balance'];
					$balance = 0;
				}
				$ubData = [
					'used_money' => $bonusInfo['used_money'] + $umoney,
					'balance' => $balance,
				];
				if ($balance == 0) {
					$ubData['bonus_status'] = 2;//已用完
				}
				$bonusUser = [
					'order_sn' => $order['order_sn'],
					'bonus_id' => $bonusInfo['bonus_id'],
					'status' => 1,
					'change_time' => help::localDate('Y-m-d H:i:s'),
					'used_money' => $umoney,
				];
				\App\Models\UserBonus::where('bonus_id', $bonusInfo['bonus_id'])->update($ubData);
				\App\Models\OrderBonusUser::create($bonusUser);

				help::logAccountChangeTwo($row['user_id'], $umoney, $order['order_sn'], $order['order_pick_stores'], sprintf('支付订单 %s', $order['order_sn']), $bonusInfo['bonus_id']);
				$bonusCompanyArr[] = $bonusInfo['bonus_company'];
				$usedTotalMoney += floatval($umoney);
			}
			if (!empty($bonusCompanyArr)) {
				$bonusCompanyArr = array_unique($bonusCompanyArr);
				$bonusCompany = implode('|', $bonusCompanyArr) . '(幸福券)';
			}
			if ($order['bonus'] != $usedTotalMoney) {
				throw new Exception('幸福券数据有误，请重试');
			}
			if ($order['surplus'] > 0) {
				if ($order['surplus'] > $row['user_money']) {
					throw new Exception('账户余额不足');
				}
				$nowTime = time();
				$orderData = array();
				$orderData['pay_time'] = $nowTime;
				$orderData['last_cfm_time'] = strtotime('+4 days', $nowTime);//收货时间
				if (isset($bonusCompany)) {
					$orderData['bonus_company'] = $bonusCompany;
				}
				help::logAccountChange($row['user_id'], $order['surplus'] * (-1), 0, 0, 0, sprintf('支付订单 %s', $order['order_sn']));
				help::logAccountChangeTwo($row['user_id'], $order['surplus'], $order['order_sn'], $order['order_pick_stores'], sprintf('支付订单 %s', $order['order_sn']));
			}
			if (!empty($payCode)) {
				Cache::put('paycode_order_' . $payCode, $order['order_sn'], 60 );
			}
			$this->clearCart();

		} catch (\Exception $e) {
			\DB::rollBack();
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		\DB::commit();
		$result = ['orderSn' => $order['order_sn']];
		return $result;
	}

	/**
	 * 清空购物车
	 * @param   int $type 类型：默认普通商品
	 */
	public function clearCart($type = EnumKeys::CART_GENERAL_GOODS)
	{
		\App\Models\Cart::where(['session_id' => $this->getToken(), 'rec_type' => $type])->delete();
	}

	/***
	 * 商户支付发送短信
	 * @author: colin
	 * @date: 2018/12/3 10:37
	 * @return bool|type
	 */
	public function sendSmsStores()
	{
		$mobile = $this->request->input('mobile');
		try {
			$row = \App\Models\Users::select('user_id', 'user_name', 'email', 'reg_time')->where('user_name', $mobile)->firstOrFail();
			$userId = $row->user_id;
			$mobileCode = mt_rand(111111, 999999);
			$sendFlag = help::sendMobileSmsCode($mobile, $mobileCode, $userId);
			if ($sendFlag === false) {
				throw new Exception('短信发送失败');
			}
			$key = 'mobileCode_' . $mobile;
			Cache::put($key, $mobileCode, 10);
		} catch (\Exception $e) {

			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return true;
	}

	/**
	 * Notes
	 * @author: colin
	 * @date: 2018/12/3 11:08
	 * @return bool|type
	 */
	public function checkSmscode()
	{
		$mobile = $this->request->input('mobile');
		$code = $this->request->input('code');
		try {
			$key = 'mobileCode_' . $mobile;
			$cacheCode = Cache::get($key);
			if (empty($cacheCode) || $cacheCode != $code) {
				throw new Exception('验证码错误！');
			}
			if ($cacheCode == $code) {
				Cache::put($key, '', 1);
				return true;
			}
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return true;
	}

	/**
	 * Notes
	 * @author: colin
	 * @date: 2018/11/27 13:45
	 * @param $goodsId
	 * @param $goodsPrice
	 * @param int $parentId
	 * @param $token
	 * @return array
	 */
	private function basicCountList($goodsId, $goodsPrice, $parentId = 0, $token)
	{
		$result = \App\Models\GroupGoods::where(['goods_id' => $goodsId, 'parent_id' => $parentId])->where('goods_price', '<', $goodsPrice)->get()->toArray();
		$basicList = [];
		if (!empty($result)) {
			foreach ($result as $val) {
				$basicList[$val['parent_id']] = $val['goods_price'];
			}
		}
		$basicCountList = [];
		if ($basicList) {
			$basicGoods = array_keys($basicList);
			$result = \App\Models\Cart::selectRaw("goods_id, SUM(goods_number) AS count")->where(['session_id' => $token, 'parent_id' => 0])
				->where('extension_code', '<>', 'package_buy');
			if (!empty($basicGoods)) {
				$result = $result->whereIn('goods_id', $basicGoods);
			}
			$result = $result->orderBy('goods_id', 'desc')->get()->toArray();
			if (!empty($result)) {
				foreach ($result as $val) {
					$basicCountList[$val['goods_id']] = $val['count'];
				}
			}
		}
		if ($basicCountList) {
			$basicCountListGoods = array_keys($basicCountList);
			$result = \App\Models\Cart::selectRaw("parent_id, SUM(goods_number) AS count")->where(['session_id' => $token, 'goods_id' => $goodsId])
				->where('extension_code', '<>', 'package_buy');
			if (!empty($basicCountListGoods)) {
				$result = $result->whereIn('goods_id', $basicCountListGoods);
			}
			if (!empty($result)) {
				foreach ($result as $val) {
					$basicCountList[$val['parent_id']] -= $val['count'];
				}
			}
		}
		return ['basicList' => $basicList, 'basicCountList' => $basicCountList];

	}

	/***
	 * 商品信息
	 * @author: colin
	 * @date: 2018/11/27 13:44
	 * @param $goodsId
	 * @param $num
	 * @return array|type
	 */
	private function goodInfo($goodsId, $num, $parent = 0, $token)
	{
		try {
			$count = 0;
			if ($parent > 0) {
				$count = \App\Models\Cart::where(['goods_id' => $parent, 'session_id' => $token])->where('extension_code', '<>', 'package_buy')->count();
				if ($count <= 0) {
					throw new Exception('该商品不可以单独销售！');
				}
			}
			$goods = \App\Models\Goods::selectRaw("is_real,is_shipping,brand_id,goods_sn,extension_code,goods_desc,is_alone_sale,cat_id,goods_id,goods_name,shop_price,market_price,promote_price,promote_start_date,promote_end_date,goods_img,goods_number")
				->where(['goods_id' => $goodsId, 'is_on_sale' => 1, 'is_delete' => 0, 'is_real' => 1])
				->first();
			if (!isset($goods->goods_id)) {
				throw new Exception('不存在的商品！');
			}
			$goods = self::goods($goods);
			if ($goods['sale_status'] != 1) {
				throw new Exception('该商品已经下架！');
			}
			if ($count == 0 && $goods['is_alone_sale'] == 0) {
				throw new Exception('该商品不允许单独销售！');
			}
			if ($num > $goods['goods_number']) {
				throw new Exception('添加购物车数量不能大于库存！');
			}
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return $goods;
	}

	/**
	 * 商品基础信息
	 * @author: colin
	 * @date: 2018/11/19 11:15
	 * @param $goods
	 * @return array
	 */
	private function goods($goods)
	{
		/* 修正促销价格 */
		if ($goods->promote_price > 0) {
			$goods->promote_price = \Helper\CFunctionHelper::isPromote($goods->promote_price, $goods->promote_start_date, $goods->promote_end_date);
		}
		if ($goods->goods_img) {
			$goods->goods_img = env('STATIC_HOST') . $goods->goods_img;
		}
		/* 商户商品上下架状态 */
		$saleStatus = \App\Models\GoodStoreAttr::where(['goods_id' => $goods->goods_id, 'gs_id' => $this->getgsId()])->value('sale_status');
		$saleStatus = $saleStatus ?? 1;
		$arr = [
			'cat_id' => $goods->cat_id,
			'goods_id' => $goods->goods_id,
			'goods_sn' => $goods->goods_sn,
			'goods_name' => $goods->goods_name,
			'shop_price' => $goods->shop_price,
			'market_price' => $goods->market_price,
			'promote_price' => $goods->promote_price,
			'goods_img' => $goods->goods_img,
			'goods_number' => $goods->goods_number,
			'extension_code' => $goods->extension_code,
			'brand_id' => $goods->brand_id,
			'is_real' => $goods->is_real,
			'is_shipping' => $goods->is_shipping,
			'is_alone_sale' => $goods->is_alone_sale,
			'sale_status' => $saleStatus,
		];
		return $arr;
	}

	/***
	 * 规格仓库
	 * @author: colin
	 * @date: 2018/11/27 13:42
	 * @param $goodsId
	 * @param array $spec
	 * @return array
	 */
	private function productsInfo($goodsId, $spec = [])
	{
		$productsInfo = array('product_number' => '', 'product_id' => 0);
		if (empty($spec)) {
			return $productsInfo;
		}
		sort($spec);
		$spec = implode('|', $spec);
		$productsInfo = \App\Models\Products::where('goods_id', $goodsId)->where('goods_attr', $spec)->first();
		if (isset($productsInfo->goods_id)) {
			$productsInfo = $productsInfo->toArray();
			return $productsInfo;
		}
		$productsInfo = array('product_number' => '', 'product_id' => 0);
		return $productsInfo;

	}

	/***
	 * 规格价格
	 * @author: colin
	 * @date: 2018/11/27 13:43
	 * @param $spec
	 * @return float|int|type
	 */
	private function specPrice($spec)
	{
		if (empty($spec))
			return 0;
		$specNew = $spec;
		try {
			$danGao = 0;
			foreach ($spec as $key => $val) {
				$attrId = \App\Models\GoodsAttr::where('goods_attr_id', $val)->value('attr_id');
				if ($attrId == 213) {
					$danGao = 1;
					$specBangId = $val;
					array_splice($specNew, $key, 1);
				}
			}
			if ($danGao > 0) {
				$priceOther = \App\Models\GoodsAttr::selectRaw("SUM(attr_price) AS attr_price")->whereIn('goods_attr_id', $specNew)->first();
				$priceOther = $priceOther->attr_price;
				$resBang = \App\Models\GoodsAttr::select('attr_price', 'attr_value')->where('goods_attr_id', $specBangId)->firstOrFail();
				$resBang->attr_price = str_replace("磅", "", $resBang->attr_price);
				$priceBang = floatval($resBang->attr_price);
				$priceBangValue = floatval($resBang->attr_value);
				$price = $priceBang + $priceOther * $priceBangValue;
			} else {
				$price = \App\Models\GoodsAttr::selectRaw("SUM(attr_price) AS attr_price")->whereIn('goods_attr_id', $spec)->firstOrFail();
				$price = $price->attr_price;
			}
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return $price;
	}

	/**
	 * 获得指定的商品属性
	 *
	 * @access      public
	 * @param       array $arr 规格、属性ID数组
	 * @param       type $type 设置返回结果类型：pice，显示价格，默认；no，不显示价格
	 *
	 * @return      string
	 */
	private function goodsAttrInfo($spec, $type = 'pice')
	{
		if (empty($spec))
			return false;
		try {
			$properties = \App\Models\GoodsAttr::leftJoin('ecs_attribute as a', 'ecs_goods_attr.attr_id', '=', 'a.attr_id')
				->select("a.attr_id", "a.attr_name", "a.attr_group", "a.is_linked", "a.attr_type", "ecs_goods_attr.goods_attr_id", "ecs_goods_attr.attr_value", "ecs_goods_attr.attr_price", "ecs_goods_attr.original_price")
				->whereIn('ecs_goods_attr.goods_attr_id', $spec)
				->orderBy('a.sort_order', 'asc')
				->get()->toArray();
			if (!isset($properties)) {
				throw new \Mockery\Exception('没有相关属性！');
			}
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		$attr = '';
		$fmt = "%s:%s;";
		foreach ($properties as $key => $val) {
			$attrPrice = round(floatval($properties[$key]['attr_price']), 2);
			$attr .= sprintf($fmt, $properties[$key]['attr_name'], $properties[$key]['attr_value'], $attrPrice);
		}
		$attr = trim($attr, ';');
		return $attr;
	}

	/**
	 * 取得商品最终使用价格
	 *
	 * @param   string $goodsId 商品编号
	 * @param   string $goodsNum 购买数量
	 * @param   boolean $isSpecPrice 是否加入规格价格
	 * @param   mix $spec 规格ID的数组或者逗号分隔的字符串
	 *
	 * @return  商品最终购买价格
	 */
	public function getFinalPrice($goodsId, $goodsNum = '1', $isSpecPrice = false, $spec = [])
	{
		$finalPrice = '0'; //商品最终购买价格
		$volumePrice = '0'; //商品优惠价格
		$promotePrice = '0'; //商品促销价格
		$userPrice = '0'; //商品会员价格

		//取得商品优惠价格列表
		$priceList = $this->getVolumePriceList($goodsId, '1');
		foreach ($priceList as $value) {
			if ($goodsNum >= $value['number']) {
				$volumePrice = $value['price'];
			}
		}
		//取得商品促销价格列表
		$goods = \App\Models\Goods::selectRaw("shop_price,promote_price,promote_start_date,promote_end_date")
			->where(['goods_id' => $goodsId, 'is_on_sale' => 1, 'is_delete' => 0])
			->first();
		/* 修正促销价格 */
		if ($goods->promote_price > 0) {
			$promotePrice = \Helper\CFunctionHelper::isPromote($goods->promote_price, $goods->promote_start_date, $goods->promote_end_date);
		}
		//比较商品 会员价格，优惠价格,促销价格 获取大于0的最小值
		$finalPrice = help::getMinAll([$goods->shop_price, $volumePrice, $promotePrice]);
		//如果需要加入规格价格
		if ($isSpecPrice) {
			$isspec = 0;
			foreach ($spec as $key => $val) {
				$attrId = \App\Models\GoodsAttr::where('goods_attr_id', $val)->value('attr_id');
				if ($attrId == 217) {
					$isspec = 1;
					$spec = [$val];
					break;
				}
			}
			if (!empty($spec)) {
				$specPrice = self::specPrice($spec);
				$finalPrice = ($isspec) ? $specPrice : $finalPrice+$specPrice;
			}

		}
		//返回商品最终购买价格
		return $finalPrice;
	}

	/**
	 * 取得商品优惠价格列表
	 *
	 * @param   string $goodsId 商品编号
	 * @param   string $priceType 价格类别(0为全店优惠比率，1为商品优惠价格，2为分类优惠比率)
	 *
	 * @return  优惠价格列表
	 */
	public function getVolumePriceList($goodsId, $priceType = '1')
	{
		$volumePrice = [];
		$res = \App\Models\VolumePrice::where(['goods_id' => $goodsId, 'price_type' => $priceType])->orderBy('volume_number', 'desc')->get()->toArray();
		foreach ($res as $k => $v) {
			$volumePrice[] = [
				'number' => $v['volume_number'],
				'price' => $v['volume_price'],
				'format_price' => \Helper\CFunctionHelper::priceFormat($v['volume_price']),
			];
		}
		return $volumePrice;
	}

	/**
	 * 获得指定礼包的商品
	 *
	 * @access  public
	 * @param   integer $packageId
	 * @return  array
	 */
	private function getPackageGoods($packageId)
	{
		$resource = \App\Models\PackageGoods::leftJoin('ecs_goods as g', 'ecs_package_goods.goods_id', '=', 'g.goods_id')
			->leftJoin('ecs_products as p', 'ecs_package_goods.product_id', '=', 'p.product_id')
			->select("g.goods_id", "g.goods_name", "ecs_package_goods.goods_number", "p.goods_attr", "p.product_number", "p.product_id")
			->where('ecs_package_goods.package_id', $packageId)
			->get()->toArray();
		if (emtpy($resource)) {
			return [];
		}
		$row = [];
		/* 生成结果数组 取存在货品的商品id 组合商品id与货品id */
		$goodProductStr = '';
		foreach ($resource as $val) {
			if ($val['product_id'] > 0) {
				/* 取存商品id */
				$goodProduct[] = $val['goods_id'];
				/* 组合商品id与货品id */
				$val['g_p'] = $val['goods_id'] . '_' . $val['product_id'];
			} else {
				/* 组合商品id与货品id */
				$val['g_p'] = $val['goods_id'];
			}
			//生成结果数组
			$row[] = $val;
		}
		/* 取商品属性 */
		if ($goodProductStr != '') {
			$resultGoodsAttr = \App\Models\GoodsAttr::select('goods_attr_id', 'attr_value')->whereIn('goods_id', $goodProduct)->get()->toArray();
			$goodsAttr = [];
			foreach ($resultGoodsAttr as $value) {
				$goodsAttr[$value['goods_attr_id']] = $value['attr_value'];
			}
		}
		/* 过滤货品 */
		$format[0] = '%s[%s]--[%d]';
		$format[1] = '%s--[%d]';
		foreach ($row as $key => $value) {
			if ($value['goods_attr'] != '') {
				$goodsAttrArr = explode('|', $value['goods_attr']);

				$goodsAttr = [];
				foreach ($goodsAttrArr as $_attr) {
					$goodsAttr[] = $goodsAttr[$_attr];
				}

				$row[$key]['goods_name'] = sprintf($format[0], $value['goods_name'], implode('，', $goodsAttr), $value['goods_number']);
			} else {
				$row[$key]['goods_name'] = sprintf($format[1], $value['goods_name'], $value['goods_number']);
			}
		}
		return $row;
	}

	/**
	 * 购物车列表 商品数据组装
	 * @author: colin
	 * @date: 2018/11/28 13:44
	 * @param $goods
	 * @return array
	 */
	private function setCartGoods($goods)
	{
		if (empty($goods)) {
			return [];
		}
		$data = [
			"rec_id" => $goods['rec_id'],
			"goods_id" => $goods['goods_id'],
			"goods_sn" => $goods['goods_sn'],
			"product_id" => $goods['product_id'],
			"goods_name" => $goods['goods_name'],
			"market_price" => $goods['market_price'],
			"goods_price" => $goods['goods_price'],
			"goods_number" => $goods['goods_number'],
			"goods_attr" => $goods['goods_attr'],
			"is_real" => $goods['is_real'],
			"parent_id" => $goods['parent_id'],
			"is_gift" => $goods['is_gift'],
			"goods_attr_id" => $goods['goods_attr_id'],
			"is_check" => $goods['is_check'],
			"goods_brand_id" => $goods['goods_brand_id'],
			"cart_stores_id" => $goods['cart_stores_id'],
			"subtotal" => $goods['subtotal'],
			"storesName" => $goods['storesName'],
			"goods_thumb" => $goods['goods_thumb'],
		];
		return $data;
	}

	/**
	 * 取得购物车商品
	 * @param   int $type 类型：默认普通商品
	 * @return  array   购物车商品数组
	 */
	public function cartGoods($type = EnumKeys::CART_GENERAL_GOODS)
	{
		$arr = \App\Models\Cart::selectRaw("rec_id, user_id, goods_id, goods_name, goods_sn, goods_number, is_check, market_price, goods_price, goods_attr, exceed_promote_num,exceed_promote_price,goods_attr_id, is_real, extension_code, parent_id, is_gift, is_shipping, cart_stores_id,(goods_price * (goods_number-exceed_promote_num)+exceed_promote_num*exceed_promote_price) AS subtotal")
			->where(['session_id' => $this->getToken(), 'rec_type' => $type, 'is_check' => 1])
			->get()->toArray();
		$time = time();
		//矫正促销商品价格
		foreach ($arr as $key => $value) {
			$res = \App\Models\Goods::where(['goods_id' => $value["goods_id"], 'is_promote' => 1])->where('promote_start_date', '>=', $time)->where('promote_end_date', '<=', $time)->get()->toArray();
			if (!empty($res)) {
				$resCart = \App\Models\OrderInfo::join('ecs_order_goods as b', 'ecs_order_info.order_id', '=', 'b.order_id')->join('ecs_goods as c', 'b.goods_id', '=', 'c.goods_id')
					->where(['ecs_order_info.user_id' => $value['user_id'], 'b.goods_id' => $value['goods_id']])
					->where('ecs_order_info.add_time', '>=', 'c.promote_start_date')
					->where('ecs_order_info.add_time', '<=', 'c.promote_end_date')
					->where('ecs_order_info.order_status', '<>', '2')
					->where('ecs_order_info.order_status', '<>', '3')
					->get()->toArray();
				if (!empty($resCart) && $res['promote_num'] <= $resCart['sumgoods_number']) {
					\App\Models\Cart::where('rec_id', $value["rec_id"])->update(['exceed_promote_num' => $value["goods_number"], 'exceed_promote_price' => $res['shop_price']]);

				} elseif (!empty($resCart) && $res['promote_num'] > $resCart['sumgoods_number'] && $res['promote_num'] < ($resCart['sumgoods_number'] + $value["goods_number"])) {
					$number = $resCart['sumgoods_number'] + $value["goods_number"] - $res['promote_num'];
					\App\Models\Cart::where('rec_id', $value["rec_id"])->update(['exceed_promote_num' => $number, 'exceed_promote_price' => $res['shop_price']]);
				}
			}
		}
		$arr = \App\Models\Cart::join('ecs_goods as b', 'ecs_cart.goods_id', '=', 'b.goods_id')
			->selectRaw("ecs_cart.rec_id, ecs_cart.user_id, ecs_cart.goods_id, ecs_cart.goods_name, ecs_cart.goods_sn, ecs_cart.goods_number, ecs_cart.is_check,ecs_cart.market_price,  ecs_cart.goods_price, ecs_cart.goods_attr, ecs_cart.exceed_promote_num,ecs_cart.exceed_promote_price,ecs_cart.goods_attr_id, ecs_cart.is_real, ecs_cart.extension_code, ecs_cart.parent_id, ecs_cart.is_gift, ecs_cart.is_shipping, ecs_cart.cart_stores_id, (ecs_cart.goods_price * (ecs_cart.goods_number-ecs_cart.exceed_promote_num)+ecs_cart.exceed_promote_num*ecs_cart.exceed_promote_price) AS subtotal, b.pickup_mode")
			->where('ecs_cart.session_id', $this->getToken())
			->where('ecs_cart.rec_type', $type)
			->where('ecs_cart.is_check', 1)
			->get()->toArray();
		/* 格式化价格及礼包商品 */
		foreach ($arr as $key => $value) {
			$arr[$key]['formated_market_price'] = help::priceFormat($value['market_price'], false);
			$arr[$key]['formated_goods_price'] = help::priceFormat($value['goods_price'], false);
			$arr[$key]['formated_subtotal'] = help::priceFormat($value['subtotal'], false);

			if ($value['extension_code'] == 'package_buy') {
				$arr[$key]['package_goods_list'] = self::getPackageGoods($value['goods_id']);
			}
			$goodsrow = \App\Models\Goods::select('goods_id', 'goods_thumb', 'shop_price_zy', 'shop_price_jm')->where('goods_id', $value['goods_id'])->first()->toArray();
			$arr[$key]['goods_thumb'] = config('merchant.static_host') . $goodsrow['goods_thumb'];
			$arr[$key]['shop_price_zy'] = $goodsrow['shop_price_zy'];
			$arr[$key]['shop_price_jm'] = $goodsrow['shop_price_jm'];
			$arr[$key]['is_check'] = $value['is_check'];
		}
		return $arr;
	}

	/**
	 * 取得购物车商品
	 * @param   int $type 类型：默认普通商品
	 * @return  array   购物车商品数组
	 */
	public function cartGoodsCheck($type = EnumKeys::CART_GENERAL_GOODS)
	{
		$arr = \App\Models\Cart::selectRaw("rec_id, user_id, goods_id, goods_name, goods_sn, goods_number, is_check, market_price, goods_price, goods_attr, exceed_promote_num,exceed_promote_price,goods_attr_id, is_real, extension_code, parent_id, is_gift, is_shipping, cart_stores_id,(goods_price * (goods_number-exceed_promote_num)+exceed_promote_num*exceed_promote_price) AS subtotal")
			->where(['session_id' => $this->getToken(), 'rec_type' => $type, 'is_check' => 1])
			->get()->toArray();

		/* 格式化价格 */
		foreach ($arr as $key => $value) {
			$arr[$key]['formated_market_price'] = help::priceFormat($value['market_price'], false);
			$arr[$key]['formated_goods_price'] = help::priceFormat($value['goods_price'], false);
			$arr[$key]['formated_subtotal'] = help::priceFormat($value['subtotal'], false);


			$goodsrow = \App\Models\Goods::select('goods_id', 'goods_thumb', 'shop_price_zy', 'shop_price_jm')->where('goods_id', $value['goods_id'])->first()->toArray();
			$arr[$key]['goods_thumb'] = config('merchant.static_host') . $goodsrow['goods_thumb'];
			$arr[$key]['shop_price_zy'] = $goodsrow['shop_price_zy'];
			$arr[$key]['shop_price_jm'] = $goodsrow['shop_price_jm'];
			$arr[$key]['is_check'] = $value['is_check'];
		}
		return $arr;
	}

	/**
	 * 获得订单信息
	 *
	 * @access  private
	 * @return  array
	 */
	public function flowOrderInfo($userId)
	{
		//初始化订单信息
		$order = [
			'shipping_id' => 0,
			'pay_id' => 0,
			'pack_id' => 0,// 初始化包装
			'card_id' => 0,// 初始化贺卡
			'bonus' => 0,// 初始化红包
			'integral' => 0,// 初始化积分
			'surplus' => 0,// 初始化余额
		];
		return $order;
		$order = $this->request->session()->get('flow_order', []);
		$userId = $this->request->session()->get('user_id', 0);
		/* 初始化配送和支付方式 */
		if (!isset($order['shipping_id']) || !isset($order['pay_id'])) {
			/* 如果还没有设置配送和支付 */
			if ($userId > 0) {
				/* 用户已经登录了，则获得上次使用的配送和支付 */
				$arr = $this->lastShippingAndPayment($userId);
				$order['shipping_id'] = isset($order['shipping_id']) ? $order['shipping_id'] : $arr['shipping_id'];
				$order['pay_id'] = isset($order['pay_id']) ? $order['pay_id'] : $arr['pay_id'];

			} else {
				$order['shipping_id'] = isset($order['shipping_id']) ? $order['shipping_id'] : 0;
				$order['pay_id'] = isset($order['pay_id']) ? $order['pay_id'] : 0;
			}
		}
		$order['pack_id'] = isset($order['pack_id']) ? $order['pack_id'] : 0;
		$order['card_id'] = isset($order['card_id']) ? $order['card_id'] : 0;
		$order['bonus'] = isset($order['bonus']) ? $order['bonus'] : 0;
		$order['integral'] = isset($order['integral']) ? $order['integral'] : 0;
		$order['surplus'] = isset($order['surplus']) ? $order['surplus'] : 0;

		$flowType = $this->request->session()->get('flow_type', '');
		/* 扩展信息 */
		if (!empty($flowType) && intval($flowType) != EnumKeys::CART_GENERAL_GOODS) {
			$order['extension_code'] = $this->request->session()->get('extension_code');
			$order['extension_id'] = $this->request->session()->get('extension_id');
		}
		return $order;
	}

	/**
	 * 获得上一次用户采用的支付和配送方式
	 *
	 * @access  public
	 * @return  void
	 */
	public function lastShippingAndPayment($userId)
	{
		$row = \App\Models\OrderInfo::select('shipping_id', 'pay_id')->where('user_id', $userId)->first()->toArray();
		if (empty($row)) {
			/* 如果获得是一个空数组，则返回默认值 */
			$row = ['shipping_id' => 0, 'pay_id' => 0];
		}
		return $row;
	}

	/**
	 * 获得订单中的费用信息
	 *
	 * @access  public
	 * @param   array $order
	 * @param   array $goods
	 * @param   array $consignee
	 * @param   bool $isGbDeposit 是否团购保证金（如果是，应付款金额只计算商品总额和支付费用，可以获得的积分取 $giftIntegral）
	 * @return  array
	 */
	public function orderFee($order, $goods, $consignee)
	{
		/* 初始化订单的扩展code */
		$order['extension_code'] = '';
		if ($order['extension_code'] == 'group_buy') {
			$groupBuy = $this->groupBuyInfo($order['extension_id']);
		}
		$total = [
			'real_goods_count' => 0,
			'gift_amount' => 0,
			'goods_price' => 0,
			'market_price' => 0,
			'discount' => 0,
			'pack_fee' => 0,
			'card_fee' => 0,
			'shipping_fee' => 0,
			'shipping_insure' => 0,
			'integral_money' => 0,
			'bonus' => 0,
			'surplus' => 0,
			'cod_fee' => 0,
			'pay_fee' => 0,
			'goods_price_zy' => 0,
			'tax' => 0,
		];

		/* 商品总价 */
		foreach ($goods AS $val) {
			/* 统计实体商品的个数 */
			if ($val['is_real']) {
				$total['real_goods_count']++;
			}
			$total['goods_price'] += ($val['goods_price'] * ($val['goods_number'] - $val['exceed_promote_num']) + $val['exceed_promote_price'] * $val['exceed_promote_num']);
			$total['market_price'] += $val['market_price'] * $val['goods_number'];
			$val['goods_price'] = $val['exceed_promote_price'];
			//计算直营店价格
			if ($order['stores_type'] == 1)  //判断是直营店 还是 加盟店
			{
				if ($val['shop_price_zy'] > 0) {
					$total['goods_price_zy'] += $val['goods_price'] * $val['goods_number'] * $val['shop_price_zy'];
				} else {
					$total['goods_price_zy'] += $val['goods_price'] * $val['goods_number'];
				}
			} else if ($order['stores_type'] == 2) {
				//计算加盟店价格
				if ($val['shop_price_jm'] > 0) {
					$total['goods_price_zy'] += $val['goods_price'] * $val['goods_number'] * $val['shop_price_jm'];
				} else {
					$total['goods_price_zy'] += $val['goods_price'] * $val['goods_number'];
				}
			}
		}
		$total['saving'] = $total['market_price'] - $total['goods_price'];
		$total['save_rate'] = $total['market_price'] ? round($total['saving'] * 100 / $total['market_price']) . '%' : 0;

		$total['goods_price_formated'] = help::priceFormat($total['goods_price'], false);
		$total['market_price_formated'] = help::priceFormat($total['market_price'], false);
		$total['saving_formated'] = help::priceFormat($total['saving'], false);

		/* 折扣 */
		if ($order['extension_code'] != 'group_buy') {
			$discount = $this->computeDiscount();
			$total['discount'] = $discount['discount'];
			if ($total['discount'] > $total['goods_price']) {
				$total['discount'] = $total['goods_price'];
			}
		}
		$total['discount_formated'] = help::priceFormat($total['discount'], false);

		/* 税额 */
		if (!empty($order['need_inv']) && $order['inv_type'] != '') {
			/* 查税率 */
			$rate = 0;
			$CFG = \Enum\EnumLang::loadConfig();
			foreach ($CFG['invoice_type']['type'] as $key => $type) {
				if ($type == $order['inv_type']) {
					$rate = floatval($CFG['invoice_type']['rate'][$key]) / 100;
					break;
				}
			}
			if ($rate > 0) {
				$total['tax'] = $rate * $total['goods_price'];
			}
		}
		$total['tax_formated'] = help::priceFormat($total['tax'], false);

		/* 包装费用 */
		if (!empty($order['pack_id'])) {
			$total['pack_fee'] = help::packFee($order['pack_id'], $total['goods_price']);
		}
		$total['pack_fee_formated'] = help::priceFormat($total['pack_fee'], false);

		/* 贺卡费用 */
		if (!empty($order['card_id'])) {
			$total['card_fee'] = help::cardFee($order['card_id'], $total['goods_price']);
		}
		$total['card_fee_formated'] = help::priceFormat($total['card_fee'], false);

		/* 配送费用 */
		$shippingCodFee = NULL;

		if ($order['shipping_id'] > 0 && $total['real_goods_count'] > 0) {
			$region['country'] = $consignee['country'];
			$region['province'] = $consignee['province'];
			$region['city'] = $consignee['city'];
			$region['district'] = $consignee['district'];
			$shippingInfo = help::shippingAreaInfo($order['shipping_id'], $region);

			if (!empty($shippingInfo)) {
				if ($order['extension_code'] == 'group_buy') {
					$weightPrice = $this->cartWeightPrice(EnumKeys::CART_GROUP_BUY_GOODS);
				} else {
					$weightPrice = $this->cartWeightPrice();
				}

				// 查看购物车中是否全为免运费商品，若是则把运费赋为零
				$shippingCount = \App\Models\Cart::where(['session_id' => $this->getToken(), 'is_shipping' => 0, 'is_check' => 1])->where('extension_code', '<>', 'package_buy')->count();

				$total['shipping_fee'] = ($shippingCount == 0 && $weightPrice['free_shipping'] == 1) ? 0 : $this->shippingFee($shippingInfo['shipping_code'], $shippingInfo['configure'], $weightPrice['weight'], $total['goods_price'], $weightPrice['number']);

				if (!empty($order['need_insure']) && $shippingInfo['insure'] > 0) {
					$total['shipping_insure'] = $this->shippingInsureFee($shippingInfo['shipping_code'],
						$total['goods_price'], $shippingInfo['insure']);
				} else {
					$total['shipping_insure'] = 0;
				}

				if ($shippingInfo['support_cod']) {
					$shippingCodFee = $shippingInfo['pay_fee'];
				}
			}
		}

		$total['shipping_fee_formated'] = help::priceFormat($total['shipping_fee'], false);
		$total['shipping_insure_formated'] = help::priceFormat($total['shipping_insure'], false);

		// 购物车中的商品能享受红包支付的总额
		$bonusAmount = $this->computeDiscountAmount();
		// 红包和积分最多能支付的金额为商品总额
		$maxAmount = $total['goods_price'] == 0 ? $total['goods_price'] : $total['goods_price'] - $bonusAmount;

		/* 计算订单总额 */
		if ($order['extension_code'] == 'group_buy' && $groupBuy['deposit'] > 0) {
			$total['amount'] = $total['goods_price'];
		} else {
			$total['amount'] = $total['goods_price'] - $total['discount'] + $total['tax'] + $total['pack_fee'] + $total['card_fee'] +
				$total['shipping_fee'] + $total['shipping_insure'] + $total['cod_fee'];
		}

		/* 余额 */
		$order['surplus'] = $order['surplus'] > 0 ? $order['surplus'] : 0;
		if ($total['amount'] > 0) {
			if (isset($order['surplus']) && $order['surplus'] > $total['amount']) {
				$order['surplus'] = $total['amount'];
				$total['amount'] = $total['amount'];
			} else {
				$total['amount'] -= floatval($order['surplus']);
			}
		} else {
			$order['surplus'] = 0;
			$total['amount'] = 0;
		}
		$total['surplus'] = $order['surplus'];
		$total['surplus_formated'] = help::priceFormat($order['surplus'], false);

		/* 积分 */
		$order['integral'] = $order['integral'] > 0 ? $order['integral'] : 0;
		if ($total['amount'] > 0 && $maxAmount > 0 && $order['integral'] > 0) {
			$integralMoney = help::valueOfIntegral($order['integral']);

			// 使用积分支付
			$useIntegral = min($total['amount'], $maxAmount, $integralMoney); // 实际使用积分支付的金额
			$total['amount'] -= $useIntegral;
			$total['integral_money'] = $useIntegral;
			$order['integral'] = help::valueOfIntegral($useIntegral);
		} else {
			$total['integral_money'] = 0;
			$order['integral'] = 0;
		}
		$total['integral'] = $order['integral'];
		$total['integral_formated'] = help::priceFormat($total['integral_money'], false);

		/* 保存订单信息 */
		$this->request->session()->put('flow_order', $order);

		$flowType = $this->request->session()->get('flow_type', '');

		/* 支付费用 */
		if (!empty($order['pay_id']) && ($total['real_goods_count'] > 0 || $flowType != EnumKeys::CART_EXCHANGE_GOODS)) {
			$total['pay_fee'] = $this->payFee($order['pay_id'], $total['amount'], $shippingCodFee);
		}
		$total['pay_fee_formated'] = help::priceFormat($total['pay_fee'], false);

		$total['amount'] += $total['pay_fee']; // 订单总额累加上支付费用
		$total['amount_formated'] = help::priceFormat($total['amount'], false);

		//计算直营 加盟 价格
		if (isset($total['goods_price_zy']) && $total['goods_price_zy'] > 0) {
			$ortherAmount = $total['amount'] - $total['goods_price'];
			$total['amount_zy'] = $total['goods_price_zy'] + $ortherAmount;
		} else {
			$total['amount_zy'] = $total['amount'];
		}
		/* 取得可以得到的积分和红包 */
		if ($order['extension_code'] == 'group_buy') {
			$total['will_get_integral'] = $groupBuy['gift_integral'];
		} elseif ($order['extension_code'] == 'exchange_goods') {
			$total['will_get_integral'] = 0;
		} else {
			$total['will_get_integral'] = $this->giveIntegral($goods);
		}
		$totalBonus = $this->totalBonus();
		$total['will_get_bonus'] = $order['extension_code'] == 'exchange_goods' ? 0 : help::priceFormat($totalBonus, false);
		$total['formated_goods_price'] = help::priceFormat($total['goods_price'], false);
		$total['formated_market_price'] = help::priceFormat($total['market_price'], false);
		$total['formated_saving'] = help::priceFormat($total['saving'], false);


		if ($order['extension_code'] == 'exchange_goods') {
			$exchangeIntegral = \App\Models\Cart::join('ecs_exchange_goods as eg', 'ecs_cart.goods_id', '=', 'eg.goods_id')
				->selectRaw("SUM(eg.exchange_integral) as exchange_integral")
				->where(['ecs_cart.session_id' => $this->getToken(), 'ecs.cart.rec_type' => EnumKeys::CART_EXCHANGE_GOODS, 'ecs_cart.is_gift' => 0, 'ecs_cart.is_check' => 1])
				->where('ecs_cart.goods_id', '>', 0)
				->first()->toArray();
			$exchangeIntegral = $exchangeIntegral['exchange_integral'] ?? 0;
			$total['exchange_integral'] = $exchangeIntegral;
		}
		return $total;
	}

	/**
	 * 取得团购活动信息
	 * @param   int $groupBuyId 团购活动id
	 * @param   int $currentNum 本次购买数量（计算当前价时要加上的数量）
	 * @return  array
	 *                  status          状态：
	 */
	public function groupBuyInfo($groupBuyId, $currentNum = 0)
	{
		/* 取得团购活动信息 */
		$groupBuyId = intval($groupBuyId);
		if (empty($groupBuyId)) {
			return [];
		}
		$groupBuy = \App\Models\GoodsActivity::selectRaw("*, act_id AS group_buy_id, act_desc AS group_buy_desc, start_time AS start_date, end_time AS end_date")
			->where('act_id', $groupBuyId)
			->where('act_type', EnumKeys::GAT_GROUP_BUY)
			->first()->toArray();

		/* 如果为空，返回空数组 */
		if (empty($groupBuy)) {
			return [];
		}

		$extInfo = unserialize($groupBuy['ext_info']);
		$groupBuy = array_merge($groupBuy, $extInfo);

		/* 格式化时间 */
		$groupBuy['formated_start_date'] = date('Y-m-d H:i', $groupBuy['start_time']);
		$groupBuy['formated_end_date'] = date('Y-m-d H:i', $groupBuy['end_time']);

		/* 格式化保证金 */
		$groupBuy['formated_deposit'] = help::priceFormat($groupBuy['deposit'], false);

		/* 处理价格阶梯 */
		$priceLadder = $groupBuy['price_ladder'];
		if (!is_array($priceLadder) || empty($priceLadder)) {
			$priceLadder = array(array('amount' => 0, 'price' => 0));
		} else {
			foreach ($priceLadder as $key => $amountPrice) {
				$priceLadder[$key]['formated_price'] = help::priceFormat($amountPrice['price'], false);
			}
		}
		$groupBuy['price_ladder'] = $priceLadder;

		/* 统计信息 */
		$stat = $this->groupBuyStat($groupBuyId, $groupBuy['deposit']);
		$groupBuy = array_merge($groupBuy, $stat);

		/* 计算当前价 */
		$curPrice = $priceLadder[0]['price']; // 初始化
		$curAmount = $stat['valid_goods'] + $currentNum; // 当前数量
		foreach ($priceLadder as $amountPrice) {
			if ($curAmount >= $amountPrice['amount']) {
				$curPrice = $amountPrice['price'];
			} else {
				break;
			}
		}
		$groupBuy['cur_price'] = $curPrice;
		$groupBuy['formated_cur_price'] = help::priceFormat($curPrice, false);

		/* 最终价 */
		$groupBuy['trans_price'] = $groupBuy['cur_price'];
		$groupBuy['formated_trans_price'] = $groupBuy['formated_cur_price'];
		$groupBuy['trans_amount'] = $groupBuy['valid_goods'];

		/* 状态 */
		$groupBuy['status'] = $this->groupBuyStatus($groupBuy);
		if (isset($GLOBALS['_LANG']['gbs'][$groupBuy['status']])) {
			$groupBuy['status_desc'] = $GLOBALS['_LANG']['gbs'][$groupBuy['status']];
		}

		$groupBuy['start_time'] = $groupBuy['formated_start_date'];
		$groupBuy['end_time'] = $groupBuy['formated_end_date'];

		return $groupBuy;
	}

	/**
	 * 计算折扣：根据购物车和优惠活动
	 * @return  float   折扣
	 */
	public function computeDiscount()
	{
		/* 查询优惠活动 */
		$now = time();
		$userRank = $this->request->session()->get('user_rank', '');
		if (empty($userRank))
			return 0;
		$userRank = ',' . $userRank . ',';
		$favourableList = \App\Models\FavourableActivity::where('start_time', '<=', $now)
			->where('end_time', '>=', $now)
			->whereRaw(" CONCAT(',', user_rank, ',') LIKE '%" . $userRank . "%' ")
			->whereIn('act_type', [EnumKeys::FAT_DISCOUNT, EnumKeys::FAT_PRICE]);
		if (!$favourableList) {
			return 0;
		}
		/* 查询购物车商品 */
		$goodsList = \App\Models\Cart::join('ecs_goods as g', 'esc_cart.goods_id', '=', 'g.goods_id')
			->selectRaw("esc_cart.goods_id, esc_cart.goods_price * esc_cart.goods_number AS subtotal, g.cat_id, g.brand_id")
			->where("esc_cart.session_id", $this->getToken())
			->where(['esc_cart.parent_id' => 0, 'esc_cart.is_gift' => 0, 'esc_cart.rec_type' => EnumKeys::CART_GENERAL_GOODS])
			->get()->toArray();
		if (!empty($goodsList)) {
			return 0;
		}
		/* 初始化折扣 */
		$discount = 0;
		$favourableName = [];
		$MerchantUser = new \Library\MerchantUser($this->request);
		/* 循环计算每个优惠活动的折扣 */
		foreach ($favourableList as $favourable) {
			$totalAmount = 0;
			switch ($favourable['act_range']) {
				case EnumKeys::FAR_ALL:
					foreach ($goodsList as $goods) {
						$totalAmount += $goods['subtotal'];
					}
					break;
				case EnumKeys::FAR_CATEGORY:
					/* 找出分类id的子分类id */
					$idList = [];
					$rawIdList = explode(',', $favourable['act_range_ext']);
					foreach ($rawIdList as $id) {
						$idList = array_merge($idList, array_keys($MerchantUser->catList($id, 0)));
					}
					$ids = join(',', array_unique($idList));
					foreach ($goodsList as $goods) {
						if (strpos(',' . $ids . ',', ',' . $goods['cat_id'] . ',') !== false) {
							$totalAmount += $goods['subtotal'];
						}
					}
					break;
				case EnumKeys::FAR_BRAND:
					foreach ($goodsList as $goods) {
						if (strpos(',' . $favourable['act_range_ext'] . ',', ',' . $goods['brand_id'] . ',') !== false) {
							$totalAmount += $goods['subtotal'];
						}
					}
					break;
				case EnumKeys::FAR_GOODS:
					foreach ($goodsList as $goods) {
						if (strpos(',' . $favourable['act_range_ext'] . ',', ',' . $goods['goods_id'] . ',') !== false) {
							$totalAmount += $goods['subtotal'];
						}
					}
					break;
				default:
					break;

			}

			/* 如果金额满足条件，累计折扣 */
			if ($totalAmount > 0 && $totalAmount >= $favourable['min_amount'] && ($totalAmount <= $favourable['max_amount'] || $favourable['max_amount'] == 0)) {
				if ($favourable['act_type'] == EnumKeys::FAT_DISCOUNT) {
					$discount += $totalAmount * (1 - $favourable['act_type_ext'] / 100);

					$favourableName[] = $favourable['act_name'];
				} elseif ($favourable['act_type'] == EnumKeys::FAT_PRICE) {
					$discount += $favourable['act_type_ext'];

					$favourableName[] = $favourable['act_name'];
				}
			}
		}

		return ['discount' => $discount, 'name' => $favourableName];
	}

	/**
	 * 获得购物车中商品的总重量、总价格、总数量
	 *
	 * @access  public
	 * @param   int $type 类型：默认普通商品
	 * @return  array
	 */
	public function cartWeightPrice($type = EnumKeys::CART_GENERAL_GOODS)
	{
		$packageRow['weight'] = 0;
		$packageRow['amount'] = 0;
		$packageRow['number'] = 0;

		$packagesRow['free_shipping'] = 1;

		/* 计算超值礼包内商品的相关配送参数 */
		$row = \App\Models\Cart::select('goods_id', 'goods_number', 'goods_price')->where(['extension_code' => 'package_buy', 'session_id' => $this->getToken()]);
		if (!empty($row)) {
			$packagesRow['free_shipping'] = 0;
			$freeShippingCount = 0;

			foreach ($row as $val) {
				// 如果商品全为免运费商品，设置一个标识变量
				$shippingCount = \App\Models\PackageGoods::join('ecs_goods as g', 'ecs_package_goods.goods_id', '=', 'g.goods_id')
					->where('g.is_shipping', 0)
					->where('ecs_package_goods.package_id', $val['goods_id'])
					->count();
				if ($shippingCount > 0) {
					// 循环计算每个超值礼包商品的重量和数量，注意一个礼包中可能包换若干个同一商品
					$goodsRow = \App\Models\PackageGoods::join('ecs_goods as g', 'ecs_package_goods.goods_id', '=', 'g.goods_id')
						->selectRaw("SUM(g.goods_weight * ecs_package_goods.goods_number) AS weight")
						->where('ecs_package_goods.package_id', $val['goods_id'])
						->firt()->toArray();
					$packageRow['weight'] += floatval($goodsRow['weight']) * $val['goods_number'];
					$packageRow['amount'] += floatval($val['goods_price']) * $val['goods_number'];
					$packageRow['number'] += intval($goodsRow['number']) * $val['goods_number'];
				} else {
					$freeShippingCount++;
				}
			}

			$packagesRow['free_shipping'] = $freeShippingCount == count($row) ? 1 : 0;
		}

		/* 获得购物车中非超值礼包商品的总重量 */
		$row = \App\Models\Cart::leftJoin('ecs_goods as g')
			->selectRaw("SUM(g.goods_weight * ecs_cart.goods_number) AS weight,SUM(ecs_cart.goods_price * ecs_cart.goods_number) AS amount,SUM(ecs_cart.goods_number) AS number")
			->where('ecs_cart.session_id', $this->getToken())
			->where('ecs_cart.rec_type', $type)
			->where('g.is_shipping', 0)
			->where('ecs_cart.extension_code', '<>', 'package_buy')
			->first()->toArray();
		$packagesRow['weight'] = floatval($row['weight']) + $packageRow['weight'];
		$packagesRow['amount'] = floatval($row['amount']) + $packageRow['amount'];
		$packagesRow['number'] = intval($row['number']) + $packageRow['number'];
		/* 格式化重量 */
		$packagesRow['formated_weight'] = help::formatedWeight($packageRow['weight']);

		return $packagesRow;
	}

	/**
	 * 计算运费
	 * @param   string $shippingCode 配送方式代码
	 * @param   mix $shippingConfig 配送方式配置信息
	 * @param   float $goodsWeight 商品重量
	 * @param   float $goodsAmount 商品金额
	 * @param   float $goodsNumber 商品数量
	 * @return  float   运费
	 */
	public function shippingFee($shippingCode, $shippingConfig, $goodsWeight, $goodsAmount, $goodsNumber = '')
	{
		if (!is_array($shippingConfig)) {
			$shippingConfig = unserialize($shippingConfig);
		}
		try {
			$ShippingObj = \Library\Factory\Shipping\ShippingFactory::make($shippingCode, $shippingConfig);
			return $ShippingObj->calculate($goodsWeight, $goodsAmount, $goodsNumber);
		} catch (\Exception $e) {
			return 0;
		}
	}

	/**
	 * 获取指定配送的保价费用
	 *
	 * @access  public
	 * @param   string $shippingCode 配送方式的code
	 * @param   float $goodsAmount 保价金额
	 * @param   mix $insure 保价比例
	 * @return  float
	 */
	public function shippingInsureFee($shippingCode, $goodsAmount, $insure)
	{
		/* 如果保价费用不是百分比则直接返回该数值 */
		if (strpos($insure, '%') === false) {
			return floatval($insure);
		}
		try {
			$ShippingObj = \Library\Factory\Shipping\ShippingFactory::make($shippingCode, '');
			$insure = floatval($insure) / 100;
			if (method_exists($ShippingObj, 'calculate_insure')) {
				return $ShippingObj->calculate_insure($goodsAmount, $insure);
			} else {
				return ceil($goodsAmount * $insure);
			}
		} catch (\Exception $e) {
			return false;
		}
	}

	/**
	 * 计算购物车中的商品能享受红包支付的总额
	 * @return  float   享受红包支付的总额
	 */
	public function computeDiscountAmount()
	{
		/* 查询优惠活动 */
		$now = time();
		$userRank = $this->request->session()->get('user_rank', '');
		if (empty($userRank))
			return 0;
		$userRank = ',' . $userRank . ',';
		$favourableList = \App\Models\FavourableActivity::where('start_time', '<=', $now)
			->where('end_time', '>=', $now)
			->whereRaw(" CONCAT(',', user_rank, ',') LIKE '%" . $userRank . "%' ")
			->whereIn('act_type', [EnumKeys::FAT_DISCOUNT, EnumKeys::FAT_PRICE]);
		if (!$favourableList) {
			return 0;
		}
		/* 查询购物车商品 */
		$goodsList = \App\Models\Cart::join('ecs_goods as g', 'esc_cart.goods_id', '=', 'g.goods_id')
			->selectRaw("esc_cart.goods_id, esc_cart.goods_price * esc_cart.goods_number AS subtotal, g.cat_id, g.brand_id")
			->where("esc_cart.session_id", $this->getToken())
			->where(['esc_cart.parent_id' => 0, 'esc_cart.is_gift' => 0, 'esc_cart.rec_type' => EnumKeys::CART_GENERAL_GOODS])
			->get()->toArray();
		if (!empty($goodsList)) {
			return 0;
		}
		/* 初始化折扣 */
		$discount = 0;
		$favourableName = [];
		$MerchantUser = new \Library\MerchantUser($this->request);
		/* 循环计算每个优惠活动的折扣 */
		foreach ($favourableList as $favourable) {
			$totalAmount = 0;
			switch ($favourable['act_range']) {
				case EnumKeys::FAR_ALL:
					foreach ($goodsList as $goods) {
						$totalAmount += $goods['subtotal'];
					}
					break;
				case EnumKeys::FAR_CATEGORY:
					/* 找出分类id的子分类id */
					$idList = [];
					$rawIdList = explode(',', $favourable['act_range_ext']);
					foreach ($rawIdList as $id) {
						$idList = array_merge($idList, array_keys($MerchantUser->catList($id, 0)));
					}
					$ids = join(',', array_unique($idList));
					foreach ($goodsList as $goods) {
						if (strpos(',' . $ids . ',', ',' . $goods['cat_id'] . ',') !== false) {
							$totalAmount += $goods['subtotal'];
						}
					}
					break;
				case EnumKeys::FAR_BRAND:
					foreach ($goodsList as $goods) {
						if (strpos(',' . $favourable['act_range_ext'] . ',', ',' . $goods['brand_id'] . ',') !== false) {
							$totalAmount += $goods['subtotal'];
						}
					}
					break;
				case EnumKeys::FAR_GOODS:
					foreach ($goodsList as $goods) {
						if (strpos(',' . $favourable['act_range_ext'] . ',', ',' . $goods['goods_id'] . ',') !== false) {
							$totalAmount += $goods['subtotal'];
						}
					}
					break;
				default:
					break;

			}

			/* 如果金额满足条件，累计折扣 */
			if ($totalAmount > 0 && $totalAmount >= $favourable['min_amount'] && ($totalAmount <= $favourable['max_amount'] || $favourable['max_amount'] == 0)) {
				if ($favourable['act_type'] == EnumKeys::FAT_DISCOUNT) {
					$discount += $totalAmount * (1 - $favourable['act_type_ext'] / 100);
				} elseif ($favourable['act_type'] == EnumKeys::FAT_PRICE) {
					$discount += $favourable['act_type_ext'];
				}
			}
		}
		return $discount;
	}

	/**
	 * 获得订单需要支付的支付费用
	 *
	 * @access  public
	 * @param   integer $paymentId
	 * @param   float $orderAmount
	 * @param   mix $codFee
	 * @return  float
	 */
	public function payFee($paymentId, $orderAmount, $codFee = null)
	{
		$payment = self::paymentInfo($paymentId);
		$rate = ($payment['is_cod'] && !is_null($codFee)) ? $codFee : $payment['pay_fee'];

		if (strpos($rate, '%') !== false) {
			/* 支付费用是一个比例 */
			$val = floatval($rate) / 100;
			$payFee = $val > 0 ? $orderAmount * $val / (1 - $val) : 0;
		} else {
			$payFee = floatval($rate);
		}

		return round($payFee, 2);
	}

	/**
	 * 取得支付方式信息
	 * @param   int $payId 支付方式id
	 * @return  array   支付方式信息
	 */
	private function paymentInfo($payId)
	{
		$payment = \App\Models\Payment::where(['pay_id' => $payId, 'enabled' => 1])->first()->toArray();
		return $payment;
	}

	/**
	 * 取得购物车该赠送的积分数
	 * @return  int     积分数
	 */
	public function giveIntegral()
	{
		$sum = \App\Models\Cart::join('ecs_goods as g', 'ecs_cart.goods_id', 'g.goods_id')
			->selectRaw("SUM(ecs_cart.goods_number * IF(g.give_integral > -1, g.give_integral, ecs_cart.goods_price)) as sum")
			->where('ecs_cart.goods_id', '>', 0)
			->where(['ecs_cart.session_id' => $this->getToken(), 'ecs_cart.parent_id' => 0, 'ecs_cart.rec_type' => 0, 'ecs_cart.is_check' => 0, 'ecs_cart.is_gift' => 0])
			->first()->toArray();
		return intval($sum['sum']);
	}

	/***
	 * 取得当前用户应该得到的红包总额
	 * @author: colin
	 * @date: 2018/11/30 9:58
	 * @return float
	 */
	public function totalBonus()
	{
		$day = getdate();
		$today = help::localMktime(23, 59, 59, $day['mon'], $day['mday'], $day['year']);
		/* 按商品发的红包 */
		$goodsTotal = \App\Models\Cart::join('ecs_goods as g', 'ecs_cart.goods_id', '=', 'g.goods_id')->join('ecs_bonus_type as t', 'g.bonus_type_id', '=', 't.type_id')
			->selectRaw("SUM(ecs_cart.goods_number * t.type_money) as goodsTotal")
			->where(['ecs_cart.session_id' => $this->getToken(), 'ecs_cart.is_gift' => 0, 't.send_type' => EnumKeys::SEND_BY_GOODS, 'ecs_cart.rec_type' => EnumKeys::CART_GENERAL_GOODS, 'ecs_cart.is_check' => 1])
			->where('t.send_start_date', '<=', $today)
			->where('t.send_end_date', '>=', $today)
			->first()->toArray();
		$goodsTotal = floatval($goodsTotal['goodsTotal']);
		/* 取得购物车中非赠品总金额 */
		$amount = \App\Models\Cart::selectRaw("SUM(goods_price * goods_number) as amount")
			->where(['session_id' => $this->getToken(), 'is_gift' => 0, 'rec_type' => EnumKeys::CART_GENERAL_GOODS])
			->first()->toArray();
		$amount = floatval($amount['amount']);
		/* 按订单发的红包 */
		$orderTotal = \App\Models\BonusType::selectRaw("FLOOR('$amount' / min_amount) * type_money as total")
			->where('send_type', EnumKeys::SEND_BY_ORDER)
			->where('send_start_date', '<=', $today)
			->where('send_end_date', '>=', $today)
			->where('min_amount', '>', 0)
			->first();
		$orderTotal = empty($orderTotal) ? 0 : $orderTotal->total;
		$orderTotal = floatval($orderTotal);
		return $goodsTotal + $orderTotal;
	}

	/*
	 * 取得某团购活动统计信息
	 * @param   int     $groupBuyId   团购活动id
	 * @param   float   $deposit        保证金
	 * @return  array   统计信息
	 */
	public function groupBuyStat($groupBuyId, $deposit)
	{
		$groupBuyId = intval($groupBuyId);

		/* 取得团购活动商品ID */
		$groupBuyGoodsId = \App\Models\GoodsActivity::where(['act_id' => $groupBuyId, 'act_type' => EnumKeys::GAT_GROUP_BUY])->value('goods_id');
		if (empty($groupBuyGoodsId)) {
			return [];
		}
		/* 取得总订单数和总商品数 */
		$sql = "SELECT COUNT(*) AS total_order, SUM(g.goods_number) AS total_goods " .
			"FROM " . $GLOBALS['ecs']->table('order_info') . " AS o, " .
			$GLOBALS['ecs']->table('order_goods') . " AS g " .
			" WHERE o.order_id = g.order_id " .
			"AND o.extension_code = 'group_buy' " .
			"AND o.extension_id = '$groupBuyId' " .
			"AND g.goods_id = '$groupBuyGoodsId' " .
			"AND (order_status = '" . OS_CONFIRMED . "' OR order_status = '" . OS_UNCONFIRMED . "')";
		$stat = $GLOBALS['db']->getRow($sql);
		if ($stat['total_order'] == 0) {
			$stat['total_goods'] = 0;
		}
		$confirmed = EnumKeys::OS_CONFIRMED;
		$unconfirmed = EnumKeys::OS_UNCONFIRMED;
		$stat = \App\Models\OrderInfo::join('ecs_order_goods as g', 'ecs_order_info.order_id', '=', 'g.order_id')
			->selectRaw("COUNT(*) AS total_order, SUM(g.goods_number) AS total_goods")
			->where(['ecs_order_info.extension_code' => 'group_buy', 'ecs_order_info.extension_id' => $groupBuyId, 'g.goods_id' => $groupBuyGoodsId])
			->where(function ($query) use ($confirmed, $unconfirmed) {
				$query->where('ecs_order_info.order_status', $confirmed)->orWhere('ecs_order_info.order_status', $unconfirmed);
			});
		$row = $stat;
		$stat = $stat->first()->toArray();
		if (empty($stat)) {
			return [];
		}
		if ($stat['total_order'] == 0) {
			$stat['total_goods'] = 0;
		}
		/* 取得有效订单数和有效商品数 */
		$deposit = floatval($deposit);
		if ($deposit > 0 && $stat['total_order'] > 0) {
			$row = $row->whereRaw(" AND (o.money_paid + o.surplus) >= '$deposit'")->first->toArray();
			$stat['valid_order'] = $row['total_order'];
			if ($stat['valid_order'] == 0) {
				$stat['valid_goods'] = 0;
			} else {
				$stat['valid_goods'] = $row['total_goods'];
			}
		} else {
			$stat['valid_order'] = $stat['total_order'];
			$stat['valid_goods'] = $stat['total_goods'];
		}

		return $stat;
	}

	/**
	 * 获得团购的状态
	 *
	 * @access  public
	 * @param   array
	 * @return  integer
	 */
	public function groupBuyStatus($groupbuy)
	{
		$now = time();
		switch ($groupbuy['is_finished']) {
			case 0:
				/* 未处理 */
				if ($now < $groupbuy['start_time']) {
					$status = EnumKeys::GBS_PRE_START;
				} elseif ($now > $groupbuy['end_time']) {
					$status = EnumKeys::GBS_FINISHED;
				} else {
					$status = ($groupbuy['restrict_amount'] == 0 || $groupbuy['valid_goods'] < $groupbuy['restrict_amount']) ? EnumKeys::GBS_UNDER_WAY : EnumKeys::GBS_FINISHED;
				}
				break;
			case EnumKeys::GBS_SUCCEED:
				/* 已处理，团购成功 */
				$status = EnumKeys::GBS_SUCCEED;
				break;
			case $groupbuy['is_finished'] == EnumKeys::GBS_FAIL:
				/* 已处理，团购失败 */
				$status = EnumKeys::GBS_FAIL;
				break;
			default:
				$status = '';
				break;
		}
		return $status;
	}

	/***
	 * 购物车唯一标识
	 * @author: colin
	 * @date: 2018/12/6 10:39
	 * @return string
	 */
	public function getToken()
	{
		return md5($this->request->input('gsId') . 'add_cart_key');
	}

	/***
	 * 商户唯一标识
	 * @author: colin
	 * @date: 2018/12/6 10:39
	 * @return string
	 */
	public function getgsId()
	{
		return $this->request->input('gsId');
	}


}