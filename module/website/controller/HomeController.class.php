<?php
/**
 * Maes Jerome
 * HomeController.class.php, created at Jun 2, 2016
 *
 */
namespace module\website\controller;


class HomeController extends WebsiteController{

	// Routes

	public function homeAction(){
		return $this->render('website.home', array('slideshow' => True));
	}
	
	
}