<?php

	include("../include/init.php");
	loadlib("subscriptions");

	$id = request_str("id");

	if ($id){

		$user = users_get_by_id($id);

		if (! $user['id']){
			error_404();
		}

		$subscriptions = subscriptions_for_user($user);
		$user['subscriptions'] = $subscriptions;

		$GLOBALS['smarty']->assign_by_ref("user", $user);
	}

	$GLOBALS['smarty']->display("page_god_user.txt");
	exit();
?>
