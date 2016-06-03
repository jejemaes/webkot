<?php
/**
 * Maes Jerome
 * BaseController.class.php, created at May 28, 2016
 *
 */
namespace module\base\controller;
use system\core\BlackController as BlackController;


class BaseController extends BlackController{
	
	// Template rendering 
	
	public function _render_context(){
		$context = parent::_render_context();
		$context = array_merge($context, [
			'url' => function($path){
				return __BASE_URL . $path;
			}
		]);
		return $context;
	}

}
