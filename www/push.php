<?php

	include("include/init.php");

	loadlib("pua_subscriptions");
	loadlib("atom");

	include_once("Redis.php");

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

		$update = array(
			'verified' => time(),
		);

		pua_subscriptions_update($subscription, $update);

		echo get_str("challenge");
		exit();
	}

	$redis_key = "flickr_push_updates_{$subscription['id']}";

	# parse atom feed and store photos here...

	$xml = file_get_contents('php://input');
	$atom = atom_parse_str($xml);

	#

	$redis = new Redis();
	$new = 0;

	foreach ($atom->items as $e){

		$photo = array(
			'photo_id' => $e['id'],
			'owner' => $e['flickr']['author_nsid'],
			'ownername' => $e['author'],
			'title' => $e['title'],
			'updated' => $e['updated'],
			'photo_url' => $e['media']['atom_content@url'],
			'thumb_url' => $e['media']['thumbnail@url'],
		);

		$redis->lpush($redis_key, json_encode($photo));
		$new ++;
	}

	#

	$count = $redis->llen($redis_key);
	$max = $GLOBALS['cfg']['flickr_push_max_photos'];

	if ($count > $max){
		$redis->ltrim($redis_key, $max, $count);
	}

	error_log("[PUSH] {$redis_key}: {$count} (new: {$new})");

	#

	$update = array(
		'last_update' => time(),
	);

	pua_subscriptions_update($subscription, $update);

	#

	exit();
?>
