<?php

namespace Library\UserCenter;

use DB;
use App;
use Enum\EnumKeys;
use Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use \Exception;
use Session;
use Helper\CFunctionHelper as help;

class User extends \Library\CBase
{
	protected $request;
	protected $userMd;

	public function __construct(Request $request)
	{
		$this->request = $request;
		$this->userMd = new App\Repositories\UsersRepository();
		parent::__construct(__CLASS__);
	}

	/**
	 * 获取登录信息
	 * @param $param
	 * @return mixed
	 * @author colin
	 */
	public function getLogin()
	{
		try {
			$param = $this->request->all();
			$salt = $this->userMd->GetSaltByName($param['user_name']);
			if (empty($salt)) {
				throw new \Exception('该手机号码未注册！');
			}
			$loginData = array(
				'user_name' => $param['user_name'],
				'password' => help::setPassWord($param['pwd'], $salt),
				'last_login_time' => time(),
				'last_login_ip' => help::getRealIP(),
			);
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "login get user fail.loginInfo:" . json_encode($param) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		$loginRe = $this->login($loginData);
		return $loginRe;
	}

	/**
	 * 登录
	 * @author colin
	 * @param array $loginInfo 基本用户信息
	 * @return array|type
	 */
	public function login($loginInfo)
	{
		try {
			$numkey = md5('user_login_time_' . $loginInfo['user_name']);
			$checkNum = intval(Cache::get($numkey));
			if($checkNum >= EnumKeys::CHECK_TRY_NUMS){
				$vCode = $this->request->input('code','');
				$getImgCode = Session::get('xfjb_code');
				if (empty($vCode) || empty($getImgCode)) {
					throw new Exception("请输入图形验证码");
				}
				$vCode = strtolower($vCode);
				if ($getImgCode != $vCode) {
					throw new Exception('图形验证码错误');
				}

			}
			$userObj = $this->userMd->getInfo($loginInfo['user_name'], $loginInfo['password']);
			if ($userObj === false) {
				$checkNum++;
				Cache::put($numkey, $checkNum, 720);
				throw  new \Exception('用户名或者密码错误！');
			}
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
			return $result;
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "login get user fail.loginInfo:" . json_encode($loginInfo) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return false;
	}

	/***
	 * 注册
	 * @author: colin
	 * @date: 2019/1/8 9:54
	 * @return bool|\Library\type
	 */
	public function register()
	{
		try {
			\DB::beginTransaction();
			$param = $this->request->all();
			$key = EnumKeys::USER_REGIST_PRE_KEY . $param['mobile'];
			$mobileCode = Cache::get($key);
			if (empty($mobileCode)) {
				throw new \Exception('验证码超时！');
			}
			if ($mobileCode != $param['code']) {
				throw new \Exception('验证码错误！');
			}
			$dataInfo = [
				'user_name' => $param['mobile'],
				'password' => help::setPassWord($param['password'], $mobileCode),
				'ec_salt' => $mobileCode,
				'reg_time' => time(),
				'user_money_date' => strtotime(date("Y-m-d", strtotime("+1 months"))),
			];
			$this->userMd->create($dataInfo);
			\DB::commit();
			return true;
		} catch (\Exception $e) {
			\DB::rollBack();
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "register get user fail.loginInfo:" . json_encode($param) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}

	}

	/**
	 * 登录
	 * @author colin
	 * @param array $loginInfo 基本用户信息
	 * @return array|type
	 */
	public function smsLogin()
	{
		try {
			$param = $this->request->all();
			$cacheKey = EnumKeys::USER_LOGIN_PRE_KEY . $param['mobile'];
			$mobileCode = Cache::get($cacheKey);
			if (empty($mobileCode)) {
				throw  new \Exception('验证码超时！');
			}
			if ($mobileCode != $param['code']) {
				throw  new \Exception('验证码错误！');
			}
			$userObj = $this->userMd->getInfoByName($param['mobile']);
			if ($userObj === false) {
				throw  new \Exception('未注册用户！');
			}
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
			\App\Models\UserToken::create(['token' => $token, 'user_id' => $userInfo['uid']]);
			return $result;
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "login get user fail.loginInfo:" . json_encode($param) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return false;
	}

	/**
	 * 判断是否登录
	 * @param $param
	 * @return mixed
	 * @author colin
	 */
	public function isLogin()
	{
		try {
			if (empty($this->request->input('uid'))) {
				throw new Exception('未登录！');
			}
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "islogin fail reason:" . $e->getMessage(),
				'userMsg' => '未登录！',
				'line' => __LINE__,
			]);
		}
		return true;
	}

	/***
	 * 退出登录
	 * @author: colin
	 * @date: 2019/1/7 16:33
	 * @return bool|\Library\type
	 */
	public function logout()
	{
		try {
			$token = $this->request->header("token");
			$token = 'user_login_' . $token;
			Cache::forget($token);
			\App\Models\UserToken::where('token', $token)->delete();
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "logout get user fail.user_id:{$this->request->input('uid')},reason:" . $e->getMessage(),
				'userMsg' => "不存在用户！",
				'line' => __LINE__,
			]);
		}
		return true;
	}

	/***
	 * 用户登录发送短信
	 * @author: colin
	 * @date: 2019/1/7 14:32
	 * @return bool|\Library\type
	 */
	public function sendSms()
	{
		$mobile = $this->request->input('mobile');
		try {
			$getIp = help::getRealIP();
			$cacheKey = md5('getyzm_' . $getIp);
			$sendCount = intval(Cache::get($cacheKey));
			if($sendCount >= EnumKeys::CHECK_SEND_MOBILE_TRY_NUMS){
				$vCode = $this->request->input('code','');
				$getImgCode = Session::get('xfjb_code');
				if (empty($vCode) || empty($getImgCode)) {
					throw new Exception("请输入图形验证码");
				}
				$vCode = strtolower($vCode);
				if ($getImgCode != $vCode) {
					throw new Exception('图形验证码错误');
				}

			}
			$userObj = $this->userMd->getInfoByName($mobile);
			if ($userObj === false) {
				throw  new \Exception('用户名或者密码错误！');
			}
			$userId = $userObj->user_id;
			$mobileCode = mt_rand(111111, 999999);
			$sendFlag = help::sendMobileSmsCode($mobile, $mobileCode, $userId);
			$sendCount++;
			Cache::put($cacheKey, $sendCount, 60);
			if ($sendFlag === false) {
				throw new Exception('短信发送失败');
			}
			$key = EnumKeys::USER_LOGIN_PRE_KEY . $mobile;
			Cache::put($key, $mobileCode, 10);
		} catch (\Exception $e) {

			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return true;
	}

	/***
	 * 用户修改密码发送短信验证码
	 * @author: colin
	 * @date: 2019/1/7 16:56
	 * @return bool|\Library\type
	 */
	public function modifysendSms()
	{
		$mobile = $this->request->input('mobile');
		try {
			$getIp = help::getRealIP();
			$cacheKey = md5('modify_password_get_yzm_' . $getIp);
			$sendCount = intval(Cache::get($cacheKey));
			if($sendCount >= EnumKeys::CHECK_SEND_MOBILE_TRY_NUMS){
				$vCode = $this->request->input('code','');
				$getImgCode = Session::get('xfjb_code');
				if (empty($vCode) || empty($getImgCode)) {
					throw new Exception("请输入图形验证码");
				}
				$vCode = strtolower($vCode);
				if ($getImgCode != $vCode) {
					throw new Exception('图形验证码错误');
				}

			}
			$mobileCode = mt_rand(111111, 999999);
			$sendFlag = help::sendMobileSmsCode($mobile, $mobileCode, 0);
			$sendCount++;
			Cache::put($cacheKey, $sendCount, 60);
			if ($sendFlag === false) {
				throw new Exception('短信发送失败');
			}
			$key = EnumKeys::USER_MODIFY_PASS_PRE_KEY . $mobile;
			Cache::put($key, $mobileCode, 10);
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return true;
	}
	/***
	 * 用户注册发送短信验证码
	 * @author: colin
	 * @date: 2019/1/7 16:56
	 * @return bool|\Library\type
	 */
	public function registerSendSms()
	{
		$mobile = $this->request->input('mobile');
		try {
			$getIp = help::getRealIP();
			$cacheKey = md5('regist_get_yzm_' . $getIp);
			$sendCount = intval(Cache::get($cacheKey));
			if($sendCount >= EnumKeys::CHECK_SEND_MOBILE_TRY_NUMS){
				$vCode = $this->request->input('code','');
				$getImgCode = Session::get('xfjb_code');
				if (empty($vCode) || empty($getImgCode)) {
					throw new Exception("请输入图形验证码");
				}
				$vCode = strtolower($vCode);
				if ($getImgCode != $vCode) {
					throw new Exception('图形验证码错误');
				}

			}
			$mobileCode = mt_rand(111111, 999999);
			$sendFlag = help::sendMobileSmsCode($mobile, $mobileCode, 0);
			$sendCount++;
			Cache::put($cacheKey, $sendCount, 60);
			if ($sendFlag === false) {
				throw new Exception('短信发送失败');
			}
			$key = EnumKeys::USER_REGIST_PRE_KEY . $mobile;
			Cache::put($key, $mobileCode, 10);
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return true;
	}
	/**
	 * 修改头像
	 * @author: colin
	 * @date: 2019/1/8 15:44
	 * @return \Library\type|string
	 */
	public function upload()
	{
		try{
			$uid = $this->request->input('uid');
			$file = $this->request->file('file');//获取图片
			$allowedExtensions = ["png", "jpg", "gif"];
			if ($file->getClientOriginalExtension() && !in_array($file->getClientOriginalExtension(), $allowedExtensions)) {
				return self::showError('图片格式不正确！');
			}
			$destinationPath = 'uploads/faces/'.date('Ymd').'/';
			$extension = $file->getClientOriginalExtension();
			$fileName = str_random(10).'_'.mt_rand(11111,99999) . '.' . $extension;
			$file->move($destinationPath, $fileName);
			$url = '/'.$destinationPath . $fileName;
			$userObj = $this->userMd->ById($uid);
			if($userObj === false){
				throw  new \Exception('修改头像失败！');
			}
			$userObj->faces = $url;
			$userObj->save();
		}catch(\Exception $e){
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return $url;
	}
	/**
	 * 修改用户信息
	 * @author: colin
	 * @date: 2019/1/8 17:22
	 * @return bool
	 */
	public function modifyUser()
	{
		try{
			$data = help::setParamData(['nickname'], $this->request->all());
			if (empty($data)) {
				throw new \Exception('非法参数！');
			}
			$res = $this->userMd->updateByid($this->request->input('uid'),$data);
			if ($res === false) {
				throw new \Exception('修改失败！！');
			}
			return $res;
		}catch(\Exception $e){
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
	}

	/***
	 * 通过旧密码设置新密码
	 * @author: colin
	 * @date: 2019/1/9 10:48
	 * @return bool|\Library\type|type
	 * @throws Exception
	 */
	public function modifypsw()
	{
		$res = $this->getUserExist();//验证是否存在
		if ($res === false) {
			return $res;
		}
		$res = $this->UpPwd();//修改密码
		return $res;
	}
	/**
	 * 判断商户是否存在
	 * @author: colin
	 * @date: 2018/11/8 14:41
	 * @param $uid
	 * @param $token
	 * @return type
	 */
	public function getUserExist()
	{
		try {
			$password = $this->request->input('password');
			$userInfo = $this->request->input('userInfo');
			$salt = $this->userMd->GetSaltByName($userInfo['name']);
			if (empty($salt)) {
				throw new \Exception('该手机号码未注册！');
			}
			$password = help::setPassWord($password, $salt);
			$userObj = $this->userMd->getInfo($userInfo['name'],$password);
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "判断用户是否存在:" . $userInfo['name'] . ",reason:" . $e->getMessage(),
				'userMsg' => "当前密码输入有误！",
				'line' => __LINE__,
			]);
		}
		return $userObj;
	}

	/**
	 * 重置密码
	 * @author: colin
	 * @date: 2019/1/9 9:52
	 * @return bool|\Library\type
	 * @throws Exception
	 */
	public function UpPwd()
	{
		try {
			$password = $this->request->input('new_password');
			$userInfo = $this->request->input('userInfo');
			$salt = $this->userMd->GetSaltByName($userInfo['name']);
			if (empty($salt)) {
				throw new \Exception('该手机号码未注册！');
			}
			$password = help::setPassWord($password, $salt);
			$userToken = \App\Models\UserToken::where('user_id', $this->request->input('uid'))->get()->pluck('token')->toArray();
			foreach ($userToken as $val) {
				Cache::forget($val);
			}
			\App\Models\UserToken::where('user_id', $this->request->input('uid'))->delete();
			$this->userMd->updateByid($this->request->input('uid'),['password' => $password]);
		} catch (\Illuminate\Database\QueryException $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "forgetpsw fail.user_id:{$this->request->input('uid')},reason:" . $e->getMessage(),
				'userMsg' => '修改密码失败！',
				'line' => __LINE__,
			]);
		}
		return true;
	}
	/**
	 * 重置密码
	 * @author: colin
	 * @date: 2019/1/9 9:52
	 * @return bool|\Library\type
	 * @throws Exception
	 */
	public function UpPwdByMobile()
	{
		try {
			$password = $this->request->input('new_password');
			$userInfo = $this->request->input('userInfo');
			$salt = $this->userMd->GetSaltByName($userInfo['name']);
			if (empty($salt)) {
				throw new \Exception('该手机号码未注册！');
			}
			$password = help::setPassWord($password, $salt);
			$userToken = \App\Models\UserToken::where('user_id', $this->request->input('uid'))->get()->pluck('token')->toArray();
			foreach ($userToken as $val) {
				Cache::forget($val);
			}
			\App\Models\UserToken::where('user_id', $this->request->input('uid'))->delete();
			$this->userMd->updateByid($this->request->input('uid'),['password' => $password]);
		} catch (\Illuminate\Database\QueryException $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "forgetpsw fail.user_id:{$this->request->input('uid')},reason:" . $e->getMessage(),
				'userMsg' => '修改密码失败！',
				'line' => __LINE__,
			]);
		}
		return true;
	}
	/***
	 * 通过手机验证码修改密码
	 * @author: colin
	 * @date: 2019/1/9 10:49
	 * @return bool|\Library\type|type
	 * @throws Exception
	 */
	public function modifyPswBymobile()
	{
		$res = $this->getUserExistBycode();//验证是否存在
		if ($res === false) {
			return $res;
		}
//		$res = $this->UpPwdByMobile();//修改密码
		return $res;
	}

	/**
	 * 根据验证和手机号码判断用户是否存在并重置密码
	 * @author: colin
	 * @date: 2019/1/9 10:50
	 * @return bool|\Library\type
	 * @throws Exception
	 */
	public function getUserExistBycode()
	{
		try {
			$code= $this->request->input('code');
			$mobile = $this->request->input('mobile');
			$password = $this->request->input('new_password');

			$key = EnumKeys::USER_MODIFY_PASS_PRE_KEY . $mobile;
			$modifyCode = Cache::get($key);
			if(empty($modifyCode)){
				throw new \Exception('验证码过期！');
			}
			if($modifyCode != $code){
				throw new \Exception('验证码有误！');
			}

			$userObj = $this->userMd->getInfoByName($mobile);
			if($userObj === false){
				throw new \Exception('手机号码有误！');
			}
			$userObj->password = help::setPassWord($password, $userObj->ec_salt);
			$userToken = \App\Models\UserToken::where('user_id', $userObj->user_id)->get()->pluck('token')->toArray();
			foreach ($userToken as $val) {
				Cache::forget($val);
			}
			$userObj->save();
			\App\Models\UserToken::where('user_id', $this->request->input('uid'))->delete();
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "getUserExistBycode:" . $userInfo['name'] . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return true;
	}

	/***
	 * 用户详情
	 * @author: colin
	 * @date: 2019/1/9 18:05
	 * @return \Library\type
	 */
	public function userDetail()
	{
		try{
			$userId = $this->request->input('uid');
			$userObj = $this->userMd->ById($userId);
			if($userObj === false){
				throw new \Exception('获取用户详情失败！');
			}
			$userDetail = [
				'user_name' => $userObj->user_name,
				'nickname' => $userObj->nickname,
				'balance_time' => $userObj->user_money_date,
				'balance_time_str' => date('Y-m-d',$userObj->user_money_date),
				'gender' =>  $userObj->sex,
				'balance' =>  $userObj->user_money,
				'read_tips' =>  $userObj->read_tips,
				'avatar' =>  empty($userObj->faces)? '' : config('app.static_domain').$userObj->faces,
				'is_use_wx' =>   !empty($userObj->wx_openid)? 1 : 0,
			];
			$totalData = App\Models\UserBonus::selectRaw("sum(balance) as totalM,count(bonus_id) as ct")->where(['bonus_status'=>1,'user_id'=>$userId])->firstOrFail();
			$userDetail['can_use_card'] = $totalData->ct;
			$userDetail['use_card_total'] = sprintf("%.2f", floatval($totalData->totalM));
			return $userDetail;
		}catch(\Exception $e){
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "userDetail:" . $userId. ",reason:" . $e->getMessage(),
				'userMsg' => '获取用户详情失败!',
				'line' => __LINE__,
			]);
		}
	}

	/***
	 * 我的收藏-店铺
	 * @author: colin
	 * @date: 2019/1/10 8:50
	 * @return array|\Library\type
	 */
	public function collectStores()
	{
		try{
			$userId = $this->request->input('uid');
			$userObj = $this->userMd->ById($userId);
			if($userObj === false){
				throw new \Exception('获取用户详情失败！');
			}
			$userDetail = [
				'user_name' => $userObj->user_name,
				'nickname' => $userObj->nickname,
				'balance_time' => $userObj->user_money_date,
				'balance_time_str' => date('Y-m-d',$userObj->user_money_date),
				'gender' =>  $userObj->sex,
				'balance' =>  $userObj->user_money,
				'read_tips' =>  $userObj->read_tips,
				'avatar' =>  empty($userObj->faces)? '' : config('app.static_domain').$userObj->faces,
				'is_use_wx' =>   !empty($userObj->wx_openid)? 1 : 0,
			];
			$totalData = App\Models\UserBonus::selectRaw("sum(balance) as totalM,count(bonus_id) as ct")->where(['bonus_status'=>1,'user_id'=>$userId])->firstOrFail();
			$userDetail['can_use_card'] = $totalData->ct;
			$userDetail['use_card_total'] = sprintf("%.2f", floatval($totalData->totalM));
			return $userDetail;
		}catch(\Exception $e){
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "userDetail:" . $userId. ",reason:" . $e->getMessage(),
				'userMsg' => '获取用户详情失败!',
				'line' => __LINE__,
			]);
		}
	}
	/***
	 * 我的账单
	 * @author: colin
	 * @date: 2019/1/10 16:19
	 * @return array|\Illuminate\Contracts\Pagination\LengthAwarePaginator|\Library\type
	 */
	public function accoountBill()
	{
		$uid = $this->request->input('uid');
		$param = $this->request->all();
		$startTime = strtotime($param['year'].'-'.$param['month'].'-'.'01');
		$endTime = strtotime("+1 months", $startTime);
		$userAount = new App\Repositories\AccountBonusRepository();
		$sort = [];
		try {
			$rechargeBill =$userAount->rechargeBill($uid,$startTime,$endTime,$sort);//充值账单
			$shopBill =$userAount->shopBill($uid,$startTime,$endTime,$sort);//消费账单
			$returnBill =$userAount->returnBill($uid,$startTime,$endTime,$sort);//退款账单（或者其他）
			$accountBill = array_merge($rechargeBill,$shopBill,$returnBill);
			!empty($sort) && array_multisort($sort, SORT_DESC, $accountBill);
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "UserOrder 获取用户订单查询信息失败:" . json_decode($param) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return $shopBill;
	}
	/**
	 * 幸福卡使用信息卡统计
	 * @author: colin
	 * @date: 2019/1/11 10:26
	 * @return \Library\type
	 */
	public function userCardStatis()
	{
		$uid = $this->request->input('uid');
		$userBMd = new App\Repositories\UserBonusRepository();
		try {
			$result = $userBMd->userCardStatis($uid);
			if($result === false){
				throw new \Exception('未查询到幸福卡信息！');
			}
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "userCardStatis 查询卡统计信息失败:" . json_decode($uid) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return $result;
	}
	/**
	 * 幸福卡列表
	 * @author: colin
	 * @date: 2019/1/11 10:26
	 * @return \Library\type
	 */
	public function userCardList()
	{
		$uid = $this->request->input('uid');
		$param = $this->request->all();
		$userBMd = new App\Repositories\UserBonusRepository();
		try {
			$result = $userBMd->list($uid,$param);
			if($result === false){
				throw new \Exception('未查询到幸福卡信息！');
			}
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "userCardStatis 查询卡统计信息失败:" . json_decode($uid) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return $result;
	}

	/**
	 * 创建付款码
	 * @author: colin
	 * @date: 2019/1/11 15:00
	 * @return bool|\Library\type
	 */
	public function createPayCode()
	{
		$uid = $this->request->input('uid');
		$userPMd = new App\Repositories\UserPaymentCodeRepository();
		$expireTime = 50;//有效时间 50秒
		$cacheKey = "paymentCode_" . $uid;
		$nowTime = time();
		try {
			$imgUrl = '//' . $_SERVER['HTTP_HOST'] . '/mctApi/user/showPayCode';
			$result = [
				'left_time' => $expireTime,
				'qr_url' => $imgUrl. '?type=1',//二维码图片地址
				'bar_url' => $imgUrl . '?type=2',//条码(不带数字)图片地址
				'bar_url_num' => $imgUrl . '?type=2&show_txt=1',//条码(带数字)图片地址
			];
			$cacheData = Cache::get($cacheKey);
			$nowTime = time();
			if (!empty($cacheData)) {
				//检查是否有效
				$payCode = $cacheData;
				$payData = $userPMd->ByPayCode($payCode);
				if (!empty($payData) && $payData->status < 1 && $payData->create_time_int < $nowTime - $expireTime) {
					return $result;
				}
			}
			$payCode = '';
			do {
				$payCode = $nowTime . '' . mt_rand(100000, 999999); //生成付款码
				$payId = $userPMd->getPyid($payCode);
			} while (!empty($payId)); //如果是付款码重复则重新提交数据
			$pcodeData = [
				'user_id'         => $uid,
				'pcode'           => $payCode,
				'create_time_int' => $nowTime,
			];
			Cache::put($cacheKey, $payCode, 1);
			$userPMd->create($pcodeData);
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "createPayCode 创建支付码失败:" . json_decode($uid) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return $result;
	}

	/**
	 * 检测付款码是否生成订单
	 * @author: colin
	 * @date: 2019/5/21 19:26
	 * @return array|\Library\type
	 */
	public  function checkPayCode()
	{
		try{
			$uid = $this->request->input('uid');
			$cacheKey = "paymentCode_" . $uid;
			$payCode = Cache::get($cacheKey);
			if (empty($payCode))
				throw new \Exception("没有生成付款码");
			$orderSn = Cache::get('paycode_order_' . $payCode);
			if (empty($orderSn))
				throw new Exception('没有生成订单');
			$data   = ['order_sn' => $orderSn];
		}catch(\Exception $e){
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "checkPayCode 检测支付码是否生成订单失败:" . json_decode($uid) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return $data;
	}
	/**
	 * 展示付款码
	 * @author: colin
	 * @date: 2019/1/11 17:24
	 */
	public function showPayCode()
	{
		$type = $this->request->input('type');
		$uid = $this->request->input('uid');
		try{
			$cacheKey = "paymentCode_" . $uid;
			$code = Cache::get($cacheKey);
			if (empty($code)) {
				throw new Exception("没有生成付款码");
			}
			switch($type){
				case 1:
					\Library\Third\QRcode::png($code, false, 1, 5, 2);
					break;
				case 2:
					$barcode = new \Library\Third\BarCode128($code);
					$showTxt = $this->request->input('show_txt');
					$barcode->createBarCode('png', $showTxt);
					break;
				default:
					break;
			}
		}catch(\Exception $e){
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "showPayCode 展示图形码失败:" . json_decode($uid) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
	}

	/***
	 * 系统消息列表
	 * @author: colin
	 * @date: 2019/1/14 15:21
	 * @return bool|\Library\type
	 */
	public function notification(){
		$uid = $this->request->input('uid');
		$param = $this->request->all();
		$userBMd = new App\Repositories\UserMessageRepository();
		//ecs_user_message_records
		try {
			$result = $userBMd->list($uid,$param);
			if($result === false){
				throw new \Exception('未查询到系统信息！');
			}
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "notification 系统消息列表查询失败:" . json_decode($uid) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return $result;
	}
	/***
	 * 系统消息详情
	 * @author: colin
	 * @date: 2019/1/14 15:21
	 * @return bool|\Library\type
	 */
	public function notifidetail(){
		$uid = $this->request->input('uid');
		$id = $this->request->input('id');
		$userBMd = new App\Repositories\UserMessageRepository();
		try {
			$result = $userBMd->detail($uid,$id);
			if($result === false){
				throw new \Exception('未查询消息详情信息！');
			}
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "notifidetail 消息详情查询:" . json_decode($uid) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return $result;
	}

	/**
	 * 意见反馈
	 * @author: colin
	 * @date: 2019/1/14 17:37
	 * @return array|bool|\Library\type
	 */
	public function feedBack()
	{
		$uid = $this->request->input('uid');
		$userInfo = $this->request->input('userInfo');
		$content = $this->request->input('content');
		try {
			$data = [
				'user_id' => $uid,
				'user_name' => $userInfo['name'],
				'msg_title' => '意见反馈',
				'msg_time' => time(),
				'msg_content' => $content,
				'msg_area' => 1,
			];
			\App\Models\FeedBack::create($data);
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "feedBack 意见反馈:" . json_decode($uid) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return true;
	}

	/**
	 * 幸福券激活
	 * @author: colin
	 * @date: 2019/5/21 15:29
	 * @return array|\Library\type
	 * @throws Exception
	 */
	public function activate()
	{
		$uid = $this->request->input('uid');
		$bonusSn = $this->request->input('bonusSn');
		$cardpwd = $this->request->input('cardpwd');
		try {
			$result = help::addBonus($uid,$bonusSn,$cardpwd);
			if($result['status'] === false)
				throw new \Exception($result['message']);
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "activate 激活幸福卡:" . json_decode($uid) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return true;
	}
	/**
	 * 幸福券延期
	 * @author: colin
	 * @date: 2019/2/15 11:13
	 * @return bool|\Library\type
	 */
	public function cardDelay()
	{
		$uid = $this->request->input('uid');
		$cardId = $this->request->input('cardId');
		$userBMd = new App\Repositories\UserBonusRepository();
		\DB::beginTransaction();
		try {
			$bonusData = $userBMd->getBonus($cardId);
			if (empty($bonusData) || $bonusData['user_id'] != $uid) {
				throw new Exception('该券不存在，请确认');
			}
			$time = time();
			$endData = $bonusData['bonus_end_date'] ? $bonusData['bonus_end_date'] : $bonusData['use_end_date'];
			if ($endData > $time) {
				throw new Exception('该券还在有效期内，不需要延期');
			}
			$fee = 5;//扣除的费用
			if ($bonusData['balance'] < $fee) {
				throw new Exception('该券余额小于扣除的费用，延期失败');
			}
			$balance = $bonusData['balance'] - $fee;
			$bonusEndDate = strtotime('+6 month', $endData);

			help::logAccountChangeTwo($uid, $fee * (-1), '', 0, '幸福券(' . $bonusData['bonus_sn'] . ')延期扣除费用', $cardId);
			$uData = [
				'balance'        => $balance,
				'bonus_status'   => $balance > 0 ? 1 : 2,
				'bonus_end_date' => $bonusEndDate,
			];
			$affectedRows = $userBMd->update($uData,['bonus_id'=>$cardId]);
			if (!$affectedRows) {
				throw new Exception('申请延期失败，请重试');
			}
		} catch (\Exception $e) {
			\DB::rollBack();
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "feedBack 意见反馈:" . json_decode($uid) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		\DB::commit();
		return $uData;
	}
	/**
	 * 获取小程序手机号码
	 * @author: colin
	 * @date: 2019/5/14 15:21
	 * @return array
	 */
    public function getwxtel()
	{
		try {
			$appId = config('wechat.mini_program.default.app_id');
			$appSecret = config('wechat.mini_program.default.secret');
			if (empty($appId) || empty($appSecret)) {
				throw new Exception("未配置微信相关数据");
			}
			$param = $this->request->all();
			//微信接口
			$wxApiUrl = "https://api.weixin.qq.com/sns/jscode2session?appid=$appId&secret=$appSecret&js_code={$param['code']}&grant_type=authorization_code";
			$result = help::curlHttp($wxApiUrl);
			$response = json_decode($result, true);
			if (empty($response) || !empty($response['errcode']) || empty($response['openid'])) {
				$errCode = isset($response['errcode']) ? $response['errcode'] : '';
				throw new Exception('接口调用失败,code:' . $errCode);
			}
			$sessionKey = $response['session_key'];
			$pc = new \Library\Wechat\WXBizDataCrypt($appId, $sessionKey);
			$data = '';
			$errCode = $pc->decryptData($param['encryptedData'], $param['iv'], $data);
			if ($errCode == 0) {
				$arrData = json_decode($data, true);
				if (empty($arrData['phoneNumber'])) {
					throw new Exception('未绑定手机号码');
				}
				$resData = $this->userMd->getInfoByName($arrData['phoneNumber']);
				if ($resData === false) {
					//直接注册成为会员
					$dataInfo = [
						'user_name' => $param['mobile'],
						'nickname' => '微信用户',
						'password' => help::setPassWord($param['password'], 1111),
						'ec_salt' => 1111,
						'device_type' => 'wxcxx',
						'reg_time' => time(),
						'user_money_date' => strtotime(date("Y-m-d", strtotime("+1 months"))),
					];
					$userObj = $this->userMd->create($dataInfo);
					if ($userObj === false) {
						throw new Exception('该手机号码[' . $arrData['phoneNumber'] . ']注册失败');
					}
					$userInfo = [
						'uid' => $userObj->user_id,
						'name' => $userObj->user_name,
						'ctime' => time(),
					];
					$token = md5(json_encode($userInfo));
				} else {
					$userInfo = [
						'uid' => $resData->user_id,
						'name' => $resData->user_name,
						'ctime' => time(),
					];
					$token = md5(json_encode($userInfo));
				}
				$expiresAt = 60 * 24 * 30;
				Cache::put('user_login_' . $token, $userInfo, $expiresAt);
				\App\Models\UserToken::create(['token' => $token, 'uid' => $userInfo['uid']]);
				$res =  ['token' => $token, 'uname' => $arrData['phoneNumber']];
			} else {
				throw new Exception('登录失败，errCode:' . $errCode);
			}
		} catch (Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "getwxtel 获取微信小程序失败:" . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return $res;
	}

}