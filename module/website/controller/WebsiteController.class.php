<?php
/**
 * Maes Jerome
 * WebsiteController.class.php, created at Sep 28, 2015
 *
 */
namespace module\website\controller;

use system\core\IrModule as IrModule;
use system\http\Session as Session;
use module\web\controller\WebController as WebController;
use module\website\model\Page as WebsitePage;
use module\website\model\Website;
use module\website\model\Menu as Menu;

class WebsiteController extends WebController{

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
	
	// Tools / Utils
	
	protected function error_page($exception){
		return $this->render('website.error', array('error' => $exception));
	}
	
	// Rendering
	/**
	 * Override render method to add frontend global function
	 * @see \SlimController\SlimController::render()
	 */
	public function render($template, $data = array()){
		global $Router;
		$menus = Menu::get_root_menus();
		$default = array(
			'slideshow' => false,
			'menus' => $menus,
			'website' => Website::getInstance(),
			'website_url' => __BASE_URL,
		);
		$data = array_merge($default, $data);
		return parent::render($template, $data);
	}

}