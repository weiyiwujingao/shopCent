<?php
/**
 * 首页管理
 */

namespace App\Http\Controllers\Api\ProductCenter;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ProductCenter\HomeRequest;
use App\Http\Requests\Api\ProductCenter\HomeSellersRequest;


class HomeController extends Controller
{
    protected $request;
    protected $Obj;
    protected $userInfo;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->Obj = new \Library\ProductCenter\Home($this->request);
    }
	/**
	 * 首页接口
	 * @author: colin
	 * @date: 2019/1/15 11:37
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
	public function index(HomeRequest $request)
	{
		$list = $this->Obj->index();
		if ($list === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($list);
	}

	/**
	 * 门店列表
	 * @author: colin
	 * @date: 2019/1/16 10:05
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
    public function sellers(HomeSellersRequest $request)
    {
    	$list = $this->Obj->sellers();
        if ($list === false) {
            return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
        }
        return self::showSuccess($list);
    }

	/**
	 * 根据地区名称获取地区id
	 * @author: colin
	 * @date: 2019/1/18 11:13
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
	public function position()
	{
		$position = $this->Obj->position();
		if ($position === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($position);
	}

	/**
	 * 获取地区列表
	 * @author: colin
	 * @date: 2019/1/18 13:55
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
	public function citys()
	{
		$citys = $this->Obj->citys();
		if ($citys === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($citys);
	}
	/**
	 * 获取所有地区列表
	 * @author: colin
	 * @date: 2019/1/18 13:55
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
	public function allCity()
	{
		$citys = $this->Obj->allCity();
		if ($citys === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($citys);
	}
	/**
	 * 系统时间
	 * @author: colin
	 * @date: 2019/5/13 16:01
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
	public function systemTime()
	{
		$time = time();
		if ($time === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($time);
	}

}
