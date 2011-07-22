<?php

	loadlib("flickr");
	loadlib("flickr_users");

	#################################################################

	function flickr_push_topic_map($str_keys=0){

		# note: changes here should also be reflected
		# in subscriptions_topic_map()

		$map = array(
			1 => 'contacts_photos',
			2 => 'contacts_faves',
			3 => 'my_photos',
			4 => 'my_faves',
			5 => 'photos_of_me',
			6 => 'photos_of_contacts',
		);

		if ($str_keys){
			$map = array_flip($map);
		}

		return $map;
	}

	#################################################################

	function flickr_push_subscribe($subscription){

		$flickr_user = flickr_users_get_by_user_id($subscription['user_id']);

		$callback = "{$GLOBALS['cfg']['abs_root_url']}push/{$subscription['secret_url']}/";

		$method = 'flickr.push.subscribe';

		$map = flickr_push_topic_map();
		$topic = $map[$subscription['topic_id']];

		$args = array(
			'auth_token' => $flickr_user['auth_token'],
			'topic' => $topic,
			'verify' => 'sync',
			'verify_token' => $subscription['verify_token'],
			'callback' => $callback,
		);

		$rsp = flickr_api_call($method, $args);
		return $rsp;
	}

	#################################################################

	function flickr_push_unsubscribe($subscription){

		$flickr_user = flickr_users_get_by_user_id($subscription['user_id']);

		$callback = "{$GLOBALS['cfg']['abs_root_url']}push/{$subscription['secret_url']}/";

		$method = 'flickr.push.unsubscribe';

		$map = flickr_push_topic_map();
		$topic = $map[$subscription['topic_id']];

		$args = array(
			'auth_token' => $flickr_user['auth_token'],
			'topic' => $topic,
			'verify' => 'sync',
			'verify_token' => $subscription['verify_token'],
			'callback' => $callback,
		);

		$rsp = flickr_api_call($method, $args);

		return $rsp;
	}

	#################################################################

?>
