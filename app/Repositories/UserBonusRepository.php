<?php

namespace App\Repositories;

use App\Models\UserBonus;
use App\Models\BonusType;

class UserBonusRepository
{
	/**
	 * 创建账单
	 * @param array $params
	 * @return mixed
	 */
	public function create(array $params)
	{
		return UserBonus::create($params);
	}

	/**
	 * 修改账单
	 * @author: colin
	 * @date: 2019/1/9 16:23
	 * @param $params
	 * @param $where
	 * @return mixed
	 */
	public function update($params, $where)
	{
		return UserBonus::where($where)->update($params);
	}

	/**
	 * 删除选中账单
	 * @author: colin
	 * @date: 2019/1/9 16:37
	 * @param $where
	 * @return mixed
	 */
	public function delete($where)
	{
		return UserBonus::where($where)->delete();
	}

	/**
	 * 根据id获取账单资料
	 * @param $id
	 * @return mixed
	 */
	public function ById($id)
	{
		return UserBonus::find($id);
	}

	/**
	 * 幸福卡使用情况统计
	 * @author: colin
	 * @date: 2019/1/11 11:07
	 * @param $uid
	 * @return bool
	 */
	public function userCardStatis($uid)
	{
		try {
			$dataList = UserBonus::selectRaw('bonus_status as s,count(bonus_id) as ct')->where(['user_id' => $uid])->groupBy('bonus_status')->get()->toArray();
		} catch (\Exception $e) {
			return false;
		}
		return $dataList;
	}

	/**
	 * 幸福卡列表
	 * @author: colin
	 * @date: 2019/1/11 13:52
	 * @param $uid
	 * @param $type
	 * @param $param
	 * @return bool
	 */
	public function list($uid, $param)
	{
		try {
			$status = $param['type'];//1可以使用， 2已用完，3已过期，4已作废
			$param['page'] = ($param['page'] - 1) * $param['pageSize'];
			$list = UserBonus::join('ecs_bonus_type as t', 'ecs_user_bonus.bonus_type_id', '=', 't.type_id')
				->selectRaw('ecs_user_bonus.bonus_id,ecs_user_bonus.bonus_sn,ecs_user_bonus.bonus_money,ecs_user_bonus.used_money,ecs_user_bonus.bonus_company,ecs_user_bonus.balance,t.use_end_date,ecs_user_bonus.bonus_status,ecs_user_bonus.bonus_end_date')
				->where(['ecs_user_bonus.user_id' => $uid, 'ecs_user_bonus.bonus_status' => $status]);
			$list = $list->skip($param['page'])->take($param['pageSize'])->orderBy('ecs_user_bonus.bonus_end_date', 'DESC')->get()->toArray();
			foreach ($list as &$item) {
				$endData = $item['bonus_end_date'] ? $item['bonus_end_date'] : $item['use_end_date'];
				$item['use_end_date'] = date('Y.m.d', $endData);
			}
		} catch (\Exception $e) {
			return false;
		}
		return $list;
	}

	/**
	 * 获取幸福券信息
	 * @author: colin
	 * @date: 2019/2/15 13:38
	 * @param $cardId
	 * @return bool
	 */
	public function getBonus($cardId)
	{
		try {
			$data = BonusType::from('ecs_bonus_type as t')->join('ecs_user_bonus as b', 't.type_id', '=', 'b.bonus_type_id')
				->selectRaw("t.*, b.*")
				->where('b.bonus_id',$cardId);
			$data = $data->firstOrFail()->toArray();
			return $data;
		} catch (\Exception $e) {
			return false;
		}
		return $data;
	}

}