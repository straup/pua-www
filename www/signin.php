<?php

	include("include/init.php");
	loadlib("flickr");

	if ($GLOBALS['cfg']['user']['id']){
		$redir = (get_str('redir')) ? get_str('redir') : '/';
		header("location: {$redir}");
		exit();
	}

	# invite/signup stuff

	$invite_cookie = login_get_cookie('invite');

	if (! $invite_cookie){	

		$crumb_key = 'signin';
		$GLOBALS['smarty']->assign("crumb_key", $crumb_key);
	
		$crumb_ok = crumb_check($crumb_key);
		$invite_ok = 0;

		$invite_codes = array(
			'urmum',
		);

		if ($crumb_ok){

			if ($invite = post_str('invite')){

				if (in_array($invite, $invite_codes)){
					$invite_ok = 1;
				}

				else {
					$GLOBALS['error']['bad_code'] = 1;
				}
			}

			else {
				$GLOBALS['smarty']->assign("step", "do_invite");
			}
		}

		if (! $invite_ok){
			$GLOBALS['smarty']->display("page_signin_invite.txt");
			exit();		
		}

		login_set_cookie('invite', 1);
	}

	#

	$extra = array();

	if ($redir = get_str('redir')){
		$extra['redir'] = $redir;
	}

	$url = flickr_auth_url("read", $extra);

	header("location: {$url}");
	exit();
?>
