<?php

	include("../include/init.php");
	loadlib("subscriptions");

	$rsp = subscriptions_recent_activity();

	$activity = array();

	foreach ($rsp['rows'] as $row){

		$row['user'] = users_get_by_id($row['user_id']);
		$activity[] = $row;
	}

	$GLOBALS['smarty']->assign_by_ref("activity", $activity);

	$GLOBALS['smarty']->display("page_god_recent_activity.txt");
	exit();
?>
