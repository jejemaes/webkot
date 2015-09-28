<?php
/**
 * Maes Jerome
 * BlackRouter.class.php, created at Sep 22, 2015
 *
 */
namespace system\core;

class BlackRouter {
	
	protected static $_instance;
	private $_slim;
	
	public static function getInstance(){
		if (!isset(self::$_instance)) {
			$c = __CLASS__;
			self::$_instance = new $c;
			self::$_instance->__construct();
		}
		return self::$_instance;
	}
	
	private function __construct(){
		$this->_slim = new \SlimController\Slim(array(
			'debug' => true,
			'templates.path' => '',
			'controller.class_prefix' => '',
			'controller.method_suffix' => '',
			'controller.template_suffix' => NULL,
			'controller.param_prefix' => NULL,
			'view' => 'system\core\BlackControllerView',
		));
	}
	

	//###########################
	//######### ROUTING #########
	//###########################
	
	/**
	 * add a route to the mapping routes
	 * @param string $path : the path of the route to add. Should be unique.
	 * @param string $class_and_method : callback of the route, controller class and method name such as 'className:methodName'
	 * @param string $meth : method allowed to access  the route. String concat with '|' as separator.
	 * @param string $auth : authentification required to access the route ('public', .
	 * @param array $conditions : key-array of type/regex condition for the route params. Use the SlimFramework format (@see http://docs.slimframework.com/routing/conditions/)
	 * @return Ambigous <\Slim\Route, \Slim\Route>
	 */
	public function addRoute($path, $class_and_method, $meth = 'GET|POST', $auth='public', $conditions=array()){
		// TODO : maybe remove or improve !
		$path = str_replace(' ', '%20', __BASE_PATH_URL) . $path;
	
		// authenticate callable (slim middleware)
		$self = $this;
		$authenticateRoute = function ($auth='public', $self) {
			return function () use ($auth, $self) {
				$is_authenticate = $self->_authenticate($auth);
				if (!$is_authenticate) {
					$self->_slim->redirect('/login');
				}
			};
		};
	
		// create route
		$route = $this->_slim->addControllerRoute($path, $class_and_method, array($authenticateRoute($auth, $this)));
	
		// add methods
		$meth = preg_split("/[|]/", $meth);
		foreach ($meth as $m){
			$route = $route->via(strtoupper($m));
		}
	
		// add conditions
		return $route->conditions($conditions);
	}
	
	public function hasRoute(){
		return $this->_slim->container->get('router')->getMatchedRoutes($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'], true);
	}
	
	/**
	 * dispatch the current request to the correct route
	 * @return the response of the matched controller method
	 */
	public function dispatch(){
		return $this->_slim->run();
	}

	
	//###########################
	//#### AUTHENTIFICATION #####
	//###########################
	
	/**
	 * authentification dispatching
	 * @param unknown $auth
	 * @throws ProgrammingException
	 */
	public function _authenticate($auth){
		$method_name = '_authenticate' . ucfirst($auth);
		if(method_exists($this, $method_name)){
			return $this->$method_name();
		}
		throw new \Exception('AUTH ERROR : no ' . $method_name . ' definied for type authentification ' . $auth);
	}
	
	
	public function _authenticatePublic(){
		// TODO
		return true;
	}
	
	public function _authenticateUser(){
		// TODO
		return true;
	}


	//###########################
	//########## OTHER ##########
	//###########################
	
	/**
	 * Add a hook before dispatching route, and render template. Use this hook to
	 * add data into the data required to render the template (data use in website layout extension).
	 * @param string $class_and_method : callback of the route, controller class and method name such as 'className:methodName'
	 */
	public function addHook($class_and_method){
		// split the method and class names
		list($class_name, $method_name) = preg_split('[:]', $class_and_method);
		// extract closure from hook method
		$reflection = new \ReflectionClass($class_name);
		$closure = $reflection->getMethod($method_name)->getClosure();
		// add the hook
		$this->_slim->hook('slim.before.dispatch', $closure);
	}
	
	
}
