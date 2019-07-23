<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ShopConfig
 *
 * @property int $id
 * @property int $parent_id
 * @property string $code
 * @property string $type
 * @property string $store_range
 * @property string $store_dir
 * @property string $value
 * @property int $sort_order
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShopConfig newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShopConfig newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShopConfig query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShopConfig whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShopConfig whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShopConfig whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShopConfig whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShopConfig whereStoreDir($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShopConfig whereStoreRange($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShopConfig whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShopConfig whereValue($value)
 * @mixin \Eloquent
 */
class ShopConfig extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */

    protected $fillable = [
        "parent_id",
        "code",
        "type",
        "store_range",
        "store_dir",
        "value",
        "sort_order",
    ];
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ecs_shop_config';
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
