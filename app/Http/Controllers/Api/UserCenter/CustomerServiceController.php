<?php
/**
 * 客服中心
 */

namespace App\Http\Controllers\Api\UserCenter;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserCenter\CustomerdetailRequest;


class CustomerServiceController extends Controller
{
    protected $request;
    protected $Obj;
    protected $userInfo;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->Obj = new \Library\UserCenter\CustomerService($this->request);
    }

    /**
     * 客服列表
     * @author colin
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function list()
    {
    	$list = $this->Obj->getlist();
        if ($list === false) {
            return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
        }
        return self::showSuccess($list);
    }
	/***
	 * 删除选中地址
	 * @author: colin
	 * @date: 2019/1/9 15:52
	 * @param AddressAddRequest $request
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
	public function detail(CustomerdetailRequest $request)
	{
		$result = $this->Obj->detail();
		if ($result === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($result);
	}

	/**
	 * 获取客服电话
	 * @author: colin
	 * @date: 2019/5/27 17:39
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
	public function servicTel()
	{
		$servicTel = $this->Obj->servicTel();
		if ($servicTel === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($servicTel);
	}



}
