<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BrandExtend
 *
 * @property int $id 自增id
 * @property int $brand_id 品牌id
 * @property int $cat_id 分类id,ecs_category表cat_id
 * @property string|null $gs_extract 提取时间
 * @property string|null $gs_desc 详细描述
 * @property string|null $update_time 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BrandExtend newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BrandExtend newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BrandExtend query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BrandExtend whereBrandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BrandExtend whereCatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BrandExtend whereGsDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BrandExtend whereGsExtract($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BrandExtend whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BrandExtend whereUpdateTime($value)
 * @mixin \Eloquent
 */
class BrandExtend extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */

    protected $fillable = [
        'brand_id',
        'cat_id',
        'gs_extract',
        'gs_desc',
        'update_time',
    ];
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ecs_brand_extend';
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
