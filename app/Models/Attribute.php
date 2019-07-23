<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Attribute
 *
 * @property int $attr_id
 * @property int $cat_id
 * @property string $attr_name
 * @property int $attr_input_type
 * @property int $attr_type
 * @property string $attr_values
 * @property int $attr_index
 * @property int $sort_order
 * @property int $is_linked
 * @property int $attr_group
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attribute newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attribute newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attribute query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attribute whereAttrGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attribute whereAttrId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attribute whereAttrIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attribute whereAttrInputType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attribute whereAttrName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attribute whereAttrType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attribute whereAttrValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attribute whereCatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attribute whereIsLinked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attribute whereSortOrder($value)
 * @mixin \Eloquent
 */
class Attribute extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */

    protected $fillable = [
        "cat_id",
        "attr_name",
        "attr_input_type",
        "attr_type",
        "attr_values",
        "attr_index",
        "sort_order",
        "is_linked",
        "attr_group",
    ];
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ecs_attribute';
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
    protected $primaryKey = 'attr_id';
    /**
     * 模型日期列的存储格式
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
