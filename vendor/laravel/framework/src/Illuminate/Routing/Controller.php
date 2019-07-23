<?php

namespace Illuminate\Routing;

use BadMethodCallException;

abstract class Controller
{
    /**
     * The middleware registered on the controller.
     *
     * @var array
     */
    protected $middleware = [];

    /**
     * Register middleware on the controller.
     *
     * @param  array|string|\Closure  $middleware
     * @param  array   $options
     * @return \Illuminate\Routing\ControllerMiddlewareOptions
     */
    public function middleware($middleware, array $options = [])
    {
        foreach ((array) $middleware as $m) {
            $this->middleware[] = [
                'middleware' => $m,
                'options' => &$options,
            ];
        }

        return new ControllerMiddlewareOptions($options);
    }


    /**
     * Get the middleware assigned to the controller.
     *
     * @return array
     */
    public function getMiddleware()
    {
        return $this->middleware;
    }

    /**
     * Execute an action on the controller.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function callAction($method, $parameters)
    {
        return call_user_func_array([$this, $method], $parameters);
    }

    /**
     * Handle calls to missing methods on the controller.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        throw new BadMethodCallException("Method [{$method}] does not exist on [".get_class($this).'].');
    }

	/**
	 * 显示成功结果
	 * @param type $data
	 * @param type $message
	 */
	public static function showSuccess($data = [], $message = "") {
		return self::showJson($data, \Enum\EnumMain::HTTP_CODE_OK, $message);
	}

	/**
	 * 显示失败结果
	 * @param type $message
	 */
	public static function showError($message = "", $code = \Enum\EnumMain::HTTP_CODE_FAIL, $data = []) {
		$code = intval($code);
		if(empty($code) || $code < 200){
			$code = \Enum\EnumMain::HTTP_CODE_FAIL;
		}
		$logger = new \Helper\CLoggerHelper(LOG_PATH . "Http/" , date("YmdH"));
		$logMsg = "http return fail.message:{$message},code:{$code},url:".\Request::url();
		$logMsg .= ",accessToken:".\Cookie::get("access_token").",param:".json_encode(\Request::all());
		$logger->logNotice($logMsg, \Helper\CLoggerHelper::LOG_LEVEL_INFO);
		//var_dump($data, $code, $message, $code);die;
		return self::showJson($data, $code, $message, $code);
	}

	/**
	 * 显示json结果
	 * @param type $data
	 * @param type $code
	 * @param type $message
	 */
	public static function showJson($data = [], $code = \Enum\EnumMain::HTTP_CODE_OK, $message = "") {
		//客户端的HTTP CODE和redCode一致，非客户端的，HTTP CODE全为200
		if(\Request::input('platform') == 1 || \Request::input('platform') == 2){
			$httpCode = $code;
		}else{
			$httpCode = 200;
		}
		$callback = \Request::get('callback');
		$header = [
			'Access-Control-Allow-Headers'      => 'x-requested-with,content-type',
			'Content-Type'                      => 'application/json;charset=UTF-8',
		];
		if($callback){
			return response()->json(array(
				'status' => $code,
				'data' => $data,
				'message' => $message,
			), $httpCode, $header, JSON_UNESCAPED_UNICODE)->setCallback($callback);
		}else{
			return response()->json(array(
				'status' => $code,
				'data' => $data,
				'message' => $message,
			), $httpCode, $header, JSON_UNESCAPED_UNICODE);
		}
	}
}
