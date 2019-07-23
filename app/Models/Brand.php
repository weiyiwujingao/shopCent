<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Brand
 *
 * @property int $brand_id 地区排序
 * @property string $brand_name
 * @property string $first_letter 首字母
 * @property string $brand_logo
 * @property string $brand_desc
 * @property string $site_url
 * @property int $sort_order
 * @property int $is_show
 * @property string $region_id 地区ID串
 * @property string $gs_extract_16
 * @property string $gs_desc_16
 * @property string $gs_extract_17
 * @property string $gs_desc_17
 * @property string $gs_extract_18
 * @property string $gs_desc_18
 * @property string $gs_extract_19
 * @property string $gs_desc_19
 * @property string $gs_extract_24
 * @property string $gs_desc_24
 * @property int $reserve_type
 * @property string $reserve_other 其它预定类型
 * @property int $reserve_hours 提前预定小时数
 * @property int $support_shipping
 * @property string|null $region_sort 地区(东莞)排序
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand whereBrandDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand whereBrandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand whereBrandLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand whereBrandName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand whereFirstLetter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand whereGsDesc16($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand whereGsDesc17($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand whereGsDesc18($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand whereGsDesc19($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand whereGsDesc24($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand whereGsExtract16($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand whereGsExtract17($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand whereGsExtract18($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand whereGsExtract19($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand whereGsExtract24($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand whereIsShow($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand whereRegionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand whereRegionSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand whereReserveHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand whereReserveOther($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand whereReserveType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand whereSiteUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand whereSupportShipping($value)
 * @mixin \Eloquent
 */
class Brand extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */

    protected $fillable = [
//        'goods_id',
//        'cat_id',
    ];
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ecs_brand';
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
  //  protected $primaryKey = 'cat_id';
    /**
     * 模型日期列的存储格式
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
