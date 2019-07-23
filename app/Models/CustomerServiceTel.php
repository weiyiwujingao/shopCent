<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CustomerServiceTel
 *
 * @property int $id 处境id
 * @property int $city_id 城市id
 * @property string $tel 电话号码
 * @property string $wx_openid 微信openid
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomerServiceTel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomerServiceTel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomerServiceTel query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomerServiceTel whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomerServiceTel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomerServiceTel whereTel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomerServiceTel whereWxOpenid($value)
 * @mixin \Eloquent
 */
class CustomerServiceTel extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */

    protected $fillable = [
        "city_id",
        "tel",
        "wx_openid",
    ];
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ecs_customer_service_tel';
    /**
     * 表明模型是否应该被打上时间戳
     * 默认情况下，Eloquent 期望created_at和updated_at已经存在于数据表中，如果你不想要这些 Laravel 自动管理的列，在模型类中设置$timestamps属性为false：
     * @var bool
     */
    public $timestamps = true;
    /**
     * 关联到模型的数据表
     * Eloquent 默认每张表的主键名为id，你可以在模型类中定义一个$primaryKey属性来覆盖该约定
     *
     * @var string
     */
  //  protected $primaryKey = 'id';
    /**
     * 模型日期列的存储格式
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
