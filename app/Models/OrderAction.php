<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OrderAction
 *
 * @property int $action_id
 * @property int $order_id
 * @property string $rec_ids 表ecs_ordre_goods中的rec_id，多个逗号隔开
 * @property string $action_user
 * @property int $order_status
 * @property int $shipping_status
 * @property int $pay_status
 * @property int $action_place
 * @property float $refund_amount 退款金额
 * @property string $action_note
 * @property int $log_time
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderAction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderAction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderAction query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderAction whereActionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderAction whereActionNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderAction whereActionPlace($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderAction whereActionUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderAction whereLogTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderAction whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderAction whereOrderStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderAction wherePayStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderAction whereRecIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderAction whereRefundAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderAction whereShippingStatus($value)
 * @mixin \Eloquent
 */
class OrderAction extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */

    protected $fillable = [
        "order_id",
        "action_user",
        "order_status",
        "shipping_status",
        "pay_status",
        "action_place",
        "action_note",
        "log_time",
        "rec_ids",
        "refund_amount",
    ];
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ecs_order_action';
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
    protected $primaryKey = 'action_id';
    /**
     * 模型日期列的存储格式
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
