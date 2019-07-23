<?php
/**
 * 订单中心
 */

namespace App\Http\Controllers\Api\ProductCenter;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ProductCenter\UpOrderRequest;
use App\Http\Requests\Api\ProductCenter\OrderDetailRequest;

class OrderController extends Controller
{
    protected $request;
    protected $Obj;
    protected $userInfo;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->Obj = new \Library\ProductCenter\Order($this->request);
    }

	/**
	 * 提交订单
	 * @author: colin
	 * @date: 2019/1/24 16:28
	 * @param UpOrderRequest $request
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
    public function subOrder(UpOrderRequest $request)
    {
    	$list = $this->Obj->subOrder();
        if ($list === false) {
            return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
        }
        return self::showSuccess($list);
    }

	/**
	 * 订单详情
	 * @author: colin
	 * @date: 2019/1/29 15:02
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
    public function detail(OrderDetailRequest $request)
	{
		$data = $this->Obj->detail();
		if ($data === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($data);
	}

	/**
	 * 取消订单
	 * @author: colin
	 * @date: 2019/5/23 14:32
	 * @param OrderDetailRequest $request
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
	public function cancelOrder(OrderDetailRequest $request)
	{
		$data = $this->Obj->cancelOrder();
		if ($data === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($data);
	}
	/**
	 * 申请退款
	 * @author: colin
	 * @date: 2019/5/23 17:53
	 * @param OrderDetailRequest $request
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
	public function chargeBack(OrderDetailRequest $request)
	{
		$data = $this->Obj->chargeBack();
		if ($data === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($data);
	}

}
