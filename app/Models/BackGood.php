<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BackGood
 *
 * @property int $rec_id
 * @property int|null $back_id
 * @property int $goods_id
 * @property int $product_id
 * @property string|null $product_sn
 * @property string|null $goods_name
 * @property string|null $brand_name
 * @property string|null $goods_sn
 * @property int|null $is_real
 * @property int|null $send_number
 * @property string|null $goods_attr
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BackGood newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BackGood newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BackGood query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BackGood whereBackId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BackGood whereBrandName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BackGood whereGoodsAttr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BackGood whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BackGood whereGoodsName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BackGood whereGoodsSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BackGood whereIsReal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BackGood whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BackGood whereProductSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BackGood whereRecId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BackGood whereSendNumber($value)
 * @mixin \Eloquent
 */
class BackGood extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */

    protected $fillable = [
//        'goods_id',
//        'cat_id',
    ];
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ecs_back_goods';
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
