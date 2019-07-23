<?php
namespace Library\Third\Payment\Wechat;
use App;
use \Enum\EnumBusi;
use \Enum\EnumLang;
use \Helper\CFunctionHelper as help;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use EasyWeChat\Factory;
use function EasyWeChat\Kernel\Support\generate_sign;
/**
 * Class MiniWechatPay
 * @package Library\Third\Payment
 */
class Wechat extends \Library\CBase {

	protected $config = [];
    public function __construct($paymentId) {
		$this->config = [
			'app_id'             => config('third.wechat.xcx_appid'),
			'mch_id'             => config('third.wechat.wx_mchid'),
			'key'                => config('third.wechat.wx_mchkey'),   // API 密钥
			'secret'                => config('third.wechat.xcx_appsecret'),   // 小程序secret
			'cert_path'          => 'cert/apiclient_cert.pem', // XXX: 绝对路径
			'key_path'           => 'cert/apiclient_key.pem',      // XXX: 绝对路径
			'notify_url'         =>env('API_HOST').'/mctApi/payment/recharge/notify',
			'response_type'      => 'array',
			'log' => [
				'level' => 'debug',
				'file' => "../logs/wechatpay/".date('Ym')."/wechat.log",
			],
			'sandbox' => false,
		];
		parent::__construct(__CLASS__);
    }

	/**
	 * 微信小程序充值支付
	 * @author: colin
	 * @date: 2019/5/30 14:00
	 * @param $orderDetail
	 * @param $openid
	 * @return \Yansongda\Supports\Collection
	 */
	public function doRecharge($orderDetail)
	{
		try{
			$authCode = \Request::input('code');
			$uid = \Request::input('uid');
			if($orderDetail['pay_status'] != 0)
				throw new \Exception('该订单状态有误！');
			if($orderDetail['pay_id'] != 5)
				throw new \Exception('该订单支付类型有误！');
			$app = Factory::miniProgram($this->config);
			$user = $app->auth->session($authCode);//获取用户信息
			if(!isset($user['openid'])){
				help::wechatPayLog('Wechat doRecharge 获取用户信息失败：'.print_r($user,true),'mini_wechat_dorecharge');
				throw new \Exception('获取openid失败！');
			}
			$openid = $user['openid'];
			$payment = Factory::payment($this->config); // 微信支付
			$order = [
				'body'         => '幸福加焙充值',
				'out_trade_no' => $orderDetail['bonus_order_sn'],
				'trade_type'   => 'JSAPI',  // 必须为JSAPI
				'openid'       => $openid, // 这里的openid为付款人的openid
				'total_fee'    => $orderDetail['bonus_amount']*100, // 总价
			];
			$result = $payment->order->unify($order);
			help::wechatPayLog('Wechat doRecharge 提交支付返回：'.print_r($result,true),'mini_wechat_dorecharge');
			// 如果成功生成统一下单的订单，那么进行二次签名
			if ($result['return_code'] === 'SUCCESS') {
				$result = $payment->jssdk->appConfig($result['prepay_id']);//第二次签名
				// 二次签名的参数必须与下面相同
				$params = [
					'appId'     => $this->config['app_id'],
					'timeStamp' => ''.time().'',
					'nonceStr'  => uniqid(),
					'package'   => 'prepay_id=' . $result['prepay_id'],
					'signType'  => 'MD5',
				];
				$params['paySign'] = generate_sign($params, $this->config['key']);
//				help::wechatPayLog('Wechat doRecharge 二次签名数据：'.print_r($params,true),'mini_wechat_dorecharge');
				$cacheKey =  md5(EnumBusi::BUSI_PAYCAHE_RECHARGE . $orderDetail['bonus_order_sn']);
				$cacheData = [
					'bo_id'        => $orderDetail['bo_id'],
					'user_id'      => $uid,
					'total_fee'    => $orderDetail['bonus_amount']*100,
					'out_trade_no' => $orderDetail['bonus_order_sn'],
					'type'         => 'recharge'
				];
				Cache::put($cacheKey,$cacheData,60);
				return $result;
			} else {
				help::wechatPayLog('Wechat doRecharge 支付返回有误：'.print_r($result,true),'mini_wechat_dorecharge');
				return $result;
			}
		}catch(\Exception $e){
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "Wechat doRecharge:" . json_encode($orderDetail) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
	}

	/**
	 * 购物支付
	 * @author: colin
	 * @date: 2019/6/5 11:06
	 * @param $orderDetail
	 * @return array|\EasyWeChat\Kernel\Support\Collection|\Library\type|object|\Psr\Http\Message\ResponseInterface|string
	 */
	public function pay($orderDetail)
	{
		try{
			$authCode = \Request::input('code');
			$uid = \Request::input('uid');
			if($orderDetail['pay_status'] != 0)
				throw new \Exception('该订单状态有误！');
			$app = Factory::miniProgram($this->config);
			$user = $app->auth->session($authCode);//获取用户信息
			if(!isset($user['openid'])){
				help::wechatPayLog('Wechat pay 获取用户信息失败：'.print_r($user,true),'mini_wechat_pay');
				throw new \Exception('获取openid失败！');
			}
			$boId = help::insertPayLog($orderDetail['order_id'], $orderDetail['surplus'],0);
			$openid = $user['openid'];
			$payment = Factory::payment($this->config); // 微信支付
			$order = [
				'body'         => '幸福加焙订单支付',
				'out_trade_no' => $orderDetail['order_sn'].'|'.$boId,
				'trade_type'   => 'JSAPI',  // 必须为JSAPI
				'openid'       => $openid, // 这里的openid为付款人的openid
				'total_fee'    => $orderDetail['surplus']*100, // 总价
			];
			$result = $payment->order->unify($order);
			help::wechatPayLog('Wechat pay 提交支付返回：'.print_r($result,true),'mini_wechat_pay');
			// 如果成功生成统一下单的订单，那么进行二次签名
			if ($result['return_code'] === 'SUCCESS') {
				$result = $payment->jssdk->appConfig($result['prepay_id']);//第二次签名
				// 二次签名的参数必须与下面相同
				$params = [
					'appId'     => $this->config['app_id'],
					'timeStamp' => ''.time().'',
					'nonceStr'  => uniqid(),
					'package'   => 'prepay_id=' . $result['prepay_id'],
					'signType'  => 'MD5',
				];
				$params['paySign'] = generate_sign($params, $this->config['key']);
				$cacheKey =  md5(EnumBusi::BUSI_PAYCAHE_RECHARGE . $order['out_trade_no']);
				$cacheData = [
					'bo_id'        => $boId,
					'user_id'      => $uid,
					'total_fee'    => $orderDetail['surplus']*100,
					'out_trade_no' => $orderDetail['order_sn'],
					'type'         => 'shopping'
				];
				Cache::put($cacheKey,$cacheData,60);
				return $result;
			} else {
				help::wechatPayLog('Wechat pay 支付返回有误：'.print_r($result,true),'mini_wechat_pay');
				return $result;
			}
		}catch(\Exception $e){
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "Wechat doRecharge:" . json_encode($orderDetail) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
	}
	/***
	 * 支付回调
	 * @author: colin
	 * @date: 2019/5/30 14:02
	 * @return \Symfony\Component\HttpFoundation\Response
	 * @throws \Yansongda\Pay\Exceptions\InvalidArgumentException
	 */
	public function notify()
	{
		try{
			help::wechatPayLog('Wechat notify 访问参数：'.print_r(\Request::all(),true),'mini_wechat_notify');
			$app = Factory::payment($this->config);
			$response = $app->handlePaidNotify(function ($message, $fail) {
				help::wechatPayLog('Wechat notify 访问参数：'.print_r($message,true),'mini_wechat_notify');
				if(empty($message)){
					help::wechatPayLog('Wechat notify 没有查到该订单：','mini_wechat_notify');
					return true;
				}
				if ($message['return_code'] === 'SUCCESS') { // return_code 表示通信状态，不代表支付状态
					// 用户是否支付成功
					if (array_get($message, 'result_code') === 'SUCCESS') {
						// 用户支付失败
					} elseif (array_get($message, 'result_code') === 'FAIL') {
						help::wechatPayLog('Wechat notify 支付失败：'.print_r($message,true),'mini_wechat_notify');
						return true;
					}
				} else {
					return $fail('通信失败，请稍后再通知我');
				}
				// 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
				$cacheKey =  md5(EnumBusi::BUSI_PAYCAHE_RECHARGE.$message['out_trade_no']);
				$cachedata = Cache::get($cacheKey);
				if($cachedata)
					throw new \Exception( '预支付数据为空！');
				$diff = abs(bcsub($message['total_fee'], $cachedata['total_fee']));
				if ($diff > 0) {
					help::wechatPayLog('Wechat notify 支付金额：'.$message['total_fee'].'_订单金额：'.$cachedata['total_fee'],'mini_wechat_notify');
				}
				$msg = '更新充值订单信息失败';
				if($cachedata['type']=='recharge'){
					$result = self::bonusOrderPaid($message['out_trade_no']);
				}else{
					$result = help::orderPaid($cachedata['bo_id'], 2, '小程序付款');;
					$msg = '更新购物订单信息失败';
				}
				if($result === false){
					help::wechatPayLog('Wechat notify '.$msg,'mini_wechat_notify');
				}
				return true;
			});
			help::wechatPayLog('Wechat notify 访问参数：'.print_r($response,true),'mini_wechat_notify');
			if($response == true)
				return '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
			else
				return '<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[支付失败]]></return_msg></xml>';

		} catch (\Exception $e) {
			return  '<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[' . $e->getMessage() . ']]></return_msg></xml>';
		}
	}

	/***
	 * 充值业务处理
	 * @author: colin
	 * @date: 2019/5/31 14:54
	 * @param $orderSn
	 * @return bool|\Library\type
	 */
	private function bonusOrderPaid($orderSn)
	{
		try{
			$bonusTypeMd = new App\Repositories\BonusTypeRepository();
			$where = ['bonus_order_sn'=>$orderSn];
			$order = $bonusTypeMd->orderDetail($where);
			if($order === false)
				throw new \Exception('订单不存在！');
			if($order['pay_status'] == 1)
				return true;
			$update = $bonusTypeMd->update(['pay_status'=>1],$where);
			if($update == false)
				throw new \Exception( '更新失败');
			//发放一个红包
			$bonusWhere = ['bonus_order_sn'=>$orderSn,'bonus_type_id'=>$order['bonus_type_id']];
			$userBonus = $bonusTypeMd->UserBonusDetail($bonusWhere);
			if(!empty($userBonus)){
				return true;
			}
			/* 红下红包的类型ID和生成的数量的处理 */
			$bonusTypeid = $order['bonus_type_id'];
			$startNum = $bonusTypeMd->BonusCount();
			//查询当前类型面值
			$bonusTypeDetail = $bonusTypeMd->detail(['type_id'=>$bonusTypeid]);
			$typeMoney = $bonusTypeDetail['type_money'];
			$buyUserID = $order['user_id'];  // 购买会员ID
			$startNum++;
			$bonusSn = sprintf("%010d", $startNum);
            $bonusTypeMd->UpBonusCount($startNum);
			$bonusTypeMd->update(['bo_bouns_id'=>0],$where);
			//处理充赠
			$zengsong = 0;
			$_CFG = EnumLang::loadConfig();
			$chongzeng = isset($_CFG['chongzeng']) ? $_CFG['chongzeng'] : "";
			$groupChz = explode('|', $chongzeng);
			foreach($groupChz as $onepair)
			{
				list($key, $value) = explode('+', $onepair, 2);
				if ($key == $typeMoney){
					$zengsong = $value;
					break;
				}
			}
			if ($zengsong > 0)
				$typeMoney += $zengsong;
			$userMoneyDate = $bonusTypeMd->GetUserDate($buyUserID);
			$userInfo = \App\Models\Users::where('user_id',$buyUserID)->firstOrFail();
			$userInfo->user_money += $typeMoney;//添加余额
			$userInfo->bonus_id = 0;
			$oneYearLater = time() + 3600 * 24 * 365;
			if(empty($userMoneyDate) || ($oneYearLater > $userMoneyDate))
			{
				$userInfo->user_money_date = $oneYearLater;
			}
			$userInfo->save();
			help::logAccountChangeTwo($buyUserID,0-$typeMoney,0,'微信支付充值'.($zengsong >0 ? '(含赠送'.$zengsong.'元)': '').$bonusSn);
			return true;
		}catch(\Exception $e){
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "Wechat bonusOrderPaid:" . json_encode($orderSn) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
	}

}