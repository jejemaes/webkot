<?php
/**
 * Maes Jerome
 * BaseController.class.php, created at May 28, 2016
 *
 */
namespace module\base\controller;
use system\core\BlackController as BlackController;
use system\http\Session as Session;


class BaseController extends BlackController{
	
	// Template rendering 
	
	public function _render_context(){
		$context = parent::_render_context();
		$context = array_merge($context, [
			'url' => function($path){
				if(substr($path, 0, 1) == '/'){
					$path = substr($path, 1);
				}
				return __BASE_URL . $path;
			},
			'session' => Session::getInstance(),
			'session_flash_messages' => $this->session->fetchMessages(),
		]);
		return $context;
	}

}
