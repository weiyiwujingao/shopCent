<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\GooddCat
 *
 * @property int $goods_id
 * @property int $cat_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GooddCat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GooddCat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GooddCat query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GooddCat whereCatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GooddCat whereGoodsId($value)
 * @mixin \Eloquent
 */
class GooddCat extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */

    protected $fillable = [
        'goods_id',
        'cat_id',
    ];
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ecs_goods_cat';
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
  //  protected $primaryKey = 'cat_id';
    /**
     * 模型日期列的存储格式
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
