<?php
/**
 * Maes Jerome
 * website.class.php, created at Sep 28, 2015
 *
 */
namespace module\website\model;

use \system\core\BlackModel as BlackModel;
use \module\website\model\Menu as Menu;

class Website {

	public static function hook_prerender(){
		global $Router;
		$menus = Menu::get_root_menus();
		$Router->addRenderData(array(
				'router' => $Router,
				'menus' => $menus,
				'url' => function($relative){
					$pos = strpos($relative, 'http');
					if($pos === false){
						// remove the first '/'
						if($relative[0] === '/'){
							$relative = substr($relative, 1, strlen($relative)); 
						}
						// make absolute url
						return __BASE_URL . $relative;
					}
					return $relative;
				}
		));
	}

}