<?php
/**
 * Maes Jerome
 * ModuleLoader.class.php, created at May 27, 2016
 *
 */
namespace system\core;
use system\interfaces\iModuleLoader as iModuleLoader;
use system\core\Environment as Env;
use system\http\Router as Router;

abstract class ModuleLoader implements iModuleLoader{

	
	public static function load_models(){
		
	}
	
	public static function load_routes(){
		
	}
	
	public static function install(){
		
	}
	
	public static function uninstall(){
		
	}
	
	public static function route_get($path, $callable, $name, $auth='public'){
		return self::_register_route($path, array('GET'), $callable, $name, $auth);
	}
	
	public static function route_post($path, $callable, $name, $auth){
		return self::_register_route($path, array('POST'), $callable, $name, $auth);
	}
	
	public static function route_any($path, $callable, $name, $auth){
		return self::_register_route($path, array('GET', 'POST'), $callable, $name, $auth);
	}
	
	private static function _register_route($path, $methods, $callable, $name, $auth='public'){
		$router = Router::get();
		return $router->route($path, $callable, $methods, $name, $auth);
	}
	
	public static function register_model($model){
		$env = Env::get();
		$env->register($model);
	}
}