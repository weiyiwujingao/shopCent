<?php

namespace App\Http\Controllers\Api\Merchant;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\Api\Merchant\OrderRequest;
use App\Http\Requests\Api\Merchant\ExpressDetail;
use App\Http\Requests\Api\Merchant\OrderReturnRequest;
use App\Http\Requests\Api\Merchant\OrderDenyReturnRequest;
use App\Http\Requests\Api\Merchant\SetExpress;
use Illuminate\Support\Facades\Redis;

class OrderController extends Controller
{
    protected $request;
    protected $iationObj;
    protected $userInfo;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->Obj = new \Library\MerchantUser($this->request);
    }

    /***
     * 订单查询
     * @author: colin
     * @date: 2018/11/1 10:58
     * @param $param array 查询信息
     */
    public function settlement($isExport = '')
    {
        $settlementInfo = $this->Obj->settlement($isExport);
        if ($settlementInfo === false) {
            return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
        }
        return self::showSuccess($settlementInfo);
    }

    /**
     * 拨打隐私号码
     * @return $this|\Illuminate\Http\JsonResponse
     */
    public function getPrivateNum()
    {
        $params = $this->request->all();
        try {
            if (!isset($params['orderSn']) || empty($params['orderSn'])) {
                throw new \Exception('参数orderSn不能为空');
            }
            $type = isset($params['type']) ? intval($params['type']) : 1;
            $privateNum = $this->Obj->getPrivateNum($params['orderSn'], $type);
            return self::showSuccess($privateNum);
        } catch (\Exception $e) {
            return self::showError($e->getMessage());
        }
    }

    /***
     * 导出订单查询数据
     * @author: colin
     * @date: 2018/11/1 10:58
     * @param $param array 查询信息
     */
    public function excelSettlement()
    {

        $result = $this->Obj->excelSettlement();
        if ($result === false) {
            return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
        }
        $name = substr(md5(time()), 0, 15) . rand(1000, 9999);
        $res = \Helper\OfiiceHelper::exportExcel($result['titles'], $result['data'], $name, [], 'order_sn', true);
        if ($res['status'] === \Enum\EnumMain::HTTP_CODE_FAIL) {
            return self::showError($res['error']);
        }
        return self::showSuccess(['fileName' => $res['fileName']]);
    }

    /***
     * 确认提货
     * @author: colin
     * @date: 2018/11/8 10:30
     * @return $this|\Illuminate\Http\JsonResponse
     */
    public function ConfirmDelivery()
    {
        $confirm = $this->Obj->confirmDelivery();
        if ($confirm === false) {
            return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
        }
        return self::showSuccess('', '确认提货成功！');
    }

    /***
     * 获取用户可以和并的订单数组
     * @author: colin
     * @date: 2018/11/7 10:18
     * @return $this|\Illuminate\Http\JsonResponse
     */
    public function getUserMerge()
    {
        $merge = $this->Obj->getUserMerge();
        return self::showSuccess($merge);
    }

    /***
     * 订单信息
     * @author: colin
     * @date: 2018/11/19 11:33
     * @param OrderRequest $OrderRequest
     * @return $this|\Illuminate\Http\JsonResponse
     */
    public function orderDetail(OrderRequest $OrderRequest)
    {
        $goodsInfo = $this->Obj->orderDetail();
        if ($goodsInfo === false) {
            return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
        }
        return self::showSuccess($goodsInfo);
    }

    /***
     * 快递详情查看
     * @author: colin
     * @date: 2018/11/19 11:33
     * @param OrderRequest $OrderRequest
     * @return $this|\Illuminate\Http\JsonResponse
     */
    public function expressDetail(ExpressDetail $ExpressDetail)
    {
        $expressDetail = $this->Obj->expressDetail();
        if ($expressDetail === false) {
            return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
        }
        return self::showSuccess($expressDetail);
    }

    /***
     * 快递公司查看
     * @author: colin
     * @date: 2018/11/19 11:33
     * @param OrderRequest $OrderRequest
     * @return $this|\Illuminate\Http\JsonResponse
     */
    public function expressInfo()
    {
        $expressInfo = $this->Obj->expressInfo();
        if ($expressInfo === false) {
            return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
        }
        return self::showSuccess($expressInfo);
    }

    /***
     * 快递公司查看
     * @author: colin
     * @date: 2018/11/19 11:33
     * @param OrderRequest $OrderRequest
     * @return $this|\Illuminate\Http\JsonResponse
     */
    public function setExpress(SetExpress $SetExpress)
    {
        $setExpress = $this->Obj->setExpress();
        if ($setExpress === false) {
            return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
        }
        return self::showSuccess('', '设置物流成功！');
    }

    /**
     * 退货列表
     * @return $this|\Illuminate\Http\JsonResponse
     */
    public function refundList()
    {
        $refundList = $this->Obj->refundList();
        if ($refundList === false) {
            return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
        }
        return self::showSuccess($refundList);
    }
    /**
     * 退货详情
     * @return $this|\Illuminate\Http\JsonResponse
     */
    public function refundDetail()
    {
        $refundDetail = $this->Obj->refundDetail();
        if ($refundDetail === false) {
            return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
        }
        return self::showSuccess($refundDetail);
    }

    /***
     * 退货
     * @author: colin
     * @date: 2018/11/19 11:33
     * @param OrderRequest $OrderRequest
     * @return $this|\Illuminate\Http\JsonResponse
     */
    public function orderReturn()
    {
        $orderReturn = $this->Obj->orderReturn();
        if ($orderReturn === false) {
            return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
        }
        return self::showSuccess($orderReturn);
    }

    /***
     * 撤销退货
     * @author: colin
     * @date: 2018/11/19 11:33
     * @param OrderRequest $OrderRequest
     * @return $this|\Illuminate\Http\JsonResponse
     */
    public function denyRefund()
    {
        $denyRefund = $this->Obj->denyRefund();
        if ($denyRefund === false) {
            return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
        }
        return self::showSuccess($denyRefund);
    }

    /**
     * 接单
     * @param OrderDenyReturnRequest $OrderDenyReturnRequest
     * @return $this|\Illuminate\Http\JsonResponse
     */
    public function take(OrderDenyReturnRequest $OrderDenyReturnRequest)
    {
        $orderTake = $this->Obj->orderTake();
        if ($orderTake === false) {
            return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
        }
        return self::showSuccess();
    }

    /**
     * 发货
     * @param OrderDenyReturnRequest $OrderDenyReturnRequest
     * @return $this|\Illuminate\Http\JsonResponse
     */
    public function delivery(OrderDenyReturnRequest $OrderDenyReturnRequest)
    {
        $result = $this->Obj->delivery();
        if ($result === false) {
            return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
        }
        return self::showSuccess();
    }
}
