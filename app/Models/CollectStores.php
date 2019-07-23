<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CollectStores
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $gs_id 门店id,ecs_goods_stores表id
 * @property string $create_time 添加时间
 * @property-read \App\Models\StoresUser $store
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CollectStores newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CollectStores newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CollectStores query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CollectStores whereCreateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CollectStores whereGsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CollectStores whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CollectStores whereUserId($value)
 * @mixin \Eloquent
 */
class CollectStores extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */

    protected $fillable = [
        'user_id',
        'gs_id',
    ];
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ecs_collect_stores';
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
     protected $primaryKey = 'id';
    /**
     * 模型日期列的存储格式
     *
     * @var string
     */
    protected $dateFormat = 'U';

	/**
	 * 店铺信息
	 * @author: colin
	 * @date: 2019/1/10 10:47
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
    public function store()
	{
		return $this->belongsTo(StoresUser::class,'gs_id','gs_id');
	}
}
