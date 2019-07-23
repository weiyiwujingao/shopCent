<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AccountLogBonus
 *
 * @property int $logb_id
 * @property int $user_id
 * @property string $order_sn
 * @property int $stores_id
 * @property int $bonus_id 礼品卡的id
 * @property float $user_money
 * @property int $change_time
 * @property string $change_desc
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AccountLogBonus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AccountLogBonus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AccountLogBonus query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AccountLogBonus whereBonusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AccountLogBonus whereChangeDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AccountLogBonus whereChangeTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AccountLogBonus whereLogbId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AccountLogBonus whereOrderSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AccountLogBonus whereStoresId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AccountLogBonus whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AccountLogBonus whereUserMoney($value)
 * @mixin \Eloquent
 */
class AccountLogBonus extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */

    protected $fillable = [
        "user_id",
        "order_sn",
        "stores_id",
        "bonus_id",
        "user_money",
        "change_time",
        "change_desc",
    ];
	/**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ecs_account_log_bonus';
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
    protected $primaryKey = 'logb_id';
    /**
     * 模型日期列的存储格式
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
