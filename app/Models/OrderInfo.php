<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OrderInfo
 *
 * @property int $order_id
 * @property string $order_sn 订单编码
 * @property int $user_id 用户id
 * @property int $order_status 订单状态：0待确认,1已确认,2已取消,3无效,4退货,5已分单，6部分分单
 * @property int $shipping_status 配送状态:0未发货,1已发货,2已收货,3申请退货
 * @property int $pay_status 支付状态:0未付款,1付款中,2已付款
 * @property int $order_taking 接单状态:0无需接单，1待接单，2已接单
 * @property string $taking_time 接单时间
 * @property string $consignee 收货人
 * @property int $country 国家
 * @property int $province 省份
 * @property int $city 城市
 * @property int $district 区域
 * @property string $address 配送详细地址
 * @property string $zipcode 邮编
 * @property string $tel 电话
 * @property string $mobile 手机
 * @property string $email 邮箱
 * @property string $best_time
 * @property string $sign_building
 * @property string $postscript
 * @property int $is_shipping 是否配送：0否,1是
 * @property int $shipping_id
 * @property string $shipping_name
 * @property int $pay_id 支付方式:表 ecs_payment 字段pay_id
 * @property string $pay_name
 * @property string $how_oos
 * @property string $how_surplus
 * @property string $pack_name
 * @property string $card_name
 * @property string $card_message
 * @property string $inv_payee
 * @property string $inv_content
 * @property float $goods_amount 订单总金额
 * @property float $shipping_fee 配送费
 * @property float $insure_fee
 * @property float $pay_fee
 * @property float $pack_fee
 * @property float $card_fee
 * @property float $money_paid
 * @property float $surplus
 * @property int $integral
 * @property float $integral_money
 * @property float $bonus 使用幸福券的金额
 * @property float $order_amount
 * @property int $from_ad
 * @property string $referer
 * @property int $add_time 订单添加时间
 * @property int $confirm_time
 * @property int $pay_time 支付时间
 * @property int $last_cfm_time 最后确认时间，针对还没确认收货状态的订单，到期后系统自动完成收货
 * @property int $delay_cfm_count 收货延期次数
 * @property int $shipping_time 配送时间
 * @property int $pack_id
 * @property int $card_id
 * @property int $bonus_id
 * @property string $invoice_no
 * @property string $extension_code
 * @property int $extension_id
 * @property string $to_buyer
 * @property string $pay_note
 * @property int $agency_id
 * @property string $inv_type
 * @property float $tax
 * @property int $is_separate
 * @property int $parent_id
 * @property float $discount
 * @property string $order_note 备注
 * @property string $order_lxr 联系人
 * @property string $order_tel 联系电话
 * @property string $order_pick_time 取货时间
 * @property int $order_pick_stores 取货门店ID
 * @property int $is_evaluation 订单是否评价
 * @property float $zhekou 线上支付折扣率
 * @property float $order_amount_all 线上总金额，不打折总金额
 * @property float $order_amount_zy 直营店总价
 * @property int $stores_type 门店类型
 * @property string $bonus_company
 * @property int $user_bonus_id 表ecs_user_bonus中的bonus_id,表示使用该卡支付
 * @property float $user_bonus_money 使用卡消费的金额
 * @property float $user_money 使用余额消费的金额
 * @property int $is_user_del
 * @property string|null $wx_prepay_id
 * @property string|null $wx_transaction_id
 * @property string|null $nonce_str
 * @property int|null $isread
 * @property string $sys_remark 系统备注
 * @property string $return_reason 退货原因
 * @property int|null $dfrom 来源
 * @property int $has_settled 是否完成结算:1完成
 * @property float $settle_money 结算金额
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereAddTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereAgencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereBestTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereBonusCompany($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereBonusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereCardFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereCardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereCardMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereCardName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereConfirmTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereConsignee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereDelayCfmCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereDfrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereDistrict($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereExtensionCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereExtensionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereFromAd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereGoodsAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereHasSettled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereHowOos($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereHowSurplus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereInsureFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereIntegral($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereIntegralMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereInvContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereInvPayee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereInvType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereInvoiceNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereIsEvaluation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereIsSeparate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereIsShipping($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereIsUserDel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereIsread($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereLastCfmTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereMoneyPaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereNonceStr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereOrderAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereOrderAmountAll($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereOrderAmountZy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereOrderLxr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereOrderNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereOrderPickStores($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereOrderPickTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereOrderSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereOrderStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereOrderTaking($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereOrderTel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo wherePackFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo wherePackId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo wherePackName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo wherePayFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo wherePayId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo wherePayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo wherePayNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo wherePayStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo wherePayTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo wherePostscript($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereReferer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereReturnReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereSettleMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereShippingFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereShippingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereShippingName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereShippingStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereShippingTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereSignBuilding($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereStoresType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereSurplus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereSysRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereTakingTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereTax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereTel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereToBuyer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereUserBonusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereUserBonusMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereUserMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereWxPrepayId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereWxTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereZhekou($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInfo whereZipcode($value)
 * @mixin \Eloquent
 */
class OrderInfo extends Model
{
    /**
     * 字段赋值白名单
     *
     * @var array
     */

    protected $fillable = [
        "order_sn",
        "user_id",
        "order_status",
        "shipping_status",
        "pay_status",
        "consignee",
        "country",
        "province",
        "city",
        "districtC",
        "address",
        "zipcode",
        "tel",
        "mobile",
        "email",
        "best_time",
        "sign_building",
        "postscript",
        "shipping_id",
        "shipping_name",
        "pay_id",
        "pay_name",
        "how_oos",
        "how_surplus",
        "pack_name",
        "card_name",
        "card_message",
        "inv_payee",
        "inv_content",
        "goods_amount",
        "shipping_fee",
        "insure_fee",
        "pay_fee",
        "pack_fee",
        "card_fee",
        "money_paid",
        "surplus",
        "integral",
        "integral_money",
        "bonus",
        "order_amount",
        "from_ad",
        "referer",
        "add_time",
        "confirm_time",
        "pay_time",
        "last_cfm_time",
        "delay_cfm_count",
        "delay_cfm_count",
        "shipping_time",
        "pack_id",
        "card_id",
        "bonus_id",
        "invoice_no",
        "extension_code",
        "extension_id",
        "to_buyer",
        "pay_note",
        "agency_id",
        "inv_type",
        "tax",
        "is_separate",
        "parent_id",
        "discount",
        "order_note",
        "order_lxr",
        "order_tel",
        "order_pick_time",
        "order_pick_stores",
        "is_evaluation",
        "zhekou",
        "order_amount_all",
        "order_amount_zy",
        "stores_type",
        "bonus_company",
        "user_bonus_id",
        "user_bonus_money",
        "user_money",
        "is_user_del",
        "wx_prepay_id",
        "wx_transaction_id",
        "nonce_str",
        "isread",
        "sys_remark",
        "return_reason",
        "dfrom",
    ];
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ecs_order_info';
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
    protected $primaryKey = 'order_id';
    /**
     * 模型日期列的存储格式
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
