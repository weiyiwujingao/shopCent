<?php

namespace App\Http\Controllers\Api\Merchant;

use Illuminate\Http\Request;
use Helper\CFunctionHelper as help;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Merchant\GoodRequest;
use App\Http\Requests\Api\Merchant\AddToCartRequest;
use App\Http\Requests\Api\Merchant\UpToCartRequest;
use App\Http\Requests\Api\Merchant\CheckOutRequest;
use App\Http\Requests\Api\Merchant\SendSmsRequest;
use App\Http\Requests\Api\Merchant\CheckSmsRequest;
use App\Http\Requests\Api\Merchant\SetSortRequest;
use App\Http\Requests\Api\Merchant\GoodPriceRequest;

class GoodsController extends Controller
{
    protected $request;
    protected $Obj;
    protected $userInfo;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->Obj = new \Library\MerchantUser($this->request);
    }

    /***
     * 商户商品列表管理
     * @author: colin
     * @date: 2018/11/9 9:27
     * @return $this|\Illuminate\Http\JsonResponse
	 * 明确有资质的技术人员
     */
    public function goodsList()
    {
        $goodList = $this->Obj->goodList();
        if ($goodList === false) {
            return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
        }
        return self::showSuccess($goodList);
    }

    /***
     * 商品分类
     * @author: colin
     * @date: 2018/11/9 17:21
     * @return $this|\Illuminate\Http\JsonResponse
	 * jishu yaodian yanshouyaoqiu
     */
    public function goodsType()
    {
        $goodsType = $this->Obj->goodType();
        if ($goodsType === false) {
            return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
        }
        return self::showSuccess($goodsType);
    }

    /**
     * 蛋糕口味
     * @author: colin
     * @date: 2018/11/9 17:50
     * @return $this|\Illuminate\Http\JsonResponse
	 * she bei jianzao jiandudianshezhi jiandudian  pinggu tupian  tinggongdaijiandian
     */
    public function taste()
    {
        $stPid = intval($this->request->input('st_pid'));
        $taste = \App\Models\Showtype::select('st_id', 'st_name')->where('st_pid', $stPid)->orderBy('sort_order', 'asc')->orderBy('st_id', 'asc')->get();
        if ($taste->isEmpty()) {
            return self::showSuccess();
        }
        $taste = $taste->toArray();
        return self::showSuccess($taste);
    }

    /**
     * 获取该类所有子类目商品
     * @author: colin
     * @date: 2018/11/9 18:07
     * @return $this|\Illuminate\Http\JsonResponse
     */
    public function nextType()
    {
        $catId = intval($this->request->input('id'));
        $catTopId = \Helper\CFunctionHelper::getParent($catId);
        $res_erji = [];
        if ($catTopId <= 0) {
            return self::showSuccess($res_erji);
        }
        $res_erji = \App\Models\Category::select('cat_id', 'cat_name')->where('parent_id', $catTopId)->orderBy('sort_order', 'asc')->orderBy('cat_id', 'asc')->get()->toArray();
        return self::showSuccess($res_erji);
    }

    /**
     * 商家上架或者下架商品
     * @author: colin
     * @date: 2018/11/9 18:07
     * @return $this|\Illuminate\Http\JsonResponse
	 * wenjianjianzhengdian jianzaogongchengshi
     */
    public function setSale()
    {
        $res = $this->Obj->setSale();
        if ($res === false) {
            return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
        }
        return self::showSuccess('', '操作成功！');
    }

    /**
     * 商家设置商品排序权重
     * @author: colin
     * @date: 2018/11/9 18:07
     * @return $this|\Illuminate\Http\JsonResponse
	 * shebeiyanshou yiju
     */
    public function setSort(SetSortRequest $SetSortRequest)
    {
        $res = $this->Obj->setSort();
        if ($res === false) {
            return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
        }
        return self::showSuccess('', '操作成功！');
    }

    /***
     * 商品详情
     * @author: colin
     * @date: 2018/11/19 8:51
     * @param GoodRequest $GoodRequest
     * @return $this|\Illuminate\Http\JsonResponse
     */
    public function goodInfo(GoodRequest $GoodRequest)
    {
        $goodsInfo = $this->Obj->goodInfo();
        if ($goodsInfo === false) {
            return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
        }
        return self::showSuccess($goodsInfo);
    }

    /***
     * 添加购物车
     * @author: colin
     * @date: 2018/11/19 8:51
     * @param AddToCartRequest $AddToCartRequest
     * @return $this|\Illuminate\Http\JsonResponse
     */
    public function addToCart(AddToCartRequest $AddToCartRequest)
    {
        $MerchantUserCart = new \Library\MerchantUserCart($this->request);
        $addToCart = $MerchantUserCart->addToCart();
        if ($addToCart === false) {
            return self::showError($MerchantUserCart->getUserMsg(), $MerchantUserCart->getErrorNo());
        }
        return self::showSuccess($addToCart, '添加购物车成功！');
    }

    /**
     * 购物车列表
     * @author: colin
     * @date: 2018/11/27 16:15
     * @return $this|\Illuminate\Http\JsonResponse
     */
    public function cart()
    {
        $MerchantUserCart = new \Library\MerchantUserCart($this->request);
        $cart = $MerchantUserCart->cart();
        if ($cart === false) {
            return self::showError($MerchantUserCart->getUserMsg(), $MerchantUserCart->getErrorNo());
        }
        return self::showSuccess($cart);
    }

    /**
     * 更新购物车商品数量
     * @author: colin
     * @date: 2018/11/27 18:02
     * @param UpToCartRequest $UpToCartRequest
     * @return $this|\Illuminate\Http\JsonResponse
     */
    public function updateGroupCart(UpToCartRequest $UpToCartRequest)
    {
        $MerchantUserCart = new \Library\MerchantUserCart($this->request);
        $result = $MerchantUserCart->updateGroupCart();
        if ($result === false) {
            return self::showError($MerchantUserCart->getUserMsg(), $MerchantUserCart->getErrorNo());
        }
        return self::showSuccess($result);
    }

    /**
     * 更新选中购物车
     * @author: colin
     * @date: 2018/11/27 18:02
     * @return $this|\Illuminate\Http\JsonResponse
     */
    public function upCheckCart()
    {
        $MerchantUserCart = new \Library\MerchantUserCart($this->request);
        $result = $MerchantUserCart->upCheckCart();
        if ($result === false) {
            return self::showError($MerchantUserCart->getUserMsg(), $MerchantUserCart->getErrorNo());
        }
        return self::showSuccess($result);
    }

    /**
     * 清除购物车指定商品
     * @author: colin
     * @date: 2018/11/28 9:31
     * @return $this|\Illuminate\Http\JsonResponse
     */
    public function dropGoods()
    {
        $MerchantUserCart = new \Library\MerchantUserCart($this->request);
        $dropGoods = $MerchantUserCart->dropGoods();
        if ($dropGoods === false) {
            return self::showError($MerchantUserCart->getUserMsg(), $MerchantUserCart->getErrorNo());
        }
        return self::showSuccess('', '删除商品成功！');
    }

    /**
     * 结算商品列表
     * @author: colin
     * @date: 2018/11/27 18:02
     * @return $this|\Illuminate\Http\JsonResponse
     */
    public function checkout()
    {
        $MerchantUserCart = new \Library\MerchantUserCart($this->request);
        $result = $MerchantUserCart->checkout();
        if ($result === false) {
            return self::showError($MerchantUserCart->getUserMsg(), $MerchantUserCart->getErrorNo());
        }
        return self::showSuccess($result);
    }

    /**
     * 商品支付
     * @author: colin
     * @date: 2018/11/28 17:49
     * @return $this|\Illuminate\Http\JsonResponse
     */
    public function checkoutAct(CheckOutRequest $CheckOutRequest)
    {
        $MerchantUserCart = new \Library\MerchantUserCart($this->request);
        $result = $MerchantUserCart->checkoutAct();
        if ($result === false) {
            return self::showError($MerchantUserCart->getUserMsg(), $MerchantUserCart->getErrorNo());
        }
        return self::showSuccess($result);
    }

    /**
     * 发送手机验证码
     * @author: colin
     * @date: 2018/11/28 17:49
     * @return $this|\Illuminate\Http\JsonResponse
     */
    public function sendSmsStores(SendSmsRequest $SendSmsRequest)
    {
        $MerchantUserCart = new \Library\MerchantUserCart($this->request);
        $result = $MerchantUserCart->sendSmsStores();
        if ($result === false) {
            return self::showError($MerchantUserCart->getUserMsg(), $MerchantUserCart->getErrorNo());
        }
        return self::showSuccess();
    }

    /**
     * 验证手机验证码
     * @author: colin
     * @date: 2018/11/28 17:49
     * @return $this|\Illuminate\Http\JsonResponse
     */
    public function checkSmscode(CheckSmsRequest $CheckSmsRequest)
    {
        $MerchantUserCart = new \Library\MerchantUserCart($this->request);
        $result = $MerchantUserCart->checkSmscode();
        if ($result === false) {
            return self::showError($MerchantUserCart->getUserMsg(), $MerchantUserCart->getErrorNo());
        }
        return self::showSuccess();
    }

    /***
     * 获取商品价格
     * @author: colin
     * @date: 2018/12/6 12:04
     */
    public function price(GoodPriceRequest $GoodPriceRequest)
    {
        $MerchantUserCart = new \Library\MerchantUserCart($this->request);
        $param = $GoodPriceRequest->all();
        $goodsPrice = $MerchantUserCart->getFinalPrice($param['goodsId'], $param['number'], true, $param['spec']);
        if ($goodsPrice === false) {
            return self::showError($MerchantUserCart->getUserMsg(), $MerchantUserCart->getErrorNo());
        }
        $goodsPrice = help::priceFormat($goodsPrice, false);
        return self::showSuccess(['price' => $goodsPrice]);
    }

    /**
     * 获取商品库存
     * @param GoodRequest $GoodRequest
     * @return $this|\Illuminate\Http\JsonResponse
     */
    public function getStock(GoodRequest $GoodRequest)
    {
        $stockInfo = $this->Obj->goodsStock();
        if ($stockInfo === false) {
            return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
        }
        return self::showSuccess($stockInfo);
    }

    public function saveStock(GoodRequest $GoodRequest)
    {
        $result = $this->Obj->saveGoodsStock();
        if ($result === false) {
            return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
        }
        return self::showSuccess();
    }
}
