<?php
/**
 * 用户收藏管理
 */

namespace App\Http\Controllers\Api\UserCenter;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserCenter\CollectDeleteRequest;
use App\Http\Requests\Api\UserCenter\CollectAddRequest;


class UserCollectController extends Controller
{
    protected $request;
    protected $Obj;
    protected $userInfo;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->Obj = new \Library\UserCenter\UserCollect($this->request);
    }

    /**
     * 收藏店铺列表
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
	public function delete(CollectDeleteRequest $request)
	{
		$delete = $this->Obj->delete();
		if ($delete === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($delete);
	}

	/***
	 * 添加门店收藏
	 * @author: colin
	 * @date: 2019/5/24 19:54
	 * @param CollectAddRequest $request
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
	public function add(CollectAddRequest $request)
	{
		$create = $this->Obj->add();
		if ($create === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($create);
	}

	/***
	 * 判断是否收藏
	 * @author: colin
	 * @date: 2019/5/24 20:09
	 * @param CollectAddRequest $request
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
	public function isCollect(CollectAddRequest $request)
	{
		$isCollect = $this->Obj->isCollect();
		return self::showSuccess($isCollect);
	}



}
