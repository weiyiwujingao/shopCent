<?php
namespace Library\Third\Push\Notification\ios;
use \Library\Third\Push\Notification\IOSNotification;
class IOSUnicast extends IOSNotification {
	function __construct() {
		parent::__construct();
		$this->data["type"] = "unicast";
		$this->data["device_tokens"] = NULL;
	}

}