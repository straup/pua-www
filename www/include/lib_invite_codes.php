<?php

	loadlib("random");

	#################################################################

	function invite_codes_generate_cookie(&$invite){

		$raw = implode("-", array(
			$invite['email'],
			$invite['code'],
			$invite['created']
		));

		return crypto_encrypt($raw, $GLOBALS['cfg']['crypt_invite_secret']);
	}

	#################################################################

	function invite_codes_get_by_cookie($cookie){

		$cookie = crypto_decrypt($cookie, $GLOBALS['cfg']['crypt_invite_secret']);

		if (! $cookie){
			return;
		}

		$cookie = explode("-", $cookie, 3);

		if (count($cookie) != 3){
			return;
		}

		return invite_codes_get_by_email($cookie[0], $cookie[1]);
	}

	#################################################################

	function invite_codes_get_by_email($email, $code=''){

		$enc_email = AddSlashes($email);

		$sql = "SELECT * FROM InviteCodes WHERE email='{$enc_email}'";
		$rsp = db_fetch($sql);

		$row = db_single($rsp);

		if (($row) && ($code)){
			$row = ($row['code'] == $code) ? $row : null;
		}

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

		$code = random_string(12);

		$invite = array(
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

	function invite_codes_update(&$invite, &$update){

		$hash = array();
			
		foreach ($update as $k => $v){
			$hash[$k] = AddSlashes($v);
		}

		$enc_email = AddSlashes($invite['email']);
		$enc_code = AddSlashes($invite['code']);

		$where = "email='{$enc_email}' AND code='{$enc_code}'";

		return db_update('InviteCodes', $hash, $where);
	}

	#################################################################
?>
