<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Article
 *
 * @property int $article_id
 * @property int $cat_id
 * @property string $title
 * @property string $content
 * @property string $author
 * @property string $author_email
 * @property string $keywords
 * @property int $article_type
 * @property int $is_open
 * @property int $add_time
 * @property string $file_url
 * @property int $open_type
 * @property string $link
 * @property string|null $description
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article whereAddTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article whereArticleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article whereArticleType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article whereAuthor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article whereAuthorEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article whereCatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article whereFileUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article whereIsOpen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article whereKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article whereOpenType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article whereTitle($value)
 * @mixin \Eloquent
 */
class Article extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */

    protected $fillable = [
        'article_id',
        'cat_id',
        'title',
        'content',
        'author',
        'author_email',
        'keywords',
        'article_type',
        'is_open',
        'add_time',
        'file_url',
        'open_type',
        'link',
        'description',
    ];
   /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ecs_article';
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
    protected $primaryKey = 'article_id';
    /**
     * 模型日期列的存储格式
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
