<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;

class StoreAuthMiddleware
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
		$auth = \App\Models\StoresUser::where('gs_id',$sessionData['gs_id'])->value('gs_auth');
		if(empty($auth)){
			$res = ['status' => \Enum\EnumMain::HTTP_CODE_FAIL,'data'=>'','message' => '您还没有操作权限！'];
			return response()->json($res);
		}
		$userInfo['auth'] = $auth;
		$request->merge($userInfo);
		return $next($request);
    }
}
