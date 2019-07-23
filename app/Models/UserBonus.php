<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserBonus
 *
 * @property int $bonus_id
 * @property int $bonus_type_id
 * @property string $bonus_sn
 * @property int $user_id
 * @property int $used_time
 * @property int $order_id
 * @property int $emailed
 * @property int $buy_user_id 购买会员ID
 * @property string $bonus_pass
 * @property string $bonus_salt
 * @property string $bonus_order_sn
 * @property string $bonus_pass_exp 明文
 * @property int $ad_id 卡样ID
 * @property float $bonus_money 幸福卡面值
 * @property float $used_money 已使用金额
 * @property float $balance 余额
 * @property int $delay_count 延期次数
 * @property int $bonus_status 状态：1正在使用，2已用完，3已过期，4已作废
 * @property string $ban_stores_id 禁止使用门店ID
 * @property string $bonus_company
 * @property int $bonus_start_date 使用开始日期
 * @property int $bonus_end_date 使用结束日期
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBonus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBonus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBonus query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBonus whereAdId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBonus whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBonus whereBanStoresId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBonus whereBonusCompany($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBonus whereBonusEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBonus whereBonusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBonus whereBonusMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBonus whereBonusOrderSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBonus whereBonusPass($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBonus whereBonusPassExp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBonus whereBonusSalt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBonus whereBonusSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBonus whereBonusStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBonus whereBonusStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBonus whereBonusTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBonus whereBuyUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBonus whereDelayCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBonus whereEmailed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBonus whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBonus whereUsedMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBonus whereUsedTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBonus whereUserId($value)
 * @mixin \Eloquent
 */
class UserBonus extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */

    protected $fillable = [
        "bonus_type_id",
        "bonus_sn",
        "user_id",
        "used_time",
        "order_id",
        "emailed",
        "buy_user_id",
        "bonus_pass",
        "bonus_salt",
        "bonus_order_sn",
        "bonus_pass_exp",
        "ad_id",
        "bonus_money",
        "used_money",
        "balance",
        "bonus_status",
        "ban_stores_id",
        "bonus_company",
        "bonus_end_date",
    ];
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ecs_user_bonus';
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
    protected $primaryKey = 'bonus_id';
    /**
     * 模型日期列的存储格式
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
