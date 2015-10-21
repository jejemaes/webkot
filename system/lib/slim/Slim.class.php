<?php
/**
 * Maes Jerome
 * Slim.class.php, created at Oct 21, 2015
 *
 */
namespace system\lib\slim;


class Slim extends \SlimController\Slim {
	
	public $auth_map;
	
	public function __construct(array $settings = array()){
		
		parent::__construct($settings);
		
		// new attributes
		$this->container->singleton('session', function () {
			return SlimSession::getInstance();
		});
		
		$this->auth_map = new \Slim\Helper\Set();
		
		// authenticate middleware (will be execute after session middleware)
		$auth_middleware = new SlimAuthMiddleware();
		$this->add($auth_middleware);
			
		// session handler middleware
		$session_middleware = new SlimSessionMiddleware(array(
				'name' => $settings['session.name'], // name of the cookie containing the PHPSESSID
				'autorefresh' => $settings['session.autorefresh'],
				'lifetime' => $settings['session.lifetime']
		));
		$this->add($session_middleware);
		
	}
	
	
	public function addRoute($path, $class_and_method, $auth='public'){
		
		$this->auth_map->set($path, $auth);
		
		return $this->addControllerRoute($path, $class_and_method);
	}
}