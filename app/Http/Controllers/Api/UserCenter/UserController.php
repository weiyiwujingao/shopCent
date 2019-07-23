<?php
/**
 * 用户中心,用户信息处理
 */

namespace App\Http\Controllers\Api\UserCenter;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserCenter\LoginRequest;
use App\Http\Requests\Api\UserCenter\SendSmsRequest;
use App\Http\Requests\Api\UserCenter\SmsLoginRequest;
use App\Http\Requests\Api\UserCenter\RegisterRequest;
use App\Http\Requests\Api\UserCenter\RegisterSendSmsRequest;
use App\Http\Requests\Api\UserCenter\ModifySendSmsRequest;
use App\Http\Requests\Api\UserCenter\ModPswByMobileRequest;
use App\Http\Requests\Api\UserCenter\AccountBillRequest;
use App\Http\Requests\Api\UserCenter\CardListRequest;
use App\Http\Requests\Api\UserCenter\ModPswRequest;
use App\Http\Requests\Api\UserCenter\ShowPayCodeRequest;
use App\Http\Requests\Api\UserCenter\NotifycationRequest;
use App\Http\Requests\Api\UserCenter\NotifidetailRequest;
use App\Http\Requests\Api\UserCenter\FeedBackRequest;
use App\Http\Requests\Api\UserCenter\CardRelayRequest;
use App\Http\Requests\Api\UserCenter\GetWxtelRequest;
use App\Http\Requests\Api\UserCenter\CardActivateRequest;
use Session;


class UserController extends Controller
{
    protected $request;
    protected $Obj;
    protected $userInfo;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->Obj = new \Library\UserCenter\User($this->request);
    }

    /**
     * 登录
     * @author colin
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login(LoginRequest $LoginRequest)
    {
    	$loginRe = $this->Obj->getLogin();
        if ($loginRe === false) {
            return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
        }
        return self::showSuccess($loginRe, '登录成功!');
    }

	/**
	 * 注册
	 * @author: colin
	 * @date: 2019/1/8 9:11
	 * @param RegisterRequest $Request
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
	public function register(RegisterRequest $Request)
	{
		$loginRe = $this->Obj->register();
		if ($loginRe === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($loginRe, '注册成功!');
	}
	/**
	 * 手机验证码登录
	 * @author colin
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function smsLogin(SmsLoginRequest $SmsLoginRequest)
	{
		$loginRe = $this->Obj->smsLogin();
		if ($loginRe === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($loginRe, '登录成功!');
	}

	/**
	 * 用户登录发送短信验证码
	 * @author: colin
	 * @date: 2019/1/7 17:04
	 * @param SendSmsRequest $SendSmsRequest
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
	public function sendSms(SendSmsRequest $SendSmsRequest){
		$result = $this->Obj->sendSms();
		if ($result === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess();
	}

	/**
	 * 用户注册发送短信验证码
	 * @author: colin
	 * @date: 2019/1/7 17:04
	 * @param RegisterSendSmsRequest $Request
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
	public function registerSendSms(RegisterSendSmsRequest $Request){
		$result = $this->Obj->registerSendSms();
		if ($result === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess();
	}

	/**
	 * 修改密码发送短信
	 * @author: colin
	 * @date: 2019/1/9 10:33
	 * @param ModifySendSmsRequest $SendSmsRequest
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
	public function modifysendSms(ModifySendSmsRequest $request){
		$result = $this->Obj->modifysendSms();
		if ($result === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess();
	}
	/**
	 * 判断是否是有效的登录token
	 * @author colin
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function isLogin()
	{
		$isLogin = $this->Obj->isLogin();
		if ($isLogin === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess('', '请求成功!');
	}
    /**
     * 注销用户
     * @author colin
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function logout()
    {
        $logout = $this->Obj->logout();
        if ($logout === false) {
            return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
        }
        return self::showSuccess($logout, '注销成功!');
    }
	/**
	 * 验证码地址
	 * @author: colin
	 * @date: 2019/1/8 10:33
	 */
	public function getVcode()
	{
		$url = '//'.$_SERVER['HTTP_HOST'].'/mctApi/user/vcode';
		return self::showSuccess($url);
	}
	/**
	 * 验证码
	 * @author: colin
	 * @date: 2019/1/8 10:33
	 */
    public function vcode()
	{
		$vObj = new \Library\Third\Vcode();
		$vObj->doimg();
		Session::put('xfjb_code',$vObj->getCode());
	}
	/**
	 * 上传头像图片
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function upload()
	{
		$url = $this->Obj->upload();
		if ($url === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($url, '头像修改成功!');
	}
	/**
	 * 修改用户信息
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function modifyUser()
	{
		$url = $this->Obj->modifyUser();
		if ($url === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($url, '修改成功!');
	}

	/**
	 * 用户修改密码-知道密码
	 * @author: colin
	 * @date: 2019/1/9 9:20
	 * @param ModPswRequest $ModPswRequest
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
	public function modifyPsw(ModPswRequest $ModPswRequest)
	{
		$res = $this->Obj->modifypsw();//参数处理
		if ($res === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		$this->logout();//注销登录信息，重新登录
		return self::showSuccess('', '重置密码成功！');

	}
	/**
	 * 用户修改密码-手机验证码
	 * @author: colin
	 * @date: 2019/1/9 10:55
	 * @param ModPswByMobileRequest $request
	 * @return $this|\Illuminate\Http\JsonResponse
	 * @throws \Exception
	 */
	public function modifyPswBymobile(ModPswByMobileRequest $request)
	{
		$res = $this->Obj->modifyPswBymobile();//参数处理
		if ($res === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		$this->logout();//注销登录信息，重新登录
		return self::showSuccess('', '重置密码成功！');

	}

	/**
	 * 用户详情
	 * @author: colin
	 * @date: 2019/6/6 16:06
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
	public function userDetail()
	{
		$userInfo = $this->Obj->userDetail();
		if ($userInfo === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($userInfo);

	}

	/**
	 * 我收藏的店铺
	 * @author: colin
	 * @date: 2019/1/10 8:46
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
	public function collectStores()
	{
		$userInfo = $this->Obj->collectStores();
		if ($userInfo === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($userInfo);

	}
	/**
	 * 我的账单
	 * @author colin
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function accoountBill(AccountBillRequest $request)
	{
		$list = $this->Obj->accoountBill();
		if ($list === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($list);
	}
	/**
	 * 幸福卡统计
	 * @author: colin
	 * @date: 2019/1/11 10:23
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
	public function userCardStatis()
	{
		$list = $this->Obj->userCardStatis();
		if ($list === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($list);
	}
	/**
	 * 幸福卡列表
	 * @author: colin
	 * @date: 2019/1/11 10:23
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
	public function userCardList(CardListRequest $request)
	{
		$list = $this->Obj->userCardList();
		if ($list === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($list);
	}

	/**
	 * 创建付款码
	 * @author: colin
	 * @date: 2019/1/11 15:08
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
	public function createPayCode()
	{
		$result = $this->Obj->createPayCode();
		if ($result === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($result);
	}

	/**
	 * 检测付款码是否生成订单
	 * @author: colin
	 * @date: 2019/5/21 19:18
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
	public function checkPayCode()
	{
		$result = $this->Obj->checkPayCode();
		if ($result === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($result);
	}
	/**
	 * 展示付款码
	 * @author: colin
	 * @date: 2019/1/11 15:08
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
	public function showPayCode(ShowPayCodeRequest $request)
	{
		$this->Obj->showPayCode();
	}
	/**
	 * 系统消息列表
	 * @author: colin
	 * @date: 2019/1/14 15:14
	 * @param NotifycationRequest $request
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
	public function notification(NotifycationRequest $request)
	{
		$result = $this->Obj->notification();
		if ($result === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($result);
	}
	/**
	 * 系统消息详情
	 * @author: colin
	 * @date: 2019/1/14 15:14
	 * @param NotifycationRequest $request
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
	public function notifidetail(NotifidetailRequest $request)
	{
		$result = $this->Obj->notifidetail();
		if ($result === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($result);
	}

	/**
	 * 消息反馈
	 * @author: colin
	 * @date: 2019/1/14 17:36
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
	public function feedBack(FeedBackRequest $request)
	{
		$result = $this->Obj->feedBack();
		if ($result === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($result);
	}
	/***
	 * 幸福券延期
	 * @author: colin
	 * @date: 2019/2/15 11:12
	 * @param CardRelayRequest $request
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
	public function cardDelay(CardRelayRequest $request)
	{
		$result = $this->Obj->cardDelay();
		if ($result === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($result);
	}
	/***
	 * 幸福券延期
	 * @author: colin
	 * @date: 2019/2/15 11:12
	 * @param CardRelayRequest $request
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
	public function activate(CardActivateRequest $request)
	{
		$result = $this->Obj->activate();
		if ($result === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($result);
	}
	/***
	 * 获取微信小程序绑定的手机号码
	 * @author: colin
	 * @date: 2019/5/14 14:38
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
	public function getwxtel(GetWxtelRequest $request)
	{
		$result = $this->Obj->getwxtel();
		if ($result === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($result);
	}

}
