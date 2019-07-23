<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Products
 *
 * @property int $product_id
 * @property int $goods_id
 * @property string|null $goods_attr
 * @property string|null $product_sn
 * @property int|null $product_number
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products whereGoodsAttr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products whereProductNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products whereProductSn($value)
 * @mixin \Eloquent
 */
class Products extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */

    protected $fillable = [
        'product_id',
        'goods_id',
        'goods_attr',
        'product_sn',
        'product_number',
    ];
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ecs_products';
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
    protected $primaryKey = 'product_id';
    /**
     * 模型日期列的存储格式
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
