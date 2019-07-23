<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\StoresUser
 *
 * @property int $gs_id
 * @property string $gs_name 门店名称
 * @property string $gs_login_name 门店登录用户名
 * @property string $gs_login_pass
 * @property int $gs_login_salt
 * @property int $gs_brand_id 品牌ID
 * @property int $city_id 所在的城市id
 * @property int $gs_region_id 地区ID
 * @property int $gs_region_sq 商圈(地区)id
 * @property string $show_citys 展示城市，多个逗号隔开
 * @property string $gs_goods_id 产品ID串(该店拥有的产品ID)
 * @property int|null $full_free_post 是否满包邮:1是
 * @property float $free_post_money 包邮条件：达到金额数包邮
 * @property float $post_fee 省内邮费
 * @property float $post_fee_2 省外邮费
 * @property int $sort_order
 * @property int $gs_stats 状态，0关闭  1营业
 * @property string $gs_address
 * @property string $gs_notice 门店公告
 * @property float $gs_lng 经度
 * @property float $gs_lat 纬度
 * @property string $gs_contacter 门店联系人
 * @property string $gs_mobile 联系方式
 * @property int $gs_auth 是否有权限操作商户中心，0无；1有
 * @property int $is_manage 是否管理者
 * @property int $pid 父级ID
 * @property int $gs_type 门店类型
 * @property int $max_order_time
 * @property int $SaleAmount
 * @property string $store_pic
 * @property string $business_hours
 * @property int $pickup_mode
 * @property string $open_time 开门时间
 * @property string $close_time 关门时间
 * @property string $rec_goods_ids 推荐的产品id，多个逗号隔开
 * @property string $wx_openid 微信openid
 * @property int $picktime_start 取货最早时间
 * @property int $picktime_end 取货最晚时间
 * @property string $uptime_start 线下开店时间
 * @property string $uptime_end 线下关店时间
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoresUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoresUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoresUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoresUser whereBusinessHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoresUser whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoresUser whereCloseTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoresUser whereFreePostMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoresUser whereFullFreePost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoresUser whereGsAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoresUser whereGsAuth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoresUser whereGsBrandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoresUser whereGsContacter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoresUser whereGsGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoresUser whereGsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoresUser whereGsLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoresUser whereGsLng($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoresUser whereGsLoginName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoresUser whereGsLoginPass($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoresUser whereGsLoginSalt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoresUser whereGsMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoresUser whereGsName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoresUser whereGsNotice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoresUser whereGsRegionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoresUser whereGsRegionSq($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoresUser whereGsStats($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoresUser whereGsType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoresUser whereIsManage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoresUser whereMaxOrderTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoresUser whereOpenTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoresUser wherePicktimeEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoresUser wherePicktimeStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoresUser wherePickupMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoresUser wherePid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoresUser wherePostFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoresUser wherePostFee2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoresUser whereRecGoodsIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoresUser whereSaleAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoresUser whereShowCitys($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoresUser whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoresUser whereStorePic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoresUser whereUptimeEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoresUser whereUptimeStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoresUser whereWxOpenid($value)
 * @mixin \Eloquent
 */
class StoresUser extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */

    protected $fillable = [
        "gs_name",
        "gs_login_name",
        "gs_login_pass",
        "gs_login_salt",
        "gs_brand_id",
        "gs_region_id",
        "sort_order",
        "sort_order",
        "gs_stats",
        "gs_address",
        "gs_notice",
        "gs_notice",
        "is_manage",
        "pid",
        "gs_type",
        "max_order_time",
        "SaleAmount",
        "store_pic",
        "business_hours",
        "pickup_mode",
        "open_time",
        "close_time",
        "rec_goods_ids",
        "wx_openid",
        "picktime_end",
    ];
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ecs_goods_stores';
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
    protected $primaryKey = 'gs_id';
    /**
     * 模型日期列的存储格式
     *
     * @var string
     */
    protected $dateFormat = 'U';

    public function goods()
	{

	}
}

