<?php
namespace Library\Third\Push\Notification\android;
use \Library\Third\Push\Notification\AndroidNotification;
class AndroidGroupcast extends AndroidNotification {
	function  __construct() {
		parent::__construct();
		$this->data["type"] = "groupcast";
		$this->data["filter"]  = NULL;
	}
}