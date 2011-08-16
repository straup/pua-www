<?php

	include("include/init.php");

	loadlib("subscriptions");
	loadlib("flickr_users");

	login_ensure_loggedin("/photos/friends");

	$flickr_user = flickr_users_get_by_user_id($GLOBALS['cfg']['user']['id']);

	$map = flickr_push_topic_map('string keys');
	$topic_id = $map['contacts_photos'];

	$subscription = subscriptions_get_by_user_and_topic($GLOBALS['cfg']['user'], $topic_id);

	if (! $subscription){
		header("location: {$GLOBALS['cfg']['abs_root_url']}photos/friends/subscribe");
		exit;
	}

	$GLOBALS['smarty']->assign("topic", $subscription['topic_url']);

	$GLOBALS['smarty']->display("page_photos_friends.txt");
	exit();
?>
