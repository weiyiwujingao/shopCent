<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ExpressDetail
 *
 * @property int $id
 * @property string $order_sn 订单号
 * @property string $ex_num 快递单号
 * @property string|null $ex_cnt 物流详细信息
 * @property string $create_time 添加时间
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExpressDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExpressDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExpressDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExpressDetail whereCreateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExpressDetail whereExCnt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExpressDetail whereExNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExpressDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExpressDetail whereOrderSn($value)
 * @mixin \Eloquent
 */
class ExpressDetail extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */

    protected $fillable = [
    	'order_sn',
    	'ex_num',
    	'ex_cnt',
    	'create_time',
	];
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ecs_express_detail';
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
  //  protected $primaryKey = 'id';
    /**
     * 模型日期列的存储格式
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
