<?php
/**
 * Maes Jerome
 * WebsiteController.class.php, created at Sep 28, 2015
 *
 */
namespace module\website\controller;

use system\core\BlackController as BlackController;
use system\core\IrModule as IrModule;
use system\http\Session as Session;
use module\website\model\Page as WebsitePage;
use module\website\model\Website;
use module\website\model\Menu as Menu;

class WebsiteController extends BlackController{

	public function indexAction(){
		return $this->render('website.home', array('slideshow' => True));
	}

	public function pageAction($page_slug){
		$page = WebsitePage::get_page($page_slug);
		return $this->render('website.page', array('page' => $page));
	}
	
	public function updateAction($module){
		$module = IrModule::get_module($module);
		$module->do_update();
	}
	
	
	/**
	 * Override render method to add website global function
	 * @see \SlimController\SlimController::render()
	 */
	public function render($template, $data = array()){
		global $Router;
		$menus = Menu::get_root_menus();
		$default = array(
			'slideshow' => false,
			'session' => Session::getInstance(),
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
			},
			'website' => Website::getInstance(),
			'website_url' => __BASE_URL,
		);
		$data = array_merge($default, $data);
		return parent::render($template, $data);
	}

}