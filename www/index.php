<?php

	include("include/init.php");
	loadlib("pua_subscriptions");

	if ($GLOBALS['cfg']['user']){
		$rsp = pua_subscriptions_for_user($GLOBALS['cfg']['user']);
		$GLOBALS['smarty']->assign_by_ref("subscriptions", $rsp['rows']);
	}

	$GLOBALS['smarty']->display("page_index.txt");
	exit;
?>
