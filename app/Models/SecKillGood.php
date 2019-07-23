<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 05 Jun 2019 19:49:50 +0800.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class SecKillGood
 *
 * @property int $id
 * @property int $sec_id
 * @property int $goods_id
 * @property string $stores_id
 * @property int $city_id
 * @property int $sort
 * @property bool $status
 * @property \Carbon\Carbon $start_time
 * @property \Carbon\Carbon $create_time
 * @package App\Models
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SecKillGood newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SecKillGood newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SecKillGood query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SecKillGood whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SecKillGood whereCreateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SecKillGood whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SecKillGood whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SecKillGood whereSecId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SecKillGood whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SecKillGood whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SecKillGood whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SecKillGood whereStoresId($value)
 * @mixin \Eloquent
 */
class SecKillGood extends Eloquent
{
	public $timestamps = false;

	protected $casts = [
		'sec_id' => 'int',
		'goods_id' => 'int',
		'city_id' => 'int',
		'sort' => 'int',
		'status' => 'bool'
	];

	protected $dates = [
		'start_time',
		'create_time'
	];

	protected $fillable = [
		'sec_id',
		'goods_id',
		'stores_id',
		'city_id',
		'sort',
		'status',
		'start_time',
		'create_time'
	];
}
