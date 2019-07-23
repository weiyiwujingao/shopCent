<?php
/**
 * 用户收货地址管理
 */

namespace App\Http\Controllers\Api\UserCenter;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserCenter\AddressAddRequest;
use App\Http\Requests\Api\UserCenter\AddressUpdateRequest;
use App\Http\Requests\Api\UserCenter\AddressDeleteRequest;


class UserAddressController extends Controller
{
    protected $request;
    protected $Obj;
    protected $userInfo;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->Obj = new \Library\UserCenter\UserAddress($this->request);
    }

    /**
     * 收货地址列表
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
	 * 获取用户地址详情
	 * @author: colin
	 * @date: 2019/5/22 11:25
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
	public function userAddressDetail()
	{
		$userAddress = $this->Obj->userAddress();
		if ($userAddress === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($userAddress);
	}
	/***
	 * 添加收货地址
	 * @author: colin
	 * @date: 2019/1/9 15:52
	 * @param AddressAddRequest $request
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
	public function create(AddressAddRequest $request)
	{
		$add = $this->Obj->create();
		if ($add === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($add);
	}
	/***
	 * 编辑收货地址
	 * @author: colin
	 * @date: 2019/1/9 15:52
	 * @param AddressAddRequest $request
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
	public function update(AddressUpdateRequest $request)
	{
		$add = $this->Obj->update();
		if ($add === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($add);
	}
	/***
	 * 删除选中地址
	 * @author: colin
	 * @date: 2019/1/9 15:52
	 * @param AddressAddRequest $request
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
	public function delete(AddressDeleteRequest $request)
	{
		$delete = $this->Obj->delete();
		if ($delete === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($delete);
	}
	/***
	 * 获取地区列表
	 * @author: colin
	 * @date: 2019/1/9 15:52
	 * @param AddressAddRequest $request
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
	public function getCity()
	{
		$delete = $this->Obj->getCity();
		if ($delete === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($delete);
	}


}
