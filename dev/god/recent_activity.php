<?php

	include("../include/init.php");
	loadlib("subscriptions");

	$page = ($p = get_int32("page")) ? $p : 1;

	$args = array(
		'page' => $page,
	);

	$rsp = subscriptions_recent_activity($args);

	$activity = array();

	$now = time();
	$then = $now - (60 * 60);

	foreach ($rsp['rows'] as $row){

		$row['user'] = users_get_by_id($row['user_id']);

		$row['recent'] = ($row['last_request'] >= $then) ? 1 : 0;

		$activity[] = $row;
	}

	$GLOBALS['smarty']->assign_by_ref("activity", $activity);

	$topic_map = flickr_push_topic_map();
	$GLOBALS['smarty']->assign_by_ref("topic_map", $topic_map);

	$GLOBALS['smarty']->assign("pagination_url", "god/recent_activity.php");
	$GLOBALS['smarty']->assign("pagination_query_params", 1);

	$GLOBALS['smarty']->display("page_god_recent_activity.txt");
	exit();
?>
