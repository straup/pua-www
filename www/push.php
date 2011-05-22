<?php

	include("include/init.php");

	loadlib("pua_subscriptions");

	$secret_url = get_str("secret_url");

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

		# update subscription here

		echo get_str("challenge");
		exit();
	}

	$cache_key = "flickr_push_{$subscription['user_id']}_{$subscription['type']}";

	# parse atom feed and store photos here...

	exit();
?>
