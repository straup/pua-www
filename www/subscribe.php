<?php

	include("include/init.php");

	loadlib("subscriptions");
	loadlib("subscription_urls");

	loadlib("flickr_push");
	loadlib("flickr_users");

	login_ensure_loggedin("/faves/friends");

	$flickr_user = flickr_users_get_by_user_id($GLOBALS['cfg']['user']['id']);

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

	# FIX ME: to deal with topics that have args...

	$subscription = subscriptions_get_by_user_and_topic($GLOBALS['cfg']['user'], $topic_id);

	if ($subscription){

		header("location: {$GLOBALS['cfg']['abs_root_url']}{$sub_map[$topic_ic]['url']}");
		exit();
	}

	#

	$GLOBALS['smarty']->assign_by_ref("topic", $topic);
	$GLOBALS['smarty']->assign_by_ref("subscription_type", $sub_map[$topic_id]);

	$crumb_key = "subscribe_{$topic}";
	$GLOBALS['smarty']->assign("crumb_key", $crumb_key);

	$crumb_ok = crumb_check($crumb_key);

	if (($crumb_ok) && (post_str("confirm"))){

		$GLOBALS['smarty']->assign("step", "do_subscribe");

		$topic_url = $sub_map[$topic_id]['url'];
		$topic_args = null;

		$topic_ok = 1;

		if ($topic == 'geo'){

			if ($woeid = post_str('woeid')){
				$topic_url .= "{$woeid}/";
				$topic_args = array('woeid' => $woeid);
			}

			else {
				$topic_ok = 0;
			}
		}

		if (! $topic_ok){
			$GLOBALS['error']['subscribe'] = 1;
			$GLOBALS['error']['details'] = 'missing arg';
			$GLOBALS['smarty']->display("page_subscribe.txt");
			exit();
		}

		#

		$rsp = subscription_urls_create($topic_url, $topic_args);

		if (! $rsp['ok']){
			$GLOBALS['error']['subscribe'] = 1;
			$GLOBALS['error']['details'] = 'url';
			$GLOBALS['smarty']->display("page_subscribe.txt");
			exit();
		}

		$url_id = $rsp['url']['id'];

		#

		$subscription = array(
			'user_id' => $GLOBALS['cfg']['user']['id'],
			'topic_id' => $topic_id,
			'url_id' => $url_id,
		);

dumper($subscription);
exit;

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
