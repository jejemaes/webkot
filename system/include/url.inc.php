<?php
/**
 * Maes Jerome
 * url.inc.php, created at Oct 29, 2015
 *
 */


function url_from_path($path){
	$base_url = __BASE_URL;
	var_dump($base_url);
	if(substr($base_url, -1) == '/'){
		$base_url = substr($base_url, 0, -1);
	}
	return $base_url . $path;
}