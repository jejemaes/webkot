<?php
/**
 * Maes Jerome
 * url.inc.php, created at Oct 29, 2015
 *
 */
namespace system\tools;

class Url{
	
	/**
	 * Create a complete url for the given path
	 * @param string $path : should start with '/'
	 * @return string
	 */
	static function url_from_path($path){
		$base_url = __BASE_URL;
		if(substr($base_url, -1) == '/'){
			$base_url = substr($base_url, 0, -1);
		}
		return $base_url . $path;
	}
	
	/**
	 * Create a complete admin url for the given path
	 * @param string $path : should start with '/'
	 * @return string
	 */
	static function url_admin_from_path($path){
		$base_url = __BASE_URL;
		if(substr($base_url, -1) == '/'){
			$base_url = substr($base_url, 0, -1);
		}
		return $base_url . ADMIN_PATH . $path;
	}
	
}

