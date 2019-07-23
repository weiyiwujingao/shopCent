<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Comment
 *
 * @property int $comment_id
 * @property int $comment_type
 * @property int $id_value
 * @property string $email
 * @property string $user_name
 * @property string $content
 * @property int $comment_rank
 * @property int $add_time
 * @property string $ip_address
 * @property int $status
 * @property int $parent_id
 * @property int $user_id
 * @property int $order_id 针对该产品订单ID
 * @property int $is_anonymous 是否匿名评价
 * @property int $comment_rank_2
 * @property int $comment_rank_3
 * @property string $cmt_img_1
 * @property string $cmt_img_2
 * @property string $cmt_img_3
 * @property string $cmt_img_4
 * @property string $cmt_img_5
 * @property int $is_img 是否有图
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereAddTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereCmtImg1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereCmtImg2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereCmtImg3($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereCmtImg4($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereCmtImg5($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereCommentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereCommentRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereCommentRank2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereCommentRank3($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereCommentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereIdValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereIsAnonymous($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereIsImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereUserName($value)
 * @mixin \Eloquent
 */
class Comment extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */

    protected $fillable = [
        'comment_type',
        'id_value',
    ];
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ecs_comment';
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
     protected $primaryKey = 'comment_id';
    /**
     * 模型日期列的存储格式
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
