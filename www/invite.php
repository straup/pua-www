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

			$ok = 0;

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
		}

		else if (post_str("request")){

			$email = post_str("email");
			# validate email here

			$rsp = invite_codes_create($email);

			if ($rsp['ok']){
				invite_codes_send_invite($rsp['invite']);
			}

			else {
				# errors go here
			}
		}

		else {}
	}

	$GLOBALS['smarty']->display("page_invite.txt");
	exit();
	
?>
