<?php

	loadlib("random");

	#################################################################

	function pua_subscriptions_generate_secret_url(){

		$tries = 0;
		$max_tries = 50;

		while (1){

			$tries += 1;

			$url = random_string(64);

			if (! pua_subscriptions_get_by_secret_url($url)){
				return $url;
			}

			if ($tries >= $max_tries){
				return null;
			}
		}
	}

	#################################################################

	function pua_subscriptions_get_by_secret_url($url){

		$enc_url = AddSlashes($url);

		$sql = "SELECT * FROM Subscriptions WHERE secret_url='{$enc_url}'";

		$rsp = db_fetch($sql);
		$row = db_single($rsp);

		return $row;
	}

	#################################################################

	function pua_subscriptions_get_by_user_and_topic(&$user, $topic_id){

		$enc_id = AddSlashes($user['id']);
		$enc_topic = AddSlashes($topic_id);

		$sql = "SELECT * FROM Subscriptions WHERE user_id='{$enc_id}' AND topic_id='{$enc_topic}'";

		$rsp = db_fetch($sql);
		$row = db_single($rsp);

		return $row;
	}

	#################################################################

	function pua_subscriptions_create_subscription($subscription){

		$secret_url = pua_subscriptions_generate_secret_url();

		if (! $secret_url){

			return array(
				'ok' => 0,
				'error' => 'Failed to generate secret URL',
			);
		}

		$token = random_string(32);

		$subscription['id'] = dbtickets_create();

		$subscription['secret_url'] = $secret_url;
		$subscription['verify_token'] = $token;
		$subscription['created'] = time();

		$insert = array();

		foreach ($subscription as $k => $v){
			$insert[$k] = AddSlashes($v);
		}

		$rsp = db_insert('Subscriptions', $insert);

		if ($rsp['ok']){
			$rsp['subscription'] = $subscription;
		}

		return $rsp;
	}

	#################################################################

	function pua_subscriptions_update(&$subscription, $update){

		$hash = array();

		foreach ($update as $k => $v){
			$hash[$k] = AddSlashes($v);
		}

		$enc_id = AddSlashes($subscription['id']);
		$where = "id='{$enc_id}'";

		$rsp = db_update('Subscriptions', $hash, $where);

		if ($rsp['ok']){
			$subscription = array_merge($subscription, $update);
		}

		return $rsp;
	}

	#################################################################
?>
