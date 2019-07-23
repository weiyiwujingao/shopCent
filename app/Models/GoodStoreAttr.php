<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\GoodStoreAttr
 *
 * @property int $id 主键id
 * @property int $gs_id 商户id
 * @property int $goods_id 商品id
 * @property int $sale_status 销售状态：1上架，2下架
 * @property int $sort 排序权重值
 * @property int $pickup_mode 提货方式：1门店自提；2商家配送；其他
 * @property int $reserve_hours 提前预定小时数
 * @property string $create_time 添加时间
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodStoreAttr newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodStoreAttr newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodStoreAttr query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodStoreAttr whereCreateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodStoreAttr whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodStoreAttr whereGsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodStoreAttr whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodStoreAttr wherePickupMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodStoreAttr whereReserveHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodStoreAttr whereSaleStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodStoreAttr whereSort($value)
 * @mixin \Eloquent
 */
class GoodStoreAttr extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */

    protected $fillable = [
        "gs_id",
        "goods_id",
        "sale_status",
        "sort",
        "create_time",
    ];
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ecs_goods_stores_attribute';
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
   // protected $primaryKey = '';
    /**
     * 模型日期列的存储格式
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
