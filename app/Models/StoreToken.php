<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\StoreToken
 *
 * @property int $id
 * @property int $gs_id 商户id
 * @property string $token token值
 * @property \Illuminate\Support\Carbon $updated_at 更新时间
 * @property \Illuminate\Support\Carbon $created_at 创建时间
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoreToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoreToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoreToken query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoreToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoreToken whereGsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoreToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoreToken whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoreToken whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class StoreToken extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */

    protected $fillable = [
        "gs_id",
        "token",
    ];
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'goods_store_token';
    /**
     * 表明模型是否应该被打上时间戳
     * 默认情况下，Eloquent 期望created_at和updated_at已经存在于数据表中，如果你不想要这些 Laravel 自动管理的列，在模型类中设置$timestamps属性为false：
     * @var bool
     */
    public $timestamps = true;
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
