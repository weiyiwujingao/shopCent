<?php
/**
 * 商品页面管理
 */

namespace App\Http\Controllers\Api\ProductCenter;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ProductCenter\GoodsRequest;
use App\Http\Requests\Api\ProductCenter\GoodDetailRequest;
use App\Http\Requests\Api\ProductCenter\GoodPriceRequest;
use App\Http\Requests\Api\ProductCenter\GoodSellersRequest;
use App\Http\Requests\Api\ProductCenter\SellersRecRequest;

class GoodsController extends Controller
{
    protected $request;
    protected $Obj;
    protected $userInfo;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->Obj = new \Library\ProductCenter\Goods($this->request);
    }

	/**
	 * 商品列表
	 * @author: colin
	 * @date: 2019/1/21 14:46
	 * @param GoodsRequest $request
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
    public function goods(GoodsRequest $request)
    {
    	$list = $this->Obj->goods();
        if ($list === false) {
            return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
        }
        return self::showSuccess($list);
    }

	/**
	 * 结算购物车商品时获取最新的信息
	 * @author: colin
	 * @date: 2019/1/23 16:30
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
    public function goodMess()
	{
		$list = $this->Obj->goodMess();
		if ($list === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($list);
	}

	/**
	 * 商品详情
	 * @author: colin
	 * @date: 2019/1/23 17:56
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
	public function goodDetail(GoodDetailRequest $request)
	{
		$gooddetail = $this->Obj->goodDetail();
		if ($gooddetail === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($gooddetail);
	}
	/***
	 * 获取商品价格
	 * @author: colin
	 * @date: 2018/12/6 12:04
	 */
	public function price(GoodPriceRequest $request){
		$param = $request->all();
		$goodsPrice = $this->Obj->getFinalPrice($param['goodsId'], $param['number'], true, $param['spec']);
		if ($goodsPrice === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess(['price' => $goodsPrice]);
	}
	/**
	 * 搜索
	 * @author: colin
	 * @date: 2019/1/31 9:37
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
    public function search()
	{
		$gooddetail = $this->Obj->search();
		if ($gooddetail === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($gooddetail);
	}

	/**
	 * 产品分类
	 * @author: colin
	 * @date: 2019/5/15 16:35
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
	public function classify()
	{
		$classify = $this->Obj->classify();
		if ($classify === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($classify);
	}

	/**
	 * 根据产品推荐类似门店
	 * @author: colin
	 * @date: 2019/5/20 15:27
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
	public function goodSellers(GoodSellersRequest $request)
	{
		$classify = $this->Obj->goodSellers();
		if ($classify === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($classify);
	}


	/**
	 * 商家推荐商品
	 * @author: colin
	 * @date: 2019/5/20 17:58
	 * @param GoodSellersRequest $request
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
	public function sellerRec(SellersRecRequest $request)
	{
		$sellerRec = $this->Obj->sellerRec();
		if ($sellerRec === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($sellerRec);
	}
	/**
	 * 商家推荐商品
	 * @author: colin
	 * @date: 2019/5/20 17:58
	 * @param GoodSellersRequest $request
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
	public function hotSearch()
	{
		$shotSearch = $this->Obj->hotSearch();
		if ($shotSearch === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($shotSearch);
	}

}
