<?php
/**
 * Maes Jerome
 * BlackController.class.php, created at Sep 22, 2015
 *
 */

namespace system\core;

use \system\core\BlackRouter as BlackRouter;
use \system\tools\Url as Url;

class BlackController extends \SlimController\SlimController{

	protected $renderTemplateSuffix = NULL; // don't add suffix to template name
	
	
	public function __construct(\Slim\Slim &$app){
		parent::__construct($app);
	}
	
	protected function session(){
		return $this->app->session;
	}
	
	/**
	 * Redirect to given url. Absolute and relative url works.
	 * @see \SlimController\SlimController::redirect()
	 */
	public function redirect($url, $status=302){
		if(substr($url, 0, 4) !== 'http'){
			$url = Url::url_from_path($url);
		}
		return $this->app->redirect($url, $status);
	}
	
	
	public function forbidden($message){
		echo "FORBIDDEN" . $message;
		return;
	}
	
	public function checkMandatoryParams(array $names){
		$params = $this->params($names);
		foreach($params as $p){
			if(!$p){
				return false;
			}
		}
		return true;
	}
	
	
	public function json_response($data=array()){
		$this->app->response()->header('Content-Type', 'application/json');
		$body = utf8_decode(json_encode($data, JSON_UNESCAPED_UNICODE));
		$this->app->response()->body($body);
	}

}