<?php

namespace App\Repositories;

use App\Models\UserMessage;
use App\Models\UserMessageRecord;

class UserMessageRepository
{
	/**
	 * 获取系统消息列表
	 * @author: colin
	 * @date: 2019/1/14 15:54
	 * @param $uid
	 * @param $param
	 * @return array|bool
	 */
	public function list($uid, $param)
	{
		try {
			$param['page'] = ($param['page'] - 1) * $param['pageSize'];
			$list = UserMessageRecord::join('ecs_user_message as m', 'ecs_user_message_records.message_id', '=', 'm.id')
				->selectRaw('m.id,m.title,m.create_time,ecs_user_message_records.status')
				->where(['ecs_user_message_records.user_id' => $uid]);
			$count = $list;
			$count = $count->count();
			$list = $list->skip($param['page'])->take($param['pageSize'])->orderBy('ecs_user_message_records.id', 'DESC')->get()->toArray();
			return ['count'=>$count,'list'=>$list];
		} catch (\Exception $e) {
			return false;
		}
	}

	/**
	 * 系统消息详情
	 * @author: colin
	 * @date: 2019/1/14 16:20
	 * @param $uid
	 * @param $id
	 * @return array|bool
	 */
	public function detail($uid, $id)
	{
		try {
			$detail = UserMessageRecord::join('ecs_user_message as m', 'ecs_user_message_records.message_id', '=', 'm.id')
				->selectRaw('m.message,ecs_user_message_records.status')
				->where(['ecs_user_message_records.user_id' => $uid,'m.id'=>$id])
				->firstOrFail();
			$detail->status = 1;
			$detail->save();
			$detail = $detail->toArray();
		} catch (\Exception $e) {
			return false;
		}
		return $detail;
	}

}