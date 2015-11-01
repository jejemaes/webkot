<?php
/**
 * Maes Jerome
 * SlimAuthMiddleware.class.php, created at Oct 21, 2015
 *
 */
namespace system\http;

use \system\res\ResUser as User;
use \system\exceptions\SessionExpiredException as SessionExpiredException;


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
		$method_name = '_authenticate_' . strtolower($auth);
		if(method_exists($this, $method_name)){
			return $this->$method_name();
		}
		throw new \Exception('AUTH ERROR : no ' . $method_name . ' definied for type authentification ' . $auth);
	}
	
	/**
	 * Auth 'none' : there is no session. 
	 */
	protected function _authenticate_none(){
		$this->app->session->clear();
	}
	
	/**
	 * Auth 'public' : if there is a user, we'll keep it. Otherwise the session is empty.
	 * @return boolean
	 */
	protected function _authenticate_public(){
		$session = $this->app->session;
		if($session->uid){
			$this->_authenticate_user();
		}
	}
	
	/**
	 * Auth 'user' : a logged in user is required
	 * @throws SessionExpiredException
	 */
	protected function _authenticate_user(){
		$session = $this->app->session;
		$uid = $session->uid;
		if(!$uid){
			throw new SessionExpiredException('Session expired, can not find current user.');
		}
		$session->set('user', User::find($uid));
	}
	

}