<?php

	include("include/init.php");
	loadlib("subscriptions");
	loadlib("flickr_users");

	login_ensure_loggedin("/places/");

	$map = flickr_push_topic_map('string keys');
	$topic_id = $map['geo'];

	$woeid = get_str("woeid");

	if (! $woeid){
		header("location: {$GLOBALS['cfg']['abs_root_url']}photos/places/subscribe");
		exit;
	}

	$more = array(
		'extra' => $woeid,
	);

	$subscription = subscriptions_get_by_user_and_topic($GLOBALS['cfg']['user'], $topic_id, $more);

	if (! $subscription){
		header("location: {$GLOBALS['cfg']['abs_root_url']}places/subscribe");
		exit;
	}

	$GLOBALS['smarty']->assign("topic", "geo");
	$GLOBALS['smarty']->assign("woeid", "woeid");
	$GLOBALS['smarty']->assign_by_ref("subscription", $subscription);

	$GLOBALS['smarty']->display("page_places.txt");
	exit();
	
?>
