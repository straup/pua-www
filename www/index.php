<?php

	include("include/init.php");
	loadlib("subscriptions");

	if ($GLOBALS['cfg']['user']){

		$map = subscriptions_topic_map();
		$rsp = subscriptions_for_user($GLOBALS['cfg']['user']);

		$subscriptions = array();

		foreach ($rsp['rows'] as $row){
			$subscriptions[$row['topic_id']] = $row;
		}

		$GLOBALS['smarty']->assign_by_ref("subscriptions", $subscriptions);
		$GLOBALS['smarty']->assign_by_ref("topic_map", $map);
	}

	$GLOBALS['smarty']->display("page_index.txt");
	exit;
?>
