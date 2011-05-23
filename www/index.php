<?php

	include("include/init.php");
	loadlib("subscriptions");

	if ($GLOBALS['cfg']['user']){

		$map = subscriptions_topic_map();
		$rsp = subscriptions_for_user($GLOBALS['cfg']['user']);

		$GLOBALS['smarty']->assign_by_ref("subscriptions", $rsp['rows']);
		$GLOBALS['smarty']->assign_by_ref("topic_map", $map);
	}

	$GLOBALS['smarty']->display("page_index.txt");
	exit;
?>
