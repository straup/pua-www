<?php


	#################################################################

	function subscription_urls_get_by_url($url){

		$enc_url = AddSlashes($url);
		$sql = "SELECT * FROM SubscriptionUrls WHERE url='{$enc_url}'";

		$rsp = db_single(db_fetch($sql));
		return $rsp;
	}

	#################################################################

	function subscription_urls_create($url, $args=null){

		if ($row = subscription_urls_get_by_url($url)){

			return array(
				'ok' => 1,
				'url' => $row,
			);
		}

		$id = dbtickets_create();

		$row = array(
			'id' => $id,
			'url' => $url,
		);

		if (isset($args)){
			$row['args'] = json_encode($args);
		}

		$insert = array();

		foreach ($row as $k => $v){
			$insert[$k] = AddSlashes($v);
		}

		$rsp = db_insert('SubscriptionUrls', $insert);

		if ($rsp['ok']){
			$rsp['url'] = $row;
		}

		return $rsp;
	}

	#################################################################	
?>
