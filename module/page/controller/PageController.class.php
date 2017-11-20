<?php
/**
 * Maes Jerome
 * PageController.class.php, created at Nov 20, 2017
 *
 */
namespace module\page\controller;

use module\website\controller\WebsiteController as WebsiteController;
use module\page\model\Page as Page;


class PageController extends WebsiteController{

	public function pageAction($slug){
		$page = Page::getPage($slug);
		return $this->render('page.page_container', array(
				'page' => $page,
		));
	}

	public function pagelistAction(){
		$pages = Page::getListPage();
		return $this->render('page.page_list', array(
				'pages' => $pages,
		));
	}

}
