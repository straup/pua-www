<?php

	#################################################################

	function api_output_ok($rsp){
		api_output_send($rsp);
	}

	#################################################################

	function api_output_error($code=999, $msg=''){

		$out = array('error' => array(
			'code' => $code,
			'error' => $msg,
		));

		api_output_send($out, "ima error");
	}

	#################################################################

	function api_output_send($rsp, $is_error=0){

		$json = json_encode($rsp);

		utf8_headers();

		if ($is_error){
 			header("HTTP 500 Server Error");
 			header("Status: 500 Server Error");
		}

		header("Access-Control-Allow-Origin: *");

		header("Content-Type: text/json");
		header("Content-Length: " . strlen($json));

		echo $json;
		exit();
	}

	#################################################################

?>
