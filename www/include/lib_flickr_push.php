<?php

	loadlib("flickr");

	#################################################################

	function flickr_push_subscriptions_map($str_keys=0){

		$map = array(
			1 => 'contacts_photos',
		);

		if ($str_keys){
			$map = array_flip($map);
		}

		return $map;
	}

	#################################################################

	function flickr_push_subscribe($flickr_user, $subscription){

		$callback = "{$GLOBALS['cfg']['abs_root_url']}push/{$subscription['secret_url']}/";

		$method = 'flickr.push.subscribe';

		$topic = 'contacts_photos'; 	# fix me

		$args = array(
			'auth_token' => $flickr_user['auth_token'],
			'topic' => $topic,
			'mode' => 'subscribe',
			'verify' => 'sync',
			'verify_token' => $subscription['verify_token'],
			'callback' => $callback,
		);

		$rsp = flickr_api_call($method, $args);
		return $rsp;
	}

	#################################################################
?>
