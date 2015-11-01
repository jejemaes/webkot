<?php
/**
 * Maes Jerome
 * BlackController.class.php, created at Sep 22, 2015
 *
 */

namespace system\core;

use \system\core\BlackRouter as BlackRouter;

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
		if(substr($url, 0, 3) !== 'http'){
			$url = url_from_path($url);
		}
		return $this->app->redirect($url, $status);
	}
	
	
	public function forbidden($message){
		echo "FORBIDDEN" . $message;
		return;
	}

}