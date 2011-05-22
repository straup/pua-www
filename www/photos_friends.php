<?php

	include("include/init.php");
	loadlib("pua_subscriptions");
	loadlib("flickr_push");

	login_ensure_loggedin("/photos/friends");

	$subscription = pua_subscriptions_get_by_userid_and_type($GLOBALS['cfg']['user']['id'], 0);

	$crumb_key = 'photos_friends';
	$GLOBALS['smarty']->assign("crumb_key", $crumb_key);

	if ($subscription){
		$GLOBALS['smarty']->assign_by_ref("subscription", $subscription);
	}

	else {

		$crumb_ok = crumb_check($crumb_key);

		if (($crumb_ok) && (post_str("confirm"))){

			$flickr_user = flickr_users_get_by_user_id($GLOBALS['cfg']['user']['id']);

			$subscription = array(
				'user_id' => $GLOBALS['cfg']['user']['id'],
				'type' => 0,
			);

			$sub_rsp = pua_subscriptions_create_subscription($flickr_user, $subscription);

			$fl_rsp = flickr_push_subscribe($flickr_user, $subscription);
		}

		else {

		}
	}

	$GLOBALS['smarty']->display("page_photos_friends.txt");
	exit();
?>

