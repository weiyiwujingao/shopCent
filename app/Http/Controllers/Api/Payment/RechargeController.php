<?php

namespace App\Http\Controllers\Api\Payment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Payment\RechargePayRequest;

class RechargeController extends Controller
{
	protected $request;
	protected $Obj;
	protected $userInfo;

	public function __construct(Request $request)
	{
		$this->request = $request;
		$this->Obj = new \Library\RechargeCenter\RechargePay($this->request);
	}
	/**
	 * 充值支付
	 * @author: colin
	 * @date: 2019/5/28 19:16
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
	public function pay(RechargePayRequest $request){
		$result = $this->Obj->pay();
		if ($result === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($result);
	}
	/**
	 * 支付验签
	 * @author: colin
	 * @date: 2019/5/28 19:16
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
	public function notify(){
		echo $this->Obj->notify();
		exit;
	}
}
