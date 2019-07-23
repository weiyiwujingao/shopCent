<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UsersPaymentCode
 *
 * @property int $pyid 自增id
 * @property int $user_id 用户id
 * @property string $pcode 二维码字符串
 * @property int $status 状态: 1已使用 2已过期
 * @property string $create_time 创建时间
 * @property int $create_time_int 添加时间，时间戳
 * @property string $change_time 状态改变的时间
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersPaymentCode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersPaymentCode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersPaymentCode query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersPaymentCode whereChangeTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersPaymentCode whereCreateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersPaymentCode whereCreateTimeInt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersPaymentCode wherePcode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersPaymentCode wherePyid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersPaymentCode whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersPaymentCode whereUserId($value)
 * @mixin \Eloquent
 */
class UsersPaymentCode extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */

    protected $fillable = [
        "user_id",
        "pcode",
        "status",
        "create_time",
        "create_time_int",
        "change_time",
    ];
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ecs_user_payment_code';
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
    protected $primaryKey = 'pyid';
    /**
     * 模型日期列的存储格式
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
