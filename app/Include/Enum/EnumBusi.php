<?php
//业务相关枚举，一般用于数据库字段枚举

namespace Enum;

final class EnumBusi {

    /******************************  支付相关  ************************************/
	//支付类别
    const BUSI_PAYMENT_TYPE_BALANCE     = 1; //余额支付
    const BUSI_PAYMENT_TYPE_BANK        = 2; //银行汇款/转账
    const BUSI_PAYMENT_TYPE_TRADING_BY  = 3; //货到付款
    const BUSI_PAYMENT_TYPE_ALIPAY      = 4; //支付宝支付
    const BUSI_PAYMENT_TYPE_WXPAY       = 5; //微信支付
    const BUSI_PAYMENT_TYPE_WAP_ALIPAY  = 6; //支付宝客户端支付


    public static $paymentMap = [
        self::BUSI_PAYMENT_TYPE_BALANCE     => "余额支付",
        self::BUSI_PAYMENT_TYPE_BANK        => "银行汇款",
        self::BUSI_PAYMENT_TYPE_TRADING_BY  => "货到付款",
        self::BUSI_PAYMENT_TYPE_ALIPAY      => "支付宝支付",
        self::BUSI_PAYMENT_TYPE_WXPAY       => "微信支付",
        self::BUSI_PAYMENT_TYPE_WAP_ALIPAY  => "支付宝客户端支付",


    ];
    /******************************  支付  ************************************/
    public static $rechargeTypeMap = [
		self::BUSI_PAYMENT_TYPE_WXPAY       =>  "微信支付",
		self::BUSI_PAYMENT_TYPE_WAP_ALIPAY  =>  "支付宝客户端支付",
		self::BUSI_PAYMENT_TYPE_BALANCE     =>  "余额支付",
	];

	//支付类缓存
	const BUSI_PAYCAHE_RECHARGE     = 'miniapp_recharge_pay'; //充值信息缓存

}