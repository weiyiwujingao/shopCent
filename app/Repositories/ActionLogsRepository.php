<?php
namespace App\Repositories;


use App\Models\ActionLog;

class ActionLogsRepository
{
    /**
     * @param $data
     * @return mixed
     */
    public function create($data)
    {
        return ActionLog::create($data);
    }

    /**
     * 获取全部的操作日志
     * @return mixed
     */
    public function getWithAdminActionLogs()
    {
        return ActionLog::with('admin')->latest('created_at')->paginate(20);
    }
}