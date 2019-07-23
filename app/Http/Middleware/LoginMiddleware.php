<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;

class LoginMiddleware
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
		$res = ['status' => -99,'data'=>'','message' => '未登录！'];
        if(empty($token)){
			return response()->json($res);
		}
        $sessionData = Cache::get($token);
        if(!isset($sessionData['gs_id'])){
            return response()->json($res);
        }
		if (!empty($sessionData)) {
			$expiresAt = 60 * 24 * 30;
			Cache::put($token, $sessionData, $expiresAt);
		}
		$userInfo['userInfo'] = $sessionData;
		$userInfo['gsId'] = $sessionData['gs_id'];
		$request->merge($userInfo);
        return $next($request);

    }
}
