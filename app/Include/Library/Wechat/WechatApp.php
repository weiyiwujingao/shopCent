<?php

namespace Library\Wechat;

use DB;
use App;
use EasyWeChat\Factory;
use Enum\EnumKeys;
use \App\Models\Users;
use Helper\CFunctionHelper as help;
use Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use \Exception;

class WechatApp extends \Library\CBase
{
	protected $request;
	protected $config;

	public function __construct(Request $request)
	{
		$this->request = $request;
		$this->config = [
			'app_id'             => config('third.wechat.xcx_appid'),
			'secret'             => config('third.wechat.xcx_appsecret'),
			'response_type'      => 'array',
			'log' => [
				'level' => 'debug',
				'file' => "../logs/wechat/".date('Ym')."/wechat.log",
			],
			'sandbox' => true,
		];
		parent::__construct(__CLASS__);
	}

	/**
	 * js微信配置
	 * @author: colin
	 * @date: 2019/6/3 10:34
	 * @return array|\Library\type
	 */
	public function jsConfig(){
		try {
			$url = $this->request->input('url');
			$jssdk = new \Library\Wechat\WxJssdk(config('third.wechat.wx_appid'), config('third.wechat.wx_appsecret'), $url);
			$data = $jssdk->GetSignPackage();
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return $data;
	}
	/***
	 * 小程序授权登录
	 * @author: colin
	 * @date: 2019/5/31 19:42
	 * @return array|\Library\type
	 */
	public function login()
	{
		try{
			$code = $this->request->input('code');
			$app = Factory::miniProgram($this->config);
			$response = $app->auth->session($code);
			if (empty($response) || !empty($response['errcode']) || empty($response['openid'])) {
				$errCode = isset($response['errcode']) ? $response['errcode'] : '';
				throw new Exception('微信接口调用失败,code:' . $errCode);
			}
			$openid = $response['openid'];
			$userObj = Users::where('wx_openid',$openid)->first();
			if(empty($userObj))
				throw new \Exception('未绑定账户');
			$userInfo = [
				'uid' => $userObj->user_id,
				'name' => $userObj->user_name,
				'ctime' => time(),
			];
			$token = md5(json_encode($userInfo));
			$expiresAt = 60 * 24 * 30;
			Cache::put('user_login_' . $token, $userInfo, $expiresAt);
			$result = [
				'token' => $token,
			];
			$userObj->last_login = time();
			$userObj->last_time = date('Y-m-d H:i:s');
			$userObj->last_ip = help::getRealIP();
			$userObj->save();
			\App\Models\UserToken::create(['token' => $token, 'uid' => $userInfo['uid']]);
		}catch(\Exception $e){
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "WechatApp login:" . json_encode($code) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return $result;
	}

	/**
	 * 生成微信小程序的二维码图片
	 * @author: colin
	 * @date: 2019/6/3 11:05
	 * @return array|\Library\type
	 */
	public function qrcode()
	{
		try{
			$path = $this->request->input('path');
			$spath = './data/images/';
			$jsonData = '{"path":"' . $path . '","width":500}';
			$fileName = md5($jsonData) . '.png';
			$fpath = $spath . $fileName;
			$data = ['path'=>$fpath];
			if (file_exists($fpath)) {
				return $data;
			}
			$app = Factory::miniProgram($this->config);
			$response = $app->app_code->getQrCode($spath,500);
			if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
				$filename = $response->saveAs($spath, $fileName);
			}else{
				throw new \Exception('获取二维码失败');
			}
		}catch(\Exception $e){
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "WechatApp qrcode:" . json_encode($path) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return $data;
	}
	/**
	 * 微信授权登录
	 * @author: colin
	 * @date: 2019/5/31 19:58
	 * @return array|\Library\type
	 */
	public function wxlogin()
	{
		try{
			$code = $this->request->input('code');
			$app = Factory::miniProgram($this->config);
			$response = $app->auth->session($code);
			if (empty($response) || !empty($response['errcode']) || empty($response['openid'])) {
				$errCode = isset($response['errcode']) ? $response['errcode'] : '';
				throw new Exception('微信接口调用失败,code:' . $errCode);
			}
			$openid = $response['openid'];
			$userObj = Users::where('wx_openid',$openid)->first();
			if(empty($userObj))
				throw new \Exception('未绑定账户');
			$userInfo = [
				'uid' => $userObj->user_id,
				'name' => $userObj->user_name,
				'ctime' => time(),
			];
			$token = md5(json_encode($userInfo));
			$expiresAt = 60 * 24 * 30;
			Cache::put('user_login_' . $token, $userInfo, $expiresAt);
			$result = [
				'token' => $token,
			];
			$userObj->last_login = time();
			$userObj->last_time = date('Y-m-d H:i:s');
			$userObj->last_ip = help::getRealIP();
			$userObj->save();
			\App\Models\UserToken::create(['token' => $token, 'uid' => $userInfo['uid']]);
		}catch(\Exception $e){
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "WechatApp login:" . json_encode($code) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return $result;
	}

}
