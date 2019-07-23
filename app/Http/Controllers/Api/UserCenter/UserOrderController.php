<?php
/**
 * 用户订单管理
 */

namespace App\Http\Controllers\Api\UserCenter;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserCenter\OrderListRequest;
use App\Http\Requests\Api\ProductCenter\OrderDetailRequest;


class UserOrderController extends Controller
{
    protected $request;
    protected $Obj;
    protected $userInfo;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->Obj = new \Library\UserCenter\UserOrder($this->request);
    }
	/**
	 * 订单统计
	 * @author colin
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function statis()
	{
		$list = $this->Obj->statis();
		if ($list === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($list);
	}
    /**
     * 个人订单列表
     * @author colin
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function list(OrderListRequest $request)
    {
    	$list = $this->Obj->settlement();
        if ($list === false) {
            return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
        }
        return self::showSuccess($list);
    }

	/**
	 * 确认提货
	 * @author: colin
	 * @date: 2019/5/27 13:34
	 * @param OrderDetailRequest $request
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
	public function confirm(OrderDetailRequest $request)
	{
		$list = $this->Obj->confirm();
		if ($list === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($list);
	}
}
