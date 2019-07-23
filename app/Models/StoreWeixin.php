<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\StoreWeixin
 *
 * @property int $id
 * @property int $gs_id 商户id
 * @property string $openid 微信openid
 * @property string $nickname 微信昵称
 * @property string $headimgurl 微信用户头像地址
 * @property string $create_time 添加时间
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoreWeixin newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoreWeixin newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoreWeixin query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoreWeixin whereCreateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoreWeixin whereGsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoreWeixin whereHeadimgurl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoreWeixin whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoreWeixin whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoreWeixin whereOpenid($value)
 * @mixin \Eloquent
 */
class StoreWeixin extends Model
{
	/**
	 * 字段赋值白名单
	 *
	 * @var array
	 */

	protected $fillable = [
		'gs_id',
		'openid',
		'nickname',
		'headimgurl',
		'create_time',
	];
	/**
	 * 关联到模型的数据表
	 *
	 * @var string
	 */
	protected $table = 'ecs_stores_weixin';
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
