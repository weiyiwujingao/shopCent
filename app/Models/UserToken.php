<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 05 Jun 2019 19:49:50 +0800.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class UserToken
 *
 * @property int $id
 * @property int $user_id
 * @property string $token
 * @property int $updated_at
 * @property int $created_at
 * @package App\Models
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserToken query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserToken whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserToken whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserToken whereUserId($value)
 * @mixin \Eloquent
 */
class UserToken extends Eloquent
{
	protected $table = 'user_token';

	protected $casts = [
		'user_id' => 'int',
		'updated_at' => 'int',
		'created_at' => 'int'
	];

	protected $hidden = [
		'token'
	];

	protected $fillable = [
		'user_id',
		'token'
	];
}
