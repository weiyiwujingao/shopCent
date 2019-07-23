<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BonusType
 *
 * @property int $type_id
 * @property string $type_name
 * @property float $type_money
 * @property int $send_type
 * @property float $min_amount
 * @property float $max_amount
 * @property int $send_start_date
 * @property int $send_end_date
 * @property int $use_start_date
 * @property int $use_end_date
 * @property float $min_goods_amount
 * @property int $is_display
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BonusType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BonusType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BonusType query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BonusType whereIsDisplay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BonusType whereMaxAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BonusType whereMinAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BonusType whereMinGoodsAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BonusType whereSendEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BonusType whereSendStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BonusType whereSendType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BonusType whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BonusType whereTypeMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BonusType whereTypeName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BonusType whereUseEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BonusType whereUseStartDate($value)
 * @mixin \Eloquent
 */
class BonusType extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */
    protected $fillable = [
        'type_name',
        'type_money',
        'send_type',
        'min_amount',
        'max_amount',
        'send_start_date',
        'send_end_date',
        'use_start_date',
        'use_end_date',
        'min_goods_amount',
        'is_display',

    ];
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ecs_bonus_type';
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
     protected $primaryKey = 'type_id';
    /**
     * 模型日期列的存储格式
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
