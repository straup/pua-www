<?php

	include("include/init.php");
	loadlib("flickr_users");
	loadlib("flickr_push");

	$nsid = get_str("nsid");

	if (! $nsid){
		error_404();
	}

	$flickr_user = flickr_users_get_by_nsid($nsid);

	if (! $flickr_user){
		error_404();
	}

	if (get_str("verify_token")){

		# do (un)sub dance here
		exit();
	}

	$cache_key = "photos_friends_{$flickr_user['user_id']}";

	# parse atom feed here...

	exit();
?>
