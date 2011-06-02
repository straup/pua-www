<?php

	loadlib("random");
	loadlib("flickr_push");

	#################################################################

	function subscriptions_topic_map(){

		# these keys should match those defined in
		# flickr_push_topic_map()

		$map = array(
			1 => array('label' => 'your contacts photos', 'url' => 'photos/friends/', 'enabled' => 1),
			2 => array('label' => 'your contacts faves', 'url' => 'faves/friends/', 'enabled' => 1),
		);

		return $map;
	}

	#################################################################

	function subscriptions_generate_secret_url(){

		$tries = 0;
		$max_tries = 50;

		while (1){

			$tries += 1;

			$url = random_string(64);

			if (! subscriptions_get_by_secret_url($url)){
				return $url;
			}

			if ($tries >= $max_tries){
				return null;
			}
		}
	}

	#################################################################

	function subscriptions_get_by_secret_url($url){

		$cache_key = "subscriptions_secret_{$url}";
		$cache = cache_get($cache_key);

		if ($cache['ok']){
			return $cache['data'];
		}

		$enc_url = AddSlashes($url);

		$sql = "SELECT * FROM Subscriptions WHERE secret_url='{$enc_url}'";

		$rsp = db_fetch($sql);
		$row = db_single($rsp);

		if ($row){
			cache_set($cache_key, $row, "cache locally");
		}

		return $row;
	}

	#################################################################

	function subscriptions_get_by_user_and_topic(&$user, $topic_id){

		$cache_key = "subscriptions_user_{$user['id']}_{$topic_id}";
		$cache = cache_get($cache_key);

		if ($cache['ok']){
			return $cache['data'];
		}

		$enc_id = AddSlashes($user['id']);
		$enc_topic = AddSlashes($topic_id);

		$sql = "SELECT * FROM Subscriptions WHERE user_id='{$enc_id}' AND topic_id='{$enc_topic}'";

		$rsp = db_fetch($sql);
		$row = db_single($rsp);

		if ($row){
			cache_set($cache_key, $row, "cache locally");
		}

		return $row;
	}

	#################################################################

	function subscriptions_for_user_as_hash(&$user){

		$rsp = subscriptions_for_user($user);

		$subscriptions = array();

		foreach ($rsp['rows'] as $row){
			$subscriptions[$row['topic_id']] = $row;
		}

		return $subscriptions;
	}

	#################################################################

	function subscriptions_for_user(&$user){

		$cache_key = "subscriptions_for_user_{$user['id']}";
		$cache = cache_get($cache_key);

		if ($cache['ok']){
			return $cache['data'];
		}

		$cluster_id = $user['cluster_id'];
		$enc_user = AddSlashes($user['id']);

		$sql = "SELECT * FROM Subscriptions WHERE user_id='{$enc_user}'";
		$rsp = db_fetch_users($cluster_id, $sql);

		if ($rsp['ok']){
			cache_set($cache_key, $rsp, "cache locally");
		}

		return $rsp;
	}

	#################################################################

	function subscriptions_create_subscription($subscription){

		$user = users_get_by_id($subscription['user_id']);
		$cluster_id = $user['cluster_id'];

		$secret_url = subscriptions_generate_secret_url();

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

		$rsp = db_insert_users($cluster_id, 'Subscriptions', $insert);

		if ($rsp['ok']){
			$rsp['subscription'] = $subscription;

			$cache_key = "subscriptions_for_user_{$user['id']}";
			cache_unset($cache_key);
		}

		return $rsp;
	}

	#################################################################

	# this both adds the subscription to the database and registers
	# it with the flickr.push API

	function subscriptions_register_subscription($subscription){

		$rsp = subscriptions_create_subscription($subscription);

		if ($rsp['ok']){

			$subscription = $rsp['subscription'];

			$rsp = flickr_push_subscribe($subscription);

			if ($rsp['ok']){
				$rsp['subscription'] = $subscription;
			}
		}

		return $rsp;
	}

	#################################################################

	function subscriptions_delete(&$subscription){

		$user = users_get_by_id($subscription['user_id']);
		$cluster_id = $user['cluster_id'];

		$enc_id = AddSlashes($subscription['id']);

		$sql = "DELETE FROM Subscriptions WHERE id='{$enc_id}'";

		$rsp = db_write_users($cluster_id, $sql);

		if ($rsp['ok']){

			$cache_keys = array(
				"subscriptions_secret_{$subscription['secret_url']}",
				"subscriptions_user_{$user['id']}_{$subscription['topic_id']}",
				"subscriptions_for_user_{$user['id']}",
			);

			foreach ($cache_keys as $k){
				cache_unset($k);
			}
		}

		return $rsp;
	}

	#################################################################

	function subscriptions_update(&$subscription, $update){

		$user = users_get_by_id($subscription['user_id']);
		$cluster_id = $user['cluster_id'];

		$hash = array();

		foreach ($update as $k => $v){
			$hash[$k] = AddSlashes($v);
		}

		$enc_id = AddSlashes($subscription['id']);
		$where = "id='{$enc_id}'";

		$rsp = db_update_users($cluster_id, 'Subscriptions', $hash, $where);

		if ($rsp['ok']){
			$subscription = array_merge($subscription, $update);

			$cache_keys = array(
				"subscriptions_secret_{$subscription['secret_url']}",
				"subscriptions_user_{$user['id']}_{$subscription['topic_id']}",
				"subscriptions_for_user_{$user['id']}",
			);

			foreach ($cache_keys as $k){
				cache_unset($k);
			}
		}

		return $rsp;
	}

	#################################################################

	function subscriptions_recent_activity($args=array()){

		$sql  = "SELECT * FROM Subscriptions FORCE INDEX (recent_activity)";
		$sql .= " ORDER BY last_request DESC, last_update DESC";

		# See this: It means that if pua is ever federated this query
		# will stop working before Subscriptions are "sharded".
		# (20110531/straup)

		return db_fetch_paginated($sql, $args);
	}
?>
