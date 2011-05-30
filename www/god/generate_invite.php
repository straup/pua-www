<?php

	include("../include/init.php");

	loadlib("invite_codes");
	loadlib("rfc822");

	$crumb_key = 'god_invite';
	$GLOBALS['smarty']->assign("crumb_key", $crumb_key);

	$crumb_ok = crumb_check($crumb_key);

	if ($crumb_ok){

		$email = post_str("email");

		if (! $email){
			$GLOBALS['error'] = "Missing email";
		}

		else if (! rfc822_is_valid_email_address($email)){
			$GLOBALS['error'] = "Invalid email ({$email})";
		}

		else if ($invite = invite_codes_get_by_email($email)){
			$GLOBALS['smarty']->assign_by_ref("invite", $invite);
		}

		else {
			$rsp = invite_codes_invite_user($email);

			if ($rsp['ok']){
				$GLOBALS['smarty']->assign_by_ref("invite", $rsp['invite']);
			}

			else {
				$GLOBALS['error'] = $rsp['error'];
			}
		}
	}

	$GLOBALS['smarty']->display("page_god_generate_invite.txt");
	exit();
?>
