<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Region
 *
 * @property int $region_id
 * @property int $parent_id
 * @property string $region_name
 * @property int $region_type
 * @property int $agency_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Region newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Region newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Region query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Region whereAgencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Region whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Region whereRegionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Region whereRegionName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Region whereRegionType($value)
 * @mixin \Eloquent
 */
class Region extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */

    protected $fillable = [
        'parent_id',
        'region_name',
        'region_type',
        'agency_id',
    ];
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ecs_region';
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
    protected $primaryKey = 'region_id';
    /**
     * 模型日期列的存储格式
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
