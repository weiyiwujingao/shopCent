<?php

namespace Enum;
use Illuminate\Support\Facades\Cache;
final class EnumLang {
	/* 商户操作业务类型 */
	const   BUSINESS_UPDATE_PASSWORD  = 1; // 修改密码
	const   BUSINESS_UPDATE_ONSALE    = 2; // 上架
	const   BUSINESS_UPDATE_NOSALE    = 3; // 下架
	const   BUSINESS_UPDATE_STORE     = 4; // 门店设置
	const   BUSINESS_SURE_STATUS     = 5; // 确认提货
	const   BUSINESS_UNTI_WECHAT     = 6; // 解绑微信
	const   BUSINESS_DENY_RETURN     = 7; // 取消退货申请
	const   BUSINESS_ADD_CART        = 8; // 取消退货申请
	const   BUSINESS_UPDATE_SORT    = 9; // 设置商品排序权重
	const   BUSINESS_SET_STOCK      = 10; // 设置商品库存
	const   BUSINESS_SET_PROMOTE_STOCK = 11; // 设置商品促销库存
	public static $businessMap = [
			self::BUSINESS_UPDATE_PASSWORD => '商户重置密码',
			self::BUSINESS_UPDATE_ONSALE   => '上架商品',
			self::BUSINESS_UPDATE_NOSALE   => '下架商品',
			self::BUSINESS_UPDATE_STORE    => '门店设置',
			self::BUSINESS_SURE_STATUS     => '确认提货',
			self::BUSINESS_UNTI_WECHAT     => '解绑微信',
			self::BUSINESS_DENY_RETURN     => '取消退货申请',
			self::BUSINESS_ADD_CART        => '添加购物车',
			self::BUSINESS_UPDATE_SORT    => '设置商品排序权重',
			self::BUSINESS_SET_STOCK    => '设置商品库存',
			self::BUSINESS_SET_PROMOTE_STOCK    => '设置商品促销库存',
	];
    //首页配置专用枚举
    public static $LANG = [
        /* 订单状态 */
        'os'=>[
            \Enum\EnumKeys::OS_UNCONFIRMED=> '未确认',
            \Enum\EnumKeys::OS_CONFIRMED=> '已确认',
            \Enum\EnumKeys::OS_CANCELED=> '<font color="red"> 取消</font>',
            \Enum\EnumKeys::OS_INVALID=> '<font color="red">无效</font>',
            \Enum\EnumKeys::OS_RETURNED=> '<font color="red">退货</font>',
            \Enum\EnumKeys::OS_SPLITED=> '已分单',
            \Enum\EnumKeys::OS_SPLITING_PART=> '部分分单',
            ],
        'ss'=>[
            \Enum\EnumKeys::SS_UNSHIPPED=> '未发货',
            \Enum\EnumKeys::SS_PREPARING=> '配货中',
            \Enum\EnumKeys::SS_SHIPPED=> '已发货',
            \Enum\EnumKeys::SS_RECEIVED=> '收货确认',
            \Enum\EnumKeys::SS_SHIPPED_PART=> '已发货(部分商品)',
            \Enum\EnumKeys::SS_SHIPPED_ING=> '发货中',
        ],
        'ps'=>[
            \Enum\EnumKeys::PS_UNPAYED=> '未付款',
            \Enum\EnumKeys::PS_PAYING=> '付款中',
            \Enum\EnumKeys::PS_PAYED=> '已付款',
        ],
        /* 发货单状态 */
        'delivery_status'=>[
            0=> '已发货',
            1=> '退货',
            2=> '正常'
        ],
		'gbs' =>[
			\Enum\EnumKeys::GBS_PRE_START => '未开始',
			\Enum\EnumKeys::GBS_UNDER_WAY => '进行中',
			\Enum\EnumKeys::GBS_FINISHED => '结束未处理',
			\Enum\EnumKeys::GBS_SUCCEED => '成功结束',
			\Enum\EnumKeys::GBS_FAIL => '失败结束',
		],

    ];
    //星期数组
	public static $weekarray = ["日","一","二","三","四","五","六"];
    /**
     * 载入配置信息
     *
     * @access  public
     * @return  array
     */
    public static function loadConfig()
    {
        $arr = array();
        $key = md5('xfjb_shop_config');
        $data = Cache::get($key);
        if (!$data) {
            $res = \App\Models\ShopConfig::select('code','value')->where('parent_id','>','0')->get()->toArray();
            foreach ($res AS $row) {
                $arr[$row['code']] = $row['value'];
            }
            /* 对数值型设置处理 */
            $arr['watermark_alpha'] = intval($arr['watermark_alpha']);
            $arr['market_price_rate'] = floatval($arr['market_price_rate']);
            $arr['integral_scale'] = floatval($arr['integral_scale']);
            //$arr['integral_percent']     = floatval($arr['integral_percent']);
            $arr['cache_time'] = intval($arr['cache_time']);
            $arr['thumb_width'] = intval($arr['thumb_width']);
            $arr['thumb_height'] = intval($arr['thumb_height']);
            $arr['image_width'] = intval($arr['image_width']);
            $arr['image_height'] = intval($arr['image_height']);
            $arr['best_number'] = !empty($arr['best_number']) && intval($arr['best_number']) > 0 ? intval($arr['best_number']) : 3;
            $arr['new_number'] = !empty($arr['new_number']) && intval($arr['new_number']) > 0 ? intval($arr['new_number']) : 3;
            $arr['hot_number'] = !empty($arr['hot_number']) && intval($arr['hot_number']) > 0 ? intval($arr['hot_number']) : 3;
            $arr['promote_number'] = !empty($arr['promote_number']) && intval($arr['promote_number']) > 0 ? intval($arr['promote_number']) : 3;
            $arr['top_number'] = intval($arr['top_number']) > 0 ? intval($arr['top_number']) : 10;
            $arr['history_number'] = intval($arr['history_number']) > 0 ? intval($arr['history_number']) : 5;
            $arr['comments_number'] = intval($arr['comments_number']) > 0 ? intval($arr['comments_number']) : 5;
            $arr['article_number'] = intval($arr['article_number']) > 0 ? intval($arr['article_number']) : 5;
            $arr['page_size'] = intval($arr['page_size']) > 0 ? intval($arr['page_size']) : 10;
            $arr['bought_goods'] = intval($arr['bought_goods']);
            $arr['goods_name_length'] = intval($arr['goods_name_length']);
            $arr['top10_time'] = intval($arr['top10_time']);
            $arr['goods_gallery_number'] = intval($arr['goods_gallery_number']) ? intval($arr['goods_gallery_number']) : 5;
            $arr['no_picture'] = !empty($arr['no_picture']) ? str_replace('../', './', $arr['no_picture']) : 'images/no_picture.gif'; // 修改默认商品图片的路径
            $arr['qq'] = !empty($arr['qq']) ? $arr['qq'] : '';
            $arr['ww'] = !empty($arr['ww']) ? $arr['ww'] : '';
            $arr['default_storage'] = isset($arr['default_storage']) ? intval($arr['default_storage']) : 1;
            $arr['min_goods_amount'] = isset($arr['min_goods_amount']) ? floatval($arr['min_goods_amount']) : 0;
            $arr['one_step_buy'] = empty($arr['one_step_buy']) ? 0 : 1;
            $arr['invoice_type'] = empty($arr['invoice_type']) ? array('type' => array(), 'rate' => array()) : unserialize($arr['invoice_type']);
            $arr['show_order_type'] = isset($arr['show_order_type']) ? $arr['show_order_type'] : 0;    // 显示方式默认为列表方式
            $arr['help_open'] = isset($arr['help_open']) ? $arr['help_open'] : 1;    // 显示方式默认为列表方式

            if (!isset($GLOBALS['_CFG']['ecs_version'])) {
                /* 如果没有版本号则默认为2.0.5 */
                $GLOBALS['_CFG']['ecs_version'] = 'v2.0.5';
            }
            //限定语言项
            $lang_array = array('zh_cn', 'zh_tw', 'en_us');
            if (empty($arr['lang']) || !in_array($arr['lang'], $lang_array)) {
                $arr['lang'] = 'zh_cn'; // 默认语言为简体中文
            }
            $expiresAt = 60*24*30;
            Cache::put($key, $arr,$expiresAt);
        } else {
            $arr = $data;
        }
        $arr['template'] = 'wap';
        return $arr;
    }

	/***
	 * 根据支付id获取指定的支付信息
	 * @author: colin
	 * @date: 2018/11/19 14:34
	 * @param $payid
	 * @param string $value
	 * @return bool|mixed
	 */
    public static function payment($payid,$value='pay_name'){
    	if(empty($payid) || empty($value))
    		return false;
		$key = md5('xfjb_order_pay_'.$payid.'_'.$value);
		$data = Cache::get($key);
		$data = '';
		if (!$data) {
			$data = \App\Models\Payment::where('pay_id',$payid)->value($value);
			$expiresAt = 60*24*30;
			if(!empty($data)){
				$data = strip_tags($data);
				Cache::put($key, $data,$expiresAt);
			}
		}
		return $data;
	}



}
