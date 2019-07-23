<?php

namespace App\Repositories;

use App\Models\UsersPaymentCode;

class UserPaymentCodeRepository
{
	/**
	 * 创建付款码
	 * @param array $params
	 * @return mixed
	 */
	public function create(array $params)
	{
		return UsersPaymentCode::create($params);
	}

	/**
	 * 修改付款码
	 * @author: colin
	 * @date: 2019/1/9 16:23
	 * @param $params
	 * @param $where
	 * @return mixed
	 */
	public function update($params, $where)
	{
		return UsersPaymentCode::where($where)->update($params);
	}

	/**
	 * 删除选中付款码
	 * @author: colin
	 * @date: 2019/1/9 16:37
	 * @param $where
	 * @return mixed
	 */
	public function delete($where)
	{
		return UsersPaymentCode::where($where)->delete();
	}

	/**
	 * 根据id获取付款码资料
	 * @param $id
	 * @return mixed
	 */
	public function ById($id)
	{
		return UsersPaymentCode::find($id);
	}
	/**
	 * 根据paycode获取付款码资料
	 * @param $id
	 * @return mixed
	 */
	public function ByPayCode($payCode)
	{
		try{
			$result = UsersPaymentCode::selectRaw('pyid,status,create_time_int')->where('pcode',$payCode)->firstOrFail();
		}catch(\Exception $e){
			return false;
		}
		return $result;
	}
	/**
	 * 根据paycode获取付款码id
	 * @param $id
	 * @return mixed
	 */
	public function getPyid($payCode)
	{
		return UsersPaymentCode::where('pcode',$payCode)->value('pyid');
	}
}