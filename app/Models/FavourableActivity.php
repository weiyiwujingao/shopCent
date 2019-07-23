<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FavourableActivity
 *
 * @property int $act_id
 * @property string $act_name
 * @property int $start_time
 * @property int $end_time
 * @property string $user_rank
 * @property int $act_range
 * @property string $act_range_ext
 * @property float $min_amount
 * @property float $max_amount
 * @property int $act_type
 * @property float $act_type_ext
 * @property string $gift
 * @property int $sort_order
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FavourableActivity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FavourableActivity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FavourableActivity query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FavourableActivity whereActId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FavourableActivity whereActName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FavourableActivity whereActRange($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FavourableActivity whereActRangeExt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FavourableActivity whereActType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FavourableActivity whereActTypeExt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FavourableActivity whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FavourableActivity whereGift($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FavourableActivity whereMaxAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FavourableActivity whereMinAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FavourableActivity whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FavourableActivity whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FavourableActivity whereUserRank($value)
 * @mixin \Eloquent
 */
class FavourableActivity extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */

    protected $fillable = [
        "act_name",
        "act_name",
        "start_time",
        "end_time",
        "user_rank",
        "act_range",
        "act_range_ext",
        "min_amount",
        "max_amount",
        "act_type",
        "act_type_ext",
        "gift",
        "sort_order",
    ];
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ecs_favourable_activity';
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
