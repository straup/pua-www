<?php

	include("include/init.php");
	loadlib("subscriptions");

	if ($GLOBALS['cfg']['user']){

		$subscriptions = subscriptions_for_user_as_hash($GLOBALS['cfg']['user']);

		#

		$crumb_key = 'subscriptions';
		$GLOBALS['smarty']->assign("crumb_key", $crumb_key);

		$crumb_ok = crumb_check($crumb_key);

		if (($crumb_ok) && (post_str("delete"))){

			list($topic_id, $sub_id) = explode("-", post_str("delete"), 2);

			if ((isset($subscriptions[$topic_id])) && ($subscriptions[$topic_id]['id'] == $sub_id)){

				$_sub = $subscriptions[$topic_id];

				$rsp = flickr_push_unsubscribe($_sub);

				if ($rsp['ok']){
					$subscriptions = subscriptions_for_user_as_hash($GLOBALS['cfg']['user']);
				}
			}
		}

		#

		$GLOBALS['smarty']->assign_by_ref("subscriptions", $subscriptions);
	}

	$map = subscriptions_topic_map();
	$GLOBALS['smarty']->assign_by_ref("topic_map", $map);

	$GLOBALS['smarty']->display("page_index.txt");
	exit;
?>
