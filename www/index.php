<?php

	include("include/init.php");
	loadlib("subscriptions");

	if ($GLOBALS['cfg']['user']){

		$subscriptions = subscriptions_for_user_as_hash($GLOBALS['cfg']['user']);

		$crumb_key = 'subscriptions';
		$GLOBALS['smarty']->assign("crumb_key", $crumb_key);

		$crumb_ok = crumb_check($crumb_key);

		if (($crumb_ok) && (post_str("delete"))){

			list($topic_id, $sub_id) = explode("-", post_str("delete"), 2);

			$subscription = null;

			if (isset($subscriptions[$topic_id])){

				foreach ($subscriptions[$topic_id] as $_sub){
					if ($_sub['id'] == $sub_id){
						$subscription = $_sub;
					}
				}
			}

			if ($subscription){

				$rsp = flickr_push_unsubscribe($subscription);

				if (($rsp['ok']) || ($rsp['error'] == 'Subscription not found')){
					subscriptions_delete($subscription);
					$subscriptions = subscriptions_for_user_as_hash($GLOBALS['cfg']['user']);
				}

				else {
					$GLOBALS['error']['unsubscribe'] = 1;
					$GLOBALS['error']['details'] = $rsp['error'];
				}
			}

			else {
				$GLOBALS['error']['invalid_subscription'] = 1;
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
