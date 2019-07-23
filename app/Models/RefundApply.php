<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\RefundApply
 *
 * @property int $apply_id
 * @property int $order_id
 * @property string $rec_ids 表ecs_ordre_goods中的rec_id，多个逗号隔开
 * @property string $apply_user
 * @property int $apply_user_type 1:客户;2:商户
 * @property int $apply_time
 * @property int $apply_status 0:未处理；1：已退货；2：已取消
 * @property string|null $dispose_user
 * @property int|null $dispose_time
 * @property string $return_reason 退货原因
 * @property int $wx_send 是否发送微信通知
 * @property string $send_time 微信通知时间
 * @property string $remark 备注
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RefundApply newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RefundApply newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RefundApply query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RefundApply whereApplyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RefundApply whereApplyStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RefundApply whereApplyTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RefundApply whereApplyUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RefundApply whereApplyUserType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RefundApply whereDisposeTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RefundApply whereDisposeUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RefundApply whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RefundApply whereRecIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RefundApply whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RefundApply whereReturnReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RefundApply whereSendTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RefundApply whereWxSend($value)
 * @mixin \Eloquent
 */
class RefundApply extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */

    protected $fillable = [
		'order_id',
		'apply_user',
		'apply_user_type',
		'apply_time',
		'apply_status',
		'dispose_user',
		'dispose_time',
		'return_reason',
	];
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ecs_refund_apply';
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
    protected $primaryKey = 'apply_id';
    /**
     * 模型日期列的存储格式
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
