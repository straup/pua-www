<?php

	include("include/init.php");

	loadlib("subscriptions");
	loadlib("flickr_push");
	loadlib("flickr_users");

	login_ensure_loggedin("/faves/friends");

	$topic = request_str("topic");

	if (! $topic){
		error_404();
	}	

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

	#

	$subscription = subscriptions_get_by_user_and_topic($GLOBALS['cfg']['user'], $topic_id);

	if ($subscription){

		header("location: {$GLOBALS['cfg']['abs_root_url']}{$sub_map[$topic_ic]['url']}");
		exit();
	}

	#

	$GLOBALS['smarty']->assign_by_ref("subscription_type", $sub_map[$topic_id]);

	$crumb_key = "subscribe_{$topic}";
	$GLOBALS['smarty']->assign("crumb_key", $crumb_key);

	$crumb_ok = crumb_check($crumb_key);

	if (($crumb_ok) && (post_str("confirm"))){

		$GLOBALS['smarty']->assign("step", "do_subscribe");

		$subscription = array(
			'user_id' => $GLOBALS['cfg']['user']['id'],
			'topic_id' => $topic_id,
		);

		$rsp = subscriptions_register_subscription($subscription);

		if ($rsp['ok']){
			$subscription = $rsp['subscription'];
			$GLOBALS['smarty']->assign_by_ref("subscription", $subscription);
		}

		else {
			$GLOBALS['error']['subscribe'] = 1;
			$GLOBALS['error']['details'] = $rsp['error'];
		}
	}

	else {
		$GLOBALS['smarty']->assign("step", "do_confirm");
	}

	$GLOBALS['smarty']->display("page_subscribe.txt");
	exit();
?>
