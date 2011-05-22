<?php

	loadlib("flickr");

	#################################################################

	function flickr_push_subscribe($flickr_user, $subscription){

		$callback = "{$GLOBALS['cfg']['abs_root_url']}push/{$subscription['secret_url']}/";

		$method = 'flickr.push.subscribe';

		$args = array(
			'auth_token' => $flickr_user['auth_token'],
			'topic' => $subscription['type'],
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
