<?php
/**
 * Maes Jerome
 * BlackController.class.php, created at Sep 22, 2015
 *
 */

namespace system\core;

class BlackController extends \SlimController\SlimController{

	protected $renderTemplateSuffix = NULL; // don't add suffix to template name
	
	
	public function __construct(\Slim\Slim &$app){
		parent::__construct($app);
		
		echo $this->session();
		
	}
	
	protected function session(){
		return $this->app->session;
	}

}