<?php

	include("include/init.php");

	loadlib("subscriptions");
	loadlib("flickr_users");

	login_ensure_loggedin("/faves/friends");

	$flickr_user = flickr_users_get_by_user_id($GLOBALS['cfg']['user']['id']);

	$subscription = subscriptions_get_by_user_and_topic($GLOBALS['cfg']['user'], $topic_id);

	if (! $subscription){
		header("location: {$GLOBALS['cfg']['abs_root_url'}faves/friends/subscribe");
		exit;
	}

	$GLOBALS['smarty']->assign_by_ref("subscription", $subscription);

	$GLOBALS['smarty']->display("page_faves_friends.txt");
	exit();
?>
