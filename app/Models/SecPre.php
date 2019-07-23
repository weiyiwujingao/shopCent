<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 05 Jun 2019 19:49:50 +0800.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class SecPre
 *
 * @property int $id
 * @property int $user_id
 * @property int $goods_id
 * @property int $kill_id
 * @property bool $status
 * @property \Carbon\Carbon $create_time
 * @package App\Models
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SecPre newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SecPre newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SecPre query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SecPre whereCreateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SecPre whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SecPre whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SecPre whereKillId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SecPre whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SecPre whereUserId($value)
 * @mixin \Eloquent
 */
class SecPre extends Eloquent
{
	protected $table = 'sec_pre';
	public $timestamps = false;

	protected $casts = [
		'user_id' => 'int',
		'goods_id' => 'int',
		'kill_id' => 'int',
		'status' => 'bool'
	];

	protected $dates = [
		'create_time'
	];

	protected $fillable = [
		'user_id',
		'goods_id',
		'kill_id',
		'status',
		'create_time'
	];
}
