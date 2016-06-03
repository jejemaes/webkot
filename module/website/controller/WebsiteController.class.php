<?php
/**
 * Maes Jerome
 * WebsiteController.class.php, created at May 28, 2016
 *
 */
namespace module\website\controller;
use module\base\controller\BaseController as BaseController;
use module\website\model\Website;


class WebsiteController extends BaseController{
	
	// Render templates
	
	public function _render_context(){
		$context = parent::_render_context();
		$context = array_merge($context, [
			'website' => Website::getInstance(),
			'menus' => [],
			'website_url' => __BASE_URL,
			'website_title' => False,
		]);
		return $context;
	}

	// Pager
	
	/**
	 * Generate the url for pager item
	 * @param int $page
	 * @param string $url
	 * @param array $url_args
	 * @return Ambigous <unknown, string>
	 */
	private function _pager_url($page, $url, array $url_args=array()){
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
	 * Build the pager dict of value for rendering
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
	
	// Page actions
	
	public function pageAction($page){
		
	}
}
