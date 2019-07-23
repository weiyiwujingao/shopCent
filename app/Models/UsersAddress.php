<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UsersAddress
 *
 * @property int $address_id
 * @property string $address_name
 * @property int $user_id
 * @property string $consignee 收货人
 * @property string $email
 * @property int $country 国家
 * @property int $province 省份id
 * @property string $province_name 省份名称
 * @property int $city 城市id
 * @property string $city_name 城市名称
 * @property int $district 区id
 * @property string $district_name 地区名称
 * @property string $address 详细地址
 * @property string $zipcode
 * @property string $tel 电话
 * @property string $mobile 手机
 * @property int $sex 性别:1男,2女
 * @property int|null $is_default 是否默认址:1是,0否
 * @property string $sign_building
 * @property string $best_time
 * @property string $update_time 更新时间
 * @property-read \App\Models\GiftGoodCat $cats
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersAddress newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersAddress newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersAddress query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersAddress whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersAddress whereAddressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersAddress whereAddressName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersAddress whereBestTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersAddress whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersAddress whereCityName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersAddress whereConsignee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersAddress whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersAddress whereDistrict($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersAddress whereDistrictName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersAddress whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersAddress whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersAddress whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersAddress whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersAddress whereProvinceName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersAddress whereSex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersAddress whereSignBuilding($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersAddress whereTel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersAddress whereUpdateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersAddress whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersAddress whereZipcode($value)
 * @mixin \Eloquent
 */
class UsersAddress extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */

    protected $fillable = [
        "address_name",
        "user_id",
        "consignee",
        "email",
        "country",
        "province",
        "province_name",
        "city",
        "city_name",
        "district",
        "district_name",
        "address",
        "zipcode",
        "tel",
        "mobile",
        "sex",
        "is_default",
        "sign_building",
        "best_time",
        "update_time",
    ];
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ecs_user_address';
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
    protected $primaryKey = 'address_id';
    /**
     * 模型日期列的存储格式
     *
     * @var string
     */
    protected $dateFormat = 'U';
	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function cats()
	{
		return $this->hasOne(GiftGoodCat::class, 'id', 'cat_id');
	}

}
