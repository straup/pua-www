<?php

	# write me

	#################################################################

	function flickr_push_subscribe($flickr_user, $subscription){

		$callback = "{$GLOBALS['cfg']['abs_root_url']}push/{$secret_url}/";

		$method = 'flickr.push.subscribe';

		$args = array(
			'auth_token' => 'fix me',
			'topic' => $subscription['type'],
			'mode' => 'subscribe',
			'verify' => 'sync',
			'verify_token' => 'fix me',
			'callback' => $callback,
		);
	}

	#################################################################
?>
