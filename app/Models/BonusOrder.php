<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BonusOrder
 *
 * @property int $bo_id
 * @property int $bonus_type_id 充值卡id
 * @property int $bonus_order_sn
 * @property int $user_id 用户id
 * @property int $addtime 创建时间
 * @property float $bonus_amount 卡金额
 * @property int $pay_id 支付方式
 * @property int $pay_status 支付状态 0未支付 1已支付
 * @property int $ad_id 卡样ID
 * @property string $bo_randomNum
 * @property int $bo_bouns_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BonusOrder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BonusOrder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BonusOrder query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BonusOrder whereAdId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BonusOrder whereAddtime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BonusOrder whereBoBounsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BonusOrder whereBoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BonusOrder whereBoRandomNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BonusOrder whereBonusAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BonusOrder whereBonusOrderSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BonusOrder whereBonusTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BonusOrder wherePayId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BonusOrder wherePayStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BonusOrder whereUserId($value)
 * @mixin \Eloquent
 */
class BonusOrder extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */
    protected $fillable = [
        'bonus_type_id',
        'bonus_order_sn',
        'user_id',
        'addtime',
        'bonus_amount',
        'pay_id',
        'pay_status',
        'ad_id',
        'bo_randomNum',
        'bo_bouns_id',
    ];

	/**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ecs_bonus_order';
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
     protected $primaryKey = 'bo_id';
    /**
     * 模型日期列的存储格式
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
