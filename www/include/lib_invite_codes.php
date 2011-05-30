<?php

	loadlib("random");

	#################################################################

	function invite_codes_generate_cookie(&$invite){

		$raw = implode("-", array(
			$invite['code'],
			$invite['created']
		));

		return crypto_encrypt($raw, $GLOBALS['cfg']['crypt_invite_secret']);
	}

	#################################################################

	function invite_codes_get_by_cookie($cookie=''){

		$cookie = login_get_cookie('invite');

		if (! $cookie){
			return null;
		}

		$cookie = crypto_decrypt($cookie, $GLOBALS['cfg']['crypt_invite_secret']);

		if (! $cookie){
			return null;
		}

		$cookie = explode("-", $cookie, 2);

		if (count($cookie) != 2){
			return null;
		}

		return invite_codes_get_by_code($cookie[0], $cookie[1]);
	}

	function invite_codes_set_cookie(&$invite){

		$cookie = invite_codes_generate_cookie($invite);

		$expires = time() * 2;
		login_set_cookie('invite', $cookie, $expires);
	}

	#################################################################

	function invite_codes_get_by_email($email){

		$cache_key = "invite_codes_email_{$email}";
		$cache = cache_get($cache_key);

		if ($cache['ok']){
			return $cache['data'];
		}

		$enc_email = AddSlashes($email);

		$sql = "SELECT * FROM InviteCodes WHERE email='{$enc_email}'";
		$rsp = db_fetch($sql);

		$row = db_single($rsp);

		cache_set($cache_key, $row, "cache locally");
		return $row;
	}

	#################################################################

	function invite_codes_get_by_code($code, $ensure_sent=1){

		$cache_key = "invite_codes_code_{$code}";
		$cache = cache_get($cache_key);

		if ($cache['ok']){
			return $cache['data'];
		}

		$enc_code = AddSlashes($code);

		$sql = "SELECT * FROM InviteCodes WHERE code='{$code}'";

		$rsp = db_fetch($sql);
		$row = db_single($rsp);

		if (($ensure_sent) && (! $row['sent'])){
			$row = null;
		}

		cache_set($cache_key, $row, "cache locally");
		return $row;
	}

	#################################################################

	function invite_codes_create($email){

		if ($invite = invite_codes_get_by_email($email)){

			return array(
				'ok' => 1,
				'invite' => $invite
			);
		}

		$code = null;
		$tries = 0;

		while (! $code){

			$code = random_string(12);
			$tries += 1;

			if (invite_codes_get_by_code($code)){
				$code = null;
			}

			if ($tries == 50){
				break;
			}
		}

		if (! $code){

			return array(
				'ok' => 0,
				'error' => 'Failed to generate code',
			);
		}

		$id = dbtickets_create();

		$invite = array(
			'id' => $id,
			'email' => $email,
			'code' => $code,
			'created' => time(),
		);

		$hash = array();

		foreach ($invite as $k => $v){
			$hash[$k] = AddSlashes($v);
		}

		$rsp = db_insert('InviteCodes', $hash);

		if ($rsp['ok']){
			$rsp['invite'] = $invite;
		}

		return $rsp;
	}

	#################################################################

	function invite_codes_invite_user($email){

		$rsp = invite_codes_create($email);

		if ($rsp['ok']){
			$rsp = invite_codes_send_invite($rsp['invite']);
		}

		return $rsp;
	}

	#################################################################

	function invite_codes_update(&$invite, &$update){

		$hash = array();
			
		foreach ($update as $k => $v){
			$hash[$k] = AddSlashes($v);
		}

		$enc_id = AddSlashes($invite['id']);
		$where = "id='{$enc_id}'";

		$rsp = db_update('InviteCodes', $hash, $where);

		if ($rsp['ok']){

			$keys = array(
				"invite_codes_code_{$invite['code']}",
				"invite_codes_email_{$invite['email']}",
			);

			foreach ($keys as $k){
				cache_unset($k);
			}
		}

		return $rsp;
	}

	#################################################################

	function invite_codes_register_invite(&$invite){

		return array(
			'ok' => 1,
		);
	}

	#################################################################

	function invite_codes_send_invite(&$invite){

		$GLOBALS['smarty']->assign_by_ref("invite", $invite);

		$args = array(
			'to_email' => $invite['email'],
			'template' => 'email_invite_code.txt',
			'from_name' => 'Pua Email Robot',
			'from_email' => 'do-not-reply@mail.pua.spum.org',
		);

		$ok = email_send($args);

		if ($ok){

			$update = array(
				'sent' => time(),
			);

			invite_codes_update($invite, $update);
		}

		return array(
			'ok' => $ok,
		);
	}

	#################################################################

	function invite_codes_signin(&$invite){

		if (! $invite['redeemed']){

			$update = array(
				'redeemed' => time(),
			);

			invite_codes_update($invite, $update);
		}

		invite_codes_set_cookie($invite);
		header("location: /signin/");
		exit();

	}

	#################################################################

?>
