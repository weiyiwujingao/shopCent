<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\HomeNav
 *
 * @property int $id
 * @property string $name 名称
 * @property string $img 图标地址
 * @property string $url 链接地址
 * @property string $wechat_url 小程序链接地址
 * @property int $sort 排序:数字越大越靠前
 * @property int $enbale 是否启用:1是，0否
 * @property string $update_time 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\HomeNav newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\HomeNav newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\HomeNav query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\HomeNav whereEnbale($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\HomeNav whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\HomeNav whereImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\HomeNav whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\HomeNav whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\HomeNav whereUpdateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\HomeNav whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\HomeNav whereWechatUrl($value)
 * @mixin \Eloquent
 */
class HomeNav extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */

    protected $fillable = [
        'name',
        'img',
        'url',
        'sort',
        'enbale',
        'update_time',
    ];
	/**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ecs_home_nav';
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
