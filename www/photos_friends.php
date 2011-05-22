<?php

	include("include/init.php");

	loadlib("pua_subscriptions");
	loadlib("flickr_push");
	loadlib("flickr_users");

	login_ensure_loggedin("/photos/friends");

	$map = flickr_push_topic_map('string keys');
	$topic_id = $map['contacts_photos'];

	$subscription = pua_subscriptions_get_by_user_and_topic($GLOBALS['cfg']['user'], $topic_id);

	if ($subscription){
		$GLOBALS['smarty']->assign_by_ref("subscription", $subscription);
	}

	else {

		$crumb_key = 'photos_friends';
		$GLOBALS['smarty']->assign("crumb_key", $crumb_key);

		$crumb_ok = crumb_check($crumb_key);

		if (($crumb_ok) && (post_str("confirm"))){

			$GLOBALS['smarty']->assign("step", "do_subscribe");

			$flickr_user = flickr_users_get_by_user_id($GLOBALS['cfg']['user']['id']);

			$subscription = array(
				'user_id' => $GLOBALS['cfg']['user']['id'],
				'topic_id' => $topic_id,
			);

			$sub_rsp = pua_subscriptions_create_subscription($subscription);

			$fl_rsp = flickr_push_subscribe($flickr_user, $subscription);
		}

		else {
			$GLOBALS['smarty']->assign("step", "do_confirm");
		}
	}

	$GLOBALS['smarty']->display("page_photos_friends.txt");
	exit();
?>

