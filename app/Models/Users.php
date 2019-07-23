<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Users
 *
 * @property int $user_id
 * @property string $email
 * @property string $user_name 用户名
 * @property string $password
 * @property string $question
 * @property string $answer
 * @property int $sex 性别:1男,2女
 * @property string $birthday
 * @property float $user_money
 * @property float $frozen_money
 * @property int $pay_points
 * @property int $rank_points
 * @property int $address_id 现默认为1，如果开发配送地址 改为0
 * @property int $reg_time
 * @property int $last_login
 * @property string $last_time
 * @property string $last_ip
 * @property int $visit_count
 * @property int $user_rank
 * @property int $is_special
 * @property string|null $ec_salt
 * @property string $salt
 * @property int $parent_id
 * @property int $flag
 * @property string $alias
 * @property string $msn
 * @property string $qq
 * @property string $office_phone
 * @property string $home_phone
 * @property string $mobile_phone
 * @property int $is_validated
 * @property float $credit_line
 * @property string|null $passwd_question
 * @property string|null $passwd_answer
 * @property string $faces 头像图片地址
 * @property int $user_money_date 余额到期使用时间
 * @property int $bonus_id 幸福卡ID
 * @property string $nickname
 * @property string $bonus_company
 * @property string $device_sn
 * @property string $device_type
 * @property int $order_count
 * @property string|null $login_device 登录设备md5字符
 * @property string $login_token 登录token
 * @property string $wx_openid 微信openid
 * @property int $read_tips 是否已读提示标识: 1已读
 * @property float $commission 用户获取佣金
 * @property int $source 注册来源
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users whereAddressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users whereAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users whereBirthday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users whereBonusCompany($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users whereBonusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users whereCommission($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users whereCreditLine($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users whereDeviceSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users whereDeviceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users whereEcSalt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users whereFaces($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users whereFlag($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users whereFrozenMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users whereHomePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users whereIsSpecial($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users whereIsValidated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users whereLastIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users whereLastLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users whereLastTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users whereLoginDevice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users whereLoginToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users whereMobilePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users whereMsn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users whereOfficePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users whereOrderCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users wherePasswdAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users wherePasswdQuestion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users wherePayPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users whereQq($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users whereQuestion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users whereRankPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users whereReadTips($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users whereRegTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users whereSalt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users whereSex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users whereUserMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users whereUserMoneyDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users whereUserName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users whereUserRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users whereVisitCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users whereWxOpenid($value)
 * @mixin \Eloquent
 */
class Users extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */

    protected $fillable = [
        "user_id",
        "email",
        "user_name",
        "password",
        "question",
        "answer",
        "sex",
        "birthday",
        "user_money",
        "frozen_money",
        "pay_points",
        "rank_points",
        "address_id",
        "reg_time",
        "last_ip",
        "visit_count",
        "user_rank",
        "is_special",
        "ec_salt",
        "salt",
        "parent_id",
        "flag",
        "alias",
        "msn",
        "qq",
        "office_phone",
        "home_phone",
        "mobile_phone",
        "is_validated",
        "credit_line",
        "passwd_question",
        "passwd_answer",
        "faces",
        "user_money_date",
        "bonus_id",
        "nickname",
        "bonus_company",
        "device_sn",
        "device_type",
        "order_count",
        "login_device",
        "login_token",
        "wx_openid",
        "read_tips",
    ];
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ecs_users';
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
    protected $primaryKey = 'user_id';
    /**
     * 模型日期列的存储格式
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
