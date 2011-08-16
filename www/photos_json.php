<?php

	# to be replace by a proper API endpoint if and when
	# I ever get there... (20110523/straup)

	include("include/init.php");

	loadlib("flickr_push");
	loadlib("api_output");

	loadlib("subscriptions");
	loadlib("subscription_urls");

	include_once("Redis.php");

	if (! $GLOBALS['cfg']['user']['id']){
		error_404();
	}

	$whoami = md5($_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']);

	if ($topic = get_str("topic")){

		$flickr_map = flickr_push_topic_map("string keys");
		$sub_map = subscriptions_topic_map();

		if (! isset($flickr_map[$topic])){
			error_404();
		}

		$topic_id = $flickr_map[$topic];

		if (! isset($sub_map[$topic_id])){
			error_404();
		}

		if (! $sub_map[$topic_id]['enabled']){
			error_404();
		}

		if ($topic == 'geo'){

			$woeids = get_str("woeids");
			$url = subscription_urls_get_by_url("photos/places/{$woeids}/");

			if (! $url){
				error_404();
			}

			$subscription = subscriptions_get_by_user_and_url($GLOBALS['cfg']['user'], $url['id']);
		}

		else if ($topic == 'tags'){

			$tags = get_str("tags");
			$url = subscription_urls_get_by_url("photos/tags/{$tags}/");

			if (! $url){
				error_404();
			}

			$subscription = subscriptions_get_by_user_and_url($GLOBALS['cfg']['user'], $url['id']);
		}

		else {

			$subscription = subscriptions_get_by_user_and_topic($GLOBALS['cfg']['user'], $topic_id);
		}

		if (! $subscription){
			error_404();
		}

		$updates_key = "pua_subscription_{$subscription['id']}";
		$seen_key = "pua_seen_{$subscription['id']}_{$whoami}";
	}

	else {
		$updates_key = "pua_subscription_user_{$GLOBALS['cfg']['user']['id']}";
		$seen_key = "pua_seen_user_{$GLOBALS['cfg']['user']['id']}_{$whoami}";
	}

	$redis = new Redis();

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

		if (count($photos) == 50){
			break;
		}
	}

	# this is a bit annoying, to reconsider
	# (20110608/straup)

	if ($subsciption){
		$update = array(
			'last_request' => time(),
			'last_request_photo_count' => count($photos),
		);

		subscriptions_update($subscription, $update);
	}

	#

	$is_gil = ($GLOBALS['cfg']['user']['id'] == 50) ? 1 : 0;

	$out = array(
		'count' => count($photos),
		'is_gil' => $is_gil,
		'photos' => $photos,
	);

	if ($topic){
		$out['topic'] = $topic;
	}

	$more = array(
		'inline' => get_str('inline'),
	);

	api_output_ok($out, $more);
	exit();
?>
