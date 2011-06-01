<?php

	include("../include/init.php");
	loadlib("subscriptions");

	$rsp = subscriptions_recent_activity();

	$activity = array();

	$now = time();
	$then = $now - (60 * 60);

	foreach ($rsp['rows'] as $row){

		$row['user'] = users_get_by_id($row['user_id']);

		$row['recent'] = ($row['last_request'] >= $then) ? 1 : 0;

		$activity[] = $row;
	}

	$GLOBALS['smarty']->assign_by_ref("activity", $activity);

	$GLOBALS['smarty']->display("page_god_recent_activity.txt");
	exit();
?>
