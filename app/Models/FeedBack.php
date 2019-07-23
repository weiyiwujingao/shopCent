<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FeedBack
 *
 * @property int $msg_id
 * @property int $parent_id
 * @property int $user_id
 * @property string $user_name
 * @property string $user_email
 * @property string $msg_title
 * @property int $msg_type
 * @property int $msg_status
 * @property string $msg_content
 * @property int $msg_time
 * @property string $message_img
 * @property int $order_id
 * @property int $msg_area
 * @property int $send_status 0未生成会员消息;1已生成会员消息
 * @property string|null $create_time 添加时间
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeedBack newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeedBack newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeedBack query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeedBack whereCreateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeedBack whereMessageImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeedBack whereMsgArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeedBack whereMsgContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeedBack whereMsgId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeedBack whereMsgStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeedBack whereMsgTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeedBack whereMsgTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeedBack whereMsgType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeedBack whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeedBack whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeedBack whereSendStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeedBack whereUserEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeedBack whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeedBack whereUserName($value)
 * @mixin \Eloquent
 */
class FeedBack extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */

    protected $fillable = [
        'parent_id',
        'user_id',
        'user_name',
        'user_email',
        'msg_title',
        'msg_type',
        'msg_status',
        'msg_content',
        'msg_time',
        'message_img',
        'order_id',
        'msg_area',
        'send_status',
        'create_time',
    ];
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ecs_feedback';
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
    protected $primaryKey = 'msg_id';
    /**
     * 模型日期列的存储格式
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
