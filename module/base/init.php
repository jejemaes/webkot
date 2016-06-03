<?php
/**
 * Maes Jerome
 * init.php, created at May 28, 2016
 *
 */
namespace module\base;
use system\core\ModuleLoader as AbstractLoader;


class ModuleLoader extends AbstractLoader{
	
	public static function load_models(){
		self::register_model('module\base\model\User');
	}
	
	public static function load_routes(){
		// Module routes
		$controller_class = 'module\base\controller\ModuleController';
		self::route_get('/module/test/{id:[0-9]+}', $controller_class . ':testAction', 'base_test_module');
		
		self::route_get('/module/install/{name:[a-zA-Z_]+}', $controller_class . ':installAction', 'base_module_install');
		self::route_get('/module/update/{name}', $controller_class . ':updateAction', 'base_module_update');
		self::route_get('/module/updatelist', $controller_class . ':updateListAction', 'base_module_updatelist');
	}
}
