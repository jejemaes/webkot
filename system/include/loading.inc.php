<?php
/**
 * Maes Jerome
 * loading.php, created at May 27, 2016
 *
 */

/**
 * Load system php files
 */
function load_system(){
	_load_directory(DIR_SYS_CORE);
	_load_directory(DIR_SYST_LIB);
}

function _load_directory($directory){
	$files = scandir($directory);
	foreach($files as $item){
		if($item != '.' && $item != '..'){
			include $directory . $item;
		}
	}
}


/**
 * Load given module
 * @param array $modules
 */
function load_modules(array $modules){
	foreach($modules as $module){
		$loader_file = DIR_ADDONS . $module . DIRECTORY_SEPARATOR . 'init.php';
		if(is_file($loader_file)){
			include DIR_ADDONS . $module . DIRECTORY_SEPARATOR . 'init.php';
			$loader_class = '\module\\' . $module . '\ModuleLoader';
			$loader_class::load_models();
			$loader_class::load_routes();
		}
	}
}
