<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BackOrder
 *
 * @property int $back_id
 * @property string $delivery_sn
 * @property string $order_sn
 * @property int $order_id
 * @property string|null $invoice_no
 * @property int|null $add_time
 * @property int|null $shipping_id
 * @property string|null $shipping_name
 * @property int|null $user_id
 * @property string|null $action_user
 * @property string|null $consignee
 * @property string|null $address
 * @property int|null $country
 * @property int|null $province
 * @property int|null $city
 * @property int|null $district
 * @property string|null $sign_building
 * @property string|null $email
 * @property string|null $zipcode
 * @property string|null $tel
 * @property string|null $mobile
 * @property string|null $best_time
 * @property string|null $postscript
 * @property string|null $how_oos
 * @property float|null $insure_fee
 * @property float|null $shipping_fee
 * @property int|null $update_time
 * @property int|null $suppliers_id
 * @property int $status
 * @property int|null $return_time
 * @property int|null $agency_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BackOrder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BackOrder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BackOrder query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BackOrder whereActionUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BackOrder whereAddTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BackOrder whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BackOrder whereAgencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BackOrder whereBackId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BackOrder whereBestTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BackOrder whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BackOrder whereConsignee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BackOrder whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BackOrder whereDeliverySn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BackOrder whereDistrict($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BackOrder whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BackOrder whereHowOos($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BackOrder whereInsureFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BackOrder whereInvoiceNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BackOrder whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BackOrder whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BackOrder whereOrderSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BackOrder wherePostscript($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BackOrder whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BackOrder whereReturnTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BackOrder whereShippingFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BackOrder whereShippingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BackOrder whereShippingName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BackOrder whereSignBuilding($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BackOrder whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BackOrder whereSuppliersId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BackOrder whereTel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BackOrder whereUpdateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BackOrder whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BackOrder whereZipcode($value)
 * @mixin \Eloquent
 */
class BackOrder extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */

    protected $fillable = [
        'delivery_sn',
        'order_sn',
        'order_id',
        'invoice_no',
        'add_time',
        'shipping_id',
        'shipping_name',
        'user_id',
        'action_user',
        'consignee',
        'address',
        'country',
        'province',
        'city',
        'district',
        'sign_building',
        'email',
        'zipcode',
        'tel',
        'mobile',
        'best_time',
        'postscript',
        'how_oos',
        'insure_fee',
        'shipping_fee',
        'update_time',
        'suppliers_id',
        'status',
        'return_time',
        'agency_id',
    ];
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ecs_back_order';
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
   protected $primaryKey = 'back_id';
    /**
     * 模型日期列的存储格式
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
