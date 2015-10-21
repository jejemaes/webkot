<?php
/**
 * Maes Jerome
 * SlimAuthMiddleware.class.php, created at Oct 21, 2015
 *
 */
namespace system\lib\slim;

class SlimAuthMiddleware extends \Slim\Middleware {
	
	/**
	 * Constructor
	 * @param  array  $settings
	 */
	public function __construct($settings = array())
	{	
		$default = array('key' => 'value');
		$this->settings = array_merge($default, $settings);
	}

	/**
	 * Call
	 */
	public function call(){
		$route = $this->app->router->getCurrentRoute();
		if($route){
			$path = $route->getPattern();
			$auth_type = $this->app->auth_map->get($path, 'public');
			$this->_authenticate($auth_type);
		}
		$this->next->call();
	}

	
	/**
	 * authentification dispatching
	 * @param string $auth_type : the authentication type required for the current route
	 * @throws ProgrammingException
	 */
	protected function _authenticate($auth){
		$method_name = '_authenticate' . ucfirst($auth);
		if(method_exists($this, $method_name)){
			return $this->$method_name();
		}
		throw new \Exception('AUTH ERROR : no ' . $method_name . ' definied for type authentification ' . $auth);
	}
	
	protected function _authenticatePublic(){
		$_SESSION = array();
		return true;
	}
	
	protected function _authenticateUser(){
		// TODO
		return true;
	}
	

}
