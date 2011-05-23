<?php

	include("include/init.php");

	loadlib("flickr_push");
	loadlib("pua_subscriptions");
	include_once("Redis.php");

	if (! $GLOBALS['cfg']['user']['id']){
		exit();
	}

	$map = flickr_push_topic_map('string keys');
	$topic_id = $map['contacts_photos'];

	$subscription = pua_subscriptions_get_by_user_and_topic($GLOBALS['cfg']['user'], $topic_id);

	if (! $subscription){
		exit();
	}

	$whoami = md5($_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']);

	$redis = new Redis();

	$updates_key = "pua_subscription_{$subscription['id']}";
	$seen_key = "pua_seen_{$subscription['id']}_{$whoami}";

	$count = $redis->llen($updates_key);
	$limit = $count - 1;

	$photos = array();

	foreach (range(0, $limit) as $i){

		$data = $redis->lindex($updates_key, $i);

		if ($redis->hexists($seen_key, $hash)){
			continue;
		}

		$redis->hset($seen_key, $hash, time());

		$photos[] = json_decode($data, "as hash");

		if (count($photos) == 20){
			break;
		}
	}

	dumper($photos);
?>
