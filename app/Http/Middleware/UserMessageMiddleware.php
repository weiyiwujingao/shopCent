<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;

class UserMessageMiddleware
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
        if(empty($token)){
			return $next($request);
		}
		$token = 'user_login_'.$token;
        $sessionData = Cache::get($token);
        if(!isset($sessionData['uid'])){
			return $next($request);
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
