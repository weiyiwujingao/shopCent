<?php
namespace Library\Third\Push\Notification\ios;
use \Library\Third\Push\Notification\IOSNotification;
class IOSBroadcast extends IOSNotification {
	function  __construct() {
		parent::__construct();
		$this->data["type"] = "broadcast";
	}
}