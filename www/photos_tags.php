<?php

	include("include/init.php");

	loadlib("subscriptions");
	loadlib("subscription_urls");
	loadlib("flickr_users");

	login_ensure_loggedin("/photo/tags/");

	$tags = get_str("tags");

	if (! $tags){
		header("location: {$GLOBALS['cfg']['abs_root_url']}photos/tags/subscribe");
		exit;
	}

	$url = "photos/places/{$tags}/";
	$url = subscription_urls_get_by_url($url);

	if (! $url){
		error_404();
	}

	$subscription = subscriptions_get_by_user_and_url($GLOBALS['cfg']['user'], $url['id']);

	if (! $subscription){
		header("location: {$GLOBALS['cfg']['abs_root_url']}places/subscribe");
		exit;
	}

	$mock_topic = array(
		'url' => $url['url'],
	);

	$GLOBALS['smarty']->assign("topic", $mock_topic);
	$GLOBALS['smarty']->assign_by_ref("subscription", $subscription);

	$GLOBALS['smarty']->display("page_places.txt");
	exit();
	
?>
