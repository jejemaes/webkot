<?php
/**
 * 
 *
 * @author jeromemaes
 * Dec 1, 2017
 */
namespace module\link\controller;

use module\website\controller\WebsiteController as WebsiteController;
use module\link\model\Link as Link;
use module\link\model\LinkManager as LinkManager;
use module\link\model\LinkCategory as LinkCatergory;
use module\link\model\LinkCategoryManager as LinkCategoryManager;


class LinkController extends WebsiteController{

	public function pageLinksAction(){
		$manager = LinkManager::getInstance();
		$links = $manager->getListLink();
		
		$manager = LinkCategoryManager::getInstance();
		$categories = $manager->getListCategory();
		
		$links_by_category = array();
		foreach ($categories as $category){
			$links_by_category[$category->id] = array();
		}
		foreach ($links as $link){
			$links_by_category[$link->category][] = $link;
		}
		
		return $this->render('link.page_links', array(
				'links_by_category' => $links_by_category,
				'categories' => $categories,
		));
	}
}
