<?php
/**
 * 商户中心,用户信息处理
 */

namespace App\Http\Controllers\Api\Merchant;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use App\Http\Requests\Api\Merchant\ShopSetRequest;
use App\Http\Requests\Api\Merchant\LoginRequest;
use App\Http\Requests\Api\Merchant\ModPswRequest;
use App\Http\Requests\Api\Merchant\UntiWeixinRequest;

class UserController extends Controller
{
    protected $request;
    protected $iationObj;
    protected $userInfo;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->Obj = new \Library\MerchantUser($this->request);
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
     * 商户信息
     * @author colin
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function userInfo()
    {
        $info = $this->Obj->getUserInfo();
        if ($info === false) {
            return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
        }
        return self::showSuccess($info);
    }

    /***
     * 商户经营信息
     * @author: colin
     * @date: 2018/11/8 15:56
     */
    public function BusinessInfo()
    {
        $info = $this->Obj->getBusinessInfo();
        if ($info === false) {
            return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
        }
        return self::showSuccess($info);
    }

    /**
     * 商户修改密码
     * @author: colin
     * @date: 2018/11/8 14:41
     * @return $this|\Illuminate\Http\JsonResponse
     */
    public function modifyPsw(ModPswRequest $ModPswRequest)
    {
        $res = $this->Obj->getModifypsw();//参数处理
        if ($res === false) {
            return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
        }
        $this->logout();//注销登录信息，重新登录
        return self::showSuccess('', '重置密码成功！');

    }

    /***
     * 门店设置信息
     * @author: colin
     * @date: 2018/11/8 16:47
     */
    public function shopInfo()
    {
        $shopInfo = $this->Obj->shopInfo();
        if ($shopInfo === false) {
            return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
        }
        return self::showSuccess($shopInfo);
    }

    /***
     * 修改门店设置信息
     * @author: colin
     * @date: 2018/11/8 16:47
     */
    public function shopSet(ShopSetRequest $ShopSetRequest)
    {
        $res = $this->Obj->shopSet($ShopSetRequest);
        if ($res === false) {
            return self::showError('更新店铺设置失败！');
        }
        return self::showSuccess('', '更新店铺设置成功！');
    }

    /***
     * 微信解绑
     * @author: colin
     * @date: 2018/11/8 16:47
     */
    public function UntieWeixin(UntiWeixinRequest $UntiWeixinRequest)
    {
        $res = $this->Obj->UntieWeixin();
        if ($res === false) {
            return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
        }
        return self::showSuccess('', '微信解绑成功！');
    }

    /**
     * 绑定微信
     * @return mixed
     */
    public function bindWechat()
    {
        $token = $this->request->token;
        if (!empty($token)) {
            $uData = Cache::get($token);
            if (!empty($uData['gs_id'])) {
                $user = session('wechat.oauth_user.default');
                if (!empty($user)) {
                    $getData = \App\Models\StoreWeixin::where(['gs_id' => $uData['gs_id'], 'openid' => $user['id']])->first();
                    if (empty($getData)) {
                        \App\Models\StoreWeixin::updateOrCreate([
                            'gs_id'      => $uData['gs_id'],
                            'openid'     => $user['id'],
                            'nickname'   => $user['nickname'],
                            'headimgurl' => $user['avatar'],
                        ]);
                    }
                }
            }
        }
        return redirect('/home/user/index');
    }

	/**
	 * 分店信息
	 * @author: colin
	 * @date: 2019/5/27 15:10
	 * @return $this|\Illuminate\Http\JsonResponse
	 */
    public function branchStore()
	{
		$branchStore = $this->Obj->branchStore();
		if ($branchStore === false) {
			return self::showError($this->Obj->getUserMsg(), $this->Obj->getErrorNo());
		}
		return self::showSuccess($branchStore);
	}
}
