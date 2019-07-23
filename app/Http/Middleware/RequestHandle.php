<?php

namespace App\Http\Middleware;

use Closure;

class RequestHandle
{

    /**
     * @var int 默认分页大小
     */
    protected $defaultPageSize = 20;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $providerUser['user'] = session(config('session.login_user'));
        $providerUser['pageSize'] = $this->defaultPageSize;
        $request->has('page') ?: $providerUser['page'] = 1;
        $request->merge($providerUser);
        $request->replace($this->filterSpace($request->all()));
        return $next($request);
    }

    /**
     * 过滤空字符
     * @param array $params
     * @return array
     */
    protected function filterSpace($params)
    {
        foreach ((array)$params as $k => $v) {
            if (is_array($v)) {
                $params[$k] = $this->filterSpace($v);
            } else {
                $params[$k] = trim($v);
            }
        }
        return $params;
    }
}
