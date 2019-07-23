<?php
namespace Library\Third\Push\Notification\android;
use \Library\Third\Push\Notification\AndroidNotification;
class AndroidListcast extends AndroidNotification {
	function __construct() {
		parent::__construct();
		$this->data["type"] = "listcast";
		$this->data["device_tokens"] = NULL;
	}

}