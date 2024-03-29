<?php

	# This file has been copied from the Citytracking fork of flamework.
	# It has not been forked, or cloned or otherwise jiggery-poked, but
	# copied: https://github.com/Citytracking/flamework (20101209/straup)

	#################################################################

	function cache_memcache_connect(){

		if (! isset($GLOBALS['remote_cache_conns']['memcache'])){
			
			$host = $GLOBALS['cfg']['memcache_host'];
			$port = $GLOBALS['cfg']['memcache_port'];

			$start = microtime_ms();

			$memcache = new Memcache();

			if (! $memcache->connect($host, $port)){
				$memcache = null;
			}

			if (! $memcache){
				log_fatal("Connection to memcache {$host}:{$port} failed");
			}

			$end = microtime_ms();
			$time = $end - $start;

			log_notice("cache", "connect to memcache {$host}:{$port} ({$time}ms)");
			$GLOBALS['remote_cache_conns']['memcache'] = $memcache;

			$GLOBALS['timings']['memcache_conns_count']++;
			$GLOBALS['timings']['memcache_conns_time'] += $time;
		}

		return $GLOBALS['remote_cache_conns']['memcache'];
	}

	#################################################################

	function cache_memcache_get($cache_key){

		$memcache = cache_memcache_connect();

		if (! $memcache){
			return array( 'ok' => 0, 'error' => 'failed to connect to memcache' );
		}

		$rsp = $memcache->get($cache_key);

		if (! $rsp){
			return array( 'ok' => 0 );
		}

		return array(
			'ok' => 1,
			'data' => unserialize($rsp),
		);
	}

	#################################################################

	function cache_memcache_set($cache_key, $data){

		if (! $data){
			return array( 'ok' => 0, 'error' => "nothing to cache for key '{$cache_key}'" );
		}

		$memcache = cache_memcache_connect();

		if (! $memcache){
			return array( 'ok' => 0, 'error' => 'failed to connect to memcache' );
		}

		$ok = $memcache->set($cache_key, serialize($data));
		return array( 'ok' => $ok );
	}

	#################################################################

	function cache_memcache_unset($cache_key){

		$memcache = cache_memcache_connect();

		if (! $memcache){
			return array( 'ok' => 0, 'error' => 'failed to connect to memcache' );
		}

		# Note the 0. It's important. See notes here:
		# http://php.net/manual/en/memcache.delete.php

		$ok = $memcache->delete($cache_key, 0);

		return array( 'ok' => $ok );
	}

	#################################################################

?>
