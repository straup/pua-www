<?php

	include("include/init.php");
	loadlib("invite_codes");

	if ($GLOBALS['cfg']['user']['id']){
		header("location: /");
		exit();
	}

	$cookie = login_get_cookie('invite');

	if (($cookie) && ($invite = invite_codes_get_by_cookie($cookie))){
		header("location: /signin/");
		exit();
	}

	$crumb_key = 'invite';
	$GLOBALS['smarty']->assign("crumb_key", $crumb_key);
	
	$crumb_ok = crumb_check($crumb_key);

	if ($crumb_ok){

		if (post_str("submit")){

			$email = post_str("email");
			$code = post_str("code");

			if ($invite = invite_codes_get_by_email($email, $code)){

				if (! $invite['redeemed']){

					$update = array(
						'redeemed' => time(),
					);

					invite_codes_update($invite, $update);
				}

				$cookie = invite_codes_generate_cookie($invite);

				$expires = time() * 2;
				login_set_cookie('invite', $cookie, $expires);

				header("location: /signin/");
				exit();
			}

			$GLOBALS['smarty']->assign("step", "submit");		
			$GLOBALS['error']['invalid_code'] = 1;
		}

		else if (post_str("request")){

			$GLOBALS['smarty']->assign("step", "requested");

			$email = post_str("email");
			# validate email here

			$rsp = invite_codes_create($email);

			if ($rsp['ok']){
				$rsp = invite_codes_send_invite($rsp['invite']);
			}

			if (! $rsp['ok']){
				$GLOBALS['error']['request_failed'] = 1;
				$GLOBALS['error']['details'] = $rsp['error'];
			}
		}

		else {}
	}

	else {

		if (get_str("request")){
			$GLOBALS['smarty']->assign("step", "request");
		}
	}

	$GLOBALS['smarty']->display("page_invite.txt");
	exit();
	
?>
