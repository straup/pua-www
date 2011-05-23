<?php

	include("include/init.php");

	loadlib("subscriptions");
	loadlib("flickr_users");

	login_ensure_loggedin("/photos/friends");

	$flickr_user = flickr_users_get_by_user_id($GLOBALS['cfg']['user']['id']);

	$map = flickr_push_topic_map('string keys');
	$topic_id = $map['contacts_photos'];

	$subscription = subscriptions_get_by_user_and_topic($GLOBALS['cfg']['user'], $topic_id);

	if ($subscription){

		$GLOBALS['smarty']->assign_by_ref("subscription", $subscription);
	}

	else {

		$crumb_key = 'photos_friends';
		$GLOBALS['smarty']->assign("crumb_key", $crumb_key);

		$crumb_ok = crumb_check($crumb_key);

		if (($crumb_ok) && (post_str("confirm"))){

			$GLOBALS['smarty']->assign("step", "do_subscribe");

			$subscription = array(
				'user_id' => $GLOBALS['cfg']['user']['id'],
				'topic_id' => $topic_id,
			);

			$rsp = subscriptions_register_subscription($flickr_user, $subscription);

			if ($rsp['ok']){
				$subscription = $rsp['subscription'];
				$GLOBALS['smarty']->assign_by_ref("subscription", $subscription);
			}

			else {
				$GLOBALS['error']['subscribe'] = 1;
				$GLOBALS['error_details'] = $rsp['error'];
			}
		}

		else {
			$GLOBALS['smarty']->assign("step", "do_confirm");
		}
	}

	$GLOBALS['smarty']->display("page_photos_friends.txt");
	exit();
?>
