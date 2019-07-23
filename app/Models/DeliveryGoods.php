<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\DeliveryGoods
 *
 * @property int $rec_id
 * @property int $delivery_id
 * @property int $goods_id
 * @property int|null $product_id
 * @property string|null $product_sn
 * @property string|null $goods_name
 * @property string|null $brand_name
 * @property string|null $goods_sn
 * @property int|null $is_real
 * @property string|null $extension_code
 * @property int|null $parent_id
 * @property int|null $send_number
 * @property string|null $goods_attr
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeliveryGoods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeliveryGoods newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeliveryGoods query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeliveryGoods whereBrandName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeliveryGoods whereDeliveryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeliveryGoods whereExtensionCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeliveryGoods whereGoodsAttr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeliveryGoods whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeliveryGoods whereGoodsName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeliveryGoods whereGoodsSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeliveryGoods whereIsReal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeliveryGoods whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeliveryGoods whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeliveryGoods whereProductSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeliveryGoods whereRecId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeliveryGoods whereSendNumber($value)
 * @mixin \Eloquent
 */
class DeliveryGoods extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */

    protected $fillable = [
		'delivery_id',
		'goods_id',
		'product_id',
		'product_sn',
		'goods_name',
		'brand_name',
		'goods_sn',
		'is_real',
		'extension_code',
		'parent_id',
		'send_number',
		'goods_attr',
	];
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ecs_delivery_goods';
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
