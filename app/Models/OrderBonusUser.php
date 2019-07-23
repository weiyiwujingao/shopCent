<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OrderBonusUser
 *
 * @property int $id 自增id
 * @property string $order_sn 订单号
 * @property int $bonus_id 幸福券id
 * @property float $used_money 使用的金额
 * @property int $status 状态：0未完成,1完成,2已退回
 * @property string $create_time 创建时间
 * @property string $change_time 最后改变时间
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderBonusUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderBonusUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderBonusUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderBonusUser whereBonusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderBonusUser whereChangeTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderBonusUser whereCreateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderBonusUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderBonusUser whereOrderSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderBonusUser whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderBonusUser whereUsedMoney($value)
 * @mixin \Eloquent
 */
class OrderBonusUser extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */

    protected $fillable = [
        "order_sn",
        "bonus_id",
        "used_money",
        "status",
        "create_time",
        "change_time",
    ];
	/**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ecs_order_bonus_used';
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
//    protected $primaryKey = 'id';
    /**
     * 模型日期列的存储格式
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
