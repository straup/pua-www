<?php

	include("include/init.php");

	loadlib("subscriptions");
	loadlib("flickr_users");

	login_ensure_loggedin("/photosof/friends");

	$flickr_user = flickr_users_get_by_user_id($GLOBALS['cfg']['user']['id']);

	$map = flickr_push_topic_map('string keys');
	$topic_id = $map['photos_of_contacts'];

	$subscription = subscriptions_get_by_user_and_topic($GLOBALS['cfg']['user'], $topic_id);

	if (! $subscription){
		header("location: {$GLOBALS['cfg']['abs_root_url']}photosof/friends/subscribe");
		exit;
	}

	$topic_map = subscriptions_topic_map();
	$topic = $topic_map[$topic_id];

	$GLOBALS['smarty']->assign("topic", $topic);
	$GLOBALS['smarty']->assign_by_ref("subscription", $subscription);

	$GLOBALS['smarty']->display("page_photosof_friends.txt");
	exit();
?>
