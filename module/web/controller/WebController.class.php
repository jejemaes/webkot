<?php
/**
 * Maes Jerome
 * WebController.class.php, created at Nov 30, 2015
 *
 */
namespace module\web\controller;

use system\core\BlackController as BlackController;
use system\core\BlackView as BlackView;
use system\core\IrModel as IrModel;
use system\http\Session as Session;


class WebController extends BlackController{
	
	/**
	 * Override render method to add website global function
	 * @see \SlimController\SlimController::render()
	 */
	public function render($template, $data = array()){
		global $Router;
		$default = array(
				'session' => Session::getInstance(),
				'router' => $Router,
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
				'base_url' => __BASE_URL,
				'session_flash_messages' => array(),
		);
		$data = array_merge($default, $data);
		return parent::render($template, $data);
	}
	
	private function _pager_url($page, $url, $url_args=array()){
		$_url = $url;
		if($page > 1){
			$_url = sprintf('%s/page/%s', $url, $page);
		}
		if($url_args){
			$_url = sprintf('%s?%s', $url, http_build_query($url_args));
		}
		return $_url;
	}
	
	/**
	 * Built the pager dict of value for rendering
	 * @param unknown $url
	 * @param unknown $total
	 * @param number $page
	 * @param number $step
	 * @param number $scope
	 * @param unknown $url_args
	 * @return multitype:number multitype:NULL Ambigous <number, mixed>  multitype:mixed NULL  multitype:NULL mixed  multitype:multitype:NULL unknown   multitype:mixed Ambigous <unknown, string>
	 */
	public function pager($url, $total, $page=1, $step=20, $scope=5, $url_args=array()){
		$page_count = intval(ceil((float) $total / (float) $step));
		
		$page = max(1, min($page, $page_count));
		$scope -= 1;
		
		$pmin = max($page - intval(floor((float) $scope / 2)), 1);
		$pmax = min($pmin + $scope, $page_count);
		
		if($pmax - $pmin < $scope){
			if($pmax - $scope > 0){
				$pmin = $pmax - $scope;
			}else{
				$pmin = 1;
			}
		}
		
		$page_list = array();
		foreach (range($pmin, $pmax) as $number) {
	    	$page_list[] = array(
	    		'url' => $this->_pager_url($number, $url),
	    		'num' => $number
	    	);
		}
		
		return array(
			'page_count' => $page_count,
			'offset' => ($page - 1) * $step,
			'page' => array(
				'url' => $this->_pager_url($page, $url, $url_args),
				'num' => $page,
			),
			'page_start' => array(
				'url' => $this->_pager_url($pmin, $url, $url_args),
				'num' => $pmin
			),
			'page_previous' => array(
				'url' =>$this->_pager_url(max($pmin, $page - 1), $url, $url_args),
				'num' => max($pmin, $page - 1)
			),
			'page_next' => array(
				'url' => $this->_pager_url(min($pmax, $page + 1), $url, $url_args),
				'num' => min($pmax, $page + 1)
			),
			'page_end' => array(
				'url' => $this->_pager_url($pmax, $url, $url_args),
				'num'=> $pmax
			),
			'pages' => $page_list
		);
	}

	
	//---------------------------
	// Model Query API
	//----------------------------
	public function nameSearchAction($model){
		$class_name = IrModel::get_model($model);
		$name = $this->request()->params('name');
		$result = $class_name::name_search($name);
		return $this->json_response($result);
	}
	
}