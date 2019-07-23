<?php
/**
 * 订单中心
 */

namespace App\Http\Controllers\Api\ProductCenter;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ProductCenter\RechargeUpOrderRequest;

class RechargeOrderController extends Controller
{
    protected $request;
    protected $Obj;
    protected $userInfo;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->Obj = new \Library\ProductCenter\RechargeOrder($this->request);
    }

	/**
	 * 获取充值金额列表
	 * @author: colin
	 * @date: 2019/5/28 11:34
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
    public function list()
    {
    	$list = $this->Obj->list();
        if ($list === false) {
            return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
        }
        return self::showSuccess($list);
    }

	/**
	 * 提交充值订单
	 * @author: colin
	 * @date: 2019/5/28 14:05
	 * @param RechargeUpOrderRequest $request
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
	public function subOrder(RechargeUpOrderRequest $request)
	{
		$list = $this->Obj->subOrder();
		if ($list === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($list);
	}


}
