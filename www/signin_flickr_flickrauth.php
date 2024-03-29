<?php

	include("include/init.php");
	loadlib("flickr");

	$redir = (get_str('redir')) ? get_str('redir') : '/';

	# Some basic sanity checking like are you already logged in?

	if ($GLOBALS['cfg']['user']['id']){
		header("location: {$redir}");
		exit();
	}

	if (! $GLOBALS['cfg']['enable_feature_signin']){
		$GLOBALS['smarty']->display("page_signin_disabled.txt");
		exit;
	}

	##########################################################

	loadlib("invite_codes");

	if ($GLOBALS['cfg']['enable_feature_invite_codes']){

		if (! invite_codes_get_by_cookie()){

			if (! $GLOBALS['cfg']['enable_feature_signup']){
				$GLOBALS['smarty']->display("page_signup_disabled.txt");
				exit;
			}

			header("location: /invite/?redir=" . urlencode($redir));
			exit();
		}
	}

	##########################################################

	# Build a URL with the perms for the auth token we're requesting
	# and send the user there. Rocket science, I know...

	$extra = array();

	if ($redir = get_str('redir')){
		$extra['redir'] = $redir;
	}

	$perms = $GLOBALS['cfg']['flickr_api_perms'];

	$url = flickr_auth_url($perms, $extra);

	header("location: {$url}");
	exit();
?>
