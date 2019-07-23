<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Card
 *
 * @property int $card_id
 * @property string $card_name
 * @property string $card_img
 * @property float $card_fee
 * @property float $free_money
 * @property string $card_desc
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Card newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Card newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Card query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Card whereCardDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Card whereCardFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Card whereCardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Card whereCardImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Card whereCardName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Card whereFreeMoney($value)
 * @mixin \Eloquent
 */
class Card extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */

    protected $fillable = [
        "card_name",
        "card_img",
        "card_fee",
        "free_money",
        "card_desc",

    ];
	/**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ecs_card';
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
    protected $primaryKey = 'card_id';
    /**
     * 模型日期列的存储格式
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
