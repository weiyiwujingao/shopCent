<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OrderGoods
 *
 * @property int $rec_id
 * @property int $order_id 订单id
 * @property int $goods_id 商品id
 * @property string $goods_name 商品名称
 * @property string $goods_sn
 * @property int $product_id
 * @property int $goods_number
 * @property float $market_price
 * @property float $goods_price
 * @property string $goods_attr
 * @property int $send_number
 * @property int $is_real
 * @property string $extension_code
 * @property int $parent_id
 * @property int $is_gift
 * @property string $goods_attr_id
 * @property float $exceed_promote_price
 * @property int $exceed_promote_num
 * @property float $settle_discount 当时的结算折扣
 * @property float $settle_price 结算价格
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderGoods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderGoods newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderGoods query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderGoods whereExceedPromoteNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderGoods whereExceedPromotePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderGoods whereExtensionCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderGoods whereGoodsAttr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderGoods whereGoodsAttrId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderGoods whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderGoods whereGoodsName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderGoods whereGoodsNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderGoods whereGoodsPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderGoods whereGoodsSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderGoods whereIsGift($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderGoods whereIsReal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderGoods whereMarketPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderGoods whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderGoods whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderGoods whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderGoods whereRecId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderGoods whereSendNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderGoods whereSettleDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderGoods whereSettlePrice($value)
 * @mixin \Eloquent
 */
class OrderGoods extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */

    protected $fillable = [
        "order_id",
        "goods_id",
        "goods_name",
        "goods_sn",
        "product_id",
        "goods_number",
        "market_price",
        "goods_price",
        "goods_attr",
        "send_number",
        "is_real",
        "extension_code",
        "parent_id",
        "is_gift",
        "goods_attr_id",
        "exceed_promote_price",
        "exceed_promote_num",
    ];
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ecs_order_goods';
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
    protected $primaryKey = 'rec_id';
    /**
     * 模型日期列的存储格式
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
