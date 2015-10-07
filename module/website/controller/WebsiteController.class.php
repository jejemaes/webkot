<?php
/**
 * Maes Jerome
 * WebsiteController.class.php, created at Sep 28, 2015
 *
 */
namespace module\website\controller;

use system\core\BlackController as BlackController;
use system\core\IrModule as IrModule;
use \module\website\model\Page as WebsitePage;

class WebsiteController extends BlackController{

	public function indexAction(){
		$this->render('website.home', array());
	}

	public function pageAction($page_slug){
		$page = WebsitePage::get_page($page_slug);
		$this->render('website.page', array('page' => $page));
	}
	
	public function updateAction($module){
		$module = IrModule::get_module($module);
		$module->do_update();
	}

}