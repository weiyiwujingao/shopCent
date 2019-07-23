<?php

namespace Library\RechargeCenter;

use App;
use Illuminate\Http\Request;
use Enum\EnumKeys;
use \Helper\CFunctionHelper as help;
use Enum\EnumBusi;
use \DB;
use \App\Models\BonusType;

class RechargePay extends \Library\CBase
{
	protected $request;
	protected $userCMd;

	public function __construct(Request $request)
	{
		$this->request = $request;
		$this->BonusTypeMd = new App\Repositories\BonusTypeRepository();
		$this->OrderMd = new App\Repositories\UserOrderRepository();
		parent::__construct(__CLASS__);
	}

	/**
	 * 充值金额列表
	 * @author: colin
	 * @date: 2019/5/28 14:05
	 * @return array|\Library\type
	 */
	public function pay()
	{
		try{
			$payId = $this->request->input('pay_id');
			$uid = $this->request->input('uid');
			$orderId = $this->request->input('order_id');
			$orderSn = $this->request->input('order_sn');
			$type = $this->request->input('type');
			$rechargeType = EnumBusi::$rechargeTypeMap;
			if(!isset($rechargeType[$payId]))
				throw new \Exception('支付方式有误！');
			$paymentObj = \Library\Third\ThirdFactory::makePayment($payId);
			if($type == 'recharge'){
				$order = $this->BonusTypeMd->orderDetail(['bo_id'=>$orderId,'user_id'=>$uid]);
				help::throwError($order,'没有该充值订单信息！');
				$initRe = $paymentObj->doRecharge($order);
			}else{
				$order = $this->OrderMd->Orderdetail($orderSn,$uid);
				help::throwError($order,'没有该订单信息！');
				$initRe = $paymentObj->pay($order);
			}
			help::throwError($initRe,'提交预支付失败！');
		}catch(\Exception $e){
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "RechargeOrder list:" . json_encode(1) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return $initRe;

	}

	/**
	 * 支付通知
	 * @author: colin
	 * @date: 2019/5/31 11:49
	 * @return \Library\type
	 */
	public function notify(){
		try{
			$paymentObj = \Library\Third\ThirdFactory::makePayment(5);//5微信小程序支付
			$initRe = $paymentObj->notify();
		}catch(\Exception $e){
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "RechargeOrder notify:" . json_encode(1) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return $initRe;

	}
}