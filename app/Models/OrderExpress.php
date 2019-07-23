<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OrderExpress
 *
 * @property int $kid
 * @property string $order_sn 订单号
 * @property int $ex_id 快递表的id
 * @property string $ex_num 快递单号
 * @property int $up_times 修改次数
 * @property string $create_time 添加时间
 * @property int|null $wx_send 微信消息发送状态
 * @property string $send_time 发送消息时间
 * @property string $remark 备注
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderExpress newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderExpress newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderExpress query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderExpress whereCreateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderExpress whereExId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderExpress whereExNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderExpress whereKid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderExpress whereOrderSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderExpress whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderExpress whereSendTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderExpress whereUpTimes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderExpress whereWxSend($value)
 * @mixin \Eloquent
 */
class OrderExpress extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */

    protected $fillable = [
    	'order_sn',
    	'ex_id',
    	'ex_num',
    	'ex_mess',
    	'up_times',
    	'create_time',
	];
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ecs_order_express';
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
    protected $primaryKey = 'kid';
    /**
     * 模型日期列的存储格式
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
