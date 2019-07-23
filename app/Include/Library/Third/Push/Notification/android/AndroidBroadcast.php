<?php

namespace Library\Third\Push\Notification\android;
use \Library\Third\Push\Notification\AndroidNotification;
class AndroidBroadcast extends AndroidNotification {
	function  __construct() {
		parent::__construct();
		$this->data["type"] = "broadcast";
	}
}