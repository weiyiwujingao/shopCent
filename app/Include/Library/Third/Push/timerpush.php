<?php
define('IN_ECS', true);
require_once(dirname(__FILE__).'/../includes/init.php');
require_once(dirname(__FILE__) . '/PushTools.php');
set_time_limit(60);

        //date_default_timezone_set('PRC');
        $sqwl = 'SELECT DISTINCT b.gs_login_name FROM ' . $ecs->table("order_info") . ' a inner join ' .$ecs->table("goods_stores").' b on a.order_pick_stores=b.gs_id 
		 WHERE b.gs_stats=1 and a.isread is null and a.pay_status=2 and '.local_gettime().'-b.max_order_time>300' ;
        $res = $db->getAll($sqwl);
		$tools = new PushTools();
		foreach($res as $arrKey=>$val)
		{  	
	        $tools -> notifyNewOrder($val['gs_login_name']);
			$sql = "update " .$ecs->table("goods_stores")." set max_order_time =".local_gettime()." where gs_login_name='".$val['gs_login_name']."'"; 
			$db->query($sql);
        }
?>