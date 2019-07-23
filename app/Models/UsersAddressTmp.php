<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UsersAddressTmp
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property string $address 收货地址
 * @property string $uname 收货人
 * @property string $tel 联系电话
 * @property string $create_time 添加时间
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersAddressTmp newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersAddressTmp newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersAddressTmp query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersAddressTmp whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersAddressTmp whereCreateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersAddressTmp whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersAddressTmp whereTel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersAddressTmp whereUname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersAddressTmp whereUserId($value)
 * @mixin \Eloquent
 */
class UsersAddressTmp extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */

    protected $fillable = [
        "user_id",
        "address",
        "uname",
        "tel",
        "create_time",
    ];
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ecs_user_address_tmp';
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
//    protected $primaryKey = 'id';
    /**
     * 模型日期列的存储格式
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
