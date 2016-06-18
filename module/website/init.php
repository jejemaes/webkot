<?php
/**
 * Maes Jerome
 * init.php, created at May 28, 2016
 *
 */
namespace module\website;
use system\core\ModuleLoader as AbstractLoader;


class ModuleLoader extends AbstractLoader{

	public static function load_models(){
		// DO NOT IMPORT website, since it's not a regular model
		self::register_model('module\website\model\Page');
		self::register_model('module\website\model\Menu');
	}
	
	public static function load_routes(){
		// Home routes
		$controller_class = 'module\website\controller\HomeController';
		self::route_get('/', $controller_class . ':homeAction', 'website_homepage');
		
		// Page routes
		$controller_class = 'module\website\controller\PageController';
		self::route_get('/page/{slug:[a-zA-Z_-]+}', $controller_class . ':pageAction', 'website_page');
		
		// User routes
		$controller_class = 'module\website\controller\UserController';
		self::route_get('/user[/page/{page:[0-9]+}]', $controller_class . ':indexAction', 'website_user_list');
		self::route_get('/user/profile/{userid:[0-9]+}', $controller_class . ':profileAction', 'website_user_profile');
	}
}
