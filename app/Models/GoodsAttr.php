<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\GoodsAttr
 *
 * @property int $goods_attr_id
 * @property int $goods_id
 * @property int $attr_id
 * @property string $attr_value
 * @property string $attr_price
 * @property string $original_price 原价
 * @property float $settle_price 属性结算价 auto(colin)
 * @property string $img 图片地址
 * @property string $thumb_img 缩略图地址
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsAttr newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsAttr newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsAttr query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsAttr whereAttrId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsAttr whereAttrPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsAttr whereAttrValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsAttr whereGoodsAttrId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsAttr whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsAttr whereImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsAttr whereOriginalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsAttr whereSettlePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsAttr whereThumbImg($value)
 * @mixin \Eloquent
 */
class GoodsAttr extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */

    protected $fillable = [
        "goods_id",
        "attr_id",
        "attr_value",
        "attr_price",
        "img",
        "thumb_img",
    ];
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ecs_goods_attr';
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
    protected $primaryKey = 'goods_attr_id';
    /**
     * 模型日期列的存储格式
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
