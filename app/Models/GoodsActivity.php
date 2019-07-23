<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\GoodsActivity
 *
 * @property int $act_id
 * @property string $act_name
 * @property string $act_desc
 * @property int $act_type
 * @property int $goods_id
 * @property int $product_id
 * @property string $goods_name
 * @property int $start_time
 * @property int $end_time
 * @property int $is_finished
 * @property string $ext_info
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsActivity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsActivity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsActivity query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsActivity whereActDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsActivity whereActId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsActivity whereActName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsActivity whereActType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsActivity whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsActivity whereExtInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsActivity whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsActivity whereGoodsName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsActivity whereIsFinished($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsActivity whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsActivity whereStartTime($value)
 * @mixin \Eloquent
 */
class GoodsActivity extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */

    protected $fillable = [
        "act_name",
        "act_desc",
        "act_type",
        "goods_id",
        "product_id",
        "goods_name",
        "start_time",
        "end_time",
        "is_finished",
        "ext_info",
    ];
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ecs_goods_activity';
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
    protected $primaryKey = 'act_id';
    /**
     * 模型日期列的存储格式
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
