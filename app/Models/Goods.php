<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Goods
 *
 * @property int $goods_id 商品id
 * @property int $cat_id 分类
 * @property string $goods_sn 货号
 * @property string $goods_name
 * @property string $goods_name_style
 * @property int $click_count
 * @property int $brand_id 品牌id
 * @property string $provider_name
 * @property int $goods_number
 * @property float $goods_weight
 * @property float $market_price 市场价格
 * @property float $settle_price 结算价格，auto(colin)
 * @property float $shop_price 销售价格
 * @property float $promote_price
 * @property int $promote_start_date
 * @property int $promote_end_date
 * @property int $warn_number
 * @property string $remind_msg 用户提示信息
 * @property string $keywords
 * @property string $goods_brief
 * @property string $goods_desc
 * @property string $goods_thumb
 * @property string $goods_img
 * @property string $original_img
 * @property string|null $imgs 产品图片(多个)
 * @property int $is_real
 * @property string $extension_code
 * @property int $is_on_sale 是否上架
 * @property int $is_alone_sale
 * @property int $is_shipping
 * @property int $integral
 * @property int $add_time
 * @property int $sort_order 推荐排序
 * @property int $is_delete
 * @property int $is_best 是否精品
 * @property int $is_new 新品
 * @property int $is_hot 热销
 * @property int $is_promote
 * @property int $bonus_type_id
 * @property int $last_update
 * @property int $goods_type
 * @property string $seller_note
 * @property int $give_integral
 * @property int $rank_integral
 * @property int|null $suppliers_id
 * @property int|null $is_check
 * @property int $aff_bid1 口味
 * @property int $aff_bid2 人群
 * @property float $shop_price_zy 直营店价格
 * @property float $shop_price_jm 加盟店价格
 * @property float $settle_discount 结算折扣 auto(colin)
 * @property int $saleqt
 * @property int $promote_num
 * @property int $pickup_mode 提货方式：1门店自提；2商家配送；其他
 * @property int $reserve_hours 提前预定小时数
 * @property int $pay_type_limit 是否限定支付方式:1是，0否
 * @property string $pay_types 支付方式id，多个逗号隔开：1幸福券，2余额，3微信,4支付宝
 * @property int $free_post 是否包邮：1是
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereAddTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereAffBid1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereAffBid2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereBonusTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereBrandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereCatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereClickCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereExtensionCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereFreePost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereGiveIntegral($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereGoodsBrief($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereGoodsDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereGoodsImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereGoodsName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereGoodsNameStyle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereGoodsNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereGoodsSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereGoodsThumb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereGoodsType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereGoodsWeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereImgs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereIntegral($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereIsAloneSale($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereIsBest($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereIsCheck($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereIsDelete($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereIsHot($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereIsNew($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereIsOnSale($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereIsPromote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereIsReal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereIsShipping($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereLastUpdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereMarketPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereOriginalImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods wherePayTypeLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods wherePayTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods wherePickupMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods wherePromoteEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods wherePromoteNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods wherePromotePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods wherePromoteStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereProviderName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereRankIntegral($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereRemindMsg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereReserveHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereSaleqt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereSellerNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereSettleDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereSettlePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereShopPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereShopPriceJm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereShopPriceZy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereSuppliersId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereWarnNumber($value)
 * @mixin \Eloquent
 */
class Goods extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */

    protected $fillable = [
        'cat_id',
        'goods_sn',
        'goods_name',
        'goods_name_style',
        'click_count',
        'brand_id',
        'provider_name',
        'goods_number',
        'goods_weight',
        'market_price',
        'shop_price',
        'promote_price',
        'promote_start_date',
        'promote_end_date',
        'warn_number',
        'remind_msg',
        'keywords',
        'goods_brief',
        'goods_desc',
        'goods_thumb',
        'goods_img',
        'original_img',
        'is_real',
        'extension_code',
        'is_on_sale',
        'is_alone_sale',
        'is_shipping',
        'integral',
        'add_time',
        'sort_order',
        'is_delete',
        'is_best',
        'is_new',
        'is_hot',
        'is_promote',
        'bonus_type_id',
        'last_update',
        'goods_type',
        'seller_note',
        'give_integral',
        'rank_integral',
        'suppliers_id',
        'is_check',
        'aff_bid1',
        'aff_bid2',
        'shop_price_zy',
        'shop_price_jm',
        'saleqt',
        'promote_num',
        'pickup_mode',
    ];
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ecs_goods';
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
    protected $primaryKey = 'goods_id';
    /**
     * 模型日期列的存储格式
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
