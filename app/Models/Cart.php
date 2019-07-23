<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Cart
 *
 * @property int $rec_id
 * @property int $user_id
 * @property string $session_id
 * @property int $goods_id
 * @property string $goods_sn
 * @property int $product_id
 * @property string $goods_name
 * @property float $market_price
 * @property float $goods_price
 * @property int $goods_number
 * @property string $goods_attr
 * @property int $is_real
 * @property string $extension_code
 * @property int $parent_id
 * @property int $rec_type
 * @property int $is_gift
 * @property int $is_shipping
 * @property int $can_handsel
 * @property string $goods_attr_id
 * @property int|null $is_check
 * @property int $goods_brand_id
 * @property int $stores_user_id
 * @property int $cart_stores_id 购物车环节选取门店ID
 * @property int $exceed_promote_num
 * @property float $exceed_promote_price
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cart newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cart newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cart query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cart whereCanHandsel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cart whereCartStoresId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cart whereExceedPromoteNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cart whereExceedPromotePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cart whereExtensionCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cart whereGoodsAttr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cart whereGoodsAttrId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cart whereGoodsBrandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cart whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cart whereGoodsName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cart whereGoodsNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cart whereGoodsPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cart whereGoodsSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cart whereIsCheck($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cart whereIsGift($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cart whereIsReal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cart whereIsShipping($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cart whereMarketPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cart whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cart whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cart whereRecId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cart whereRecType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cart whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cart whereStoresUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cart whereUserId($value)
 * @mixin \Eloquent
 */
class Cart extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */

    protected $fillable = [
        'user_id',
        'session_id',
        'goods_id',
        'goods_sn',
        'product_id',
        'goods_name',
        'market_price',
        'goods_price',
        'goods_number',
        'goods_attr',
        'is_real',
        'extension_code',
        'parent_id',
        'rec_type',
        'is_gift',
        'is_shipping',
        'can_handsel',
        'goods_attr_id',
        'is_check',
        'goods_brand_id',
        'stores_user_id',
        'cart_stores_id',
        'exceed_promote_num',
        'exceed_promote_price',
    ];
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ecs_cart';
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
