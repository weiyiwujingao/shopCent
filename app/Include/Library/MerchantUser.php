<?php

namespace Library;

use DB;
use App;
use Cookie;
use Enum\EnumKeys;
use Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use \Exception;
use Helper\CFunctionHelper as help;

class MerchantUser extends CBase
{
    protected $request;
    protected $gsId;

    public function __construct(Request $request)
    {
        $this->request = $request;
        parent::__construct(__CLASS__);
    }

    /**
     * 获取登录信息
     * @param $param
     * @return mixed
     * @author colin
     */
    public function getLogin()
    {
        $param = $this->request->all();
        $salt = $this->getGsSalt($param['gs_login_name']);
        $loginData = array(
            'gs_login_name'   => $param['gs_login_name'],
            'gs_login_pass'   => md5($param['gs_login_pass'] . $salt),//获取加盐密码
            'last_login_time' => time(),
            'last_login_ip'   => \Helper\CFunctionHelper::getRealIP(),
        );
        $loginRe = $this->login($loginData);
        return $loginRe;
    }

    /**
     * 判断是否登录
     * @param $param
     * @return mixed
     * @author colin
     */
    public function isLogin()
    {
        try {
            if (empty($this->request->input('gsId'))) {
                throw new Exception('未登录！');
            }
        } catch (\Exception $e) {
            return $this->setErrorAndReturn([
                'return'   => false,
                'code'     => \Enum\EnumMain::HTTP_CODE_FAIL,
                'errorMsg' => "islogin fail reason:" . $e->getMessage(),
                'userMsg'  => '未登录！',
                'line'     => __LINE__,
            ]);
        }
        return true;
    }

    /****
     * 获取商户加盐密码
     * @author colin
     * @param string $gs_login_name 商户名称
     * @return mixed
     */
    public function getGsSalt($gs_login_name)
    {
        $salt = App\Models\StoresUser::where('gs_login_name', $gs_login_name)->value('gs_login_salt');
        return $salt;
    }

    /**
     * 登录
     * @author colin
     * @param array $loginInfo 基本用户信息
     * @return array|type
     */
    public function login($loginInfo)
    {
        try {
            $userObj = \App\Models\StoresUser::select('gs_id', 'gs_name', 'gs_login_name', 'gs_address', 'is_manage', 'pid')
                ->where('gs_login_name', $loginInfo['gs_login_name'])
                ->where('gs_login_pass', $loginInfo['gs_login_pass'])
                ->firstOrFail();
            $userObj = $userObj->toArray();
            if (!empty($userObj)) {
                $userObj['ctime'] = time();
                $token = md5(json_encode($userObj));
                $expiresAt = 60 * 24 * 30;
                Cache::put($token, $userObj, $expiresAt);
                $result = [
                    'token' => $token,
                ];
                \App\Models\StoreToken::create(['token' => $token, 'gs_id' => $userObj['gs_id']]);
                return $result;
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->setErrorAndReturn([
                'return'   => false,
                'code'     => \Enum\EnumMain::HTTP_CODE_FAIL,
                'errorMsg' => "login get user fail.loginInfo:" . json_encode($loginInfo) . ",reason:" . $e->getMessage(),
                'userMsg'  => '登录密码错误！',
                'line'     => __LINE__,
            ]);
        }
        return false;
    }

    /***
     * 退出登录
     * @author: colin
     * @date: 2018/12/5 17:07
     * @return bool|type
     */
    public function logout()
    {
        try {
            $token = $this->request->header("token");
            Cache::forget($token);
            \App\Models\StoreToken::where('token', $token)->delete();
        } catch (\Exception $e) {
            return $this->setErrorAndReturn([
                'return'   => false,
                'code'     => \Enum\EnumMain::HTTP_CODE_FAIL,
                'errorMsg' => "logout get user fail.gs_id:{$this->request->input('gsId')},reason:" . $e->getMessage(),
                'userMsg'  => "不存在用户！",
                'line'     => __LINE__,
            ]);
        }
        return true;
    }

    /***
     * 获取信息，对商户限制条件进行处理判断
     *
     * @author: colin
     * @date: 2018/11/8 14:07
     * @param $userInfo
     * @return type
     */
    public function getUserInfo()
    {
        try {
            $userObj = \App\Models\StoresUser::where('gs_id', $this->request->input('gsId'))->firstOrFail();
            $weixin = \App\Models\StoreWeixin::select('nickname', 'id')->where('gs_id', $this->request->input('gsId'))->take(3)->orderBy('id', 'asc')->get()->toArray();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->setErrorAndReturn([
                'return'   => false,
                'code'     => \Enum\EnumMain::HTTP_CODE_FAIL,
                'errorMsg' => "getUserInfo get user fail.gs_id:{$this->request->input('gsId')},reason:" . $e->getMessage(),
                'userMsg'  => "不存在用户！",
                'line'     => __LINE__,
            ]);
        }
        $return = $this->setUserInfo($userObj, $weixin);
        return $return;

    }

    /***
     * 解绑商户绑定的微信号
     * @author: colin
     * @date: 2018/11/19 15:14
     * @return type
     */
    public function UntieWeixin()
    {
        $id = $this->request->input('id');
        try {
            $weixin = \App\Models\StoreWeixin::where(['id' => $id, 'gs_id' => $this->request->input('gsId')])->delete();
            if (empty($weixin)) {
                throw new Exception('解绑微信失败，不存在该微信号！');
            }
        } catch (\Exception $e) {
            return $this->setErrorAndReturn([
                'return'   => false,
                'code'     => \Enum\EnumMain::HTTP_CODE_FAIL,
                'errorMsg' => "UntieWeixin 解绑微信失败 fail.gs_id:{$this->request->input('gsId')},reason:" . $e->getMessage(),
                'userMsg'  => $e->getMessage(),
                'line'     => __LINE__,
            ]);
        }
        //添加操作日志
        \Helper\CFunctionHelper::setStoreLogs($this->request->input('gsId'), \Enum\EnumLang::BUSINESS_UNTI_WECHAT, 'ecs_stores_weixin', '3', '商户解绑微信');
        return $weixin;
    }

    /**
     * 构建商户信息
     * @author: colin
     * @date: 2018/11/8 14:09
     * @param $memberObj
     * @param $token
     */
    public function setUserInfo($memberObj, $weixin)
    {
        if (!is_array($memberObj))
            $memberObj = $memberObj->toArray();
        $infoObj = array(
            'gs_id'         => $memberObj['gs_id'],
            'gs_name'       => $memberObj['gs_name'],
            'gs_login_name' => $memberObj['gs_login_name'],
            'gs_stats'      => $memberObj['gs_stats'],
            'gs_address'    => $memberObj['gs_address'],
            'store_pic'     => config('merchant.static_host') . '/data/brandlogo/' . $memberObj['store_pic'],
            'gs_weixin'     => $weixin,
        );
        return $infoObj;
    }

    /****
     * Notes
     * @author: colin
     * @date: 2018/11/12 15:50
     * @param $gs_id
     * @return array|type
     */
    public function getBusinessInfo()
    {
        $userInfo = $this->request->input('userInfo');
        $gsIdArr = [$this->request->input('gsId')];
        $nowtime = strtotime(date('Y-m-d ', time()) . '00:00:00');
        $montime = strtotime(date('Y-m-01 ', time()) . '00:00:00');
        try {
            $key = md5('getBusinessInfo_' . $this->request->input('gsId'));
            $data = Cache::get($key);
            if (!empty($data))
                return $data;
            if ($userInfo['is_manage'] == 1) {
                $gsIdChid = \App\Models\StoresUser::where(['is_manage' => 0, 'pid' => $this->request->input('gsId')])->pluck('gs_id')->toArray();
                $gsIdArr = array_merge($gsIdArr, $gsIdChid);
            }
            $data['totay_business'] = \App\Models\OrderInfo::selectRaw("sum(goods_amount) as todaymoney")->where(['pay_status' => 2])->whereIn('order_pick_stores', $gsIdArr)->where('add_time', '>=', $nowtime)->get()->toArray();//今日营业额
            $data['month_business'] = \App\Models\OrderInfo::selectRaw("sum(goods_amount) as monthmoney")->where(['pay_status' => 2])->whereIn('order_pick_stores', $gsIdArr)->where('add_time', '>=', $montime)->get()->toArray();//本月营业额
            $data['totay_order_num'] = \App\Models\OrderInfo::where('add_time', '>=', $nowtime)->whereIn('order_pick_stores', $gsIdArr)->count();//今日订单
            $data['unconfirm_order_num'] = \App\Models\OrderInfo::where(['order_status' => 5])->whereIn('shipping_status', [0, 1])->whereIn('order_pick_stores', $gsIdArr)->count();//待确认订单
            //$data['uncancel_order_num'] = \App\Models\OrderInfo::where(['order_status' => 5, 'pay_status' => 2, 'shipping_status' => 3])->whereIn('order_pick_stores', $gsIdArr)->count();//待退货订单
            $data['uncancel_order_num'] = \App\Models\RefundApply::selectRaw('ecs_refund_apply.apply_id')
                ->join('ecs_order_info as i', 'ecs_refund_apply.order_id', '=', 'i.order_id')->whereIn('i.order_pick_stores', $gsIdArr)
                ->where('ecs_refund_apply.apply_status', 0)->count();
            //组装数据
            $data['totay_business'] = $data['totay_business'][0]['todaymoney'] ?? 0;
            $data['month_business'] = $data['month_business'][0]['monthmoney'] ?? 0;
            Cache::put($key, $data, 1);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->setErrorAndReturn([
                'return'   => false,
                'code'     => \Enum\EnumMain::HTTP_CODE_FAIL,
                'errorMsg' => "getBusinessInfo get 商户经营信息 fail.gs_id:{$this->request->input('gsId')},reason:" . $e->getMessage(),
                'userMsg'  => "不存在用户！",
                'line'     => __LINE__,
            ]);
        }
        return $data;

    }

    /***
     * 门店设置信息
     * @author: colin
     * @date: 2018/11/8 17:10
     * @param $gs_id
     * @return array|type
     */
    public function shopInfo()
    {
        try {
            $result = \App\Models\StoresUser::select('open_time', 'close_time', 'picktime_start', 'picktime_end', 'gs_stats', 'pickup_mode', 'gs_notice', 'uptime_start', 'uptime_end')->where(['gs_id' => $this->request->input('gsId')])->get();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->setErrorAndReturn([
                'return'   => false,
                'code'     => \Enum\EnumMain::HTTP_CODE_FAIL,
                'errorMsg' => "getBusinessInfo get 商户经营信息 fail.gs_id:{$this->request->input('gsId')},reason:" . $e->getMessage(),
                'userMsg'  => "不存在用户！",
                'line'     => __LINE__,
            ]);
        }
        if (!$result->isEmpty()) {
            $result = $result->toArray();
            return $result;
        }
        return [];

    }

    /***
     * 更新门店设置信息
     * @author: colin
     * @date: 2018/11/9 8:55
     * @param $resetObj
     * @return bool
     */
    public function shopSet($ShopSetRequest)
    {
        $fieldsArr = ['open_time' => '线上开始时间', 'close_time' => '线上结束时间', 'picktime_start' => '取货开始时间', 'picktime_end' => '取货结束时间', 'gs_stats' => '门店状态', 'pickup_mode' => '配送方式', 'gs_notice' => '门店公告', 'uptime_start' => '线下开始时间', 'uptime_end' => '线下结束时间'];
        $fields = array_keys($fieldsArr);
        $data = help::setParamData($fields, $ShopSetRequest->all());
        if (empty($data)) {
            return true;
        }
        $data2 = \App\Models\StoresUser::where('gs_id', $this->request->input('gsId'))->first($fields);
        if (!empty($data2)) {
            $data2 = $data2->toArray();
            $cpData = $this->compare($data, $data2);//修改前后数据对比
            if (!empty($cpData)) {
                $tmpArr = [];
                foreach ($cpData as $key => $value) {
                    $tmpArr[] = '“' . $fieldsArr[$key] . '” 从 “' . $value[1] . '” 修改为 “' . $value[0] . '”';
                }
                $tmpStr = implode('；', $tmpArr);
            }
        }
        $res = \App\Models\StoresUser::where('gs_id', $this->request->input('gsId'))->update($data);
        if ($res === false) {
            return false;
        }
        $business = ($data['gs_stats'] == 0) ? '关闭' : '营业';
        if (isset($tmpStr)) {
            $business .= '。修改内容为：' . $tmpStr;
        }
        //添加操作日志
        \Helper\CFunctionHelper::setStoreLogs($this->request->input('gsId'), \Enum\EnumLang::BUSINESS_UPDATE_STORE, 'ecs_goods_stores', '2', '修改门店设置,门店状态：' . $business);
        return $res;
    }

    /**
     * 两组数据比较 返回差异数据(一维数组比较)
     * @param array $data1 修改后
     * @param array $data2 修改前
     * @return  array
     */
    private function compare($data1 = [], $data2 = [])
    {
        $diffData = [];
        foreach ($data1 as $key => $item) {
            $oldData = $data2[$key];
            if (!isset($data2[$key]) || $item != $oldData) {
                $diffData[$key] = [$item, $oldData];
            }
        }
        return $diffData;
    }

    /***
     * 修改密码参数验证
     * @author: colin
     * @date: 2018/11/8 14:40
     * @param $param
     * @return bool|type
     */
    public function getModifypsw()
    {
        $res = $this->getUserExist();//验证是否存在
        if ($res === false) {
            return $res;
        }
        $res = $this->UpPwd();//修改密码
        return $res;
    }

    /**
     * 判断商户是否存在
     * @author: colin
     * @date: 2018/11/8 14:41
     * @param $uid
     * @param $token
     * @return type
     */
    public function getUserExist()
    {
        $password = $this->request->input('password');
        $userInfo = $this->request->input('userInfo');
        $salt = $this->getGsSalt($userInfo['gs_login_name']);
        $map['gs_id'] = $userInfo['gs_id'];
        $map['gs_login_pass'] = md5($password . $salt);
        try {
            $userObj = \App\Models\StoresUser::onWriteConnection()
                ->where($map)
                ->firstOrFail();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->setErrorAndReturn([
                'return'   => false,
                'code'     => \Enum\EnumMain::HTTP_CODE_FAIL,
                'errorMsg' => "判断商户是否存在:" . $userInfo['gs_name'] . ",reason:" . $e->getMessage(),
                'userMsg'  => "当前密码输入有误！",
                'line'     => __LINE__,
            ]);
        }
        return $userObj;
    }

    /**
     * 重置密码
     * @author: colin
     * @date: 2018/11/8 15:05
     * @param $gs_id  商户id
     * @param $password 新密码
     * @return bool|type
     */
    public function UpPwd()
    {
        $password = $this->request->input('new_password');
        $userInfo = $this->request->input('userInfo');
        $salt = $this->getGsSalt($userInfo['gs_login_name']);//获取加盐字符串
        $password = md5($password . $salt);//密码加密
        try {
            $userToken = \App\Models\StoreToken::where('gs_id', $this->request->input('gsId'))->get()->pluck('token')->toArray();
            foreach ($userToken as $val) {
                Cache::forget($val);
            }
            \App\Models\StoreToken::where('gs_id', $this->request->input('gsId'))->delete();
            \App\Models\StoresUser::where('gs_id', $this->request->input('gsId'))->update(['gs_login_pass' => $password]);
        } catch (\Illuminate\Database\QueryException $e) {
            return $this->setErrorAndReturn([
                'return'   => false,
                'code'     => \Enum\EnumMain::HTTP_CODE_FAIL,
                'errorMsg' => "forgetpsw fail.gs_id:{$this->request->input('gsId')},reason:" . $e->getMessage(),
                'userMsg'  => \Lang::trans('lottery.resetpsw_fail'),
                'line'     => __LINE__,
            ]);
        }
        \Helper\CFunctionHelper::setStoreLogs($this->request->input('gsId'), \Enum\EnumLang::BUSINESS_UPDATE_PASSWORD, 'ecs_goods_stores', '2', '修改密码');
        return true;
    }

    /***
     * 订单查询
     * @author: colin
     * @date: 2018/11/1 10:58
     * @param $param array 查询信息
     */
    public function settlement($isExport = '')
    {
        $userInfo = $this->request->input('userInfo');
        $gsIdArr = [$this->request->input('gsId')];
        $childId = $this->request->input('storeId', '');
        $param = $this->settlementParam($isExport);
        try {
            /* 管理员可以查看旗下加盟店铺的订单 */
            $fields = 'order_taking,address,order_note,order_id,order_sn,order_status,shipping_status,shipping_fee,is_shipping,pay_status,return_reason,order_pick_stores,shipping_time,
            FROM_UNIXTIME(add_time,"%Y.%m.%d %H:%i") add_time, order_lxr, order_tel,last_cfm_time,(order_amount + shipping_fee + insure_fee + pay_fee + pack_fee + card_fee + tax - discount) AS total_fee,is_group,is_show';
            if ($userInfo['is_manage'] == 1) {
                $gsIdChid = \App\Models\StoresUser::where(['is_manage' => 0, 'pid' => $this->request->input('gsId')])->pluck('gs_id')->toArray();
                $gsIdArr = array_merge($gsIdArr, $gsIdChid);
                if (!empty($childId)) {
                    if (!in_array($childId, $gsIdArr)) {
                        throw new \Exception('非法请求！');
                    }
                    $gsIdArr = [$childId];
                }
                $fields .= ',ecs_goods_stores.gs_name';
            }
            \App\Models\OrderInfo::where('order_pick_stores', $this->request->input('gsId'))->whereNull('isread')->update(['isread' => 1]);//更新未读订单浏览状态
			$result = \App\Models\OrderInfo::selectRaw($fields);
            if (isset($gsIdChid)) {
                $result = $result->leftJoin('ecs_goods_stores', 'ecs_goods_stores.gs_id', '=', 'ecs_order_info.order_pick_stores');
            }
            $result = $result->whereIn('order_pick_stores', $gsIdArr);
            if (!empty($param['dateStart'])) {
                $result = $result->where('add_time', '>=', $param['dateStart']);
            }
            if (!empty($param['dateEnd'])) {
                $result = $result->where('add_time', '<=', $param['dateEnd']);
            }
            if (!empty($param['orderSn'])) {
                $result = $result->where('order_sn', 'like', '%' . $param['orderSn'] . '%');
            }
            //订单状态查询
            switch ($param['ot']) {
                case 101: // 0待确认
                    $result = $result->where(['order_status' => 5])->whereIn('shipping_status', [0, 1]);//and pay_status = 2 and shipping_status in(1,3)//OS_UNCONFIRMED
                    break;
                case 103: // 已完成
                    $result = $result->where(['order_status' => 5, 'shipping_status' => 2]);
                    break;
                case 104: // 待退货
                    $result = $result->where(['order_status' => 5, 'pay_status' => 2, 'shipping_status' => 3]);
                    break;
                case 105:// 已退货
                    $result = $result->where(['order_status' => 4]);
                    break;
                default:
//					$result = $result->whereIn('order_status', [0, 1, 2, 3, 4, 5, 6]);
                    break;
            }
			$result = $result->where(function($query){
				return $query->whereRaw("(is_group=1 and is_show=1) or (is_group=0)");
			});
            if ($isExport == 1) {
                $result = $result->orderBy('order_id', 'DESC')->get()->toArray();
            } else {
                $result = $result->skip($param['page'])->take($param['pageSize'])->orderBy('order_id', 'DESC')->get()->toArray();
            }
        } catch (\Exception $e) {
            return $this->setErrorAndReturn([
                'return'   => false,
                'code'     => \Enum\EnumMain::HTTP_CODE_FAIL,
                'errorMsg' => "获取订单列表失败:" . $this->request->input('gsId') . ",reason:" . $e->getMessage(),
                'userMsg'  => $e->getMessage(),
                'line'     => __LINE__,
            ]);
        }
        if ($isExport == 1) {
            $result = $this->setExData($result);
        } else {
            $result = $this->settleData($result);
        }

        return $result;
    }

    /**
     * 订单查询参数处理
     * @author: colin
     * @date: 2018/11/13 14:57
     * @return array
     */
    public function settlementParam($isExport = '')
    {
        $param = $this->request->all();
        /* 参数处理 */
        $param['page'] = isset($param['page']) ? intval($param['page']) : 1;
        $param['pageSize'] = isset($param['pageSize']) ? intval($param['pageSize']) : 3;
        $param['ot'] = isset($param['ot']) ? intval($param['ot']) : 0;//订单状态：101待确认；102已付款；103已完成；104待退货；105已退货
        $param['dateStart'] = isset($param['dateStart']) ? strtotime($param['dateStart'] . ' 00:00:00') : '';//开始时间
        $param['dateEnd'] = isset($param['dateEnd']) ? strtotime($param['dateEnd'] . ' 23:59:59') : '';//结束时间
        $param['now'] = (isset($param['now']) && $param['now'] == 1) ? strtotime(date('Y-m-d ') . ' 00:00:00') : '';//今天
        $param['page'] = ($param['page'] - 1) * $param['pageSize'];
        if ($isExport === 1) {//导出excel查询处理
            //$param['dateStart'] = $param['dateStart'] ? $param['dateStart'] : strtotime(date("Y-m-d 00:00:00", strtotime("-1 month")));
            $param['pageSize'] = $param['pageSize'] ? $param['pageSize'] : 1000;
            $param['s'] = ($param['page'] - 1) * $param['pageSize'];
        }
        if ($param['now']) {
            $param['dateStart'] = $param['now'];
        }
        return $param;
    }

    /***
     * 组装订单状态
     * @author: colin
     * @date: 2018/11/1 15:20
     * @param $data
     * @return array
     */
    public function settleData($data)
    {
        if (!$data) {
            return [];
        }
        $now = time();
        foreach ($data as $k => $v) {
            $v['shipping_status'] = ($v['shipping_status'] == \Enum\EnumKeys::SS_SHIPPED_ING) ? \Enum\EnumKeys::SS_PREPARING : $v['shipping_status'];
            $goodsList = $this->orderGoods($v['order_id']);

            if ($v['is_shipping'] == 1 && !in_array($v['order_status'], [2, 3, 4])) {//  2已取消,3无效,4退货 && 交易完成7天内
                //配送订单 号码不加密
                if ($v['shipping_time'] > 0 && $now - $v['shipping_time'] >= 60 * 60 * 24 * 7) {
                    $encryption = 1;
                    $orderTel = substr_replace($v['order_tel'], '****', 3, 4);
                } else {
                    $encryption = 0;
                    $orderTel = $v['order_tel'];
                }
            } else {
                $encryption = 1;
                $orderTel = substr_replace($v['order_tel'], '****', 3, 4);
            }
            $arr[] = [
                'order_id'        => $v['order_id'],
                'order_sn'        => $v['order_sn'],
                'order_time'      => $v['add_time'],
                'order_status'    => $v['order_status'],
                'shipping_status' => $v['shipping_status'],
                'total_fee'       => \Helper\CFunctionHelper::priceFormat($v['total_fee'], false),
                'shipping_fee'    => $v['shipping_fee'],
                'goods_list'      => $goodsList,
                'order_lxr'       => $v['order_lxr'],
                'order_tel'       => $orderTel,
                'encryption'      => $encryption,
                'return_reason'   => $v['return_reason'],
                'order_taking'    => $v['order_taking'],
                'gs_name'         => isset($v['gs_name']) ? $v['gs_name'] : '',

            ];
        }
        return $arr;
    }

    public function setExData($data)
    {
        if (!$data) {
            return [];
        }
        foreach ($data as $k => $v) {
            $data[$k]['shipping_status'] = ($data[$k]['shipping_status'] == \Enum\EnumKeys::SS_SHIPPED_ING) ? \Enum\EnumKeys::SS_PREPARING : $data[$k]['shipping_status'];
            $gs = \App\Models\StoresUser::select('gs_name', 'gs_brand_id', 'gs_region_id')->where('gs_id', $data[$k]['order_pick_stores'])->first();
            $cityId = \App\Models\GoodsRegion::where('gr_id', $gs->gs_region_id)->value('parent_id');
            $cityName = \App\Models\GoodsRegion::where('gr_id', $cityId)->value('gr_name');
            $cityName = !empty($cityName) ? $cityName : \App\Models\GoodsRegion::where('gr_id', $gs->gs_region_id)->value('gr_name');
            $brandName = \App\Models\Brand::where('brand_id', $gs->gs_brand_id)->value('brand_name');
            $goodsList = $this->orderGoods($data[$k]['order_id']);
            foreach ($goodsList as $key => $val) {
                $goodsType = \App\Models\Goods::selectRaw("distinct ecs_goods.cat_id,ca.cat_name")
                    ->join('ecs_category as ca', 'ecs_goods.cat_id', '=', 'ca.cat_id')
                    ->where('ecs_goods.goods_id', $goodsList[$key]['goods_id'])->first();
                $goodsList[$key]['catName'] = $goodsType->cat_name;
                $goodsList[$key]['order_sn'] = $data[$k]['order_sn'];
                $goodsList[$key]['sum_fee'] = help::priceFormat($goodsList[$key]['goods_price'] * $goodsList[$key]['goods_number'], false);
            }
            $arr[] = [
                'order_id'        => $data[$k]['order_id'],
                'order_sn'        => ' ' . $data[$k]['order_sn'],
                'order_time'      => $data[$k]['add_time'],
                'order_status'    => $data[$k]['order_status'],
                'shipping_status' => $data[$k]['shipping_status'],
                'pay_status'      => $data[$k]['pay_status'],
                'total_fee'       => help::priceFormat($data[$k]['total_fee'], false),
                'order_lxr'       => $data[$k]['order_lxr'],
                'order_tel'       => $data[$k]['order_tel'],
                'return_reason'   => $data[$k]['return_reason'],
                'goods_list'      => $goodsList,
                'storesName'      => $gs->gs_name,
                'cityName'        => $cityName,
                'address'         => $data[$k]['address'],
                'order_note'      => $data[$k]['order_note'],
                'brandName'       => $brandName,
            ];
        }
        return $arr;
    }

    /***
     * 导出订单数据
     * @author: colin
     * @date: 2018/12/12 9:16
     * @return bool|type
     */
    public function excelSettlement()
    {
        $result = [];
        try {
            $list = $this->settlement(1);
//				dd($list);
            if (empty($list)) {
                return false;
            }
            $titles = [
                'order_sn'     => '订单编号',
                'status'       => '订单状态',
                'order_time'   => '销售日期',
                'cityName'     => '所属区域',
                'brandName'    => '品牌',
                'storesName'   => '门店名称',
                'goods_name'   => '产品名称',
                'goods_sn'     => '产品编号',
                'catName'      => '产品分类',
                'goods_number' => '销售数量',
                'goods_price'  => '单价',
                'sum_fee'      => '销售金额',
//				'total_fee' => '订单费用',
//				'order_lxr' => '联系人',
//				'order_tel' => '电话',
                'address'      => '收货地址',
                'order_note'   => '备注',
            ];
//			$titlestwo = [
//				'order_sn' => '订单编号',
//				'catName' => '产品分类',
//				'goods_name' => '产品名称',
//				'goods_number' => '销售数量',
//				'goods_price' => '单价',
//				'sum_fee' => '销售金额',
//			];
            $data = [];
            $datatwo = [];
            foreach ($list as $key => $val) {
                foreach ($val['goods_list'] as $kk => $vv) {
                    foreach ($titles as $k => $v) {
                        if ($k == 'status') {
                            if ($val['pay_status'] == 2 && $val['shipping_status'] == 1) {
                                $val[$k] = '待确认';
                            } else if ($val['order_status'] == 5 && $val['shipping_status'] == 2) {
                                $val[$k] = '已完成';
                            } else if ($val['order_status'] == 5 && $val['pay_status'] == 2 && $val['shipping_status'] == 3) {
                                $val[$k] = '待退货';
                            } else if ($val['order_status'] == 4) {
                                $val[$k] = '已退货';
                            } else {
                                $val[$k] = '其他';
                            }
                        }
                        if (in_array($k, ['goods_name', 'catName', 'goods_number', 'goods_price', 'sum_fee', 'goods_sn'])) {
                            $data[$k][] = $vv[$k];
                        } else {
                            $data[$k][] = $val[$k];
                        }

                    }
                }
//				foreach ($val['goods_list'] as $key => $val) {
//					foreach ($titlestwo as $k => $v) {
//						$datatwo[$k][] = $val[$k];
//					}
//				}

            }
            $result = [
                'titles' => $titles,
                'data'   => $data,
//				'sheet' => [
//					0 => [
//						'titlestwo' => $titlestwo,
//						'datatwo' => $datatwo,
//						'name' => '订单商品信息表',
//					]
//				]
            ];
            unset($list);
        } catch (\Exception $e) {
            return $this->setErrorAndReturn([
                'return'   => false,
                'code'     => \Enum\EnumMain::HTTP_CODE_FAIL,
                'errorMsg' => "订单数据导出有误 reason:" . $e->getMessage(),
                'userMsg'  => $e->getMessage(),
                'line'     => __LINE__,
            ]);
        }
        return $result;
    }

    /***
     * 已分单数据整合
     * @author: colin
     * @date: 2018/11/1 15:39
     * @param $data
     * @return mixed
     */
    public function setOrder($data)
    {
        $now = time();
        switch ($data['shipping_status']) {
            case \Enum\EnumKeys::SS_SHIPPED:
                $data['litime'] = $data['last_cfm_time'] - $now;
                $data['handler'] = '待收货';
                break;
            case \Enum\EnumKeys::SS_RECEIVED:
                $data['handler'] = '已完成';
                break;
            case \Enum\EnumKeys::SS_PREPARING:
                $data['handler'] = '退货中';
                break;
            default:
                if ($data['pay_status'] == PS_UNPAYED) {
                    $data['handler'] = '待付款';
                } else {
                    $data['handler'] = \Enum\EnumLang::$LANG['os'][$data['order_status']];
                }
                break;
        }
        return $data;

    }

    public function orderGoods($orderId)
    {
        if (empty($orderId)) return [];
        try {
            $result = \App\Models\OrderGoods::select("rec_id", "status", "goods_id", "goods_sn", "goods_attr_id", "goods_name", "goods_number", "goods_price", "goods_attr")
                ->where('order_id', $orderId)
                ->orderBy('rec_id', 'DESC')->get()->toArray();
        } catch (\Exception $e) {
            return $this->setErrorAndReturn([
                'return'   => false,
                'code'     => \Enum\EnumMain::HTTP_CODE_FAIL,
                'errorMsg' => "订单商品表:" . $orderId . ",reason:" . $e->getMessage(),
                'userMsg'  => '订单商品表查询有误！',
                'line'     => __LINE__,
            ]);
        }
        foreach ($result as $k => $v) {
            $result[$k]['goods_price'] = \Helper\CFunctionHelper::priceFormat($result[$k]['goods_price'], false);
            $result[$k]['goods_thumb'] = \App\Models\Goods::where('goods_id', $result[$k]['goods_id'])->value('goods_thumb');//产品缩略图
            if ($result[$k]['goods_thumb']) {
                $result[$k]['goods_thumb'] = env('STATIC_HOST') . $result[$k]['goods_thumb'];//产品缩略图
            }
            //产品规格
            if ($result[$k]['goods_attr_id']) {
                $good_attr_id = explode(',', $result[$k]['goods_attr_id']);
                $result[$k]['goods_attr'] = \App\Models\GoodsAttr::select('ecs_goods_attr.attr_id', 'ecs_goods_attr.attr_value', 'ea.attr_name')->leftJoin('ecs_attribute as ea', 'ecs_goods_attr.attr_id', '=', 'ea.attr_id')
                    ->whereIn('goods_attr_id', $good_attr_id)->get()->toArray();//规格
            } else {
                $result[$k]['goods_attr'] = [];
            }
        }
        return $result;
    }

    /**
     * 确认提货
     * @author: colin
     * @date: 2018/11/8 10:27
     * @param $param array 参数
     * @param $userId int 登录商户id
     * @return type
     */
    public function confirmDelivery()
    {
        $userInfo = $this->request->input('userInfo');
        $param = $this->request->all();
        $validator = Validator::make($param, [
            'orderSn' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->setErrorAndReturn([
                'return'  => false,
                'code'    => \Enum\EnumMain::HTTP_CODE_FAIL,
                'userMsg' => '订单号不能为空',
            ]);
        }
        try {
            $order = \App\Models\OrderInfo::select("order_id", "order_sn", "order_status", "shipping_status", "pay_status", "order_pick_stores")->where('order_sn', '=', $param['orderSn'])->get()->toArray();
            if (!isset($order['0']))
                throw new Exception('不存在订单！');
            /* 管理员可以查看旗下加盟店铺的订单 */
            $gsIdArr = [$this->request->input('gsId')];
            if ($userInfo['is_manage'] == 1) {
                $gsIdChid = \App\Models\StoresUser::where(['is_manage' => 0, 'pid' => $this->request->input('gsId')])->pluck('gs_id')->toArray();
                $gsIdArr = array_merge($gsIdArr, $gsIdChid);
            }
            if ($userInfo['gs_id'] > 0 && !in_array($order['0']['order_pick_stores'], $gsIdArr))
                throw new Exception('你没有权限操作他人订单');
            /* 检查订单 */
            if ($order['0']['shipping_status'] == \Enum\EnumKeys::SS_RECEIVED)//此订单已经确认过了，感谢您在本站购物，欢迎再次光临。
                throw new Exception('此订单已经确认过了，感谢您在本站购物，欢迎再次光临。');
            if ($order['0']['shipping_status'] != \Enum\EnumKeys::SS_SHIPPED)//您提交的订单不正确
                throw new Exception('您提交的订单不正确');
            //检查是否有待退货的商品
            $isRefund = \App\Models\OrderGoods::where(['order_id' => $order[0]['order_id'], 'status' => '1'])->count();
            if ($isRefund) {
                throw new Exception('有未处理的退款申请');
            }
            \App\Models\OrderInfo::where('order_sn', '=', $param['orderSn'])->update(['shipping_status' => \Enum\EnumKeys::SS_RECEIVED, 'shipping_time' => time()]);
            /* 记录日志 */
            $this->orderAction($order['0']['order_sn'], $order['0']['order_status'], \Enum\EnumKeys::SS_RECEIVED, $order['0']['pay_status'], '', '商户', $userInfo['gs_login_name']);

        } catch (\Exception $e) {
            return $this->setErrorAndReturn([
                'return'   => false,
                'code'     => \Enum\EnumMain::HTTP_CODE_FAIL,
                'errorMsg' => "确认收货失败" . $e->getMessage(),
                'userMsg'  => "确认收货失败:" . $e->getMessage(),
                'line'     => __LINE__,
            ]);
        }
        //添加商户操作日志
        \Helper\CFunctionHelper::setStoreLogs($this->request->input('gsId'), \Enum\EnumLang::BUSINESS_SURE_STATUS, 'ecs_order_info', '2', "确认提货,订单号({$order['0']['order_sn']})");
        return true;
    }

    /**
     * 记录订单操作记录
     *
     * @access  public
     * @param   string $order_sn 订单编号
     * @param   integer $order_status 订单状态
     * @param   integer $shipping_status 配送状态
     * @param   integer $pay_status 付款状态
     * @param   string $note 备注
     * @param   string $username 用户名，用户自己的操作则为 buyer
     * @return  void
     */
    public function orderAction($order_sn, $order_status, $shipping_status, $pay_status, $note = '', $username = null, $place = 0, $recIds = '', $refundAmount = 0)
    {
        $order_id = \App\Models\OrderInfo::where('order_sn', '=', $order_sn)->value('order_id');
        $create = [
            'order_id'        => $order_id,
            'action_user'     => $username,
            'order_status'    => $order_status,
            'shipping_status' => $shipping_status,
            'pay_status'      => $pay_status,
            'action_place'    => $place,
            'action_note'     => $note,
            'log_time'        => time(),
            'rec_ids'         => $recIds,
            'refund_amount'   => $refundAmount,
        ];
        //创建申请数据
        try {
            \App\Models\OrderAction::create($create);
        } catch (\Illuminate\Database\QueryException $e) {
            return $this->setErrorAndReturn([
                'return'   => false,
                'code'     => \Enum\EnumMain::HTTP_CODE_DB_FAIL,
                'errorMsg' => "更新记录订单操作记录,reason:" . $e->getMessage(),
                'userMsg'  => "更新记录订单操作记录失败！",
                'line'     => __LINE__,
            ]);
        }
    }

    /**
     * 获得指定礼包的商品
     *
     * @access  public
     * @param   integer $package_id
     * @return  array
     */
    public function getPackageGoods($packageId)
    {
        if (!$packageId) return [];
        try {
            $result = \App\Models\PackageGoods::leftJoin('ecs_goods as g', 'ecs_package_goods.goods_id', '=', 'g.goods_id')
                ->leftJoin('ecs_products as p', 'ecs_package_goods.product_id', '=', 'p.product_id')
                ->select('ecs_package_goods.goods_id, g.goods_name, pg.goods_number, p.goods_attr, p.product_number, p.product_id')
                ->where('ecs_package_goods.package_id', 'package_id');
            $result = $result->get()->toArray();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->setErrorAndReturn([
                'return'   => false,
                'code'     => \Enum\EnumMain::HTTP_CODE_FAIL,
                'errorMsg' => "礼包表:" . $packageId . ",reason:" . $e->getMessage(),
                'userMsg'  => '礼包表查询有误！',
                'line'     => __LINE__,
            ]);
        }
        if (!$result) {
            return [];
        }
        $row = array();
        /* 生成结果数组 取存在货品的商品id 组合商品id与货品id */
        $good_product_arr = [];
        foreach ($result as $k => $v) {
            if ($v['product_id'] > 0) {
                /* 取存商品id */
                $good_product_arr[] = $v['goods_id'];
                /* 组合商品id与货品id */
                $v['g_p'] = $v['goods_id'] . '_' . $v['product_id'];
            } else {
                /* 组合商品id与货品id */
                $v['g_p'] = $v['goods_id'];
            }

            //生成结果数组
            $row[] = $v;
        }
        /* 释放空间 */
        unset($resource, $_row, $sql);

        /* 取商品属性 */
        if ($good_product_arr) {
            $result_goods_attr = \App\Models\GoodsAttr::select('goods_attr_id, attr_value')->whereIn('goods_id', $good_product_arr)->get()->toArray();
            $_goods_attr = array();
            foreach ($result_goods_attr as $value) {
                $_goods_attr[$value['goods_attr_id']] = $value['attr_value'];
            }
        }

        /* 过滤货品 */
        $format[0] = '%s[%s]--[%d]';
        $format[1] = '%s--[%d]';
        foreach ($row as $key => $value) {
            if ($value['goods_attr'] != '') {
                $goods_attr_array = explode('|', $value['goods_attr']);

                $goods_attr = array();
                foreach ($goods_attr_array as $_attr) {
                    $goods_attr[] = $_goods_attr[$_attr];
                }
                $row[$key]['goods_name'] = sprintf($format[0], $value['goods_name'], implode('，', $goods_attr), $value['goods_number']);
            } else {
                $row[$key]['goods_name'] = sprintf($format[1], $value['goods_name'], $value['goods_number']);
            }
        }

        return $row;
    }

    /***
     * 获取用户可以和并的订单数组
     * @author: colin
     * @date: 2018/11/7 10:21
     * @param $this ->gs_id int 商户id
     * @return array
     */
    public function getUserMerge()
    {
        $list = \App\Models\OrderInfo::select('order_sn')
            ->where(['user_id' => $this->request->input('gsId'), 'extension_code' => '', 'shipping_status' => \Enum\EnumKeys::SS_UNSHIPPED, 'pay_status' => \Enum\EnumKeys::PS_UNPAYED])
            ->whereIn('order_status', [\Enum\EnumKeys::OS_UNCONFIRMED, \Enum\EnumKeys::OS_CONFIRMED])->get()->toArray();
        $merge = array();
        foreach ($list as $val) {
            $merge[$val] = $val;
        }
        return $merge;
    }

    /**
     * 获取商品列表
     * @author: colin
     * @date: 2018/11/9 10:24
     * @param $param
     * @param $gs_id
     */
    public function goodList()
    {
        try {
            $param = $this->getGoodPram();
            $catList = $this->catList($param['id'], 0, false);
            //获得指定分类下所有底层分类的ID
            $children = array_unique(array_merge([$param['id']], array_keys($catList)));
            $goodsID = \App\Models\StoresUser::where('gs_id', $this->request->input('gsId'))->value('gs_goods_id');
            $goodsIDArr = !empty($goodsID) ? unserialize($goodsID) : [];
            if (empty($goodsIDArr)) {
                throw new Exception('还未添加商品，如有疑问请联系客服！');
            }
            $goodslist = $this->categoryGetGoodsStore($children, $goodsIDArr, $param['keyword'], $param['page'], $param['pageSize']);//获得分类下的商品
        } catch (\Exception $e) {
            return $this->setErrorAndReturn([
                'return'   => false,
                'code'     => \Enum\EnumMain::HTTP_CODE_FAIL,
                'errorMsg' => "获取商品列表失败reason:" . $e->getMessage(),
                'userMsg'  => $e->getMessage(),
                'line'     => __LINE__,
            ]);
        }
        return $goodslist;
    }

    /**
     * 商品查询参数
     * @author: colin
     * @date: 2018/11/9 16:40
     * @param $param
     * @return mixed
     */
    public function getGoodPram()
    {
        $param = $this->request->all();
        $param['id'] = !empty($param['id']) ? intval($param['id']) : 0;
        $param['keyword'] = !empty($param['keyword']) ? $param['keyword'] : 0;
        $param['page'] = !empty($param['page']) ? intval($param['page']) : 1;
        $param['pageSize'] = !empty($param['pageSize']) ? intval($param['pageSize']) : 20;
        $param['page'] = ($param['page'] - 1) * $param['pageSize'];
        return $param;
    }

    /***
     * 商品分类列表
     * @author: colin
     * @date: 2018/11/9 9:48
     * @param int $cat_id
     * @param int $selected
     * @param bool $re_type
     * @param int $level
     * @param bool $is_show_all
     * @return array|string|void
     */
    public function catList($catId = 0, $selected = 0)
    {
        $key = md5(\Enum\EnumKeys::CACHE_CAT_LIST . $catId . '_0_' . $selected);
        $options = \Cache::get($key);
        if (!empty($options)) {
            return $options;
        }
        $res = \App\Models\Category::leftJoin('ecs_category as s', 's.parent_id', '=', 'ecs_category.cat_id')
            ->selectRaw("ecs_category.cat_id, ecs_category.cat_name, ecs_category.measure_unit, ecs_category.parent_id, ecs_category.is_show, ecs_category.show_in_nav,ecs_category.city, ecs_category.grade, ecs_category.sort_order, COUNT(s.cat_id) AS has_children")
            ->groupBy('ecs_category.cat_id')
            ->orderBy('ecs_category.parent_id', 'asc')
            ->orderBy('ecs_category.sort_order', 'asc')
            ->get()->toArray();
        $res2 = \App\Models\Goods::selectRaw("cat_id, COUNT(goods_id) AS goods_num")->where(['is_delete' => 0, 'is_on_sale' => 1])->groupBy('cat_id')->get()->toArray();
        $res3 = \App\Models\Goods::join('ecs_goods_cat as gc', 'gc.goods_id', '=', 'ecs_goods.goods_id')
            ->selectRaw("gc.cat_id, COUNT(gc.goods_id) AS goods_num")
            ->where(['ecs_goods.is_delete' => 0, 'ecs_goods.is_on_sale' => '1'])
            ->groupBy('gc.cat_id')->get()->toArray();
        $newres = [];
        foreach ($res2 as $k => $v) {
            $newres[$v['cat_id']] = $v['goods_num'];
            foreach ($res3 as $ks => $vs) {
                if ($v['cat_id'] == $vs['cat_id']) {
                    $newres[$v['cat_id']] = $v['goods_num'] + $vs['goods_num'];
                }
            }
        }
        foreach ($res as $k => $v) {
            $res[$k]['goods_num'] = !empty($newres[$v['cat_id']]) ? $newres[$v['cat_id']] : 0;
        }
        if (empty($res) == true) {
            return [];
        }
        $options = \Helper\CFunctionHelper::catOptions($catId, $res); // 获得指定分类下的子分类的数组
        Cache::put($key, $options, 60 * 24 * 30);
        return $options;
    }

    /**
     * 获得分类下的商品
     * @author: colin
     * @date: 2018/11/9 10:24
     * @return int
     */
    public function categoryGetGoodsStore($children, $goodsIDArr = [], $keywords = '', $page = 0, $pageSize = 20)
    {
        try {
            //精确查询条件初始化
            $where = [
                'ecs_goods.is_on_sale'    => 1,
                'ecs_goods.is_alone_sale' => 1,
                'ecs_goods.is_delete'     => 0,
            ];
            $goodsStr = implode(',', $goodsIDArr);
            $result = \App\Models\Goods::leftJoin('ecs_goods_stores_attribute as a', 'ecs_goods.goods_id', '=', 'a.goods_id')
                ->leftJoin(DB::raw('(select goods_id,sum(goods_number) AS goods_number from ecs_order_goods where goods_id in(' . $goodsStr . ') GROUP BY goods_id) as o '), function ($join) {
                    $join->on('ecs_goods.goods_id', '=', 'o.goods_id');
                })
                ->selectRaw("o.goods_number,IFNULL(o.goods_number,0) as goods_number, ecs_goods.goods_id, ecs_goods.goods_name, ecs_goods.cat_id, ecs_goods.market_price, ecs_goods.shop_price, ecs_goods.promote_price, ecs_goods.promote_start_date, ecs_goods.promote_end_date, ecs_goods.goods_thumb, a.sort")
                ->where($where)
                ->whereIn('ecs_goods.cat_id', $children)
                ->whereIn('ecs_goods.goods_id', $goodsIDArr);
            if (!empty($keywords)) {
                $result = $result->where(function ($query) use ($keywords) {
                    $query->where('ecs_goods.goods_name', 'like', "%{$keywords}%")->orWhere('ecs_goods.goods_sn', 'like', "%{$keywords}%");
                });
            }
            $result = $result->skip($page)->take($pageSize)->orderBy('a.sort', 'desc')->orderBy('o.goods_number', 'desc')->get()->toArray();
        } catch (\Illuminate\Database\QueryException $e) {
            return $this->setErrorAndReturn([
                'return'   => false,
                'code'     => \Enum\EnumMain::HTTP_CODE_FAIL,
                'errorMsg' => "获取分类商品有误reason:" . $e->getMessage(),
                'userMsg'  => '获取分类商品有误！',
                'line'     => __LINE__,
            ]);
        }
        if (empty($result))
            return [];
        return $this->setGoodsData($result);

    }

    /***
     * 组装分类下的商品信息
     * @author: colin
     * @date: 2018/11/9 16:05
     * @param $data
     * @return array|type
     */
    public function setGoodsData($data)
    {
        $arr = [];
        try {
            foreach ($data as $key => $row) {
                $promote_price = 0;
                $goodsId = $row['goods_id'];
                if ($row['shop_price'] == 0) {
                    $attr_price = \App\Models\GoodsAttr::selectRaw("min(attr_price) attr_price")->where('attr_price', '>', 0)->where('goods_id', $goodsId)->first()->toArray();
                    $row['shop_price'] = $attr_price['attr_price'] ?? '';
                }
                /* 还在促销期则返回促销价，否则返回0 */
                if ($row['promote_price'] > 0)
                    $promote_price = \Helper\CFunctionHelper::bargainPrice($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
                $promote_price = ($promote_price > 0) ? \Helper\CFunctionHelper::priceFormat($promote_price) : '';
//                /* 销量 */
//                $numberX = \App\Models\OrderGoods::selectRaw("sum(goods_number) as goods_number")->where('goods_id', $goodsId)->first()->toArray();
//                $goods_number = $numberX['goods_number'] ?? 0;
                /* 商户商品上下架状态 */
                $sale_status = \App\Models\GoodStoreAttr::where(['goods_id' => $goodsId, 'gs_id' => $this->request->input('gsId')])->value('sale_status');
                $sale_status = $sale_status ?? 1;
                $arr[] = [
                    'goods_id'      => $goodsId,
                    'goods_name'    => $row['goods_name'],
                    'market_price'  => \Helper\CFunctionHelper::priceFormat($row['market_price']),
                    'shop_price'    => \Helper\CFunctionHelper::priceFormat($row['shop_price']),
                    'promote_price' => $promote_price,
                    'goods_thumb'   => env('STATIC_HOST') . \Helper\CFunctionHelper::getImagePath($row['goods_thumb'], true),
                    'goods_id'      => $goodsId,
                    'goods_name'    => $row['goods_name'],
                    'market_price'  => \Helper\CFunctionHelper::priceFormat($row['market_price']),
                    'shop_price'    => \Helper\CFunctionHelper::priceFormat($row['shop_price']),
                    'goods_number'  => $row['goods_number'],
                    'sale_status'   => $sale_status,
                    'sort'          => $row['sort'] ?? 0,
                ];

            }
        } catch (\Illuminate\Database\QueryException $e) {
            return $this->setErrorAndReturn([
                'return'   => false,
                'code'     => \Enum\EnumMain::HTTP_CODE_FAIL,
                'errorMsg' => "获取分类商品信息有误reason:" . $e->getMessage(),
                'userMsg'  => '获取分类商品信息有误！',
                'line'     => __LINE__,
            ]);
        }
        return $arr;

    }

    /***
     * 商户上下架商品
     * @author: colin
     * @date: 2018/11/12 12:05
     * @param $param
     * @param $gs_id
     * @return type
     */
    public function setSale()
    {
        $param = $this->request->all();
        $validator = Validator::make($param, [
            'goods_id'    => 'required|integer',
            'sale_status' => 'required|regex:/^[1-2]{1}$/',
        ]);
        if ($validator->fails()) {
            return $this->setErrorAndReturn([
                'return'  => false,
                'code'    => \Enum\EnumMain::HTTP_CODE_FAIL,
                'userMsg' => $validator->errors()->first(),
            ]);
        }
        try {
            $res_stores = \App\Models\StoresUser::select('gs_id', 'gs_name', 'gs_goods_id')->where('gs_id', $this->request->input('gsId'))->get()->toArray();
            $goodsIDArr = !empty($res_stores['0']['gs_goods_id']) ? unserialize($res_stores['0']['gs_goods_id']) : [];
            if (!in_array($param['goods_id'], $goodsIDArr))
                throw new Exception('商户不存在该商品！');
            $dataInfo = [
                'goods_id'    => $param['goods_id'],
                'sale_status' => $param['sale_status'],
            ];
            $dataInfo = $this->goodsStoreArr($dataInfo);
            if ($dataInfo === false) {
                throw new Exception('获取公共商品属性失败！');
            }
            \App\Models\GoodStoreAttr::updateOrCreate(['goods_id' => $param['goods_id'], 'gs_id' => $this->request->input('gsId')], $dataInfo);
        } catch (\Exception $e) {
            return $this->setErrorAndReturn([
                'return'   => false,
                'code'     => \Enum\EnumMain::HTTP_CODE_FAIL,
                'errorMsg' => "商品上下架有误reason:" . $e->getMessage(),
                'userMsg'  => '修改商品状态不成功！',
                'line'     => __LINE__,
            ]);
        }
        $business = $param['sale_status'] == 1 ? \Enum\EnumLang::BUSINESS_UPDATE_ONSALE : \Enum\EnumLang::BUSINESS_UPDATE_NOSALE;
        $goodsname = \App\Models\Goods::where('goods_id', $param['goods_id'])->value('goods_name');
        //添加商户操作日志
        \Helper\CFunctionHelper::setStoreLogs($this->request->input('gsId'), $business, 'ecs_goods_stores_attribute', '2', \Enum\EnumLang::$businessMap[$business] . ':' . $goodsname . '(id:' . $param['goods_id'] . ')');
        return true;

    }

    /**
     * 获取商品公共属性和私有属性
     * @author: colin
     * @date: 2018/12/10 14:27
     * @param $data
     */
    public function goodsStoreArr($data)
    {
        if (empty($data['goods_id'])) {
            return false;
        }
        $count = \App\Models\GoodStoreAttr::where(['goods_id' => $data['goods_id'], 'gs_id' => $this->request->input('gsId')])->count();
        if (!empty($count)) {//已有私有属性直接返回
            return $data;
        }
        try {
            $goodsArr = \App\Models\Goods::select('pickup_mode', 'reserve_hours')->where('goods_id', $data['goods_id'])->firstOrFail()->toArray();;
        } catch (\Exception $e) {
            return false;
        }
        $data = array_merge($data, $goodsArr);
        return $data;

    }

    /***
     * 商户设置商品
     * @author: colin
     * @date: 2018/12/4 8:43
     * @return bool|type
     */
    public function setSort()
    {
        $param = $this->request->all();
        try {
            $res_stores = \App\Models\StoresUser::select('gs_id', 'gs_name', 'gs_goods_id')->where('gs_id', $this->request->input('gsId'))->firstOrFail()->toArray();
            $goodsIDArr = !empty($res_stores['gs_goods_id']) ? unserialize($res_stores['gs_goods_id']) : [];
            if (!in_array($param['goods_id'], $goodsIDArr))
                throw new Exception('商户不存在该商品！');
            $dataInfo = [
                'goods_id' => $param['goods_id'],
                'sort'     => $param['sort'],
            ];
            \App\Models\GoodStoreAttr::updateOrCreate(['goods_id' => $param['goods_id'], 'gs_id' => $this->request->input('gsId')], $dataInfo);
        } catch (\Exception $e) {
            return $this->setErrorAndReturn([
                'return'   => false,
                'code'     => \Enum\EnumMain::HTTP_CODE_FAIL,
                'errorMsg' => "商品设置排序有误reason:" . $e->getMessage(),
                'userMsg'  => '修改商品排序权重值不成功！',
                'line'     => __LINE__,
            ]);
        }
        $goodsname = \App\Models\Goods::where('goods_id', $param['goods_id'])->value('goods_name');
        //添加商户操作日志
        \Helper\CFunctionHelper::setStoreLogs($this->request->input('gsId'), \Enum\EnumLang::BUSINESS_UPDATE_SORT, 'ecs_goods_stores_attribute', '2', '设置商品排序：' . $goodsname . '__商品id：' . $param['goods_id']);
        return true;
    }

    /***
     * 获取商户的商品分类
     * @author: colin
     * @date: 2018/11/15 9:12
     * @return mixed
     */
    public function goodType()
    {
        try {
            $goods = \App\Models\StoresUser::select('gs_goods_id')->where(['gs_id' => $this->request->input('gsId')])->get()->toArray();
            $goods = $goods[0]['gs_goods_id'] ? unserialize($goods[0]['gs_goods_id']) : '';
            if (!$goods)
                throw new Exception('商户不存在已上架商品！');
            $where = ['ecs_goods.is_on_sale' => 1, 'ecs_goods.is_delete' => 0];//, 'ca.show_in_nav' => 1
            $goodsType = \App\Models\Goods::selectRaw("distinct ecs_goods.cat_id,ca.cat_name")
                ->join('ecs_category as ca', 'ecs_goods.cat_id', '=', 'ca.cat_id')
                ->where($where)->whereIn('ecs_goods.goods_id', $goods)
                ->orderBy('ca.sort_order', 'asc')->get()->toArray();
        } catch (\Exception $e) {
            return $this->setErrorAndReturn([
                'return'   => false,
                'code'     => \Enum\EnumMain::HTTP_CODE_FAIL,
                'errorMsg' => "商户的商品类别获取有误reason:" . $e->getMessage(),
                'userMsg'  => '商品类别获取不成功！',
                'line'     => __LINE__,
            ]);
        }
        return $goodsType;
    }

    /***
     * 获取商户的商品基本信息
     * @author: colin
     * @date: 2018/11/15 9:12
     * @return mixed
     */
    public function goodInfo()
    {
        $goodsId = $this->request->input('id');
        try {
            $goods = \App\Models\Goods::selectRaw("brand_id,goods_desc,cat_id,goods_id,goods_name,shop_price,market_price,promote_price,promote_start_date,promote_end_date,goods_img,goods_number,imgs")
                ->where(['ecs_goods.goods_id' => $goodsId])
                ->where(['ecs_goods.is_on_sale' => 1])
                ->get()->toArray();
            if (!isset($goods['0']['goods_id']))
                throw new Exception('不存在的商品！');
            $properties = \App\Models\GoodsAttr::leftJoin('ecs_attribute as a', 'ecs_goods_attr.attr_id', '=', 'a.attr_id')
                ->select("a.attr_id", "a.attr_name", "a.attr_group", "a.is_linked", "a.attr_type", "ecs_goods_attr.goods_attr_id", "ecs_goods_attr.attr_value", "ecs_goods_attr.attr_price", "ecs_goods_attr.original_price")
                ->where(['ecs_goods_attr.goods_id' => $goodsId])
                ->orderBy('a.sort_order', 'asc')
                ->orderBy('ecs_goods_attr.attr_price', 'asc')
                ->orderBy('ecs_goods_attr.goods_attr_id', 'asc')
                ->get()->toArray();
            /* 品牌描述 */
            $brand_desc = \App\Models\Brand::select('gs_desc_16', 'gs_extract_16')->where(['brand_id' => $goods[0]['brand_id']])->get()->toArray();
            $gs_extract = $gs_desc = '';
            if ($brand_desc['0']) {
                $gs_extract = $brand_desc['0']['gs_extract_16'];
                $gs_desc = $brand_desc['0']['gs_desc_16'];
            }
        } catch (\Exception $e) {
            return $this->setErrorAndReturn([
                'return'   => false,
                'code'     => \Enum\EnumMain::HTTP_CODE_FAIL,
                'errorMsg' => $e->getMessage(),
                'userMsg'  => $e->getMessage(),
                'line'     => __LINE__,
            ]);
        }
        $properties = self::properties($properties);
        $goods = self::goodsInfo($goods[0]);
        $goodsInfo = [
            'goods'      => $goods,
            'properties' => $properties,
            'gs_extract' => $gs_extract,
            'gs_desc'    => $gs_desc,
        ];
        return $goodsInfo;
    }

    /***
     * 商品分类属性
     * @author: colin
     * @date: 2018/11/19 10:49
     * @param $properties
     * @return array
     */
    private function properties($properties)
    {
        if (empty($properties))
            return [];
        foreach ($properties AS $row) {
            $arr[$row['attr_id']]['attr_type'] = $row['attr_type'];
            $arr[$row['attr_id']]['name'] = $row['attr_name'];
            $arr[$row['attr_id']]['values'][] = [
                'label' => $row['attr_value'],
                'price' => $row['attr_price'],
                'id'    => $row['goods_attr_id'],
            ];
        }
        return $arr;
    }

    /**
     * 商品基础信息
     * @author: colin
     * @date: 2018/11/19 11:15
     * @param $goods
     * @return array
     */
    private function goodsInfo($row)
    {
        if (empty($row))
            return [];
        /* 修正促销价格 */
        if ($row['promote_price'] > 0) {
            $row['promote_price'] = \Helper\CFunctionHelper::isPromote($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
        }
        if ($row['goods_img']) {
            $row['goods_img'] = config('merchant.static_host') . $row['goods_img'];
        }
        $pimgs = [];
        if (!empty($row['imgs'])) {
            $parr = explode(',', $row['imgs']);
            for ($i = 0; $i < count($parr); $i++) {
                $pimgs[] = config('merchant.static_host') . $parr[$i];
            }
        }
        /* 商户商品上下架状态 */
        $sale_status = \App\Models\GoodStoreAttr::where(['goods_id' => $row['goods_id'], 'gs_id' => $this->request->input('gsId')])->value('sale_status');
        $sale_status = $sale_status ?? 1;
        $arr = [
            'cat_id'        => $row['cat_id'],
            'goods_id'      => $row['goods_id'],
            'goods_name'    => $row['goods_name'],
            'shop_price'    => $row['shop_price'],
            'market_price'  => $row['market_price'],
            'promote_price' => $row['promote_price'],
            'goods_img'     => $row['goods_img'],
            'pimgs'         => $pimgs,
            'goods_number'  => $row['goods_number'],
            'sale_status'   => $sale_status,
            'description'   => \Helper\CFunctionHelper::descIMgReplace($row['goods_desc']),
        ];
        return $arr;
    }

    /**
     * @return array
     */
    public function goodsStock()
    {
        $goodsId = $this->request->input('id');
        $gsId = $this->request->input('gsId');
        try {
            $goodsAttr = \App\Models\GoodsAttr::select('goods_attr_id', 'attr_id', 'attr_value')->where(['goods_id' => $goodsId])->orderBy('attr_id', 'asc')->orderBy('goods_attr_id', 'asc')->get()->toArray();
            $stockInfoTmp = \App\Models\GoodsStock::select('attr_ids', 'num', 'num_promotion')->where(['gs_id' => $gsId, 'goods_id' => $goodsId])->get()->toArray();
            $stockInfo = [];
            if (!empty($stockInfoTmp)) {
                foreach ($stockInfoTmp as $item) {
                    $ky = $item['attr_ids'];
                    unset($item['attr_ids']);
                    $stockInfo[$ky] = $item;
                }
            }
            $attrg = [];
            $dataList = [];
            if (!empty($goodsAttr)) {
                $attrNameArr = [];
                foreach ($goodsAttr as $k => $item) {
                    $attrId = $item['attr_id'];
                    $id = $item['goods_attr_id'];
                    $attrNameArr[$id] = $item['attr_value'];
                    $attrg[$attrId][] = $item;
                }
                $tmpArr = [];
                $i = 0;
                foreach ($attrg as $item) {
                    $tmpArr[$i] = $item;
                    $i++;
                }
                $len = count($tmpArr);
                if ($len == 1) {
                    foreach ($tmpArr[0] as $k => $item) {
                        $id = $item['goods_attr_id'];
                        $dt = ['attr_ids' => $id, 'name' => $item['attr_value']];
                        $dataList[] = isset($stockInfo[$id]) ? array_merge($dt, $stockInfo[$id]) : $dt;
                    }
                } elseif ($len == 2) {
                    foreach ($tmpArr[0] as $k => $item) {
                        $id = $item['goods_attr_id'];
                        $vs = $item['attr_value'];
                        foreach ($tmpArr[1] as $j => $value) {
                            $ids = $id . '-' . $value['goods_attr_id'];
                            $dt = ['attr_ids' => $ids, 'name' => $vs . ' + ' . $value['attr_value']];
                            $dataList[] = isset($stockInfo[$ids]) ? array_merge($dt, $stockInfo[$ids]) : $dt;
                        }
                    }
                } else {
                    throw new \Exception('暂不支持三级以上的属性，请联系客服！');
                }
            } else {//无属性
                $ids = '0';
                $dt = ['attr_ids' => $ids, 'name' => '商品库存'];
                $dataList[] = isset($stockInfo[$ids]) ? array_merge($dt, $stockInfo[$ids]) : $dt;
            }
            return $dataList;
        } catch (\Exception $e) {
            return $this->setErrorAndReturn([
                'return'   => false,
                'code'     => \Enum\EnumMain::HTTP_CODE_FAIL,
                'errorMsg' => $e->getMessage(),
                'userMsg'  => $e->getMessage(),
                'line'     => __LINE__,
            ]);
        }
    }

    /**
     * 保存商品库存
     * @return bool|type
     */
    public function saveGoodsStock()
    {
        $goodsId = $this->request->input('id');
        $gsId = $this->request->input('gsId');
        \DB::beginTransaction();
        try {
            $stockData = $this->request->input('stock');
            $isPromote = (int)$this->request->input('is_promote');
            $zdm = $isPromote ? 'num_promotion' : 'num';
            if (empty($stockData)) {
                throw new \Exception('没有数据！');
            }
            $content = '属性：';
            foreach ($stockData as $ids => $value) {
                $model = new \App\Models\GoodsStock();
                $stId = $model->select('st_id')->where(['gs_id' => $gsId, 'goods_id' => $goodsId, 'attr_ids' => $ids])->value('st_id');
                if ($stId) {
                    $model->where('st_id', $stId)->update([$zdm => $value]);
                } else {
                    $model->insert(['gs_id' => $gsId, 'goods_id' => $goodsId, 'attr_ids' => $ids, $zdm => $value]);
                }
                if ($ids === 0)
                    $content = '';
                $content .= $ids ? $ids . "({$value})," : "({$value}),";
            }
            \DB::commit();
            //添加商户操作日志
            $business = $isPromote ? \Enum\EnumLang::BUSINESS_SET_PROMOTE_STOCK : \Enum\EnumLang::BUSINESS_SET_STOCK;
            $content = trim($content, ',');
            \Helper\CFunctionHelper::setStoreLogs($gsId, $business, 'ecs_goods_stock', '2', \Enum\EnumLang::$businessMap[$business] . ' 商品id：' . $goodsId . $content);
            return true;
        } catch (\Exception $e) {
            \DB::rollBack();
            return $this->setErrorAndReturn([
                'return'   => false,
                'code'     => \Enum\EnumMain::HTTP_CODE_FAIL,
                'errorMsg' => $e->getMessage(),
                'userMsg'  => $e->getMessage(),
                'line'     => __LINE__,
            ]);
        }
    }

    /***
     * Notes
     * @author: colin
     * @date: 2018/11/20 15:41
     * @return array|type
     */
    public function orderDetail()
    {
        $orderSn = $this->request->input('orderSn');
        $userInfo = $this->request->input('userInfo');
        $gsIdArr = [$this->request->input('gsId')];
        try {
            /* 管理员可以查看旗下加盟店铺的订单 */
            if ($userInfo['is_manage'] == 1) {
                $gsIdChid = \App\Models\StoresUser::where(['is_manage' => 0, 'pid' => $this->request->input('gsId')])->pluck('gs_id')->toArray();
                $gsIdArr = array_merge($gsIdArr, $gsIdChid);
            }
            \App\Models\OrderInfo::where(['order_pick_stores' => $this->request->input('gsId'), 'order_sn' => $orderSn])->whereNull('isread')->update(['isread' => 1]);//更新未读订单浏览状态
            $result = \App\Models\OrderInfo::selectRaw('user_id,pay_id,address,order_note,order_pick_stores,order_pick_time,shipping_time shipping_time,order_id,order_sn,order_status,shipping_status,pay_status,FROM_UNIXTIME(add_time,"%Y-%m-%d %H:%i:%s") add_time, order_lxr, order_tel,last_cfm_time,(order_amount + shipping_fee + insure_fee + pay_fee + pack_fee + card_fee + tax - discount) AS total_fee');
            $result = $result->where('order_sn', $orderSn)
                ->whereIn('order_pick_stores', $gsIdArr);
//                ->whereIn('order_status', [0, 1, 3, 4, 5, 6]);
            $result = $result->get()->toArray();
            if (empty($result[0])) {
                throw new \Exception('只能查看自己的订单详情！');
            }
        } catch (\Exception $e) {
            return $this->setErrorAndReturn([
                'return'   => false,
                'code'     => \Enum\EnumMain::HTTP_CODE_FAIL,
                'errorMsg' => "获取用户订单详情失败:" . $this->request->input('gsId') . ",reason:" . $e->getMessage(),
                'userMsg'  => $e->getMessage(),
                'line'     => __LINE__,
            ]);
        }
        $result = $this->detailData($result[0]);
        return $result;
    }

    /***
     * 组装订单状态
     * @author: colin
     * @date: 2018/11/1 15:20
     * @param $data
     * @return array
     */
    public function detailData($data)
    {
        if (empty($data)) {
            return [];
        }
        $data['shipping_status'] = ($data['shipping_status'] == \Enum\EnumKeys::SS_SHIPPED_ING) ? \Enum\EnumKeys::SS_PREPARING : $data['shipping_status'];
        $storeData = \App\Models\StoresUser::where(['gs_id' => $data['order_pick_stores']])->first(['gs_address', 'gs_name']);
        $gsAddress = $storeData->gs_address;
        $gsName = $storeData->gs_name;
        $goodsList = $this->orderGoods($data['order_id']);
        //物流信息获取
        $orderExp = \App\Models\OrderExpress::where('order_sn', $data['order_sn'])->first(['ex_id', 'ex_mess']);
        if (isset($orderExp->ex_id) && $orderExp->ex_id < 1) {
            $orderExp = $orderExp->toArray();
            $orderExp['ex_name'] = '其它快递';
        } else {
            $orderExp = \App\Models\OrderExpress::select('ecs_order_express.ex_num', 'i.ex_name', 'i.ex_id')->join('ecs_express_info as i', 'ecs_order_express.ex_id', '=', 'i.ex_id')->where('ecs_order_express.order_sn', $data['order_sn'])->first();
            if (isset($orderExp->ex_id)) {
                $orderExp = $orderExp->toArray();
            }
        }

        $userName = $data['user_id'] ? \App\Models\Users::where('user_id', $data['user_id'])->value('user_name') : '';
        $arr = [
            'order_id'        => $data['order_id'],
            'order_sn'        => $data['order_sn'],
            'user_name'       => $userName,
            'order_time'      => $data['add_time'],
            'pay_value'       => \Enum\EnumLang::payment($data['pay_id']),
            'order_status'    => $data['order_status'],
            'shipping_status' => $data['shipping_status'],
            'shipping_time'   => $data['shipping_time'],
            'total_fee'       => \Helper\CFunctionHelper::priceFormat($data['total_fee'], false),
            'order_lxr'       => $data['order_lxr'],
            'order_tel'       => $data['order_tel'],
            'order_pick_time' => $data['order_pick_time'],
            'address'         => $data['address'],
            'order_note'      => $data['order_note'],
            'gs_address'      => $gsAddress,
            'gs_name'         => $gsName,
            'goods_list'      => $goodsList,
            'order_exp'       => $orderExp,

        ];
        $now = time();
        if (!empty($data['address']) && !in_array($data['order_status'], [2, 3, 4])) {
            //号码不加密
            if ($data['shipping_time'] > 0 && $now - $data['shipping_time'] >= 60 * 60 * 24 * 7) {
                $arr['encryption'] = 1;
                !empty($arr['order_tel']) && $arr['order_tel'] = substr_replace($arr['order_tel'], '****', 3, 4);
                !empty($arr['user_name']) && $arr['user_name'] = substr_replace($arr['user_name'], '****', 3, 4);
            } else {
                $arr['encryption'] = 0;
            }
        } else {
            $arr['encryption'] = 1;
            !empty($arr['order_tel']) && $arr['order_tel'] = substr_replace($arr['order_tel'], '****', 3, 4);
            !empty($arr['user_name']) && $arr['user_name'] = substr_replace($arr['user_name'], '****', 3, 4);
        }
        return $arr;
    }

    /***
     * 物流信息查询
     * @author: colin
     * @date: 2018/11/21 17:37
     * @return array|type
     */
    public function expressDetail()
    {
        try {
            $orderSn = $this->request->input('orderSn');
            //物流信息获取
            $orderExp = \App\Models\OrderExpress::select('ecs_order_express.ex_num', 'i.ex_sn', 'i.ex_name', 'i.ex_tel')
                ->join('ecs_express_info as i', 'ecs_order_express.ex_id', '=', 'i.ex_id')
                ->where('ecs_order_express.order_sn', $orderSn)
                ->first();
            if (isset($orderExp->ex_sn)) {
                $orderExp = $orderExp->toArray();
            }
            if (empty($orderExp) || empty($orderExp['ex_num'])) {
                throw new Exception("无相关物流信息");
            }
            $result = [
                'ex_num'  => $orderExp['ex_num'],
                'ex_name' => $orderExp['ex_name'],
                'ex_tel'  => $orderExp['ex_tel'],
                'list'    => [],
            ];
            $exMess = \App\Models\ExpressDetail::where(['order_sn' => $orderSn, 'ex_num' => $orderExp['ex_num']])->value('ex_cnt');
            if (!empty($exMess)) {
                $result['ischeck'] = '1';
                $result['list'] = unserialize($exMess);
                return $result;
            }
            $dataArr = \Helper\CFunctionHelper::kuaidicx($orderExp['ex_sn'], $orderExp['ex_num']);
            if ($dataArr['status'] == 1 && !empty($dataArr['data']['list'])) {
                $result['state'] = $dataArr['data']['state'];
                $result['ischeck'] = $dataArr['data']['ischeck'];
                $result['list'] = $dataArr['data']['list'];
                if ($result['ischeck'] == 1) {//保存
                    $dataInfo = [
                        'order_sn' => $orderSn,
                        'ex_num'   => $orderExp['ex_num'],
                        'ex_cnt'   => serialize($dataArr['data']['list']),
                    ];
                    \App\Models\ExpressDetail::updateOrcreate(['order_sn' => $orderSn], $dataInfo);
                }
            } else {
                return $this->setErrorAndReturn([
                    'return'   => false,
                    'code'     => \Enum\EnumMain::HTTP_CODE_FAIL,
                    'errorMsg' => "expressDetail 查看物流信息失败 fail.orderSn:{$orderSn},reason:" . $dataArr['message'],
                    'userMsg'  => $dataArr['message'],
                    'line'     => __LINE__,
                ]);
            }

        } catch (\Illuminate\Database\QueryException  $e) {
            return $this->setErrorAndReturn([
                'return'   => false,
                'code'     => \Enum\EnumMain::HTTP_CODE_FAIL,
                'errorMsg' => "expressDetail 查看物流信息失败 fail.orderSn:{$orderSn},reason:" . $e->getMessage(),
                'userMsg'  => $e->getMessage(),
                'line'     => __LINE__,
            ]);
        }
        return $result;

    }

    /**
     * 快递公司信息查询
     * @author: colin
     * @date: 2018/12/4 11:19
     */
    public function setExpress()
    {
        try {
            $orderSn = $this->request->input('orderSn');
            $exId = $this->request->input('exId');
            $exNum = $this->request->input('exNum');
            $exMess = $this->request->input('exMess');
            $userInfo = $this->request->input('userInfo');
            $gsIdArr = [$this->request->input('gsId')];
            $expressInfo = [
                'order_sn' => $orderSn,
                'ex_id'    => $exId,
                'ex_num'   => trim($exNum),
                'ex_mess'  => trim($exMess),
            ];
            /* 管理员可以查看旗下加盟店铺的订单 */
            if ($userInfo['is_manage'] == 1) {
                $gsIdChid = \App\Models\StoresUser::where(['is_manage' => 0, 'pid' => $this->request->input('gsId')])->pluck('gs_id')->toArray();
                $gsIdArr = array_merge($gsIdArr, $gsIdChid);
            }
            \App\Models\OrderInfo::select('order_sn')->where(['order_sn' => $orderSn])->whereIn('order_pick_stores', $gsIdArr)->firstOrFail();
            //快递公司信息获取
            $exNum > 0 && \App\Models\ExpressInfo::select('ex_id')->where(['ex_id' => $exId])->firstOrFail();
            $upTimes = \App\Models\OrderExpress::where('order_sn', $orderSn)->value('up_times');
            if (!empty($upTimes) || $upTimes == 0) {
                $expressInfo['up_times'] = $upTimes + 1;
            }
            \App\Models\OrderExpress::updateOrCreate(['order_sn' => $orderSn], $expressInfo);
        } catch (\Exception  $e) {
            return $this->setErrorAndReturn([
                'return'   => false,
                'code'     => \Enum\EnumMain::HTTP_CODE_FAIL,
                'errorMsg' => "setExpress 设置物流信息fail,reason:" . $e->getMessage(),
                'userMsg'  => '设置物流信息失败！',
                'line'     => __LINE__,
            ]);
        }
        return true;
    }

    /**
     * 快递公司信息查询
     * @author: colin
     * @date: 2018/12/4 11:19
     */
    public function expressInfo()
    {
        try {
            //快递公司信息获取
            $expressInfo = \App\Models\ExpressInfo::select('ex_id', 'ex_name')->orderBy('ex_id', 'asc')->get()->toArray();
            if (empty($expressInfo)) {
                throw new Exception('没有快递公司信息！');
            }
        } catch (\Exception  $e) {
            return $this->setErrorAndReturn([
                'return'   => false,
                'code'     => \Enum\EnumMain::HTTP_CODE_FAIL,
                'errorMsg' => "expressInfo 查看快递单公司信息失败,reason:" . $e->getMessage(),
                'userMsg'  => '没有快递公司信息！',
                'line'     => __LINE__,
            ]);
        }
        return $expressInfo;
    }

    /**
     * 退货列表
     * @return $this|\Illuminate\Database\Query\Builder|\Illuminate\Support\Collection|type|static
     */
    public function refundList()
    {
        $param = $this->request->all();
        //print_r($param);exit;
        $userInfo = $this->request->input('userInfo');
        try {
            //\DB::connection()->enableQueryLog();
            $gsIdArr[0] = $userInfo['gs_id'];
            if ($userInfo['is_manage'] == 1) {
                $gsIdChid = \App\Models\StoresUser::where(['is_manage' => 0, 'pid' => $this->request->input('gsId')])->pluck('gs_id')->toArray();
                $gsIdArr = array_merge($gsIdArr, $gsIdChid);
            }
            $dataList = \App\Models\RefundApply::selectRaw('ecs_refund_apply.apply_id,ecs_refund_apply.order_id,ecs_refund_apply.rec_ids,ecs_refund_apply.apply_status,ecs_refund_apply.apply_time,i.user_id,i.order_sn,i.order_pick_stores')
                ->leftJoin('ecs_order_info as i', 'ecs_refund_apply.order_id', '=', 'i.order_id')->whereIn('i.order_pick_stores', $gsIdArr)
                ->when(isset($param['apply_status']), function ($query) use ($param) {
                    return $query->where('ecs_refund_apply.apply_status', intval($param['apply_status']));
                })
                ->when(isset($param['order_sn']) && !empty($param['order_sn']), function ($query) use ($param) {
                    $orderSn = trim($param['order_sn']);
                    return $query->where('i.order_sn', $orderSn);
                })
                ->when(isset($param['page']) && !empty($param['page']), function ($query) use ($param) {
                    $page = intval($param['page']);
                    $pgSize = 10;
                    $offset = ($page - 1) * $pgSize;
                    return $query->skip($offset);
                })
                ->take(10)->orderByDesc('ecs_refund_apply.apply_id')->get();
            /*print_r(DB::getQueryLog());
            exit;*/
            $dataList = $dataList->toArray();
            foreach ($dataList as &$item) {
                unset($item['user_id']);
                $item['apply_time'] = date('Y-m-d H:i', $item['apply_time']);
                $item['gs_name'] = \DB::table('ecs_goods_stores')->where('gs_id', $item['order_pick_stores'])->value('gs_name');
                $goodsList = \App\Models\OrderGoods::selectRaw('g.goods_id,g.goods_name,g.goods_thumb,ecs_order_goods.goods_number,ecs_order_goods.goods_price,ecs_order_goods.goods_attr_id')
                    ->leftJoin('ecs_goods as g', 'ecs_order_goods.goods_id', '=', 'g.goods_id')->where('ecs_order_goods.order_id', $item['order_id'])
                    ->when(!empty($item['rec_ids']), function ($query) use ($item) {
                        return $query->whereIn('rec_id', explode(',', $item['rec_ids']));
                    })
                    ->get()->toArray();
                foreach ($goodsList as $k => $goods) {
                    if (!empty($goods['goods_thumb'])) {
                        $goodsList[$k]['goods_thumb'] = env('STATIC_HOST') . $goods['goods_thumb'];//产品缩略图
                    }
                    //产品规格
                    if ($goods['goods_attr_id']) {
                        $goodAttrId = explode(',', $goods['goods_attr_id']);
                        $goodsList[$k]['goods_attr'] = \App\Models\GoodsAttr::select('ecs_goods_attr.attr_id', 'ecs_goods_attr.attr_value', 'ea.attr_name')->leftJoin('ecs_attribute as ea', 'ecs_goods_attr.attr_id', '=', 'ea.attr_id')
                            ->whereIn('goods_attr_id', $goodAttrId)->get()->toArray();//规格
                    } else {
                        $goodsList[$k]['goods_attr'] = [];
                    }
                }
                $item['goods_list'] = $goodsList;
            }
            return $dataList;
        } catch (\Exception  $e) {
            return $this->setErrorAndReturn([
                'return'   => false,
                'code'     => \Enum\EnumMain::HTTP_CODE_FAIL,
                'errorMsg' => "refundList 获取退货信息失败,reason:" . $e->getMessage(),
                'userMsg'  => '获取退货信息失败！',
                'line'     => __LINE__,
            ]);
        }
    }

    /**
     * 退货单详情
     * @return bool|array
     */
    public function refundDetail()
    {
        //$userInfo = $this->request->input('userInfo');
        $gsId = $this->request->input('gsId');
        $param = $this->request->all();
        try {
            $applyId = isset($param['apply_id']) ? intval($param['apply_id']) : 0;
            if (empty($applyId)) {
                throw new Exception("缺少参数");
            }
            $applyData = \App\Models\RefundApply::selectRaw('ecs_refund_apply.order_id,ecs_refund_apply.rec_ids,ecs_refund_apply.apply_status,ecs_refund_apply.apply_time,ecs_refund_apply.return_reason,ecs_refund_apply.dispose_time,i.user_id,i.order_sn,i.order_pick_stores,i.address,i.order_lxr,i.order_tel')
                ->leftJoin('ecs_order_info as i', 'ecs_refund_apply.order_id', '=', 'i.order_id')->where('ecs_refund_apply.apply_id', $applyId)->first();
            if (!isset($applyData->order_id)) {
                throw new Exception("数据不存在，请检查或联系客服");
            }
            $applyData = $applyData->toArray();
            $applyData['apply_time'] = date('Y/m/d H:i:s', $applyData['apply_time']);
            $applyData['dispose_time'] = !empty($applyData['dispose_time']) ? date('Y/m/d H:i:s', $applyData['dispose_time']) : '';//退款时间
            $applyData['user_name'] = \App\Models\Users::where('user_id', $applyData['user_id'])->value('user_name');//用户

            $applyData['encryption'] = 1;
            !empty($applyData['order_tel']) && $applyData['order_tel'] = substr_replace($applyData['order_tel'], '****', 3, 4);
            !empty($applyData['user_name']) && $applyData['user_name'] = substr_replace($applyData['user_name'], '****', 3, 4);


            $applyData['gs_name'] = \DB::table('ecs_goods_stores')->where('gs_id', $applyData['order_pick_stores'])->value('gs_name');//商户名称
            $goodsList = \App\Models\OrderGoods::selectRaw('g.goods_id,g.goods_name,g.goods_thumb,ecs_order_goods.goods_number,ecs_order_goods.goods_price,ecs_order_goods.goods_attr_id')
                ->leftJoin('ecs_goods as g', 'ecs_order_goods.goods_id', '=', 'g.goods_id')->where('ecs_order_goods.order_id', $applyData['order_id'])
                ->when(!empty($applyData['rec_ids']), function ($query) use ($applyData) {
                    $recIdArr = explode(',', $applyData['rec_ids']);
                    return !empty($recIdArr) ? $query->whereIn('rec_id', $recIdArr) : '';
                })
                ->get()->toArray();
            $refundTotal = 0;
            foreach ($goodsList as $k => $goods) {
                $goodsList[$k]['total_price'] = intval($goods['goods_number']) * floatval($goods['goods_price']);
                $refundTotal += $goodsList[$k]['total_price'];
                if (!empty($goods['goods_thumb'])) {
                    $goodsList[$k]['goods_thumb'] = env('STATIC_HOST') . $goods['goods_thumb'];//产品缩略图
                }
                //产品规格
                if ($goods['goods_attr_id']) {
                    $goodAttrId = explode(',', $goods['goods_attr_id']);
                    $goodsList[$k]['goods_attr'] = \App\Models\GoodsAttr::select('ecs_goods_attr.attr_id', 'ecs_goods_attr.attr_value', 'ea.attr_name')->leftJoin('ecs_attribute as ea', 'ecs_goods_attr.attr_id', '=', 'ea.attr_id')
                        ->whereIn('goods_attr_id', $goodAttrId)->get()->toArray();//规格
                } else {
                    $goodsList[$k]['goods_attr'] = [];
                }
            }
            $applyData['refund_total'] = number_format($refundTotal, 2);
            $applyData['goods_list'] = $goodsList;
            return $applyData;
        } catch (\Mockery\Exception $e) {
            return $this->setErrorAndReturn([
                'return'   => false,
                'code'     => \Enum\EnumMain::HTTP_CODE_FAIL,
                'errorMsg' => "refundDetail 获取退货详情失败,reason:" . $e->getMessage(),
                'userMsg'  => '获取退货详情失败！',
                'line'     => __LINE__,
            ]);
        }
    }

    /**
     * 退货操作
     * @return bool|type
     */
    public function orderReturn()
    {
        $userInfo = $this->request->input('userInfo');
        $gsId = $this->request->input('gsId');
        $param = $this->request->all();
        \DB::beginTransaction();
        try {
            //DB::connection()->enableQueryLog();
            $applyId = intval($param['apply_id']);
            $gsIdArr[] = $gsId;
            if ($userInfo['is_manage'] == 1) {
                $gsIdChid = \App\Models\StoresUser::where(['is_manage' => 0, 'pid' => $gsId])->pluck('gs_id')->toArray();
                $gsIdArr = array_merge($gsIdArr, $gsIdChid);
            }
            $applyData = \App\Models\RefundApply::selectRaw('ecs_refund_apply.order_id,ecs_refund_apply.rec_ids,ecs_refund_apply.apply_status,i.user_id,i.order_sn')
                ->leftJoin('ecs_order_info as i', 'ecs_refund_apply.order_id', '=', 'i.order_id')->whereIn('i.order_pick_stores', $gsIdArr)->where('ecs_refund_apply.apply_id', $applyId)->first();
            if (!isset($applyData->order_id)) {
                throw new Exception('数据不存在，请确认！');
            }
            if ($applyData->apply_status > 0) {
                throw new Exception('本次退货申请处理过了，请确认后操作！');
            }
            $recIdsArr = !empty($applyData->rec_ids) ? explode(',', $applyData->rec_ids) : [];
            //print_r($recIdsArr);exit;
            $orderGoods = \App\Models\OrderGoods::where('order_id', $applyData->order_id)
                ->when(!empty($recIdsArr), function ($query) use ($recIdsArr) {
                    return $query->where('status', 1)->whereIn('rec_id', $recIdsArr);
                })
                ->when(empty($recIdsArr), function ($query) {
                    return $query->where('status', '<', 2);
                })->get(['goods_id', 'goods_number', 'goods_attr_id', 'goods_price'])->toArray();
            $isAllRefund = false;//是否全部退货
            if (!empty($recIdsArr)) {
                $recIdsArr = array_unique($recIdsArr);
                $goodsCount = \App\Models\OrderGoods::where('order_id', $applyData->order_id)->where('status', '<', 2)->count();
                if ($goodsCount <= count($recIdsArr)) {
                    $isAllRefund = true;
                }
                \App\Models\OrderGoods::whereIn('rec_id', $recIdsArr)->update(['send_number' => 0, 'status' => 2, 'change_time' => date('Y-m-d H:i:s')]);
            } else {
                $isAllRefund = true;
            }

            /* print_r(DB::getQueryLog());
             exit;*/
            if (empty($orderGoods)) {
                throw new Exception('订单数据有误，请检查！');
            }
            $goodsPriceTotal = 0;
            foreach ($orderGoods as $goods) {
                $goodsPriceTotal += intval($goods['goods_number']) * floatval($goods['goods_price']);
                //商户自定义库存处理
                $isPromote = $this->isGoodsPromote($goods['goods_id']);
                $attrIdsArr = explode(',', $goods['goods_attr_id']);
                $sizeId = isset($attrIdsArr[0]) ? $attrIdsArr[0] : '';
                $tastesId = isset($attrIdsArr[1]) ? $attrIdsArr[1] : '';
                $this->updateGoodsStock($gsId, $goods['goods_id'], $goods['goods_number'], $isPromote, $sizeId, $tastesId);
            }
            $order = \App\Models\OrderInfo::where('order_id', $applyData->order_id)->firstOrFail(
                ['order_sn', 'user_id', 'pay_id', 'goods_amount', 'order_amount', 'order_amount_zy', 'surplus', 'bonus', 'shipping_fee', 'pay_status', 'has_settled']
            );
            $order = $order->toArray();
            if ($isAllRefund && $order['shipping_fee'] > 0) {//全部退货，如果有运费则一起退了
                $goodsPriceTotal += floatval($order['shipping_fee']);
            }
            if ($order['pay_status'] != 2) {
                throw new Exception('订单(' . $order['order_sn'] . ')未付款，请检查');
            }
            if ($order['has_settled'] == 1) {
                throw new Exception('订单(' . $order['order_sn'] . ')已经结算，请联系客服');
            }
            $goodsPriceTotal_ = $goodsPriceTotal;//备用
            $orderInfoUpdata = [//订单要修改的数据
                'goods_amount'     => $order['goods_amount'] - $goodsPriceTotal_,
                'order_amount'     => $order['order_amount'] - $goodsPriceTotal_,
                'order_amount_all' => $order['order_amount'] - $goodsPriceTotal_,
                'order_amount_zy'  => $order['order_amount_zy'] - $goodsPriceTotal_,
            ];
            if ($isAllRefund) {
                $orderInfoUpdata['order_status'] = \Enum\EnumKeys::OS_RETURNED;
                $orderInfoUpdata['pay_status'] = \Enum\EnumKeys::PS_UNPAYED;
                $orderInfoUpdata['shipping_status'] = \Enum\EnumKeys::SS_UNSHIPPED;
                $orderInfoUpdata['money_paid'] = 0;
            }
            if ($order['surplus'] > 0) {//余额或充值
                $surplus = floatval($order['surplus']);
                $diff = bcsub($goodsPriceTotal, $surplus);
                if ($diff > 0) {
                    $goodsPriceTotal = $goodsPriceTotal - $surplus;
                } else {
                    $surplus = $goodsPriceTotal;
                    $goodsPriceTotal = 0;
                }
                $orderInfoUpdata['surplus'] = $order['surplus'] - $surplus;
                //账户处理
                self::accountChange($order['user_id'], $surplus, 0, 0, 0, sprintf('由于取消、无效或退货操作，退回支付订单 %s 时使用的预付款[退款]', $order['order_sn']));
                if ($order['pay_id'] == 5) {//微信支付 退款记录
                    $totalFeeArr = \DB::table('wx_pay_refund')->where(['out_trade_no' => $order['order_sn'], 'status' => 1])->value('total_fee');
                    if (!empty($totalFeeArr)) {//可能多次退款
                        $totalFee = $totalFeeArr[0];
                        $n = count($totalFeeArr) + 1;
                    } else {
                        $totalFee = $order['surplus'] * 100;
                        $n = 1;
                    }
                    \DB::table('wx_pay_refund')->insertGetId([
                        'out_trade_no'  => $order['order_sn'],
                        'out_refund_no' => md5($order['order_sn'] . '-' . $n),
                        'total_fee'     => $totalFee,//转为分
                        'refund_fee'    => $surplus * 100,//分
                        'refund_desc'   => '商户同意退款',
                    ]);
                }
            }
            if ($goodsPriceTotal > 0 && $order['bonus'] > 0) {//使用了券
                $orderInfoUpdata['bonus'] = $order['bonus'] - $goodsPriceTotal;
                $orderSn = $order['order_sn'];
                $orderBonusUsed = \App\Models\OrderBonusUser::where(['order_sn' => $orderSn, 'status' => 1])->orderBy('id', 'asc')->get()->toArray();
                foreach ($orderBonusUsed as $item) {
                    if ($goodsPriceTotal <= 0) {
                        break;
                    }
                    $boData = \App\Models\UserBonus::where('bonus_id', $item['bonus_id'])->first(['used_money', 'balance', 'bonus_status', 'bonus_sn']);
                    if (!empty($boData->bonus_sn)) {
                        $boData = $boData->toArray();
                        $usedMoney = floatval($item['used_money']);
                        $diff = bcsub($goodsPriceTotal, $usedMoney);
                        if ($diff > 0) {//可能使用了多个券
                            $goodsPriceTotal = floatval($goodsPriceTotal - $usedMoney);
                        } else {
                            $usedMoney = $goodsPriceTotal;
                            $goodsPriceTotal = 0;
                        }
                        $uData = array(
                            'used_money' => $boData['used_money'] - $usedMoney,
                            'balance'    => $boData['balance'] + $usedMoney,
                        );
                        if ($boData['bonus_status'] == 2) {
                            $uData['bonus_status'] = 1;
                        }
                        \App\Models\UserBonus::where('bonus_id', $item['bonus_id'])->update($uData);
                        self::accountChangeTwo($order['user_id'], $usedMoney, $order['order_sn'], 0, '订单部分退款至幸福券(' . $boData['bonus_sn'] . ')', $item['bonus_id']);
                        if ($usedMoney < $item['used_money']) {//退了部分
                            \App\Models\OrderBonusUser::where('id', $item['id'])->update([
                                'used_money'  => $item['used_money'] - $usedMoney,
                                'change_time' => date('Y-m-d H:i:s')
                            ]);
                        } else {//全退
                            \App\Models\OrderBonusUser::where('id', $item['id'])->update([
                                'status'      => 2,
                                'change_time' => date('Y-m-d H:i:s')
                            ]);
                        }
                    }
                }
            }
            if ($goodsPriceTotal > 0) {
                throw new Exception('数据有误，请联系客服' . $goodsPriceTotal);
            }
            \App\Models\OrderInfo::where(['order_id' => $applyData->order_id])->update($orderInfoUpdata);
            \App\Models\RefundApply::where(['apply_status' => 0, 'apply_id' => $applyId])->update(['apply_status' => 1, 'dispose_user' => '商户：' . $userInfo['gs_login_name'], 'dispose_time' => time()]);
            /* 记录log */
            $this->orderAction($order['order_sn'], \Enum\EnumKeys::OS_RETURNED, \Enum\EnumKeys::SS_UNSHIPPED, \Enum\EnumKeys::PS_UNPAYED, '商户退款' . ($isAllRefund == false ? '[部分]' : ''), $userInfo['gs_login_name'], 0, implode(',', $recIdsArr), $goodsPriceTotal_);
            //添加商户操作日志
            \Helper\CFunctionHelper::setStoreLogs($gsId, \Enum\EnumLang::BUSINESS_SURE_STATUS, 'ecs_order_info', '2', ($isAllRefund ? '退货' : '部分退货') . ",订单号(" . $applyData->order_sn . ")");
            \DB::commit();
            $userName = \App\Models\Users::where('user_id', $order['user_id'])->value('user_name');
            $str = $isAllRefund ? "未完成交易" : "部分商品退款";
            \Helper\CFunctionHelper::send([$userName], "【幸福加焙】您有一笔订单({$order['order_sn']}){$str}，消费金额已退回，请登录查询账户余额。(2031)", $gsId, '商户退货操作');
            return true;
        } catch (\Exception $e) {
            \DB::rollBack();
            return $this->setErrorAndReturn([
                'return'   => false,
                'code'     => \Enum\EnumMain::HTTP_CODE_FAIL,
                'errorMsg' => "退货失败" . $e->getMessage(),
                'userMsg'  => "退货失败:" . $e->getMessage(),
                'line'     => __LINE__,
            ]);
        }
    }

    /**
     * 是否促销产品
     * @param int $goodsId
     * @return bool
     */
    public function isGoodsPromote($goodsId = 0)
    {
        $data = \App\Models\Goods::where('goods_id', $goodsId)->first(['is_promote', 'promote_start_date', 'promote_end_date']);
        $res = false;
        if (!empty($data)) {
            $data = $data->toArray();
            $time = time();
            if ($data['is_promote'] == 1 && $time >= $data['promote_start_date'] && $time <= $data['promote_end_date']) {
                $res = true;
            }
        }
        return $res;
    }

    /**
     * 修改商户定义的库存
     * @param int $sellerId
     * @param int $goodsId
     * @param int $num
     * @param bool $isPromote
     * @param string $sizeId
     * @param string $tastesId
     * @return bool
     */
    public function updateGoodsStock($sellerId = 0, $goodsId = 0, $num = 1, $isPromote = false, $sizeId = '', $tastesId = '')
    {
        $gsAuth = $this->getGsAuth($sellerId);
        if ($gsAuth) {
            $attrIds = '0';
            if (!empty($sizeId) && !empty($tastesId)) {
                $attrIds = $sizeId . '-' . $tastesId;
            } elseif (!empty($sizeId)) {
                $attrIds = $sizeId;
            } elseif (!empty($tastesId)) {
                $attrIds = $tastesId;
            }
            $zd = $isPromote ? 'num_promotion' : 'num';
            $data = \App\Models\GoodsStock::where(['gs_id' => $sellerId, 'goods_id' => $goodsId, 'attr_ids' => $attrIds])->first(['st_id', $zd]);
            if (!empty($data)) {
                $data = $data->toArray();
                $stockNum = $data[$zd];
                if ($num > 0) {
                    $uData = [$zd => $stockNum + $num];
                } else {
                    $val = $stockNum - abs($num);
                    $val < 0 && $val = 0;
                    $uData = [$zd => $val];
                }
                \App\Models\GoodsStock::where('st_id', $data['st_id'])->update($uData);
            }
        }
        return true;
    }

    /**
     * 获取商户权限
     * @param int $gsId
     * @return bool
     */
    public function getGsAuth($gsId = 0)
    {
        if (!empty($gsId)) {
            $gsAuth = \DB::table('ecs_goods_stores')->where('gs_id', $gsId)->value('gs_auth');
            $data = ($gsAuth < 1) ? ['res' => false] : ['res' => true];
            return $data['res'];
        }
        return false;
    }

    /**
     * 商户同意退货
     * @author: colin
     * @date: 2018/11/22 10:21
     * @return bool|type
     * @throws Exception
     */
    public function orderReturn_bak()
    {
        $userInfo = $this->request->input('userInfo');
        $gsIdArr = [$userInfo['gs_id']];
        $param = $this->request->all();
        $nowtime = time();
        \DB::beginTransaction();
        try {
            $order = self::OrderInfo($param['orderSn']);
            if ($order === false) {
                throw new Exception('没有对应的订单！');
            }
            /* 管理员可以查看旗下加盟店铺的订单 */
            if ($userInfo['is_manage'] == 1) {
                $gsIdChid = \App\Models\StoresUser::where(['is_manage' => 0, 'pid' => $this->request->input('gsId')])->pluck('gs_id')->toArray();
                $gsIdArr = array_merge($gsIdArr, $gsIdChid);
            }
            if ($userInfo['gs_id'] > 0 && !in_array($order['order_pick_stores'], $gsIdArr)) {
                throw new Exception('你没有权限操作他人订单');
            }
            /* 检查订单 */
            if (!($order['shipping_status'] == \Enum\EnumKeys::SS_PREPARING && $order['order_status'] == \Enum\EnumKeys::OS_SPLITED))
                throw new Exception('此订单非申请退货订单！');
            /* 标记订单为“退货”、“未付款”、“未发货” */
            $arr = [
                'order_status'    => \Enum\EnumKeys::OS_RETURNED,
                'pay_status'      => \Enum\EnumKeys::PS_UNPAYED,
                'shipping_status' => \Enum\EnumKeys::SS_UNSHIPPED,
                'money_paid'      => 0,
                'invoice_no'      => '',
            ];
            \App\Models\OrderInfo::where('order_sn', '=', $param['orderSn'])->update($arr);
            /* 处理退款*/
            if ($order['pay_status'] != \Enum\EnumKeys::PS_UNPAYED) {
                $refundRes = self::orderRefund($order, 1, $param['note']);
                if ($refundRes['status'] != 0) {
                    throw new Exception($refundRes['msg']);
                }
            }
            /* 记录log */
            $this->orderAction($order['order_sn'], \Enum\EnumKeys::OS_RETURNED, \Enum\EnumKeys::SS_UNSHIPPED, \Enum\EnumKeys::PS_UNPAYED, $param['note'], $userInfo['gs_login_name']);
            $goods = \App\Models\OrderGoods::select('goods_number', 'send_number')->where('order_id', $order['order_id'])->first()->toArray();
            if ($goods['goods_number'] == $goods['send_number']) {
                /* 计算并退回积分 */
                $integral = self::integralToGive($order);
                if ($integral) {
                    self::accountChange($order['user_id'], 0, 0, (-1) * intval($integral['rank_points']), (-1) * intval($integral['custom_points']), sprintf('由于退货或未发货操作，退回订单 %s 赠送的积分', $order['order_sn']));
                }
            }
            $_CFG = \Enum\EnumLang::loadConfig();
            /* 如果使用库存，则增加库存（不论何时减库存都需要） */
            if ($_CFG['use_storage'] == '1') {
                if ($_CFG['stock_dec_time'] == \Enum\EnumKeys::SDT_SHIP) {
                    self::changeOrderGoodsStorage($order['order_id'], false, \Enum\EnumKeys::SDT_SHIP);
                } elseif ($_CFG['stock_dec_time'] == \Enum\EnumKeys::SDT_PLACE) {
                    self::changeOrderGoodsStorage($order['order_id'], false, \Enum\EnumKeys::SDT_PLACE);
                }
            }
            $this->returnUserSurplusIntegralBonus($order, 4588, $this->request->input('gsId'));
            /* 获取当前操作员 */
            $delivery['action_user'] = $userInfo['gs_login_name'];
            /* 添加退货记录 */
            $deliveryList = \App\Models\DeliveryOrder::whereIn('status', [0, 2])->where('order_id', $order['order_id'])->get();
            if (!$deliveryList->isEmpty()) {
                foreach ($deliveryList as $list) {
                    $backOrderInfo = [
                        'delivery_sn'   => $list->delivery_sn,
                        'order_sn'      => $list->order_sn,
                        'order_id'      => $list->order_id,
                        'add_time'      => $list->add_time,
                        'shipping_id'   => $list->shipping_id,
                        'user_id'       => $list->user_id,
                        'action_user'   => $delivery['action_user'],
                        'consignee'     => $list->consignee,
                        'address'       => $list->address,
                        'Country'       => $list->Country,
                        'province'      => $list->province,
                        'City'          => $list->City,
                        'district'      => $list->district,
                        'sign_building' => $list->sign_building,
                        'Email'         => $list->Email,
                        'Zipcode'       => $list->Zipcode,
                        'Tel'           => $list->Tel,
                        'Mobile'        => $list->Mobile,
                        'best_time'     => $list->best_time,
                        'postscript'    => $list->postscript,
                        'how_oos'       => $list->how_oos,
                        'insure_fee'    => $list->insure_fee,
                        'shipping_fee'  => $list->shipping_fee,
                        'update_time'   => $list->update_time,
                        'suppliers_id'  => $list->suppliers_id,
                        'return_time'   => $nowtime,
                        'agency_id'     => $list->agency_id,
                        'invoice_no'    => $list->invoice_no,
                    ];
                    $createOrd = \App\Models\BackOrder::create($backOrderInfo);
                    /*** ecs_delivery_goods 这个表目前没有发现实际作用，先去掉 **/
//                    $deliveryGood = \App\Models\DeliveryGoods::select("goods_id", "product_id", "product_sn", "goods_name", "goods_sn", "is_real", "send_number", "goods_attr")
//                        ->where('delivery_id', $list->delivery_id)->first();
//                    if (!$deliveryGood->isEmpty()) {
//                        $backGoodInfo = [
//                            'back_id'     => $createOrd->back_id,
//                            'goods_id'    => $deliveryGood->goods_id,
//                            'product_id'  => $deliveryGood->product_id,
//                            'product_sn'  => $deliveryGood->product_sn,
//                            'goods_name'  => $deliveryGood->goods_name,
//                            'goods_sn'    => $deliveryGood->goods_sn,
//                            'is_real'     => $deliveryGood->is_real,
//                            'send_number' => $deliveryGood->send_number,
//                            'goods_attr'  => $deliveryGood->goods_attr,
//                        ];
//                        \App\Models\BackGood::create($backGoodInfo);
//                    }
                }
            }
            /* 修改订单的发货单状态为退货 */
            \App\Models\DeliveryOrder::whereIn('status', [0, 2])->where('order_id', $order['order_id'])->update(['status' => 1]);
            /* 将订单的商品发货数量更新为 0 */
            \App\Models\OrderGoods::where('order_id', $order['order_id'])->update(['send_number' => 0]);
            /*修改退货申请状态为已处理*/
            $adminName = str_replace("'", "''", '商户：' . $userInfo['gs_login_name']);
            \App\Models\RefundApply::where('order_id', $order['order_id'])->update(['apply_status' => 1, 'dispose_user' => $adminName, 'dispose_time' => time()]);
            if ($order['pay_id'] == 5) {//微信支付 退款记录
                $refId = \DB::table('wx_pay_refund')->insertGetId([
                    'out_trade_no'  => $order['order_sn'],
                    'out_refund_no' => md5($order['order_sn'] . '-1'),
                    'total_fee'     => $order['surplus'] * 100,//转为分
                    'refund_fee'    => $order['surplus'] * 100,//分
                    'refund_desc'   => '商户同意退款',
                ]);
            }
        } catch (\Exception $e) {
            \DB::rollBack();
            return $this->setErrorAndReturn([
                'return'   => false,
                'code'     => \Enum\EnumMain::HTTP_CODE_FAIL,
                'errorMsg' => "退货失败" . $e->getMessage(),
                'userMsg'  => "退货失败:" . $e->getMessage(),
                'line'     => __LINE__,
            ]);
        }
        //添加商户操作日志
        \Helper\CFunctionHelper::setStoreLogs($this->request->input('gsId'), \Enum\EnumLang::BUSINESS_SURE_STATUS, 'ecs_order_info', '2', "退货,订单号({$param['orderSn']})");
        \DB::commit();
        return true;
    }

    /**
     * 商户撤销退货申请
     * @return bool
     */
    public function denyRefund()
    {
        $userInfo = $this->request->input('userInfo');
        $gsId = $this->request->input('gsId');
        $param = $this->request->all();
        \DB::beginTransaction();
        try {
            $applyId = intval($param['apply_id']);
            $gsIdArr[] = $gsId;
            if ($userInfo['is_manage'] == 1) {
                $gsIdChid = \App\Models\StoresUser::where(['is_manage' => 0, 'pid' => $gsId])->pluck('gs_id')->toArray();
                $gsIdArr = array_merge($gsIdArr, $gsIdChid);
            }
            $applyData = \App\Models\RefundApply::selectRaw('ecs_refund_apply.order_id,ecs_refund_apply.rec_ids,ecs_refund_apply.apply_status,i.user_id,i.order_sn')
                ->leftJoin('ecs_order_info as i', 'ecs_refund_apply.order_id', '=', 'i.order_id')->whereIn('i.order_pick_stores', $gsIdArr)->where('ecs_refund_apply.apply_id', $applyId)->first();
            if (empty($applyData)) {
                throw new Exception('数据不存在，请确认！');
            }
            if ($applyData->apply_status > 0) {
                throw new Exception('本次退货申请处理过了，请确认后操作！');
            }
            if (!empty($applyData->rec_ids)) {
                \App\Models\OrderGoods::whereIn('rec_id', explode(',', $applyData->rec_ids))->update(['status' => 0, 'change_time' => date('Y-m-d H:i:s')]);
            }
            \App\Models\OrderInfo::where(['order_status' => 5, 'pay_status' => 2, 'shipping_status' => 3, 'order_id' => $applyData->order_id])->update(['shipping_status' => 1]);
            \App\Models\RefundApply::where(['apply_status' => 0, 'apply_id' => $applyId])->update(['apply_status' => 2, 'dispose_user' => '商户：' . $userInfo['gs_login_name'], 'dispose_time' => time()]);
            //添加商户操作日志
            \Helper\CFunctionHelper::setStoreLogs($gsId, \Enum\EnumLang::BUSINESS_DENY_RETURN, 'ecs_order_info,ecs_refund_apply', '2', '撤销退货申请');
            \DB::commit();
            return true;
        } catch (\Exception $e) {
            \DB::rollBack();
            return $this->setErrorAndReturn([
                'return'   => false,
                'code'     => \Enum\EnumMain::HTTP_CODE_FAIL,
                'errorMsg' => "撤销退货失败" . $e->getMessage(),
                'userMsg'  => "撤销退货失败:" . $e->getMessage(),
                'line'     => __LINE__,
            ]);
        }
    }

    /***
     * 商户撤销退货申请
     * @author: colin
     * @date: 2018/11/23 12:02
     * @return type
     */
    public function denyRefund_bak()
    {
        $userInfo = $this->request->input('userInfo');
        $gsIdArr = [$this->request->input('gsId')];
        $param = $this->request->all();
        try {
            $order = self::OrderInfo($param['orderSn']);
            if ($order === false) {
                throw new Exception('没有对应的订单！');
            }
            /* 管理员可以查看旗下加盟店铺的订单 */
            if ($userInfo['is_manage'] == 1) {
                $gsIdChid = \App\Models\StoresUser::where(['is_manage' => 0, 'pid' => $this->request->input('gsId')])->pluck('gs_id')->toArray();
                $gsIdArr = array_merge($gsIdArr, $gsIdChid);
            }
            if ($userInfo['gs_id'] > 0 && !in_array($order['order_pick_stores'], $gsIdArr)) {
                throw new Exception('你没有权限操作他人订单');
            }
            /* 检查订单 */
            if (!($order['shipping_status'] == \Enum\EnumKeys::SS_PREPARING && $order['order_status'] == \Enum\EnumKeys::OS_SPLITED)) {
                throw new Exception('此订单非申请退货订单！');
            }
            $aplayId = \App\Models\RefundApply::where(['order_id' => $order['order_id'], 'apply_status' => 0])->value('apply_id');
            if (empty($aplayId)) {
                throw new Exception('此订单非正在申请退货的订单！');
            }
            \App\Models\OrderInfo::where(['order_status' => 5, 'pay_status' => 2, 'shipping_status' => 3, 'order_id' => $order['order_id']])->update(['shipping_status' => 1]);
            \App\Models\RefundApply::where(['apply_status' => 0, 'apply_id' => $aplayId])->update(['apply_status' => 2, 'dispose_user' => '商户：' . $userInfo['gs_login_name'], 'dispose_time' => time()]);
            //添加商户操作日志
            \Helper\CFunctionHelper::setStoreLogs($this->request->input('gsId'), \Enum\EnumLang::BUSINESS_DENY_RETURN, 'ecs_order_info,ecs_refund_apply', '2', '撤销退货申请');
        } catch (\Exception $e) {
            return $this->setErrorAndReturn([
                'return'   => false,
                'code'     => \Enum\EnumMain::HTTP_CODE_FAIL,
                'errorMsg' => "撤销退货失败" . $e->getMessage(),
                'userMsg'  => "撤销退货失败:" . $e->getMessage(),
                'line'     => __LINE__,
            ]);
        }
    }

    /**
     * 接单操作
     * @return bool
     */
    public function orderTake()
    {
        $userInfo = $this->request->input('userInfo');
        $gsIdArr = [$this->request->input('gsId')];
        $param = $this->request->all();
        try {
            $order = self::OrderInfo($param['orderSn']);
            if ($order === false) {
                throw new Exception('没有对应的订单！');
            }
            /* 管理员可以查看旗下加盟店铺的订单 */
            if ($userInfo['is_manage'] == 1) {
                $gsIdChid = \App\Models\StoresUser::where(['is_manage' => 0, 'pid' => $this->request->input('gsId')])->pluck('gs_id')->toArray();
                $gsIdArr = array_merge($gsIdArr, $gsIdChid);
            }
            if ($userInfo['gs_id'] > 0 && !in_array($order['order_pick_stores'], $gsIdArr)) {
                throw new Exception('你没有权限操作他人订单');
            }
            $result = \App\Models\OrderInfo::where(['order_id' => $order['order_id'], 'order_status' => 5, 'pay_status' => 2])->update(['order_taking' => 2, 'taking_time' => date('Y-m-d H:i:s')]);
            if (!$result) {
                throw new Exception('订单状态已改变！');
            }
            //日志
            \Helper\CFunctionHelper::setStoreLogs($this->request->input('gsId'), 6, 'ecs_order_info', '2', "接单,订单号({$param['orderSn']})");
            return true;
        } catch (\Exception $e) {
            return $this->setErrorAndReturn([
                'return'   => false,
                'code'     => \Enum\EnumMain::HTTP_CODE_FAIL,
                'errorMsg' => "接单失败" . $e->getMessage(),
                'userMsg'  => "接单失败:" . $e->getMessage(),
                'line'     => __LINE__,
            ]);
        }
    }

    /**
     * 发货
     * @return bool
     */
    public function delivery()
    {
        $userInfo = $this->request->input('userInfo');
        $gsIdArr = [$this->request->input('gsId')];
        $param = $this->request->all();
        try {
            $order = self::OrderInfo($param['orderSn']);
            if ($order === false) {
                throw new Exception('没有对应的订单！');
            }
            /* 管理员可以查看旗下加盟店铺的订单 */
            if ($userInfo['is_manage'] == 1) {
                $gsIdChid = \App\Models\StoresUser::where(['is_manage' => 0, 'pid' => $this->request->input('gsId')])->pluck('gs_id')->toArray();
                $gsIdArr = array_merge($gsIdArr, $gsIdChid);
            }
            if ($userInfo['gs_id'] > 0 && !in_array($order['order_pick_stores'], $gsIdArr)) {
                throw new Exception('你没有权限操作他人订单');
            }
            \App\Models\OrderInfo::where(['order_id' => $order['order_id']])->update(['shipping_status' => 1, 'shipping_time' => time()]);
            //日志
            \Helper\CFunctionHelper::setStoreLogs($this->request->input('gsId'), 7, 'ecs_order_info', '2', "发货,订单号({$param['orderSn']})");
            return true;
        } catch (\Exception $e) {
            return $this->setErrorAndReturn([
                'return'   => false,
                'code'     => \Enum\EnumMain::HTTP_CODE_FAIL,
                'errorMsg' => "发货失败" . $e->getMessage(),
                'userMsg'  => "发货失败:" . $e->getMessage(),
                'line'     => __LINE__,
            ]);
        }
    }

    /**
     * 改变订单中商品库存
     * @param   int $orderId 订单号
     * @param   bool $isDec 是否减少库存
     * @param   bool $storage 减库存的时机，1，下订单时；0，发货时；
     */
    private function changeOrderGoodsStorage($orderId, $isDec = true, $storage = 0)
    {
        /* 查询订单商品信息 */
        switch ($storage) {
            case 0 :
                $res = \App\Models\OrderGoods::selectRaw("goods_id, SUM(send_number) AS num, MAX(extension_code) AS extension_code, product_id")
                    ->where(['order_id' => $orderId, 'is_real' => 1])
                    ->groupBy(['goods_id', 'product_id'])->get()->toArray();
                break;

            case 1 :
                $res = \App\Models\OrderGoods::selectRaw("goods_id, SUM(goods_number) AS num, MAX(extension_code) AS extension_code, product_id")
                    ->where(['order_id' => $orderId, 'is_real' => 1])
                    ->groupBy(['goods_id', 'product_id'])->get()->toArray();
                break;
        }
        if (empty($res))
            return false;
        foreach ($res as $row) {
            if ($row['extension_code'] != "package_buy") {
                $row['num'] = $isDec ? -$row['num'] : $row['num'];
                self::changeGoodsStorage($row['goods_id'], $row['product_id'], $row['num']);

            } else {
                $res_goods = \App\Models\PackageGoods::select("goods_id", "goods_number")->where('package_id', $row['goods_id'])->get()->toArray();
                foreach ($res_goods as $row_goods) {
                    $is_goods = \App\Models\Goods::where('package_id', $row['goods_id'])->value('is_real');
                    if ($isDec) {
                        self::changeGoodsStorage($row_goods['goods_id'], $row['product_id'], -($row['num'] * $row_goods['goods_number']));
                    } elseif ($is_goods) {
                        self::changeGoodsStorage($row_goods['goods_id'], $row['product_id'], ($row['num'] * $row_goods['goods_number']));
                    }
                }
            }
        }
    }

    /**
     * 商品库存增与减 货品库存增与减
     *
     * @param   int $good_id 商品ID
     * @param   int $product_id 货品ID
     * @param   int $number 增减数量，默认0；
     *
     * @return  bool               true，成功；false，失败；
     */
    private static function changeGoodsStorage($goodId, $productId, $number = 0)
    {
        if ($number == 0) {
            return true; // 值为0即不做、增减操作，返回true
        }
        if (empty($goodId) || empty($number)) {
            return false;
        }

        $number = ($number > 0) ? '+ ' . $number : $number;

        /* 处理货品库存 */
        $products_query = true;
        $query = '';
        if (!empty($productId)) {
            $dataInfo = \App\Models\Products::where(['goods_id' => $goodId, 'product_id' => $productId])->first();
            if (!$dataInfo->isEmpty()) {
                $dataInfo->product_number = $dataInfo->product_number + $number;
                $products_query = $dataInfo->sava();
            }
        }
        $goodInfo = \App\Models\Goods::where('goods_id', $goodId)->first();
        if (!$goodInfo->isEmpty()) {
            $goodInfo->goods_number = $goodInfo->goods_number + $number;
            $query = $goodInfo->sava();
        }
        if ($query && $products_query) {
            return true;
        } else {
            return false;
        }
    }

    /***
     * 订单信息
     * @author: colin
     * @date: 2018/11/22 16:34
     * @param $orderSn
     * @return bool
     */
    private static function OrderInfo($orderSn)
    {
        if (empty($orderSn))
            return false;
        $order = \App\Models\OrderInfo::selectRaw("*,(goods_amount - discount + tax + shipping_fee + insure_fee + pay_fee + pack_fee + card_fee) AS total_fee")
            ->where('order_sn', $orderSn)
            ->get()->toArray();
        if (!isset($order['0'])) {
            return false;
        }
        return $order[0];

    }

    /***
     * 订单退款
     * @author: colin
     * @date: 2018/11/22 11:04
     * @param $order              array      订单
     * @param $refund_type        int        退款方式 1 到帐户余额 2 到退款申请（先到余额，再申请提款） 3 不处理
     * @param $refund_note       string      退款说明
     * @param int $refund_amount float       退款金额（如果为0，取订单已付款金额）
     * @return  array
     */
    private static function orderRefund($order, $refundType, $note, $refundAmount = 0)
    {
        $result = ['status' => 1, 'msg' => ''];
        /* 检查参数 */
        $userId = $order['user_id'];
        if ($userId == 0) {
            $result['msg'] = '订单没有用户id，无法返回到余额！';
            return $result;
        }
        $amount = $refundAmount > 0 ? $refundAmount : $order['money_paid'];
        if ($amount <= 0) {
            $result['status'] = 0;
            return $result;
        }
        if (!in_array($refundType, [1])) {
            $result['msg'] = '退款方式有误！';
            return $result;
        }
        switch ($refundType) {
            case 1:
                $accountChange = self::accountChange($userId, $amount, 0, 0, 0, $note);
                break;
            default:
                $accountChange = true;
                break;
        }
        if ($accountChange === false) {
            $result['msg'] = '账户余额处理有误！';
            return $result;
        }
        $result['status'] = 0;
        return $result;
    }

    /**
     * 记录帐户变动
     * @param   int $userId 用户id
     * @param   float $userMoney 可用余额变动
     * @param   float $frozenMoney 冻结余额变动
     * @param   int $rankPoints 等级积分变动
     * @param   int $payPoints 消费积分变动
     * @param   string $changeDesc 变动说明
     * @param   int $changeType 变动类型：参见常量文件
     * @return  void
     */
    private static function accountChange($userId, $userMoney = 0, $frozenMoney = 0, $rankPoints = 0, $payPoints = 0, $changeDesc = '', $changeType = 99)
    {
        /* 插入帐户变动记录 */
        $accountLog = [
            'user_id'      => $userId,
            'user_money'   => $userMoney,
            'frozen_money' => $frozenMoney,
            'rank_points'  => $rankPoints,
            'pay_points'   => $payPoints,
            'change_time'  => time(),
            'change_desc'  => $changeDesc,
            'change_type'  => $changeType,
        ];
        \App\Models\AccountLog::create($accountLog);
        $userInfo = \App\Models\Users::where('user_id', $userId)->lockForUpdate()->first();
        if (empty($userInfo)) {
            return false;
        }
        $userInfo->user_money = $userInfo->user_money + $userMoney;
        $userInfo->frozen_money = $userInfo->frozen_money + $frozenMoney;
        $userInfo->rank_points = $userInfo->rank_points + $rankPoints;
        $userInfo->pay_points = $userInfo->pay_points + $payPoints;
        $userInfo->save();
        return true;
    }

    private static function accountChangeTwo($userId, $userMoney = 0, $orderSn = 0, $storesId = 0, $changeDesc = '', $bonusId = 0)
    {
        /* 插入帐户变动记录 */
        $account_log = array(
            'user_id'     => $userId,
            'user_money'  => $userMoney,
            'order_sn'    => $orderSn,
            'stores_id'   => $storesId,
            'bonus_id'    => $bonusId,
            'change_time' => time(),
            'change_desc' => $changeDesc,
        );
        \App\Models\AccountLogBonus::create($account_log);
    }

    /**
     * 取得某订单应该赠送的积分数
     * @param   array $order 订单
     * @return  int     积分数
     */
    private static function integralToGive($order)
    {
        /* 判断是否团购 */
        if ($order['extension_code'] == 'group_buy') {
            $group_buy = self::groupBuyInfo(intval($order['extension_id']));
            return [
                'custom_points' => $group_buy['gift_integral'],
                'rank_points'   => $order['goods_amount'],
            ];
        } else {
            $where = [
                'ecs_order_goods.order_id'  => $order['order_id'],
                'ecs_order_goods.parent_id' => 0,
                'ecs_order_goods.is_gift'   => 0,
            ];
            $buy = \App\Models\OrderGoods::selectRaw("SUM(ecs_order_goods.goods_number * IF(g.give_integral > -1, g.give_integral, ecs_order_goods.goods_price)) AS custom_points, SUM(ecs_order_goods.goods_number * IF(g.rank_integral > -1, g.rank_integral, ecs_order_goods.goods_price)) AS rank_points")
                ->join('ecs_goods as g', 'ecs_order_goods.goods_id', '=', 'g.goods_id')
                ->where($where)
                ->where('ecs_order_goods.goods_id', '>', 0)
                ->where('ecs_order_goods.extension_code', '<>', 'package_buy')
                ->get()->toArray();
            if (isset($buy['0'])) {
                return $buy['0'];
            }
        }
    }

    /**
     * 取得团购活动信息
     * @param   int $group_buy_id 团购活动id
     * @param   int $current_num 本次购买数量（计算当前价时要加上的数量）
     * @return  array
     *                  status          状态：
     */
    private static function groupBuyInfo($groupBuyId, $current_num = 0)
    {
        /* 取得团购活动信息 */
        $groupBuyId = intval($groupBuyId);
        $groupBuy = \App\Models\GoodsActivity::where('act_id', $groupBuyId)->where('act_type', EnumKeys::GAT_GROUP_BUY)->get()->toArray();
        /* 如果为空，返回空数组 */
        if (empty($groupBuy[0])) {
            return [];
        }
        $groupBuy = $groupBuy[0];
        $extInfo = unserialize($groupBuy['ext_info']);
        $groupBuy = array_merge($groupBuy, $extInfo);

        /* 格式化时间 */
        $groupBuy['formated_start_date'] = date('Y-m-d H:i', $groupBuy['start_time']);
        $groupBuy['formated_end_date'] = date('Y-m-d H:i', $groupBuy['end_time']);

        /* 格式化保证金 */
        $groupBuy['formated_deposit'] = \Helper\CFunctionHelper::priceFormat($groupBuy['deposit'], false);

        /* 处理价格阶梯 */
        $priceLadder = $groupBuy['price_ladder'];
        if (!is_array($priceLadder) || empty($priceLadder)) {
            $priceLadder = array(array('amount' => 0, 'price' => 0));
        } else {
            foreach ($priceLadder as $key => $amount_price) {
                $priceLadder[$key]['formated_price'] = \Helper\CFunctionHelper::priceFormat($amount_price['price'], false);
            }
        }
        $groupBuy['price_ladder'] = $priceLadder;
        /* 统计信息 */
        $stat = self::groupBuyStat($groupBuyId, $groupBuy['deposit']);
        $groupBuy = array_merge($groupBuy, $stat);

        /* 计算当前价 */
        $curPrice = $priceLadder[0]['price']; // 初始化
        $curAmount = $stat['valid_goods'] + $current_num; // 当前数量
        foreach ($priceLadder as $amount_price) {
            if ($curAmount >= $amount_price['amount']) {
                $curPrice = $amount_price['price'];
            } else {
                break;
            }
        }
        $groupBuy['cur_price'] = $curPrice;
        $groupBuy['formated_cur_price'] = \Helper\CFunctionHelper::priceFormat($curPrice, false);

        /* 最终价 */
        $groupBuy['trans_price'] = $groupBuy['cur_price'];
        $groupBuy['formated_trans_price'] = $groupBuy['formated_cur_price'];
        $groupBuy['trans_amount'] = $groupBuy['valid_goods'];

        /* 状态 */
        $groupBuy['status'] = \Helper\CFunctionHelper::groupBuyStatus($groupBuy);
        if (isset(\Enum\EnumLang::$LANG['gbs'][$groupBuy['status']])) {
            $groupBuy['status_desc'] = \Enum\EnumLang::$LANG['gbs'][$groupBuy['status']];
        }
        $groupBuy['start_time'] = $groupBuy['formated_start_date'];
        $groupBuy['end_time'] = $groupBuy['formated_end_date'];

        return $groupBuy;
    }

    /*
     * 取得某团购活动统计信息
     * @param   int     $group_buy_id   团购活动id
     * @param   float   $deposit        保证金
     * @return  array   统计信息
     * @param   int   total_order     总订单数
     * @param   int   total_goods     总商品数
     * @param   int   valid_order     有效订单数
     * @param   int     valid_goods     有效商品数
     */
    private static function groupBuyStat($groupBuyId, $deposit)
    {
        $groupBuyId = intval($groupBuyId);
        /* 取得团购活动商品ID */
        $groupBuyGoodsId = \App\Models\GoodsActivity::where(['act_id' => $groupBuyId, 'act_type' => \Enum\EnumKeys::GAT_GROUP_BUY])->value('goods_id');
        $where = [
            'ecs_order_info.extension_code' => 'group_buy',
            'ecs_order_info.extension_id'   => $groupBuyId,
            'g.goods_id'                    => $groupBuyGoodsId,
        ];
        $stat = \App\Models\OrderInfo::selectRaw("COUNT(*) AS total_order, SUM(g.goods_number) AS total_goods")
            ->join('ecs_order_goods as g', 'ecs_order_info.order_id', '=', 'g.order_id')
            ->where($where);
        $orderStatus = \Enum\EnumKeys::OS_CONFIRMED;
        $orderStatusTwo = \Enum\EnumKeys::OS_UNCONFIRMED;
        $stat = $stat->where(function ($query) use ($orderStatus, $orderStatusTwo) {
            $query->where('order_status', $orderStatus)->orWhere('order_status', $orderStatusTwo);
        });
        $row = $stat;
        $stat = $stat->get()->toArray();
        if (empty($stat[0])) {
            return false;
        }
        $stat = $stat[0];
        if ($stat['total_order'] == 0) {
            $stat['total_goods'] = 0;
        }
        /* 取得有效订单数和有效商品数 */
        $deposit = floatval($deposit);
        if ($deposit > 0 && $stat['total_order'] > 0) {
            $row = $row->where(function ($query) use ($deposit) {
                $query->whereRaw('(o.money_paid + o.surplus) >=' . $deposit);
            });
            $row = $row->get()->toArray();
            if (isset($row[0]['total_order'])) {
                $stat['valid_order'] = $row[0]['total_order'];
                $stat['valid_goods'] = $row[0]['total_order'];
            }
            $stat['valid_goods'] = ($stat['valid_order'] == 0) ? 0 : $stat['valid_goods'];
        } else {
            $stat['valid_order'] = $stat['total_order'];
            $stat['valid_goods'] = $stat['total_goods'];
        }
        return $stat;
    }

    /**
     * 退回余额、积分、红包（取消、无效、退货时），把订单使用余额、积分、红包设为0
     * @param   array $order 订单信息
     */
    public function returnUserSurplusIntegralBonus($order, $lineNum = 4588, $gsId)
    {
        /* 处理余额、积分、红包 */
        if ($order['user_id'] > 0 && $order['surplus'] > 0) {
            $surplus = $order['money_paid'] < 0 ? $order['surplus'] + $order['money_paid'] : $order['surplus'];
            self::accountChange($order['user_id'], $surplus, 0, 0, 0, sprintf('由于取消、无效或退货操作，退回支付订单 %s 时使用的预付款', $order['order_sn']));
        }

        if ($order['user_id'] > 0 && $order['integral'] > 0) {
            self::accountChange($order['user_id'], 0, 0, 0, $order['integral'], sprintf('由于取消、无效或退货操作，退回支付订单 %s 时使用的积分', $order['order_sn']));
        }

        if ($order['bonus_id'] > 0 && $order['bonus'] > 0) {
            $boData = \App\Models\UserBonus::select('used_money', 'balance', 'bonus_status', 'bonus_sn')->where('bonus_id', $order['bonus_id'])->first();
            if (!$boData->isEmpty()) {
                $uData = array(
                    'used_money' => $boData->used_money - $order['bonus'],
                    'balance'    => $boData->balance + $order['bonus'],
                );
                if ($boData->bonus_status == 2) {
                    $uData['bonus_status'] = 1;
                }
                \App\Models\UserBonus::where('bonus_id', $order['bonus_id'])->update();
                self::accountChangeTwo($order['user_id'], $order['bonus'], $order['order_sn'], 0, '订单退款至幸福券(' . $boData['bonus_sn'] . ')', $order['bonus_id']);
            }
        } elseif ($order['bonus_id'] == 0 && $order['bonus'] > 0) {
            $orderBonusUsed = \App\Models\OrderBonusUser::where(['order_sn' => $order['order_sn'], 'status' => 1])->get()->toArray();
            $bonusUsedId = array();
            foreach ($orderBonusUsed as $item) {
                $boData = \App\Models\UserBonus::select('used_money', 'balance', 'bonus_status', 'bonus_sn')->where('bonus_id', $item['bonus_id'])->first();
                if (!empty($boData)) {
                    $uData = array(
                        'used_money' => $boData->used_money - $item['used_money'],
                        'balance'    => $boData->balance + $item['used_money'],
                    );
                    if ($boData->bonus_status == 2) {
                        $uData['bonus_status'] = 1;
                    }
                    \App\Models\UserBonus::where('bonus_id', $item['bonus_id'])->update($uData);
                    self::accountChangeTwo($order['user_id'], $item['used_money'], $order['order_sn'], 0, '订单退款至幸福券(' . $boData['bonus_sn'] . ')', $item['bonus_id']);
                    $bonusUsedId[] = $item['id'];
                }
            }
            if (!empty($bonusUsedId)) {
                \App\Models\OrderBonusUser::whereIn('id', $bonusUsedId)->update(['status' => 2, 'change_time' => date('Y-m-d H:i:s')]);
            }
        }

        /* 修改订单 */
        $arr = [
            'bonus_id'       => 0,
            'bonus'          => 0,
            'integral'       => 0,
            'integral_money' => 0,
            'order_amount'   => 0,
            'surplus'        => 0,
        ];
        \App\Models\OrderInfo::where('order_id', $order['order_id'])->update($arr);
        $userName = \App\Models\Users::where('user_id', $order['user_id'])->value('user_name');
        \Helper\CFunctionHelper::send([$userName], "【幸福加焙】您有一笔订单({$order['order_sn']})未完成交易，消费金额已退回，请登录查询账户余额。({$lineNum})", $gsId, '商户退货操作');
    }

    /**
     * 根据订单号码查找联系人或注册号码
     * @param string $orderSn
     * @param int $type
     * @return string
     */
    public function getPrivateNum($orderSn = '', $type = 1)
    {
        $orderData = \App\Models\OrderInfo::select('user_id', 'order_tel')->where('order_sn', $orderSn)->first();
        if ($type == 2) {
            $origNum = \App\Models\Users::where('user_id', $orderData->user_id)->value('user_name');
        } else {
            $origNum = $orderData->order_tel;
        }
        $privateNum = '';
        if (!empty($origNum)) {
            $privateNum = DB::table('ecs_axprivate')->where('orig_num', $origNum)->value('private_num');
            if (empty($privateNum)) {
                $privateNum = DB::table('ecs_axprivate')->where('bind_time', 0)->value('private_num');
                if (empty($privateNum)) {
                    // throw new \Exception('暂无隐私号码，请联系客服。');
                    return [$origNum, 0];//没有可用隐私号码时，直接拨打原号码
                }
                $obj = new \Library\axPrivateNumber();
                $res = $obj->bind($origNum, $privateNum);
                if ($res['status'] == 1) {
                    $uData = [
                        'orig_num'        => $origNum,
                        'subscription_id' => $res['data']['subscriptionId'],
                        'bind_time'       => time()
                    ];
                    \DB::table('ecs_axprivate')->where('private_num', $privateNum)->update($uData);
                    \DB::table('ecs_axprivate_log')->insert([
                        'private_num'     => $privateNum,
                        'orig_num'        => $origNum,
                        'subscription_id' => $uData['subscription_id'],
                    ]);
                } else {
                    throw new \Exception('请求失败，请重试。');
                }
            }
        }
        return [$privateNum, 1];
    }

    /****
     * 获取分店信息
     * @author: colin
     * @date: 2018/11/12 15:50
     * @param $gs_id
     * @return array|type
     */
    public function branchStore()
    {
        $userInfo = $this->request->input('userInfo');
        $gsId = $this->request->input('gsId');
        try {
            if ($userInfo['is_manage'] != 1) {
                return [];
            }
            //throw new \Exception('该店铺不是总店！');
            $key = 'branchStore_' . $gsId;
            $branchStore = Cache::get($key);
            if ($branchStore)
                return $branchStore;
            $branchStore = \App\Models\StoresUser::selectRaw("gs_id,gs_name")->where(['is_manage' => 0, 'pid' => $gsId])->orderBy('gs_id', 'asc')->get()->toArray();
            Cache::put($key, $branchStore, 60);
            return $branchStore;
        } catch (\Exception $e) {
            return $this->setErrorAndReturn([
                'return'   => false,
                'code'     => \Enum\EnumMain::HTTP_CODE_FAIL,
                'errorMsg' => "branchStore 获取分店失败" . $e->getMessage(),
                'userMsg'  => $e->getMessage(),
                'line'     => __LINE__,
            ]);
        }

    }

}