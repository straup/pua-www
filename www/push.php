<?php

	include("include/init.php");

	loadlib("subscriptions");
	loadlib("atom");

	loadlib("bergcloud_littleprinter");
	include_once("Redis.php");

	$secret_url = get_str("secret_url");

	if (! $secret_url){
		error_404();
	}

	$subscription = subscriptions_get_by_secret_url($secret_url);

	if (! $subscription){
		error_404();
	}

	if ($verify_token = get_str("verify_token")){

		if ($subscription['verify_token'] != $verify_token){
			error_404();
		}

		$mode = get_str('mode');

		if ($mode == 'subscribe'){

			$update = array(
				'verified' => time(),
			);

			$rsp = subscriptions_update($subscription, $update);
		}

		else if ($mode == 'unsubscribe'){

			$rsp = subscriptions_delete($subscription);

			if (! $rsp['ok']){
				error_404();
			}
		}

		else {
			error_404();
		}

		echo get_str("challenge");
		exit();
	}

	$sub_key = "pua_subscription_{$subscription['id']}";
	$user_key = "pua_subscription_user_{$subscription['user_id']}";

	# parse atom feed and store photos here...

	$xml = file_get_contents('php://input');
	$atom = atom_parse_str($xml);

	#

	$redis = new Redis();
	$new = 0;

	$html = '';

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

		$html .= '<div style="font-size:18pt; font-weight: 700;">';
		$html .= '<p>A photo by ' . $photo['ownername'] . '</p>';
		$html .= '<img src="' . $photo['photo_url'] . '" />';
		$html .= '</div>';

		$enc_photo = json_encode($photo);

		$redis->lpush($sub_key, $enc_photo);
		$redis->lpush($user_key, $enc_photo);
		$new ++;
	}

	# $code = 'ORR7WHU7EGMO';
	# $rsp = bergcloud_littleprinter_direct_print($html, $code);

	#

	$max = $GLOBALS['cfg']['flickr_push_max_photos'];

	$count_sub = $redis->llen($sub_key);
	$count_user = $redis->llen($user_key);

	if ($count_sub > $max){
		# $redis->ltrim($sub_key, $max, $count_sub);
		$redis->ltrim($sub_key, 0, $max);
	}

	if ($count_user > $max){
		# $redis->ltrim($user_key, $max, $count_user);
		$redis->ltrim($user_key, 0, $max);
	}

	#

	$update = array(
		'last_update' => time(),
		'last_update_photo_count' => $new,
	);

	subscriptions_update($subscription, $update);

	#

	error_log("[PUSH] {$sub_key}: {$count} (new: {$new})");
	exit();
?>
