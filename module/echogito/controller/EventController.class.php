<?php
/**
 * Maes Jerome
 * EventController.class.php, created at Dec 1, 2017
 *
 */

namespace module\echogito\controller;

use module\website\controller\WebsiteController as WebsiteController;


class EventController extends WebsiteController{

	function pageEchogitoAction(){
		return $this->render('event.page_echogito', array());
	}
}
