<?php
namespace Library\Third\Payment\Balance;
use App;
use \Enum\EnumBusi;
use \Enum\EnumLang;
use \Helper\CFunctionHelper as help;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use \DB;
/**
 * Class MiniWechatPay
 * @package Library\Third\Payment
 */
class Balance extends \Library\CBase {

    public function __construct($paymentId) {
		parent::__construct(__CLASS__);
    }

	/**
	 * 余额购物支付
	 * @author: colin
	 * @date: 2019/6/6 14:15
	 * @param $orderDetail
	 * @return \Library\type
	 */
	public function pay($orderDetail)
	{
		try{
			\DB::beginTransaction();
            $uid = \Request::input('uid');
			$payId = \Request::input('pay_id');
			if($orderDetail['pay_status'] != 0)
				throw new \Exception('该订单状态有误！');
			if ($orderDetail['total_price'] + $orderDetail['shipping_fee'] != $orderDetail['surplus'] + $orderDetail['bonus'])
				throw new \Exception('订单异常，请联系客服');
			$payment = help::paymentInfo($payId);
			$orderData = [
				'pay_id'   => $payId,
				'pay_name' => strip_tags($payment['pay_name']),
			];
			/* 幸福券处理 start */
			if (!empty($orderDetail['bonus_id'])) {//使用单卡
				$bonusInfo = help::bonusInfo($orderDetail['bonus_id']);
				if (empty($bonusInfo) || $bonusInfo['user_id'] != $uid) {
					throw new \Exception('选择的幸福券不存在，请检查');
				}
				if ($bonusInfo['bonus_status'] > 1 || $bonusInfo['balance'] < $orderDetail['bonus']) {
					throw new \Exception('选取的幸福券异常，请检查');
				}
				help::logAccountChangeTwo($uid, $orderDetail['bonus'], $orderDetail['order_sn'], $orderDetail['order_pick_stores'], sprintf('幸福券支付', $orderDetail['order_sn']), $orderDetail['bonus_id']);
				$umoney = $orderDetail['bonus'];
				$balance = $bonusInfo['balance'] - $umoney;
				$ubData = [
					'used_money' => $bonusInfo['used_money'] + $umoney,
					'balance'    => $balance,
				];
				if ($balance == 0) {
					$ubData['bonus_status'] = 2;//已用完
				}
				// 更新卡数据
				\App\Models\UserBonus::where('bonus_id',$orderDetail['bonus_id'])->update($ubData);
				$orderData['bonus_company'] = $bonusInfo['bonus_company'] . '(幸福券)';
			}elseif (!empty($orderDetail['bonus']) && empty($orderDetail['bonus_id'])) {//可选择多卡
				$usedTotalMoney = 0;
				$orderBonusUsed = \App\Models\OrderBonusUser::where('order_sn',$orderDetail['order_sn'])->where('status','<','1')->get()->toArray();
				$bonusCompanyArr = array();
				foreach ($orderBonusUsed as $item) {
					$bonusInfo = help::bonusInfo($item['bonus_id']);
					if (empty($bonusInfo) || $bonusInfo['user_id'] != $uid) {
						throw new \Exception('选择的幸福券不存在，请检查');
					}
					if ($bonusInfo['bonus_status'] > 1 || $bonusInfo['balance'] < $item['used_money']) {
						throw new \Exception('选取的幸福券异常，请检查');
					}
					$usedTotalMoney += floatval($item['used_money']);
					$umoney = $item['used_money'];
					$balance = $bonusInfo['balance'] - $umoney;
					$ubData = [
						'used_money' => $bonusInfo['used_money'] + $umoney,
						'balance'    => $balance,
					];
					if ($balance == 0) {
						$ubData['bonus_status'] = 2;//已用完
					}
					// 更新卡数据
					\App\Models\UserBonus::where('bonus_id',$item['bonus_id'])->update($ubData);
					help::logAccountChangeTwo($uid, $umoney, $orderDetail['order_sn'], $orderDetail['order_pick_stores'], sprintf('幸福券支付', $orderDetail['order_sn']), $orderDetail['bonus_id']);
					$bonusCompanyArr[] = $bonusInfo['bonus_company'];
				}
				if (!empty($bonusCompanyArr)) {
					$bonusCompanyArr = array_unique($bonusCompanyArr);
					$orderData['bonus_company'] = implode('|', $bonusCompanyArr) . '(幸福券)';
				}
				if ($orderDetail['bonus'] != $usedTotalMoney) {
					throw new \Exception('订单数据错误，请联系客服' . $usedTotalMoney);
				}
				\App\Models\OrderBonusUser::where('order_sn',$orderDetail['order_sn'])->update(['status' => 1, 'change_time' => date('Y-m-d H:i:s')]);
			}
			/* 幸福券处理 end */
			//余额处理
			$resUseData = \App\Models\Users::where('user_id',$uid)->firstOrFail()->toArray();
			$nowTime = time();
			if ($orderDetail['surplus'] > $resUseData['user_money']) {
				throw new \Exception('账户余额不足');
			}
			$orderData['order_status'] = 5;
			$orderDetail['is_shipping'] < 1 && $orderData['shipping_status'] = 1;//自提的 直接发货状态
			$orderData['pay_status'] = 2;
			$orderData['pay_time'] = $nowTime;
			$orderData['last_cfm_time'] = strtotime('+4 days', $nowTime);//收货时间
			if (!isset($orderData['bonus_company'])) $orderData['bonus_company'] = $resUseData['bonus_company'] . '(余额)';
			if ($orderDetail['surplus'] > 0) {
				help::logAccountChange($uid, $orderDetail['surplus'] * (-1), 0, 0, 0,sprintf('余额支付', $orderDetail['order_sn']));
				help::logAccountChangeTwo($uid, $orderDetail['surplus'], $orderDetail['order_sn'], $orderDetail['order_pick_stores'], sprintf('余额支付', $orderDetail['order_sn']), $orderDetail['order_sn']);
			}
			\App\Models\OrderInfo::where('order_sn',$orderDetail['order_sn'])->update($orderData);
			\DB::commit();
			try {
				//给门店发送短信 start
				$stores = \App\Models\StoresUser::selectRaw("gs_id,gs_name,gs_address,gs_contacter,gs_login_name,gs_mobile")->where('gs_id',$orderDetail['order_pick_stores'])->firstOrFail()->toArray();
				if (!empty($stores['gs_mobile'])) {
					if ($orderDetail['is_shipping'] == 1) {
						$isSendMsg = 1;
					} else {
						$cakeResList = \App\Models\OrderGoods::from("ecs_order_goods as a")
							->join("ecs_goods as b",'a.goods_id','=','b.goods_id')
							->join("ecs_category as c",'b.cat_id','=','c.cat_id')
							->join("ecs_order_info as d",'a.order_id','=','d.order_id')
							->selectRaw("c.cat_id,c.parent_id,c.is_send_msg")
							->whereRaw("d.order_sn='{$orderDetail['order_sn']}'")
							->groupBy("c.cat_id")
							->get()->toArray();
						$isSendMsg = 0;
						foreach ($cakeResList as $cakeRes) {
							if ($cakeRes['is_send_msg'] == 1) {
								$isSendMsg = 1;
							} elseif ($cakeRes['is_send_msg'] == 0 && $cakeRes['parent_id'] > 0) {
								$isSendMsg = \App\Models\Category::where('cat_id',$cakeRes['parent_id'])->value('is_send_msg');
							}
							if ($isSendMsg == 1) {
								break;
							}
						}
					}
					$mlist = explode('|', $stores['gs_mobile']);
					$gsMobiles = ($isSendMsg == 1) ? $mlist : [$mlist[0]];
					help::sendMobileSms($gsMobiles, $stores['gs_contacter'] . '，您好！您有一个新订单！订单编号是：' . $orderDetail['order_sn'] . '。', $orderDetail['order_sn']);
				}
				if (!empty($stores['gs_login_name'])) {
					$tools = new \Library\Third\Push\PushTools();
					$tools->notifyNewOrder($stores['gs_login_name']);
					\App\Models\StoresUser::where('gs_login_name',$stores['gs_login_name'])->update(['max_order_time'=>time()]);
				}
				//给门店发送短信 end
			} catch (\Exception $e) {
				//发送消息错误
				$this->logBusi('Balance pay :order_payment:order_sn:' . $orderDetail['order_sn'] . '|send_msg_err:' . $e->getMessage(),__LINE__);
			}
		}catch(\Exception $e){
			\DB::rollBack();
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "Balance pay:" . json_encode($orderDetail) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}

	}

}