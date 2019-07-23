<?php
namespace App\Repositories;


use App\Models\Role;

class RolesRepository
{
    /**
     * 获取所有角色
     * @return mixed
     */
    public function getRoles()
    {
        return Role::get();
    }
}