<?php

namespace Library\UserCenter;

use App;
use Illuminate\Http\Request;
use \Exception;
use \Helper\CFunctionHelper as help;
use \Enum\EnumKeys;

class UserOrder extends \Library\CBase
{
	protected $request;
	protected $userCMd;

	public function __construct(Request $request)
	{
		$this->request = $request;
		$this->userOMd = new App\Repositories\UserOrderRepository();
		parent::__construct(__CLASS__);
	}
	/**
	 * 订单统计
	 * @author: colin
	 * @date: 2019/1/10 11:55
	 * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Library\type
	 */
	public function statis()
	{
		try {
			$uid = $this->request->input('uid');
			$statis = [
				1 => ['name'=>'待支付'],
				2 => ['name'=>'待确认'],
				3 => ['name'=>'已完成'],
				4 => ['name'=>'已取消'],
			];
			$statis[1]['value'] = $this->userOMd->statis($this->Search($uid,1));
			$statis[2]['value'] = $this->userOMd->statis($this->Search($uid,2));
			$statis[3]['value'] = $this->userOMd->statis($this->Search($uid,3));
			$statis[4]['value'] = $this->userOMd->statis($this->Search($uid,4));
			if ($statis === false) {
				throw new Exception('获取订单信息失败！');
			}
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "UserCollect getlist:" . json_encode($uid) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return $statis;
	}

	/**
	 * 统计订单参数
	 * @author: colin
	 * @date: 2019/1/10 13:50
	 * @return \Closure
	 */
	public function Search($uid,$search)
	{
		$where = function ($query) use ($search,$uid) {
			$query->where(['user_id'=>$uid, 'is_user_del'=>0]);
			switch ($search) {
				case '1':
					$query->where('pay_status',0)->whereNotIn('order_status',[2,4]);//待支付
					break;
				case '2':
					$query->where('pay_status',2)->whereIn('shipping_status',[1,3]);//待确认（已支付，未收货(已发货)）
					break;
				case '3':
					$query->where(['order_status'=>5,'shipping_status'=>2]);//已完成
					break;
				case '4':
					$query->whereIn('order_status',[2,4]);//已取消
					break;
				default:
					break;

			}
		};
		return $where;
	}
	/***
	 * 用户订单查询
	 * @author: colin
	 * @date: 2019/1/10 14:25
	 * @return \Library\type
	 */
	public function settlement()
	{
		$uid = $this->request->input('uid');
		$param = $this->request->all();
		$where = $this->Search($uid,$param['type']);
		try {
			$result = $this->userOMd->getList($param,$where);
			if($result === false){
				throw new \Exception('未查询到订单数据！');
			}
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "UserOrder 获取用户订单查询信息失败:" . json_decode($param) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		$result = $this->settleData($result);
		return $result;
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
		foreach ($data as $k => $v) {
			$data[$k]['shipping_status'] = ($data[$k]['shipping_status'] == \Enum\EnumKeys::SS_SHIPPED_ING) ? \Enum\EnumKeys::SS_PREPARING : $data[$k]['shipping_status'];
			$goodsList = $this->orderGoods($data[$k]['order_id']);
			$arr[] = [
				'order_id' => $data[$k]['order_id'],
				'order_sn' => $data[$k]['order_sn'],
				'order_time' => $data[$k]['add_time'],
				'order_status' => $data[$k]['order_status'],
				'shipping_status' => $data[$k]['shipping_status'],
				'total_fee' => help::priceFormat($data[$k]['total_fee'], false),
				'goods' => $goodsList,
				'order_lxr' => $data[$k]['order_lxr'],
				'order_tel' => $data[$k]['order_tel'],
				'return_reason' => $data[$k]['return_reason'],
				'shipping_fee' => $data[$k]['shipping_fee'],
				'order_note' => $data[$k]['order_note'],
				'pay_status' => $data[$k]['pay_status'],
				'order_pick_stores' => $data[$k]['order_pick_stores'],
				'total_price' => help::priceFormat($data[$k]['total_price'], false),
				'gs_name' => $data[$k]['gs_name'],
				'order_time_ymd' => $data[$k]['order_time_ymd'],
				'charge_back' => $data[$k]['charge_back'],
				'pay_status_text' => $data[$k]['pay_status_text'],
				'type' => $data[$k]['type'],
				'is_shipping' => $data[$k]['is_shipping'],
				'litime' => $data[$k]['litime'],
				'order_taking' => $data[$k]['order_taking'],

			];
		}
		return $arr;
	}

	/**
	 * 获取订单商品数据
	 * @author: colin
	 * @date: 2019/1/10 15:11
	 * @param $orderId
	 * @return array|\Library\type
	 */
	public function orderGoods($orderId)
	{
		if (empty($orderId)) return [];
		try {
			$result = \App\Models\OrderGoods::select("rec_id", "goods_id","goods_sn", "goods_attr_id", "goods_name", "goods_number", "goods_price", "goods_attr")
				->where('order_id', $orderId)
				->orderBy('rec_id', 'DESC')->get()->toArray();
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => " UserOrder 订单商品表:" . $orderId . ",reason:" . $e->getMessage(),
				'userMsg' => '订单商品表查询有误！',
				'line' => __LINE__,
			]);
		}
		foreach ($result as $k => $v) {
			$result[$k]['goods_price'] = help::priceFormat($result[$k]['goods_price'], false);
			$result[$k]['goods_thumb'] = \App\Models\Goods::where('goods_id', $result[$k]['goods_id'])->value('goods_thumb');//产品缩略图
			if ($result[$k]['goods_thumb']) {
				$result[$k]['goods_thumb'] = config('app.static_domain') . $result[$k]['goods_thumb'];//产品缩略图
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

	/***
	 * 确认提货
	 * @author: colin
	 * @date: 2019/5/27 11:57
	 * @return array|\Illuminate\Contracts\Pagination\LengthAwarePaginator|\Library\type
	 */
	public function confirm()
	{
		$uid = $this->request->input('uid');
		$userInfo = $this->request->input('userInfo');
		$orderSn = $this->request->input('order_sn');
		try {
			$result = $this->userOMd->confirm($orderSn,$uid,$userInfo);
			if($result['status'] === 0){
				throw new \Exception($result['msg']);
			}
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "confirm 用户确认提后失败失败:" . json_decode($orderSn) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return true;
	}


}