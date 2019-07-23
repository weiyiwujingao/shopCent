<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SmsSendLog
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property string $mobile_number 手机号码
 * @property string $content 短信内容
 * @property string $order_sn 订单号
 * @property int $result 发送结果:1成功
 * @property string $create_time 创建时间
 * @property int $dfrom 来源:1旧版，0新版
 * @property int $wx_send 是否已发送微信提醒
 * @property string|null $send_time 微信发送时间
 * @property string $remark 备注
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SmsSendLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SmsSendLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SmsSendLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SmsSendLog whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SmsSendLog whereCreateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SmsSendLog whereDfrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SmsSendLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SmsSendLog whereMobileNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SmsSendLog whereOrderSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SmsSendLog whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SmsSendLog whereResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SmsSendLog whereSendTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SmsSendLog whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SmsSendLog whereWxSend($value)
 * @mixin \Eloquent
 */
class SmsSendLog extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */

    protected $fillable = [
        "user_id",
        "mobile_number",
        "content",
        "order_sn",
        "result",
        "create_time",
        "dfrom",
        "wx_send",
        "send_time",
        "remark",
    ];
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ecs_sms_send_log';
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
   // protected $primaryKey = 'id';
    /**
     * 模型日期列的存储格式
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
