<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\DeliveryOrder
 *
 * @property int $delivery_id
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
 * @property int|null $agency_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeliveryOrder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeliveryOrder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeliveryOrder query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeliveryOrder whereActionUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeliveryOrder whereAddTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeliveryOrder whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeliveryOrder whereAgencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeliveryOrder whereBestTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeliveryOrder whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeliveryOrder whereConsignee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeliveryOrder whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeliveryOrder whereDeliveryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeliveryOrder whereDeliverySn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeliveryOrder whereDistrict($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeliveryOrder whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeliveryOrder whereHowOos($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeliveryOrder whereInsureFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeliveryOrder whereInvoiceNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeliveryOrder whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeliveryOrder whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeliveryOrder whereOrderSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeliveryOrder wherePostscript($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeliveryOrder whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeliveryOrder whereShippingFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeliveryOrder whereShippingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeliveryOrder whereShippingName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeliveryOrder whereSignBuilding($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeliveryOrder whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeliveryOrder whereSuppliersId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeliveryOrder whereTel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeliveryOrder whereUpdateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeliveryOrder whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeliveryOrder whereZipcode($value)
 * @mixin \Eloquent
 */
class DeliveryOrder extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */

    protected $fillable = [];
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ecs_delivery_order';
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
    protected $primaryKey = 'delivery_id';
    /**
     * 模型日期列的存储格式
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
