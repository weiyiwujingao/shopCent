<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AdPosition
 *
 * @property int $position_id
 * @property string $position_name
 * @property int $ad_width
 * @property int $ad_height
 * @property string $position_desc
 * @property string $position_style
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdPosition newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdPosition newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdPosition query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdPosition whereAdHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdPosition whereAdWidth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdPosition wherePositionDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdPosition wherePositionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdPosition wherePositionName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdPosition wherePositionStyle($value)
 * @mixin \Eloquent
 */
class AdPosition extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */
	protected $fillable = [
		'position_name',
		'ad_width',
		'ad_height',
		'position_desc',
		'position_style',

	];
	protected $table = 'ecs_ad_position';
    public $timestamps = false;
    protected $primaryKey = 'position_id';
    protected $dateFormat = 'U';
}
