<?php

	#############################################################

	#
	# You should NOT be editing this file. You should instead be editing
	# the config file found in dotspotting/config/dotspotting.php. See also:
	# https://github.com/Citytracking/dotspotting/blob/master/README.CONFIG.md
	#

	$GLOBALS['cfg'] = array();

	#
	# Things you might want to do quickly
	#

	$GLOBALS['cfg']['disable_site'] = 0;
	$GLOBALS['cfg']['show_show_header_message'] = 0;

	#
	# Feature flags
	# See also: http://code.flickr.com/blog/2009/12/02/flipping-out/
	#

	$GLOBALS['cfg']['enable_feature_api'] = 1;

	$GLOBALS['cfg']['api_default_format'] = 'json';

	$GLOBALS['cfg']['api_valid_formats'] = array(
		'json',
	);

	$GLOBALS['cfg']['enable_feature_signup'] = 1;
	$GLOBALS['cfg']['enable_feature_signin'] = 1;
	$GLOBALS['cfg']['enable_feature_persistent_login'] = 1;
	$GLOBALS['cfg']['enable_feature_account_delete'] = 1;
	$GLOBALS['cfg']['enable_feature_password_retrieval'] = 1;

	$GLOBALS['cfg']['enable_feature_http_prefetch'] = 0;

	#
	# God auth
	#

	$GLOBALS['cfg']['auth_enable_poormans_god_auth'] = 0;

	# $GLOBALS['cfg']['auth_poormans_god_auth'] = array(
	# 	xxx => array(
	# 		'roles' => array( 'staff' ),
	# 	),
	# );

	#
	# Crypto stuff
	#

	$GLOBALS['cfg']['crypto_cookie_secret'] = 'READ-FROM-CONFIG';
	$GLOBALS['cfg']['crypto_password_secret'] = 'READ-FROM-CONFIG';
	$GLOBALS['cfg']['crypto_crumb_secret'] = 'READ-FROM-CONFIG';

	#
	# Database stuff
	#

	$GLOBALS['cfg']['db_main'] = array(
		'host'	=> 'READ-FROM-CONFIG',
		'user'	=> 'READ-FROM-CONFIG',
		'pass'	=> 'READ-FROM-CONFIG',
		'name'	=> 'dotspotting',
		'auto_connect' => 1,
	);

	$GLOBALS['cfg']['db_enable_poormans_slaves'] = 1;
	$GLOBALS['cfg']['db_enable_poormans_ticketing'] = 1;
	$GLOBALS['cfg']['db_enable_poormans_federation'] = 1;

	$GLOBALS['cfg']['flickr_push_max_photos'] = 500;

	#
	# API stuff
	#

	# This is defined in config-api.php and gets pulled in Dotspotting's init.php
	# assuming that 'enable_feature_api' is true.

	#
	# Templates
	#

	$GLOBALS['cfg']['smarty_template_dir'] = FLAMEWORK_WWW_DIR . '/templates';
	$GLOBALS['cfg']['smarty_compile_dir'] = FLAMEWORK_WWW_DIR . '/templates_c';
	$GLOBALS['cfg']['smarty_compile'] = 1;

	#
	# App specific stuff
	#

	# Just blow away whatever Flamework says for abs_root_url. The user has the chance to reset these in
	# config/dotspotting.php and we want to ensure that if they don't the code in include/init.php for
	# wrangling hostnames and directory roots has a clean start. (20101127/straup)

	$GLOBALS['cfg']['abs_root_url'] = '';
	$GLOBALS['cfg']['safe_abs_root_url'] = '';

	$GLOBALS['cfg']['auth_cookie_domain'] = parse_url($GLOBALS['cfg']['abs_root_url'], 1);
	$GLOBALS['cfg']['auth_cookie_name'] = 'a';

	$GLOBALS['cfg']['maptiles_template_url'] = 'http://{S}tile.cloudmade.com/1a1b06b230af4efdbb989ea99e9841af/26490/256/{Z}/{X}/{Y}.png';
	$GLOBALS['cfg']['maptiles_template_hosts'] = array( 'a.', 'b.', 'c.' );

	$GLOBALS['cfg']['pagination_per_page'] = 25;
	$GLOBALS['cfg']['pagination_spill'] = 5;
	$GLOBALS['cfg']['pagination_assign_smarty_variable'] = 1;

	#
	# Email
	#

	$GLOBALS['cfg']['email_from_name']	= 'READ-FROM-CONFIG';
	$GLOBALS['cfg']['email_from_email']	= 'READ-FROM-CONFIG';
	$GLOBALS['cfg']['auto_email_args']	= 'READ-FROM-CONFIG';

	#
	# Things you can probably not worry about
	#

	$GLOBALS['cfg']['user'] = null;

	$GLOBALS['cfg']['smarty_compile'] = 1;

	$GLOBALS['cfg']['http_timeout'] = 5;
	$GLOBALS['cfg']['http_timeout_solr'] = 10;

	$GLOBALS['cfg']['check_notices'] = 1;

	$GLOBALS['cfg']['db_profiling'] = 0;

?>