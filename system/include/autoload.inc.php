<?php
/**
 * Maes Jerome
 * autoload.inc.php, created at Sep 22, 2015
 *
 */
function classLoader($class_name){
	$directories = explode("\\", $class_name);
	if($directories[0] == 'module'){
		$directories = array_slice($directories, 1);
		$module_dir = array(_DIR_MODULE);
		$found = false;
		foreach ($module_dir as $mod_dir){
			/*
			 echo '<br>'.$mod_dir;
			echo var_dump($directories);
			echo '<hr><hr>';
			*/
			if(! $found){
				$path = $mod_dir . implode($directories, DIRECTORY_SEPARATOR);
				$file = $path . '.class.php';
				$found = _include_file($file);
			}
		}
	}else{
		$root = implode($directories, DIRECTORY_SEPARATOR);
		$file = __SITE_PATH . $root . '.class.php';
		_include_file($file);
	}
}

function _include_file($filename){
	if (!file_exists($filename)){
		return false;
	}
	include $filename;
	return true;
}

spl_autoload_register('classLoader');
