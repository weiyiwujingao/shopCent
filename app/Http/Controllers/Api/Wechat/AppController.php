<?php

namespace App\Http\Controllers\Api\Wechat;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Wechat\JsconfigRequest;
use App\Http\Requests\Api\Wechat\LoginRequest;
use App\Http\Requests\Api\Wechat\WxloginRequest;
use App\Http\Requests\Api\Wechat\WxqrcodeRequest;

class AppController extends Controller
{
	protected $request;
	protected $Obj;
	protected $userInfo;

	public function __construct(Request $request)
	{
		$this->request = $request;
		$this->Obj = new \Library\Wechat\WechatApp($this->request);
	}
	public function jsconfig(JsconfigRequest $JsconfigRequest){
		$result = $this->Obj->jsConfig();
		if ($result === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($result);
	}
	/**
	 * 小程序登录
	 * @author: colin
	 * @date: 2019/5/31 17:28
	 * @param loginRequest $request
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
	public function login(LoginRequest $request)
	{
		$result = $this->Obj->login();
		if ($result === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($result);
	}

	/**
	 * 生成微信小程序的二维码图片
	 * @author: colin
	 * @date: 2019/6/3 11:10
	 * @param WxqrcodeRequest $request
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
	public function qrcode(WxqrcodeRequest $request)
	{
		$result = $this->Obj->qrcode();
		if ($result === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($result);
	}
	public function wxlogin(WxloginRequest $request)
	{
		$result = $this->Obj->wxlogin();
		if ($result === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($result);
	}
}
