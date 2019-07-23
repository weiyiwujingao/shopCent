<?php

namespace Library\Factory\Shipping;


class ShippingFactory {

    public static function make($act,$shippingConfig) {
        $logger = new \Helper\CLoggerHelper(LOG_PATH . "/Factory/", date("Ymd"));
        $modPath = APP_PATH."/Include/Library/Shipping/".$act.".php";
        if(file_exists($modPath) == false) {
            $logger->logError(__CLASS__.",act:{$act},line:".__LINE__);
            throw new \Exception("MODEL NOT FOUND", -10001);
        }
       // require_once $modPath;
        $className = "Library\Shipping\\".$act;
        return new $className($shippingConfig);
    }

}
