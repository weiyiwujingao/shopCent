<?php
//微信参数配置
return [
	//微信公众号，小程序配置
    'wechat' => [
    	'wx_appid' => env('WX_APPID',''),//公众号id
    	'wx_appsecret' => env('WX_APPSECRET',''),
    	'xcx_appid' => env('XCX_APPID',''),//小程序id
    	'xcx_appsecret' => env('XCX_APPSECRET',''),
    	'wx_mchid' => env('WX_MCHID',''),//商户号id
    	'wx_mchkey' => env('WX_MCHKEY',''),//商户号关键字
	],
	'payment' => [
		1 => [
			'name'          => '余额支付',
			'path'          => 'Balance',
			'open'          => true,
			'logo'          => '',
			'client'        => 3,//1:PC端 2:移动端 3:通用
			'apiKey'        => '',
			'apiSecret'     => '',
			'canRecharge'   => false,
		],
		5 => [
			'name'      => '微信支付',
			'path'      => 'Wechat',
			'open'      => true,
			'logo'      => '',
			'client'    => 3,//1:PC端 2:移动端 3:通用
			'apiKey'    => '',
			'apiSecret' => '',
		],
		6 => [
			'name'      => '支付宝',
			'path'      => 'Alipay',
			'open'      => true,
			'logo'      => '',
			'client'    => 3,//1:PC端 2:移动端 3:通用
			'apiKey'    => '',
			'apiSecret' => '',
		],
	],
];
