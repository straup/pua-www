<?php

	include("include/init.php");
	loadlib("flickr");

	if ($GLOBALS['cfg']['user']['id']){
		header("location: {$GLOBALS['cfg']['abs_root_url']}");
		exit();
	}
	
	$extra = array();

	if ($redir = get_str('redir')){
		$extra['redir'] = $redir;
	}

	$url = flickr_auth_url("read", $extra);

	header("location: {$url}");
	exit();
?>
