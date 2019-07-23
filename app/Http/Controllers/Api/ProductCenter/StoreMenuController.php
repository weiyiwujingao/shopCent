<?php
/**
 * 门店列表管理
 *
 */

namespace App\Http\Controllers\Api\ProductCenter;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ProductCenter\BrandSellersRequest;
use App\Http\Requests\Api\ProductCenter\SellersRequest;
use App\Http\Requests\Api\ProductCenter\CatSellersRequest;
use App\Http\Requests\Api\ProductCenter\SellersDetailRequest;


class StoreMenuController extends Controller
{
    protected $request;
    protected $Obj;
    protected $userInfo;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->Obj = new \Library\ProductCenter\StoreMenu($this->request);
    }

	/**
	 * 品牌名店列表
	 * @author: colin
	 * @date: 2019/1/18 15:15
	 * @param BrandSellersRequest $request
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
    public function brands(BrandSellersRequest $request)
    {
    	$list = $this->Obj->brands();
        if ($list === false) {
            return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
        }
        return self::showSuccess($list);
    }

	/**
	 * 品牌店
	 * @author: colin
	 * @date: 2019/1/21 11:07
	 * @param BrandSellersRequest $request
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
	public function brandSeller(SellersRequest $request)
	{
		$list = $this->Obj->brandSeller();
		if ($list === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($list);
	}

	/***
	 * 品牌详情
	 * @author: colin
	 * @date: 2019/5/20 12:01
	 */
	public function brandInfo()
	{
		$list = $this->Obj->brandInfo();
		if ($list === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($list);
	}
	/**
	 * 分类获取门店列表
	 * @author: colin
	 * @date: 2019/1/18 15:15
	 * @param BrandSellersRequest $request
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
	public function catSellers(CatSellersRequest $request)
	{
		$list = $this->Obj->catSellers();
		if ($list === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($list);
	}

	/**
	 * 商店详情
	 * @author: colin
	 * @date: 2019/1/23 8:54
	 * @param SellersDetailRequest $request
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
	public function sellerDetail(SellersDetailRequest $request)
	{
		$data = $this->Obj->sellerDetail();
		if ($data === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($data);
	}

}
