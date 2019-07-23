<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AD
 *
 * @property int $ad_id
 * @property int $position_id
 * @property int $media_type
 * @property string $ad_name
 * @property string $ad_link
 * @property string $wechat_link 小程序链接
 * @property string $ad_code
 * @property int $start_time
 * @property int $end_time
 * @property string $link_man
 * @property string $link_email
 * @property string $link_phone
 * @property int $click_count
 * @property int $enabled
 * @property string $city
 * @property string $details 详情介绍
 * @property int $sort 排序
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AD newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AD newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AD query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AD whereAdCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AD whereAdId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AD whereAdLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AD whereAdName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AD whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AD whereClickCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AD whereDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AD whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AD whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AD whereLinkEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AD whereLinkMan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AD whereLinkPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AD whereMediaType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AD wherePositionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AD whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AD whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AD whereWechatLink($value)
 * @mixin \Eloquent
 */
class AD extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */
	protected $fillable = [
		'position_id',
		'media_type',
		'ad_name',
		'ad_link',
		'ad_code',
		'start_time',
		'end_time',
		'link_man',
		'link_email',
		'link_phone',
		'click_count',
		'enabled',
		'city',
		'details',
		'sort',
	];
    protected $table = 'ecs_ad';
    public $timestamps = false;
    protected $primaryKey = 'ad_id';
    protected $dateFormat = 'U';
}
