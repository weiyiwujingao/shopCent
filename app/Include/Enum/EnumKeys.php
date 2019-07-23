<?php

namespace Enum;

final class EnumKeys
{
	/* 图片处理相关常数 */
	const   ERR_INVALID_IMAGE = 1;
	const   ERR_NO_GD = 2;
	const   ERR_IMAGE_NOT_EXISTS = 3;
	const   ERR_DIRECTORY_READONLY = 4;
	const   ERR_UPLOAD_FAILURE = 5;
	const   ERR_INVALID_PARAM = 6;
	const   ERR_INVALID_IMAGE_TYPE = 7;

	/* 插件相关常数 */
	const   ERR_COPYFILE_FAILED = 1;
	const   ERR_CREATETABLE_FAILED = 2;
	const   ERR_DELETEFILE_FAILED = 3;

	/* 商品属性类型常数 */
	const   ATTR_TEXT = 0;
	const   ATTR_OPTIONAL = 1;
	const   ATTR_TEXTAREA = 2;
	const   ATTR_URL = 3;

	/* 会员整合相关常数 */
	const   ERR_USERNAME_EXISTS = 1; // 用户名已经存在
	const   ERR_EMAIL_EXISTS = 2; // Email已经存在
	const   ERR_INVALID_USERID = 3; // 无效的user_id
	const   ERR_INVALID_USERNAME = 4; // 无效的用户名
	const   ERR_INVALID_PASSWORD = 5; // 密码错误
	const   ERR_INVALID_EMAIL = 6; // email错误
	const   ERR_USERNAME_NOT_ALLOW = 7; // 用户名不允许注册
	const   ERR_EMAIL_NOT_ALLOW = 8; // EMAIL不允许注册

	/* 加入购物车失败的错误代码 */
	const   ERR_NOT_EXISTS = 1; // 商品不存在
	const   ERR_OUT_OF_STOCK = 2; // 商品缺货
	const   ERR_NOT_ON_SALE = 3; // 商品已下架
	const   ERR_CANNT_ALONE_SALE = 4; // 商品不能单独销售
	const   ERR_NO_BASIC_GOODS = 5; // 没有基本件
	const   ERR_NEED_SELECT_ATTR = 6; // 需要用户选择属性
	const   ERR_NOT_SAME_BRAND = 7; // 需要用户选择属性

	/* 购物车商品类型 */
	const   CART_GENERAL_GOODS = 0; // 普通商品
	const   CART_GROUP_BUY_GOODS = 1; // 团购商品
	const   CART_AUCTION_GOODS = 2; // 拍卖商品
	const   CART_SNATCH_GOODS = 3; // 夺宝奇兵
	const   CART_EXCHANGE_GOODS = 4; // 积分商城

	/* 订单状态 */
	const   OS_UNCONFIRMED = 0; // 未确认
	const   OS_CONFIRMED = 1; // 已确认
	const   OS_CANCELED = 2; // 已取消
	const   OS_INVALID = 3; // 无效
	const   OS_RETURNED = 4; // 退货
	const   OS_SPLITED = 5; // 已分单
	const   OS_SPLITING_PART = 6; // 部分分单

	/* 支付类型 */
	const   PAY_ORDER = 0; // 订单支付
	const   PAY_SURPLUS = 1; // 会员预付款

	/* 配送状态 */
	const   SS_UNSHIPPED = 0; // 未发货
	const   SS_SHIPPED = 1; // 已发货
	const   SS_RECEIVED = 2; // 已收货
	const   SS_PREPARING = 3; // 备货中
	const   SS_SHIPPED_PART = 4; // 已发货(部分商品
	const   SS_SHIPPED_ING = 5; // 发货中(处理分单
	const   OS_SHIPPED_PART = 6; // 已发货(部分商品

	/* 支付状态 */
	const   PS_UNPAYED = 0; // 未付款
	const   PS_PAYING = 1; // 付款中
	const   PS_PAYED = 2; // 已付款
    public static $payArr = [
		self::PS_UNPAYED => '未付款',
		self::PS_PAYING => '付款中',
		self::PS_PAYED => '已付款',
	];
	/* 综合状态 */
	const   CS_AWAIT_PAY = 100; // 待付款：货到付款且已发货且未付款，非货到付款且未付款
	const   CS_AWAIT_SHIP = 101; // 待发货：货到付款且未发货，非货到付款且已付款且未发货
	const   CS_FINISHED = 102; // 已完成：已确认、已付款、已发货

	/* 缺货处理 */
	const   OOS_WAIT = 0; // 等待货物备齐后再发
	const   OOS_CANCEL = 1; // 取消订单
	const   OOS_CONSULT = 2; // 与店主协商

	/* 帐户明细类型 */
	const   SURPLUS_SAVE = 0; // 为帐户冲值
	const   SURPLUS_RETURN = 1; // 从帐户提款

	/* 评论状态 */
	const   COMMENT_UNCHECKED = 0; // 未审核
	const   COMMENT_CHECKED = 1; // 已审核或已回复(允许显示
	const   COMMENT_REPLYED = 2; // 该评论的内容属于回复

	/* 红包发放的方式 */
	const   SEND_BY_USER = 0; // 按用户发放
	const   SEND_BY_GOODS = 1; // 按商品发放
	const   SEND_BY_ORDER = 2; // 按订单发放
	const   SEND_BY_PRINT = 3; // 线下发放

	/* 广告的类型 */
	const   IMG_AD = 0; // 图片广告
	const   FALSH_AD = 1; // flash广告
	const   CODE_AD = 2; // 代码广告
	const   TEXT_AD = 3; // 文字广告

	/* 是否需要用户选择属性 */
	const   ATTR_NOT_NEED_SELECT = 0; // 不需要选择
	const   ATTR_NEED_SELECT = 1; // 需要选择

	/* 用户中心留言类型 */
	const   M_MESSAGE = 0; // 留言
	const   M_COMPLAINT = 1; // 投诉
	const   M_ENQUIRY = 2; // 询问
	const   M_CUSTOME = 3; // 售后
	const   M_BUY = 4; // 求购
	const   M_BUSINESS = 5; // 商家
	const   M_COMMENT = 6; // 评论

	/* 团购活动状态 */
	const   GBS_PRE_START = 0; // 未开始
	const   GBS_UNDER_WAY = 1; // 进行中
	const   GBS_FINISHED = 2; // 已结束
	const   GBS_SUCCEED = 3; // 团购成功（可以发货了）
	const   GBS_FAIL = 4; // 团购失败

	/* 红包是否发送邮件 */
	const   BONUS_NOT_MAIL = 0;
	const   BONUS_MAIL_SUCCEED = 1;
	const   BONUS_MAIL_FAIL = 2;

	/* 商品活动类型 */
	const   GAT_SNATCH = 0;
	const   GAT_GROUP_BUY = 1;
	const   GAT_AUCTION = 2;
	const   GAT_POINT_BUY = 3;
	const   GAT_PACKAGE = 4; // 超值礼包

	/* 帐号变动类型 */
	const   ACT_SAVING = 0;     // 帐户冲值
	const   ACT_DRAWING = 1;     // 帐户提款
	const   ACT_ADJUSTING = 2;     // 调节帐户
	const   ACT_OTHER = 99;     // 其他类型

	/* 密码加密方法 */
	const   PWD_MD5 = 1;  //md5加密方式
	const   PWD_PRE_SALT = 2;  //前置验证串的加密方式
	const   PWD_SUF_SALT = 3;  //后置验证串的加密方式

	/* 文章分类类型 */
	const   COMMON_CAT = 1; //普通分类
	const   SYSTEM_CAT = 2; //系统默认分类
	const   INFO_CAT = 3; //网店信息分类
	const   UPHELP_CAT = 4; //网店帮助分类分类
	const   HELP_CAT = 5; //网店帮助分类

	/* 活动状态 */
	const   PRE_START = 0; // 未开始
	const   UNDER_WAY = 1; // 进行中
	const   FINISHED = 2; // 已结束
	const   SETTLED = 3; // 已处理

	/* 验证码 */
	const   CAPTCHA_REGISTER = 1; //注册时使用验证码
	const   CAPTCHA_LOGIN = 2; //登录时使用验证码
	const   CAPTCHA_COMMENT = 4; //评论时使用验证码
	const   CAPTCHA_ADMIN = 8; //后台登录时使用验证码
	const   CAPTCHA_LOGIN_FAIL = 16; //登录失败后显示验证码
	const   CAPTCHA_MESSAGE = 32; //留言时使用验证码

	/* 优惠活动的优惠范围 */
	const   FAR_ALL = 0; // 全部商品
	const   FAR_CATEGORY = 1; // 按分类选择
	const   FAR_BRAND = 2; // 按品牌选择
	const   FAR_GOODS = 3; // 按商品选择

	/* 优惠活动的优惠方式 */
	const   FAT_GOODS = 0; // 送赠品或优惠购买
	const   FAT_PRICE = 1; // 现金减免
	const   FAT_DISCOUNT = 2; // 价格打折优惠

	/* 评论条件 */
	const   COMMENT_LOGIN = 1; //只有登录用户可以评论
	const   COMMENT_CUSTOM = 2; //只有有过一次以上购买行为的用户可以评论
	const   COMMENT_BOUGHT = 3; //只有购买够该商品的人可以评论

	/* 减库存时机 */
	const   SDT_SHIP = 0; // 发货时
	const   SDT_PLACE = 1; // 下订单时

	/* 加密方式 */
	const   ENCRYPT_ZC = 1; //zc加密方式
	const   ENCRYPT_UC = 2; //uc加密方式

	/* 商品类别 */
	const   G_REAL = 1; //实体商品
	const   G_CARD = 0; //虚拟卡

	/* 积分兑换 */
	const   TO_P = 0; //兑换到商城消费积分
	const   FROM_P = 1; //用商城消费积分兑换
	const   TO_R = 2; //兑换到商城等级积分
	const   FROM_R = 3; //用商城等级积分兑换

	/* 支付宝商家账户 */
	const   ALIPAY_AUTH = 'gh0bis45h89m5mwcoe85us4qrwispes0';
	const   ALIPAY_ID = '2088002052150939';

	/* 添加feed事件到UC的TYPE*/
	const   BUY_GOODS = 1; //购买商品
	const   COMMENT_GOODS = 2; //添加商品评论

	/* 邮件发送用户 */
	const   SEND_LIST = 0;
	const   SEND_USER = 1;
	const   SEND_RANK = 2;

	/* license接口 */
	const   LICENSE_VERSION = '1.0';

	/* 配送方式 */
	const   SHIP_LIST = 'cac|city_express|ems|flat|fpd|post_express|post_mail|presswork|sf_express|sto_express|yto|zto';

	/* 缓存前缀 */
	const   CACHE_CAT_LIST = 'cat_list';//商品分类列表
	const   CACHE_SLIDER_LIST = 'home_slider_city';//首页广告图列表
	const   CACHE_GR_NAME_GR_ID = 'get_gr_name_by_gr_id';//通过gr_id获取gr_name
	const   CACHE_HOME_NEW_GOODS = 'get_home_new_goods';//首页获取新品（每周精选）
	const   CACHE_HOME_NAV_LIST = 'get_home_nav_list';//首页获取nav
	const   CACHE_REGION_SECONDE_LIST = 'get_region_seconde_list';//获取下一级地区id
	const   CACHE_REGION_CITY_ID = 'get_region_city_id_by_city_name';//根据城市名称获取城市id
	const   CACHE_REGION_CITY_LIST = 'get_region_city_list';//根据城市id,获取地区列表
	const   CACHE_STORE_OFF_GOODS = 'get_store_off_goods';//商户自定义下架的商品
	const   CACHE_STORE_DETAIL_BY_ID = 'get_store_detail_by_id';//商户详情
	const   CACHE_CLASSIFY_LIST_HOME= 'classify_list_Cache_Key';//前端页面商品分类列表
	const   CACHE_GET_OFF_GOODS = 'get_off_goods_cache_key';//获取下架商品
	const   CACHE_GET_GS_AUTH = 'get_gs_auth_key';//获取商户权限
	const   CACHE_BRAND_INFO = 'get_brand_info_key';//获取品牌详情
	const   CACHE_BRAND_EXTEND = 'brand_extend_data_key';//获取品牌扩展数据
	const   CACHE_GOODS_SELLER_RECOMENT = 'goods_seller_recoment_key';//根据商品id获取推荐门店
	const   CACHE_SELLER_RECOMENT_GOODS = 'seller_recoment_goods_key';//根据门店推荐商品

	/* 用户中心参数 */
	const   CHECK_TRY_NUMS = 3;//防刷次数设定
	const   CHECK_SEND_MOBILE_TRY_NUMS = 10;//手机短信防刷次数设定
	const   USER_LOGIN_PRE_KEY = 'mobile_login_key';//用户登录短信验证码key前缀
	const   USER_REGIST_PRE_KEY = 'mobile_register_key';//用户注册短信验证码key前缀
	const   USER_MODIFY_PASS_PRE_KEY = 'mobile_modify_pass_key';//用户修改秘密短信验证码key前缀

	/* 门店配送方式 */
	const   BUSINESS_PICKUP_BY_SELF  = 1; // 门店自取
	const   BUSINESS_PICKUP_BY_STORE    = 2; // 商户配送
	const   BUSINESS_PICKUP_BY_ALL    = 3; // 门店自取/商户配送
	public static $pickupModel = [
		self::BUSINESS_PICKUP_BY_SELF => '门店自取',
		self::BUSINESS_PICKUP_BY_STORE   => '商户配送',
		self::BUSINESS_PICKUP_BY_ALL   => '门店自取/商户配送',
	];
	/* 门店服务类型 */
	const   BUSINESS_SERVE_TYPE_ZERO  = 0; // 门店自取
	const   BUSINESS_SERVE_TYPE_ONE    = 1; // 商户配送
	const   BUSINESS_SERVE_TYPE_TWO    = 2; // 门店自取/商户配送
	public static $serveModel = [
		self::BUSINESS_SERVE_TYPE_ZERO => '请提前6小时预定(自提)',
		self::BUSINESS_SERVE_TYPE_ONE   => '请提前24小时预定',
		self::BUSINESS_SERVE_TYPE_TWO   => '请提前6小时预定(配送)',
	];
	public static $citysProvince = [
		1 =>
			[
				'gr_id' => '1',
				'gr_name' => '东莞市',
				'province_id' => '6',
				'province_name' => '广东',
			],
		36 =>
			[
				'gr_id' => '36',
				'gr_name' => '湛江市',
				'province_id' => '6',
				'province_name' => '广东',
			],
		46 =>
			[
				'gr_id' => '46',
				'gr_name' => '深圳市',
				'province_id' => '6',
				'province_name' => '广东',
			],
		58 =>
			array (
				'gr_id' => '58',
				'gr_name' => '广州市',
				'province_id' => '6',
				'province_name' => '广东',
			),
		86 =>
			[
				'gr_id' => '86',
				'gr_name' => '珠海市',
				'province_id' => '6',
				'province_name' => '广东',
			],
		87 =>
			[
				'gr_id' => '87',
				'gr_name' => '长沙市',
				'province_id' => '14',
				'province_name' => '湖南',
			],
		105 =>
			array (
				'gr_id' => '105',
				'gr_name' => '惠州市',
				'province_id' => '6',
				'province_name' => '广东',
			),
		106 =>
			[
				'gr_id' => '106',
				'gr_name' => '中山市',
				'province_id' => '6',
				'province_name' => '广东',
			],
		107 =>
			[
				'gr_id' => '107',
				'gr_name' => '佛山市',
				'province_id' => '6',
				'province_name' => '广东',
			],
		108 =>
			[
				'gr_id' => '108',
				'gr_name' => '茂名市',
				'province_id' => '6',
				'province_name' => '广东',
			],
	];



}
