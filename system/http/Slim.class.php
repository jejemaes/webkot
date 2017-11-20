<?php
/**
 * Maes Jerome
 * Slim.class.php, created at Oct 21, 2015
 *
 */
namespace system\http;


class Slim extends \SlimController\Slim {
	
	public $auth_map;
	
	public function __construct(array $settings = array()){
		
		parent::__construct($settings);
		
		// new attributes
		$this->container->singleton('session', function () {
			return Session::getInstance();
		});
		
		$this->auth_map = new \Slim\Helper\Set();
		
		// authenticate middleware
		$auth_middleware = new SlimAuthMiddleware();
		$this->add($auth_middleware);
			
		// session handler middleware
		$session_middleware = new SlimSessionMiddleware(array(
				'name' => $settings['session.name'], // name of the cookie containing the PHPSESSID
				'expires' => $settings['session.autorefresh'],
				'lifetime' => $settings['session.lifetime']
		));
		$this->add($session_middleware);
		
	}
	
	public function addRoutes(array $routes, $globalMiddlewares = array())
	{
		if (!is_array($globalMiddlewares)) {
			if (func_num_args() > 2) {
				$args = func_get_args();
				$globalMiddlewares = array_slice($args, 1);
			} else {
				$globalMiddlewares = array($globalMiddlewares);
			}
		}
	
		foreach ($routes as $path => $routeArgs) {
			// create array for simple request
			$routeArgs = (is_array($routeArgs)) ? $routeArgs : array('any' => $routeArgs);
	
			if (array_keys($routeArgs) === range(0, count($routeArgs) - 1)) {
				// route args is a sequential array not associative
				$routeArgs = array('any' => array($routeArgs[0],
						isset($routeArgs[1]) && is_array($routeArgs[1]) ? $routeArgs[1] : array_slice($routeArgs, 1))
				);
			}
	
			foreach ($routeArgs as $httpMethod => $classArgs) {
	
				/*
				if(is_array($classArgs)) {
					$classRoute       = $classArgs[0];
					$localMiddlewares = is_array($classArgs[1]) ? $classArgs[1] : array_slice($classArgs, 1);
				} else {
					$classRoute       = $classArgs;
					$localMiddlewares = array();
				}
				*/
				
				$localMiddlewares = array_key_exists('middlewares', $classArgs) ? $classArgs['middlewares'] : array();
				$conditions = array_key_exists('conditions', $classArgs) ? $classArgs['conditions'] : false;
				$name = array_key_exists('name', $classArgs) ? $classArgs['name'] : false;
				$classRoute = $classArgs['class_route']; // mandatory
				$auth = array_key_exists('auth', $classArgs) ? $classArgs['auth'] : 'public';
				
				// specific HTTP method
				$httpMethod = strtoupper($httpMethod);
				if (!in_array($httpMethod, static::$ALLOWED_HTTP_METHODS)) {
					throw new \InvalidArgumentException("Http method '$httpMethod' is not supported.");
				}
	
				$routeMiddlewares = array_merge($localMiddlewares, $globalMiddlewares);
				$route = $this->addControllerRoute($path, $classRoute, $routeMiddlewares);
	
				if (!isset($this->routeNames[$name])) {
					$route->name($name);
					$this->routeNames[$name] = 1;
				}
	
				if ('any' === $httpMethod) {
					call_user_func_array(array($route, 'via'), static::$ALLOWED_HTTP_METHODS);
				} else {
					$route->via($httpMethod);
				}
				
				if($conditions){
					//$route->conditions($conditions);
				}
				
				if($auth){
					$this->auth_map->set($path, $auth);
				}
			}
		}
	
		return $this;
	}
	
}