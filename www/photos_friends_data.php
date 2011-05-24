<?php

	# to be replace by a proper API endpoint if and when
	# I ever get there... (20110523/straup)

	include("include/init.php");

	loadlib("flickr_push");
	loadlib("subscriptions");
	loadlib("api_output");

	include_once("Redis.php");

	if (! $GLOBALS['cfg']['user']['id']){
		error_404();
	}

	$map = flickr_push_topic_map('string keys');
	$topic_id = $map['contacts_photos'];

	$subscription = subscriptions_get_by_user_and_topic($GLOBALS['cfg']['user'], $topic_id);

	if (! $subscription){
		error_404();
	}

	$whoami = md5($_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']);

	$redis = new Redis();

	$updates_key = "pua_subscription_{$subscription['id']}";
	$seen_key = "pua_seen_{$subscription['id']}_{$whoami}";

	$count = $redis->llen($updates_key);
	$limit = $count - 1;

	$photos = array();
	$skipped = 0;

	foreach (range(0, $limit) as $i){

		$data = $redis->lindex($updates_key, $i);
		$hash = md5($data);

		if ($redis->hexists($seen_key, $hash)){

			# error_log("[PUSH] {$seen_key} {$updates_key} {$i}");
			$skipped += 1;
			continue;
		}

		$redis->hset($seen_key, $hash, time());

		$photos[] = json_decode($data, "as hash");

		if (count($photos) == 20){
			break;
		}
	}

	$count = count($photos);

	$out = array(
		'count' => count($photos),
		'photos' => $photos,
	);

	api_output_ok($out);
	exit();
?>
