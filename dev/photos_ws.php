<?php

	include("include/init.php");

	loadlib("flickr");
	loadlib("flickr_users");
	loadlib("random");

	login_ensure_loggedin();
	$topic = get_str('topic');

	if (! $topic){
		error_404();
	}

	$flickr_user = flickr_users_get_by_user_id($GLOBALS['cfg']['user']['id']);

	$args = array(
		'topic' => $topic,
		'auth_token' => $flickr_user['auth_token'],
		'method' => 'flickr.push.subscribeWS',
		'api_key' => $GLOBALS['cfg']['flickr_apikey'],
		'format' => 'json',
		'nonce' => random_string(),
		'nojsoncallback' => 1,
	);

	$sig = _flickr_api_sign_args($args, $GLOBALS['cfg']['flickr_apisecret']);
	$args['api_sig'] = $sig;

	$url = "http://api.flickr.com/services/rest/?" . http_build_query($args);

	$GLOBALS['smarty']->assign_by_ref("flickr_url", $url);

	$GLOBALS['smarty']->display("page_photos_ws.txt");
	exit();
?>
