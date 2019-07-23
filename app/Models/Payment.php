<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Payment
 *
 * @property int $pay_id
 * @property string $pay_code
 * @property string $pay_name
 * @property string $pay_fee
 * @property string $pay_desc
 * @property int $pay_order
 * @property string $pay_config
 * @property int $enabled
 * @property int $is_cod
 * @property int $is_online
 * @property string $pay_logo
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment whereIsCod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment whereIsOnline($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment wherePayCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment wherePayConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment wherePayDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment wherePayFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment wherePayId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment wherePayLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment wherePayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment wherePayOrder($value)
 * @mixin \Eloquent
 */
class Payment extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */

    protected $fillable = [
        "pay_id",
        "goods_id",
        "sale_status",
        "create_time",
    ];
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ecs_payment';
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
   // protected $primaryKey = '';
    /**
     * 模型日期列的存储格式
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
