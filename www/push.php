<?php

	include("include/init.php");

	loadlib("pua_subscriptions");
	loadlib("atom");

	$secret_url = get_str("secret_url");
	error_log("[PUSH] secret: {$secret_url}");

	if (! $secret_url){
		error_404();
	}

	$subscription = pua_subscriptions_get_by_secret_url($secret_url);

	if (! $subscription){
		error_404();
	}

	# do validation dance here

	if ($verify_token = get_str("verify_token")){

		if ($subscription['verify_token'] != $verify_token){
			error_404();
		}

		$update = array(
			'verified' => time(),
		);

		pua_subscriptions_update($subscription, $update);

		echo get_str("challenge");
		exit();
	}

	$cache_key = "flickr_push_u{$subscription['user_id']}_t{$subscription['topic_id']}";
	error_log("[PUSH] {$cache_key}");

	# parse atom feed and store photos here...

	$xml = file_get_contents('php://input');
	$atom = atom_parse_str($xml);

	foreach ($atom->items as $e){
		error_log("[PUSH] {$e['title']}");
	}

	error_log("[PUSH] finished pushing...");
	exit();
?>
