<?php

namespace App\Repositories;

use App\Models\BonusType;
use App\Models\BonusOrder;
use App\Models\UserBonus;
use App\Models\BonusCount;
use App\Models\Users;
use \Enum\EnumKeys;
use \Helper\CFunctionHelper as help;

class BonusTypeRepository
{
	/**
	 * 创建充值订单
	 * @param array $params
	 * @return mixed
	 */
	public function create(array $params)
	{
		try {
			$create = BonusOrder::create($params);
		} catch (\Exception $e) {
			return false;
		}
		return $create;

	}
	/**
	 * 修改充值订单
	 * @author: colin
	 * @date: 2019/1/9 16:23
	 * @param $params
	 * @param $where
	 * @return mixed
	 */
	public function update($params, $where)
	{
		return BonusOrder::where($where)->update($params);
	}
	/**
	 * 删除选中充值订单
	 * @author: colin
	 * @date: 2019/1/9 16:37
	 * @param $where
	 * @return mixed
	 */
	public function delete($where)
	{
		return BonusOrder::where($where)->delete();
	}
	/**
	 * 获取充值金额订单列表
	 * @author: colin
	 * @date: 2019/5/28 11:56
	 * @param $where
	 * @return bool
	 */
	public function list($where)
	{
		try{
			return BonusType::selectRaw("type_id,type_money")->where($where)->orderBy('type_id','asc')->get()->toArray();
		}catch(\Exception $e){
			return false;
		}
	}
	/**
	 * 充值订单类型详情
	 * @author: colin
	 * @date: 2019/5/28 14:10
	 * @param $where
	 * @return bool
	 */
	public function detail($where)
	{
		try{
			return BonusType::where($where)->firstOrFail()->toArray();
		}catch(\Exception $e){
			echo $e->getMessage();die;
			return false;
		}
	}
	/**
	 * 充值订单详情
	 * @author: colin
	 * @date: 2019/5/28 14:10
	 * @param $where
	 * @return bool
	 */
	public function orderDetail($where)
	{
		try{
			return BonusOrder::where($where)->firstOrFail()->toArray();
		}catch(\Exception $e){
			return false;
		}
	}

	/**
	 * 获取红包信息详情
	 * @author: colin
	 * @date: 2019/5/31 14:04
	 * @param $where
	 * @return bool
	 */
	public function UserBonusDetail($where)
	{
		try{
			return UserBonus::where($where)->firstOrFail()->toArray();
		}catch(\Exception $e){
			return false;
		}
	}
	/**
	 * 红包生成序列详情
	 * @author: colin
	 * @date: 2019/5/31 14:16
	 * @param $where
	 * @return bool
	 */
	public function BonusCount()
	{
		try{
			return UserBonus::where('bt_id',1)->value('bt_num');
		}catch(\Exception $e){
			return false;
		}
	}
	/**
	 * 更新红包生成序列
	 * @author: colin
	 * @date: 2019/5/31 14:16
	 * @param $where
	 * @return bool
	 */
	public function UpBonusCount($num)
	{
		try{
			return BonusCount::where('bt_id',1)->update(['bt_num'=>$num]);
		}catch(\Exception $e){
			return false;
		}
	}

	/**
	 * 获取用户使用红包时间
	 * @author: colin
	 * @date: 2019/5/31 14:37
	 * @param $uid
	 * @return bool
	 */
	public function GetUserDate($uid)
	{
		try{
			return Users::where('user_id',$uid)->value('user_money_date');
		}catch(\Exception $e){
			return false;
		}
	}

}