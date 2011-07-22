<?php

	include_once("magpie/rss_fetch.inc");
	include_once("magpie/rss_parse.inc");

	function atom_parse_str($xml){
		return new MagpieRSS($xml);
	}

?>
