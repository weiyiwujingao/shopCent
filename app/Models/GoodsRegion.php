<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\GoodsRegion
 *
 * @property int $gr_id
 * @property string $gr_name
 * @property int $sort_order
 * @property int $parent_id
 * @property int $type
 * @property int $enable 是否启用:1启用,0不启用
 * @property int $brand_count 品牌个数
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsRegion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsRegion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsRegion query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsRegion whereBrandCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsRegion whereEnable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsRegion whereGrId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsRegion whereGrName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsRegion whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsRegion whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsRegion whereType($value)
 * @mixin \Eloquent
 */
class GoodsRegion extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */

    protected $fillable = [
        "gr_name",
        "sort_order",
        "parent_id",
        "type",
        "enable",
        "brand_count",
    ];
	/**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ecs_goods_region';
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
    protected $primaryKey = 'gr_id';
    /**
     * 模型日期列的存储格式
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
