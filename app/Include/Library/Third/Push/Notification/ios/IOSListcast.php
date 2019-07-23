<?php
namespace Library\Third\Push\Notification\ios;
use \Library\Third\Push\Notification\IOSNotification;
class IOSListcast extends IOSNotification {
	function __construct() {
		parent::__construct();
		$this->data["type"] = "listcast";
		$this->data["device_tokens"] = NULL;
	}

}