<?php
/**
 * Maes Jerome
 * BlackView.class.php, created at Sep 22, 2015
 *
 */
namespace system\core;

use system\lib\qweb\QWebEngine;
use system\core\BlackView as BlackView;

class BlackControllerView extends \Slim\View {
	
	private $_engine;
	
	public function __construct() {
		parent::__construct();
		$loader = function($name){
			return BlackView::get_view($name);
		};
		$this->_engine = QWebEngine::getEngine($loader);
	}
	
	
	protected function render($template, $data = array()){
		$data = array_merge($this->data->all(), (array) $data);
		return $this->_engine->render($template, $data);
	}
	
}
