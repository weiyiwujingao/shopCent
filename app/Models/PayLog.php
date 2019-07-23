<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PayLog
 *
 * @property int $log_id
 * @property int $order_id
 * @property float $order_amount
 * @property int $order_type
 * @property int $is_paid
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PayLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PayLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PayLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PayLog whereIsPaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PayLog whereLogId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PayLog whereOrderAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PayLog whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PayLog whereOrderType($value)
 * @mixin \Eloquent
 */
class PayLog extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'order_amount',
        'order_type',
        'is_paid',
    ];
	/**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ecs_pay_log';
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
