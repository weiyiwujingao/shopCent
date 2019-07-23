<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserMessageRecord
 *
 * @property int $id 自增id
 * @property int $user_id 用户id
 * @property int $message_id 关联系统消息表id
 * @property int $status 是否已读：0未读：1已读；
 * @property int $wx_notice 是否有微信通知
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserMessageRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserMessageRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserMessageRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserMessageRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserMessageRecord whereMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserMessageRecord whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserMessageRecord whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserMessageRecord whereWxNotice($value)
 * @mixin \Eloquent
 */
class UserMessageRecord extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */

    protected $fillable = [
        "user_id",
        "message_id",
        "status",
        "wx_notice",
    ];

	/**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ecs_user_message_records';
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
    protected $primaryKey = 'id';
    /**
     * 模型日期列的存储格式
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
