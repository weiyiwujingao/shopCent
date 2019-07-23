<?php
namespace App\Repositories;

use App\Models\Users;

class UsersRepository
{
    /**
     * 创建用户
     * @param array $params
     * @return mixed
     */
    public function create(array $params)
    {
		try {
			return Users::create($params);
		} catch (\Exception $e) {
			return false;
		}
    }
	/**
	 * 根据user_id修改用户信息
	 * @param $name
	 * @return mixed
	 */
	public function updateByid($userId,$data)
	{
		try {
			Users::where('user_id',$userId)->update($data);
		} catch (\Exception $e) {
			return false;
		}
		return true;
	}
	/**
	 * 根据user_name查询加盐字符串
	 * @param $name
	 * @return mixed
	 */
	public function GetSaltByName($name)
	{
		return Users::where('user_name',$name)->value('ec_salt');
	}
	/**
	 * 根据user_name查询
	 * @param $name
	 * @return mixed
	 */
	public function getInfoByName($name)
	{
		try {
			$userInfo = Users::where('user_name',$name)->firstOrFail();
		} catch (\Exception $e) {
			return false;
		}
		return $userInfo;
	}
	/**
	 * 根据user_id查询
	 * @param $name
	 * @return mixed
	 */
	public function ById($userId)
	{
		try {
			$userInfo = Users::where('user_id',$userId)->firstOrFail();
		} catch (\Exception $e) {
			return false;
		}
		return $userInfo;
	}
	/**
	 * 根据账号密码获取用户信息
	 * @author: colin
	 * @date: 2019/1/7 11:46
	 * @param $userName
	 * @param $passWord
	 * @return bool
	 */
	public function getInfo($userName, $passWord)
	{
		try {
			$userInfo = Users::where(['user_name'=> $userName,'password'=> $passWord])->firstOrFail();
		} catch (\Exception $e) {
			return false;
		}
		return $userInfo;
	}
}