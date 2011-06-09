<?php

	include("include/init.php");

	loadlib("flickr_users");

	login_ensure_loggedin("/photos/");

	$flickr_user = flickr_users_get_by_user_id($GLOBALS['cfg']['user']['id']);

	$GLOBALS['smarty']->display("page_photos.txt");
	exit();
?>
