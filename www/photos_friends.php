<?php

	include("include/init.php");
	loadlib("pua_subscriptions");
	loadlib("flickr_push");
	loadlib("flickr_users");

	login_ensure_loggedin("/photos/friends");

	$subscription = pua_subscriptions_get_by_user_and_type($GLOBALS['cfg']['user'], 0);

	$crumb_key = 'photos_friends';
	$GLOBALS['smarty']->assign("crumb_key", $crumb_key);

	if ($subscription){
		$GLOBALS['smarty']->assign_by_ref("subscription", $subscription);
	}

	else {

		$crumb_ok = crumb_check($crumb_key);

		if (($crumb_ok) && (post_str("confirm"))){

			$GLOBALS['smarty']->assign("step", "do_subscribe");

			$flickr_user = flickr_users_get_by_user_id($GLOBALS['cfg']['user']['id']);

			$subscription = array(
				'user_id' => $GLOBALS['cfg']['user']['id'],
				'type' => 1,	# fix me
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

