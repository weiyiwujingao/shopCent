<?php
namespace App\Repositories;

use App\Models\Admin;

class AdminsRepository
{
    /**
     * 创建管理员
     * @param array $params
     * @return mixed
     */
    public function create(array $params)
    {
        return Admin::create($params);
    }

    /**
     * 根据id获取管理员资料
     * @param $id
     * @return mixed
     */
    public function ById($id)
    {
        return Admin::find($id);
    }

    /**
     * 获取管理员列表 with ('roles')
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAdminsWithRoles()
    {
        return Admin::with('roles')->latest('updated_at')->paginate('10');
    }

    /**
     * 根据name查询管理员资料
     * @param $name
     * @return mixed
     */
    public function ByName($name)
    {
        return Admin::where('name',$name)->first();
    }
}