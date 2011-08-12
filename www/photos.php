<?php

	include("include/init.php");

	loadlib("flickr_users");

	login_ensure_loggedin("/photos/");

	$flickr_user = flickr_users_get_by_user_id($GLOBALS['cfg']['user']['id']);

	$mock_topic = array(
		'url' => 'photos/',
		'label' => 'photos from your contacts (and their faves)',
	);

	$GLOBALS['smarty']->assign_by_ref("topic", $mock_topic);

	$GLOBALS['smarty']->display("page_photos.txt");
	exit();
?>
