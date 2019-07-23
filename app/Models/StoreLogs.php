<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\StoreLogs
 *
 * @property int $id 自增id
 * @property int $gs_id 商户id
 * @property int $business 业务类型
 * @property string $table 操作表对象
 * @property int $type 操作类型：增1改2删3
 * @property string $ip ip地址
 * @property string|null $comment 操作说明
 * @property string $create_time 创建时间
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoreLogs newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoreLogs newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoreLogs query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoreLogs whereBusiness($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoreLogs whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoreLogs whereCreateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoreLogs whereGsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoreLogs whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoreLogs whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoreLogs whereTable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoreLogs whereType($value)
 * @mixin \Eloquent
 */
class StoreLogs extends Model
{
	/**
	 * 字段赋值白名单
	 *
	 * @var array
	 */

	protected $fillable = [
		'gs_id',
		'business',
		'table',
		'type',
		'ip',
		'comment',
		'create_time',
	];
	/**
	 * 关联到模型的数据表
	 *
	 * @var string
	 */
	protected $table = 'store_action_logs';
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
