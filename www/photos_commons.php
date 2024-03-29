<?php

	include("include/init.php");

	loadlib("subscriptions");
	loadlib("flickr_users");

	login_ensure_loggedin("/photos/commons");

	$flickr_user = flickr_users_get_by_user_id($GLOBALS['cfg']['user']['id']);

	$map = flickr_push_topic_map('string keys');
	$topic_id = $map['commons'];

	$subscription = subscriptions_get_by_user_and_topic($GLOBALS['cfg']['user'], $topic_id);

	if (! $subscription){
		header("location: {$GLOBALS['cfg']['abs_root_url']}photos/commons/subscribe");
		exit;
	}

	$GLOBALS['smarty']->assign_by_ref("topic", $subscription['topic_url']);

	$GLOBALS['smarty']->display("page_photos_commons.txt");
	exit();
?>
