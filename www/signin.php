<?php

	include("include/init.php");
	loadlib("flickr");
	loadlib("invite_codes");

	$redir = (get_str('redir')) ? get_str('redir') : '/';

	if ($GLOBALS['cfg']['user']['id']){
		header("location: {$redir}");
		exit();
	}

	if ($GLOBALS['cfg']['enable_feature_invite_codes']){

		if (! invite_codes_get_by_cookie()){

			$cookie = login_get_cookie('invite');

			if ($cookie != '1'){

				if (! $GLOBALS['cfg']['enable_feature_signup']){
					$GLOBALS['smarty']->display("page_signup_disabled.txt");
					exit;
				}

				header("location: /invite/?redir=" . urlencode($redir));
				exit();
			}

			# urmum hangover stuff (aka cookie == '1')

			$now = time();
			$email = "urmum-{$now}@example.com";

			$rsp = invite_codes_create($email);
			$invite = $rsp['invite'];

			$now = time();

			$update = array(
				'sent' => $now,
				'redeemed' => $now,
			);

			invite_codes_update($invite, $update);
			invite_codes_set_cookie($invite);
		}
	}

	if (! $GLOBALS['cfg']['enable_feature_signin']){
		$GLOBALS['smarty']->display("page_signin_disabled.txt");
		exit;
	}

	$extra = array();

	if ($redir = get_str('redir')){
		$extra['redir'] = $redir;
	}

	$url = flickr_auth_url("read", $extra);

	header("location: {$url}");
	exit();
?>
