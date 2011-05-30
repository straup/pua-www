<?php

	include("include/init.php");
	loadlib("invite_codes");

	# User is already logged in

	if ($GLOBALS['cfg']['user']['id']){
#		header("location: /");
#		exit();
	}

	# User has already redeemed their invite code

	if (invite_codes_get_by_cookie()){
#		header("location: /signin/");
#		exit();
	}

	# User is trying to redeem an invite code

	if ($code = get_str("code")){

		if ($invite = invite_codes_get_by_code($code)){
			invite_codes_signin($invite);
			exit();
		}

		else {
			$GLOBALS['error']['invalid_code'] = 1;
		}
	}

	# User is trying to request an invite code?

	$crumb_key = 'invite';
	$GLOBALS['smarty']->assign("crumb_key", $crumb_key);
	
	$crumb_ok = crumb_check($crumb_key);

	if ($crumb_ok){

		$code = post_str("code");
		$email = post_str("email");

		$code = ($code == "3x4mpl3c0d3") ? null : $code;
		$email = ($email == "you@example.com") ? null : $email;

		if ($code){

			if ($invite = invite_codes_get_by_code($code)){
				invite_codes_signin($invite);
				exit();
			}

			$GLOBALS['error']['invalid_code'] = 1;
		}

		else if ($email){

			$email = post_str("email");
			# validate email here

			$rsp = invite_codes_create($email);

			if ($rsp['ok']){

				$invite = $rsp['invite'];

				if ($invite['sent']){
					invite_codes_send_invite($invite);
					$GLOBALS['smarty']->assign("invite_resent", 1);
				}

				else {
					$rsp = invite_codes_register_invite($invite);
				}
			}

			if (! $rsp['ok']){
				$GLOBALS['error']['request_failed'] = 1;
				$GLOBALS['error']['details'] = $rsp['error'];
			}

			$GLOBALS['smarty']->assign("step", "request_ok");
		}

		else {}
	}

	$GLOBALS['smarty']->display("page_invite.txt");
	exit();
	
?>
