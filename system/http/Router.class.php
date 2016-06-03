<?php
/**
 * Maes Jerome
 * Slim.class.php, created at May 29, 2016
 *
 */
namespace system\http;
use Slim\App as App;


class Router {
	
	// Singleton
	
	protected static $_instance;
	
	public static function get(){
		if (!isset(self::$_instance)) {
			$c = __CLASS__;
			self::$_instance = new $c;
		}
		return self::$_instance;
	}
	
	// Business
	
	public $options;
	private $app;
	
	private function __construct(array $options=[]) {
		$this->options = $options;
		// slim app : custom param
		$container = new \Slim\Container();
		$container['foundHandler'] = function() {
			return new ControllerStrategy();
		};
		$container['notFoundHandler'] = function () {
			return new HttpNotFoundHandler();
		};
		$container['errorHandler'] = function ($container) {
			return new HttpErrorHandler($container->get('settings')['displayErrorDetails']);
		};
		$this->app = new App($container);
		// middlewares
		$this->app->add(new SessionMiddleware());
		return $this;
	}
	
	public function route($path, $callable, $methods, $name, $auth='public'){
		// set methods in array
		if(!is_array($methods)){
			$methods = array($methods);
		}
		// register route in slim app
		foreach ($methods as $method){
			switch (strtoupper($method)) {
				case "PUT":
					$route = $this->app->put($path, $callable);
				case "DELETE":
					$route = $this->app->delete($path, $callable);
				case "POST":
					$route = $this->app->post($path, $callable);
					break;
				case "GET":
				default:
       				$route = $this->app->get($path, $callable);
			}
			$route->setName($name);
		}
	}
	
	
	/**
	 * dispatch the current request to the correct route
	 * @return the response of the matched controller method
	 */
	public function dispatch(){
		return $this->app->run();
	}
	
	/**
	 * return the url of the route named 'name', with the given param
	 * @param string $name
	 * @param array $data
	 * @param array $queryParams
	 */
	public function url_for($name, array $data=[], array $queryParams = []){
		return $this->app->router->pathFor($name, $data, $queryParams);
	}
}
