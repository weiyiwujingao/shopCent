<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;

class UserLoginMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
    	$token = $request->header("token") ?? '';
    	if(!$token)
    		$token = $request->input("token") ?? '';
		$res = ['status' => -99,'data'=>'','message' => '未登录！'];
        if(empty($token)){
			return response()->json($res);
		}
		$token = 'user_login_'.$token;
        $sessionData = Cache::get($token);
        if(!isset($sessionData['uid'])){
            return response()->json($res);
        }
		if (!empty($sessionData)) {
			$expiresAt = 60 * 24 * 30;
			Cache::put($token, $sessionData, $expiresAt);
		}
		$userInfo['userInfo'] = $sessionData;
		$userInfo['uid'] = $sessionData['uid'];
		$request->merge($userInfo);
        return $next($request);

    }
}
