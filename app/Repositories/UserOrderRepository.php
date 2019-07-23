<?php

namespace App\Repositories;

use App\Models\OrderGoods;
use App\Models\OrderInfo;
use App\Models\StoresUser;
use App\Models\Shipping;
use App\Models\BonusType;
use App\Models\Users;
use App\Models\OrderExpress;
use App\Models\GoodsAttr;
use \Enum\EnumKeys;
use \Helper\CFunctionHelper as help;

class UserOrderRepository
{
	/**
	 * 创建订单
	 * @param array $params
	 * @return mixed
	 */
	public function create(array $params)
	{
		try {
			$create = OrderInfo::create($params);
		} catch (\Exception $e) {
			//echo $e->getMessage();die;
			return false;
		}
		return $create;

	}

	/**
	 * 修改订单
	 * @author: colin
	 * @date: 2019/1/9 16:23
	 * @param $params
	 * @param $where
	 * @return mixed
	 */
	public function update($params, $where)
	{
		return OrderInfo::where($where)->update($params);
	}

	/**
	 * 删除选中订单
	 * @author: colin
	 * @date: 2019/1/9 16:37
	 * @param $where
	 * @return mixed
	 */
	public function delete($where)
	{
		return OrderInfo::where($where)->delete();
	}

	/**
	 * 根据id获取订单资料
	 * @param $id
	 * @return mixed
	 */
	public function ById($id)
	{
		return OrderInfo::find($id);
	}

	/**
	 * 统计订单信息
	 * @author: colin
	 * @date: 2019/1/10 13:44
	 */
	public function statis($where)
	{
		return OrderInfo::where($where)->count();
	}

	/**
	 * 根据手机号码获取订单资料
	 * @param $orderSn
	 * @return mixed
	 */
	public function ByMobileId($mobile)
	{
		try {
			$orderInfo = OrderInfo::where('user_mobile', $mobile)->orderBy('order_id', 'desc')->firstOrFail();
		} catch (\Exception $e) {
			return false;
		}
		return $orderInfo;

	}

	/**
	 * 获取订单列表
	 * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
	 */
	public function getList($param, $where)
	{
		try {
			$param['page'] = ($param['page'] - 1) * $param['pageSize'];
			$dataList = OrderInfo::from('ecs_order_info as oi')->join('ecs_goods_stores as gs','gs.gs_id','=','oi.order_pick_stores')->join('ecs_payment as pm','oi.pay_id','=','pm.pay_id')
				->selectRaw('oi.address,oi.add_time as order_time,oi.order_taking,oi.order_amount as total_price,oi.is_shipping,oi.shipping_fee,oi.order_note,oi.order_id,oi.order_sn,oi.order_status,oi.shipping_status,oi.pay_status,oi.return_reason,oi.order_pick_stores,FROM_UNIXTIME(oi.add_time,"%Y.%m.%d %H:%i") add_time, oi.order_lxr, oi.order_tel,oi.last_cfm_time,(oi.order_amount + oi.shipping_fee + oi.insure_fee + oi.pay_fee + oi.pack_fee + oi.card_fee + oi.tax - oi.discount) AS total_fee,pm.pay_name,gs.gs_name')
				->where($where);
			$dataList = $dataList->skip($param['page'])->take($param['pageSize'])->orderBy('order_id', 'DESC')->get()->toArray();
//			dd($dataList);
			$type = $param['type'];//订单类型："1": 待支付 "2": 待确认 "3": 已完成 "4": 已取消
			$now = time();
			$expireTime = 60 * 10;
			foreach ($dataList as &$item){
				$item['order_time_ymd'] = date('Y-m-d H:i', $item['order_time']);
				switch ($type){
					case 1:
						$diffTime = $now - $item['order_time'];
						if ($item['pay_status'] == 0 && !in_array($item['order_status'], [2, 4])) //待支付 倒计时
							$item['litime'] = ($diffTime >= $expireTime) ? 0  : $expireTime - $diffTime ;
						break;
					case 2:
						$diffTime = $item['last_cfm_time'] - $now;
						$item['litime'] = $diffTime;
						break;
					default:
						$item['litime'] = 0;
						break;
				}
				$charge_back = false;//是否可退货
				if ($item['pay_status'] == EnumKeys::PS_PAYED && $item['shipping_status'] < 2 && $item['order_status'] == EnumKeys::OS_SPLITED) {
					$charge_back = true;
				}
				$item['charge_back'] = $charge_back;
				$item['pay_status_text'] = EnumKeys::$payArr[$item['pay_status']];
				$item['pay_name'] = strip_tags($item['pay_name']);
				$item['type'] = $type;
				if ($item['shipping_fee'] > 0) {
					$item['total_price'] += $item['shipping_fee'];
					$item['total_price'] = number_format($item['total_price'], 2);
				}
			}
		} catch (\Exception $e) {
			echo $e->getMessage();die;
			return false;
		}
		return $dataList;
	}

	/**
	 * 获取门店信息
	 * @author: colin
	 * @date: 2019/1/28 10:43
	 * @param $where
	 * @return mixed
	 */
	public function StoresUser($where)
	{
		try {
			$data = StoresUser::selectRaw("gs_stats,gs_type,open_time,close_time,pickup_mode,free_post_money,post_fee")->where($where)->firstOrFail()->toArray();
		} catch (\Exception $e) {
			return false;
		}
		return $data;
	}

	/**
	 * 获取取货信息
	 * @author: colin
	 * @date: 2019/1/28 11:50
	 * @param $where
	 * @return mixed
	 */
	public function Shipp($where)
	{
		try {
			$data = Shipping::where($where)->firstOrFail()->toArray();
		} catch (\Exception $e) {
			return false;
		}
		return $data;
	}

	/**
	 * 获取幸福卡信息
	 * @author: colin
	 * @date: 2019/1/28 14:00
	 * @param $cardIds
	 * @return mixed
	 */
	public function bonusInfo($cardIds, $uid)
	{
		try {
			$data = BonusType::from('ecs_bonus_type as t')->join('ecs_user_bonus as b', 't.type_id', '=', 'b.bonus_type_id')
				->whereIn('b.bonus_id', $cardIds);
			if ($uid) {
				$data = $data->where('b.user_id', $uid);
			}
			$data = $data->get()->toArray();
		} catch (\Exception $e) {
			echo $e->getMessage();
			die;
			return false;
		}
		return $data;
	}

	/**
	 * 获取用户信息
	 * @author: colin
	 * @date: 2019/1/28 17:49
	 * @param $where
	 * @return bool
	 */
	public function userInfo($where)
	{
		try {
			$data = Users::selectRaw("user_id,user_name,user_money_date,bonus_company,user_money")->where($where)->firstOrFail()->toArray();
		} catch (\Exception $e) {
			return false;
		}
		return $data;
	}
	/**
	 * 获取用户名称
	 * @author: colin
	 * @date: 2019/1/28 17:49
	 * @param $where
	 * @return bool
	 */
	public function userName($where)
	{
		try {
			$name = Users::where($where)->value('user_name');
		} catch (\Exception $e) {
			return false;
		}
		return $name;
	}
	/**
	 * 订单详情
	 *
	 * @author: colin
	 * @date: 2019/1/29 15:37
	 * @param $orderSn
	 * @param $uid
	 * @return bool
	 */
	public function Orderdetail($orderSn, $uid)
	{
		try {
			$result = OrderInfo::from('ecs_order_info as oi')->leftJoin('ecs_goods_stores as gs', 'gs.gs_id', '=', 'oi.order_pick_stores')->leftJoin('ecs_payment as pm', 'pm.pay_id', '=', 'oi.pay_id');
			$result = $result->selectRaw("order_id,order_pick_stores,order_sn,pay_status,shipping_status,order_status,add_time as order_time,shipping_time as dispatch_time,address as dispatch_address,order_lxr as user_name,oi.pay_id,order_amount as total_price,
                order_note,gs.gs_id,gs.gs_name,gs.gs_lat,gs.gs_lng,pm.pay_name,oi.order_pick_time,oi.order_tel,gs.gs_name,gs.gs_mobile,gs.gs_address,oi.bonus,oi.bonus_id,oi.shipping_fee,oi.surplus,oi.is_shipping");
			$result = $result->where(['oi.order_sn' => $orderSn, 'oi.user_id' => $uid]);
			$result = $result->firstOrFail()->toArray();
		} catch (\Exception $e) {
			return false;
		}
		return $result;
	}

	/**
	 * 获取快递信息
	 * @author: colin
	 * @date: 2019/1/29 17:19
	 * @param $orderSn
	 * @return bool
	 */
	public function expData($orderSn)
	{
		try {
			$result = OrderExpress::from('ecs_order_express as e')->join('ecs_express_info as i', 'e.ex_id', '=', 'i.ex_id');
			$result = $result->selectRaw("e.ex_num,i.ex_name,i.ex_tel");
			$result = $result->where(['e.order_sn' => $orderSn]);
			$result = $result->firstOrFail()->toArray();
		} catch (\Exception $e) {
			return false;
		}
		return $result;
	}

	/**
	 * 获取订单商品
	 * @author: colin
	 * @date: 2019/1/29 17:30
	 * @param $orderId
	 * @return bool
	 */
	public function goodData($orderId)
	{
		try {
			$result = OrderInfo::from('ecs_order_goods as og')->leftJoin('ecs_goods as gd', 'og.goods_id', '=', 'gd.goods_id');
			$result = $result->selectRaw("og.goods_id,og.goods_name,goods_thumb,goods_price as price,og.goods_number,goods_attr_id,free_post");
			$result = $result->where(['og.order_id' => $orderId]);
			$result = $result->get()->toArray();
			foreach ($result as &$value) {//属性处理
				$value['goods_thumb'] = config('app.static_domain') . $value['goods_thumb'];
				if (!empty($value['goods_attr_id'])) {
					$goodsArrId = explode(',', $value['goods_attr_id']);
					$value['goods_attr'] = GoodsAttr::from('ecs_goods_attr as ga')->leftJoin('ecs_attribute as ea', 'ga.attr_id', '=', 'ea.attr_id')
						->selectRaw("ga.attr_id,ea.attr_name,ga.attr_value")
						->whereIn('ga.goods_attr_id', $goodsArrId)
						->get()->toArray();
				} else {
					$value['goods_attr'] = [];
				}
			}
		} catch (\Exception $e) {
			return false;
		}
		return $result;

	}

	/**
	 * 获取订单商品信息
	 * @author: colin
	 * @date: 2019/5/23 15:38
	 * @param $orderId
	 * @return bool
	 */
	public function orderGoodData($orderId)
	{
		try {
			$result = OrderGoods::where('order_id',$orderId)->get()->toArray();
		} catch (\Exception $e) {
			echo $e->getMessage();die;
			return false;
		}
		return $result;

	}

	/**
	 * 用户确认提货
	 * @author: colin
	 * @date: 2019/5/27 14:25
	 * @param $orderSn
	 * @param $uid
	 * @param $userInfo
	 * @return array
	 */
	public function confirm($orderSn,$uid,$userInfo)
	{
		try{
			$result = ['status'=>1,'msg'=>''];
			$where = ['order_sn'=>$orderSn,'user_id'=>$uid];
			/* 查询订单信息，检查状态 */
			$order = OrderInfo::where($where)->firstOrFail()->toArray();
			if ($order['shipping_status'] == EnumKeys::SS_RECEIVED){
				$result = ['status'=>0,'msg'=>'已经确认提货！'];
				return $result;
			}
			$dataInfo = [
				'shipping_status' => EnumKeys::SS_RECEIVED,
				'shipping_time' => time(),
			];
			$res = OrderInfo::where($where)->update($dataInfo);
			if(!$res){
				$result = ['status'=>0,'msg'=>'确认提货失败！'];
				return $result;
			}
			help::orderAction($order['order_id'], EnumKeys::SS_RECEIVED, $order['shipping_status'], $order['pay_status'], '用户确认提货', $userInfo['name']);
		}catch(\Exception $e){
			$result['status'] = 0;
			$result['msg'] = '没有该订单！';
			return $result;
		}
		return $result;


	}

}