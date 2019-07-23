<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\GoodsStock
 *
 * @property int $st_id 自增id
 * @property int $gs_id 商户id
 * @property int $goods_id 商品id
 * @property string $attr_ids 商品属性id，多个-连接
 * @property int $num 库存数量
 * @property int $num_promotion 促销数量
 * @property string $update_time 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsStock newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsStock newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsStock query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsStock whereAttrIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsStock whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsStock whereGsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsStock whereNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsStock whereNumPromotion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsStock whereStId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsStock whereUpdateTime($value)
 * @mixin \Eloquent
 */
class GoodsStock extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */

    protected $fillable = [
        'gs_id',
        'goods_id',
        'attr_ids',
        'num',
        'num_promotion',
        'update_time',
    ];
   /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ecs_goods_stock';
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
    protected $primaryKey = 'st_id';
    /**
     * 模型日期列的存储格式
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
