<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AccountLog
 *
 * @property int $log_id
 * @property int $user_id
 * @property float $user_money
 * @property float $frozen_money
 * @property int $rank_points
 * @property int $pay_points
 * @property int $change_time
 * @property string $change_desc
 * @property int $change_type
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AccountLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AccountLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AccountLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AccountLog whereChangeDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AccountLog whereChangeTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AccountLog whereChangeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AccountLog whereFrozenMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AccountLog whereLogId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AccountLog wherePayPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AccountLog whereRankPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AccountLog whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AccountLog whereUserMoney($value)
 * @mixin \Eloquent
 */
class AccountLog extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */

    protected $fillable = [
        "user_id",
        "user_money",
        "frozen_money",
        "rank_points",
        "pay_points",
        "change_time",
        "change_desc",
        "change_type",
    ];
	/**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ecs_account_log';
    /**
     * 表明模型是否应该被打上时间戳
     * 默认情况下，Eloquent 期望created_at和updated_at已经存在于数据表中，如果你不想要这些 Laravel 自动管理的列，在模型类中设置$timestamps属性为false：
     * @var bool
     */
    public $timestamps = false;
    /**
     * 关联到模型的数据表
     * Eloquent 默认每张表的主键名为id，你可以在模型类中定义一个$primaryKey属性来覆盖该约定
     *
     * @var string
     */
    protected $primaryKey = 'log_id';
    /**
     * 模型日期列的存储格式
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
