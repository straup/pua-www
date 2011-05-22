<?php

	include("include/init.php");

	login_ensure_loggedin("/photos/friends");

	$GLOBALS['smarty']->display("page_photos_friends.txt");
	exit();
?>

