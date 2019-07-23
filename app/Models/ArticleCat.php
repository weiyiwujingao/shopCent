<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ArticleCat
 *
 * @property int $cat_id
 * @property string $cat_name
 * @property int $cat_type
 * @property string $img 图标地址
 * @property string $keywords
 * @property string $cat_desc
 * @property int $sort_order
 * @property int $show_in_nav
 * @property int $parent_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Article[] $list
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ArticleCat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ArticleCat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ArticleCat query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ArticleCat whereCatDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ArticleCat whereCatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ArticleCat whereCatName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ArticleCat whereCatType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ArticleCat whereImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ArticleCat whereKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ArticleCat whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ArticleCat whereShowInNav($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ArticleCat whereSortOrder($value)
 * @mixin \Eloquent
 */
class ArticleCat extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */

    protected $fillable = [
        'cat_name',
        'cat_type',
        'cat_desc',
        'keywords',
        'sort_order',
        'show_in_nav',
        'parent_id',
        'img',
    ];
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ecs_article_cat';
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

	/***
	 * 对应文章列表
	 * @author: colin
	 * @date: 2019/1/15 8:47
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
    public function list()
	{
		return $this->hasMany(Article::class,'cat_id','cat_id')->where('is_open','1')->orderBy('article_id','desc');
	}
}
