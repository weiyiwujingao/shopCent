<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserMessage
 *
 * @property int $id 自增id
 * @property string $title 标题
 * @property string $message 系统消息内容
 * @property int $nums 推送人数
 * @property string $create_time 添加时间
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserMessage whereCreateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserMessage whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserMessage whereNums($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserMessage whereTitle($value)
 * @mixin \Eloquent
 */
class UserMessage extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */

    protected $fillable = [
        "title",
        "message",
        "nums",
        "create_time",
    ];
	/**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ecs_user_message';
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
