<?php

namespace Library\Third;

use \Helper\CFunctionHelper as help;

class ThirdFactory
{
	/***
	 * 创建支付类实例
	 * @author: colin
	 * @date: 2019/5/28 11:09
	 * @param $paymentId int 支付方式ID
	 * @return mixed 返回支付插件类对象
	 * @throws \Exception
	 */
	public static function makePayment($paymentId)
	{

		$logger = new \Helper\CLoggerHelper(LOG_PATH . "/Factory/", date("Ymd"));

		$name = config("third.payment.{$paymentId}.path");
		$modPath = APP_PATH."/Include/Library/Third/Payment/{$name}/{$name}.php";
		if(file_exists($modPath) == false) {
			$trace = help::backtraceToString(debug_backtrace());
			$logger->logError(__CLASS__.",paymentId:{$paymentId}.trade:{$trace},line:".__LINE__);
			throw new \Exception("MODEL NOT FOUND", -10001);
		}
		require_once $modPath;
		$className = 'Library\Third\Payment\\'.$name.'\\'.$name;
		return new $className($paymentId);
	}
}