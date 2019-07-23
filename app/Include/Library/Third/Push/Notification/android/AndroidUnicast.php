<?php
namespace Library\Third\Push\Notification\android;
use \Library\Third\Push\Notification\AndroidNotification;
class AndroidUnicast extends AndroidNotification {
	function __construct() {
		parent::__construct();
		$this->data["type"] = "unicast";
		$this->data["device_tokens"] = NULL;
	}

}