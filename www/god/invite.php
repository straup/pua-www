<?php

	include("../include/init.php");
	loadlib("invite_codes");

	$code = request_str("code");

	if (! $code){
		error_404();
	}

	$invite = invite_codes_get_by_code($code);

	if (! $invite){
		error_404();
	}

	$crumb_key = 'god_invite';
	$GLOBALS['smarty']->assign("crumb_key", $crumb_key);

	$crumb_ok = crumb_check($crumb_key);

	if (($crumk_ok) && (post_str("delete"))){

		$rsp = invite_codes_delete($invite);

		if ($rsp['ok']){
			header("location: /god/invites.php");
			exit();
		}

		$GLOBALS['error']['delete_failed'] = 1;
		$GLOBALS['error']['details'] = $rsp['error'];
	}

	$GLOBALS['smarty']->assign_by_ref("invite", $invite);

	$GLOBALS['smarty']->display("page_god_invite.txt");
	exit();
?>
