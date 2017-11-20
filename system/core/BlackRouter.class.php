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
		$this->_slim = new \system\http\Slim(array(
			'debug' => true,
			'templates.path' => '',
			'controller.class_prefix' => '',
			'controller.method_suffix' => '',
			'controller.template_suffix' => NULL,
			'controller.param_prefix' => '',
			'view' => 'system\core\BlackControllerView',
			'session.name' => 'sid',
			'session.autorefresh' => false,
			'session.lifetime' => '1 month',
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
	public function addRoute($path, $class_and_method, $meth = 'GET|POST', $auth='public', $conditions=array(), $route_name=false){
		//default values 
		$default = array(
				'auth' => 'public',
				'type' => 'http',
				'method' => 'GET',
				'conditions' => array(),
				'name' => false,
				'class_route' => $class_and_method,
				'middlewares' => array()
		);
		
		// add route per method
		$routes = array();
		$meth = preg_split("/[|]/", $meth);
		foreach ($meth as $m){
			$params = array(
				'name' => $route_name,
				'auth' => $auth,
				'type' => 'http',
				'method' => $meth,
				'conditions' => $conditions,
				'class_route' => $class_and_method,
				'middlewares' => array(/*function() {
					var_dump("THIS ROUTE IS ONLY POST");
				}*/),
			);
			$routes[$m] = array_merge($default, $params);;
		}
		
		return $this->_slim->addRoutes(array($path => $routes));
	}
	
	
	public function addAdminRoute($path, $class_and_method, $meth = 'GET|POST', $auth='user', $conditions=array(), $route_name=false){
		$auth = 'user';
		$path = '/' . ADMIN_PATH . $path;
		return $this->addRoute($path, $class_and_method, $meth, $auth, $conditions, $route_name);
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
	
	public function urlFor($name, $params=array()){
		//return substr(__HOST_URL, 0, strlen(__BASE_URL)-1)
		return __HOST_URL . __BASE_PATH_URL . $this->_slim->router->urlFor($name, $params);
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
	
	public function addRenderData(array $data){
		$this->_slim->view->appendData($data);
	}
	
	
}
