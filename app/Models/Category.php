<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Category
 *
 * @property int $cat_id
 * @property string $cat_name
 * @property string $keywords
 * @property string $cat_desc
 * @property int $parent_id
 * @property int $sort_order
 * @property string $template_file
 * @property string $measure_unit
 * @property int $show_in_nav
 * @property string $style
 * @property int $is_show
 * @property int $grade
 * @property string $filter_attr
 * @property string $ads_img1
 * @property string $ads_img1_pc
 * @property string $ads_img1_pc2
 * @property string $city
 * @property int|null $is_send_msg 是否发送短信给门店及客服手机:1是,2否
 * @property int $is_can_back 客户可自行退单:1是，2否
 * @property int $sell_goods_count 商品销售数量
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereAdsImg1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereAdsImg1Pc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereAdsImg1Pc2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereCatDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereCatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereCatName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereFilterAttr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereIsCanBack($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereIsSendMsg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereIsShow($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereMeasureUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereSellGoodsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereShowInNav($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereStyle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereTemplateFile($value)
 * @mixin \Eloquent
 */
class Category extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */

    protected $fillable = [
        'cat_name',
        'keywords',
        'cat_desc',
        'parent_id',
        'sort_order',
        'template_file',
        'measure_unit',
        'show_in_nav',
        'style',
        'is_show',
        'grade',
        'filter_attr',
        'ads_img1',
        'ads_img1_pc',
        'ads_img1_pc2',
        'city',
        'is_send_msg',
        'is_can_back',
    ];
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ecs_category';
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
    protected $primaryKey = 'cat_id';
    /**
     * 模型日期列的存储格式
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
