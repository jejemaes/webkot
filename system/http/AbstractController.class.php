<?php
/**
 * Maes Jerome
 * AbstractController.class.php, created at May 29, 2016
 *
 */
namespace system\http;
use \Slim\Interfaces\ContainerInterface as ContainerInterface;
use \system\core\Environment as Env;
use \system\http\Session as Session;


abstract class AbstractController{
	
	protected $ci;
	
	public $request;
	public $response;
	public $session;
	public $env;
	
	// Constructor
	public function __construct($ci){
		$this->ci = $ci;
		$this->request = $ci->request;
		$this->response = $ci->response;
		$this->session = Session::getInstance();
		$this->env = Env::get();
	}
	
	public function render($template, array $data=[], array $headers=[]){
		// update context
		$data = array_merge($this->_render_context(), $data); 
		// render template and build response
		$output = $this->_render($template, $data);
		$this->response->getBody()->write($output);
		// set response headers
		if(($headers)){
			foreach($headers as $key => $value){			
				$this->response = $this->response->withHeader($key, $value);
			}
		}
		return $this->response;
	}
	
	public function _render($template, array $data=array()){
		$view = $this->env['ir_view'];
		return $view::render($template, $data);
	}
	
	/**
	 * Return a base render context. this should be override to add other 
	 * data required to render templates.
	 */
	public function _render_context(){
		return [];
	}
	
	/**
	 * Redirect to given url.
	 * @param string $url
	 * @param int $status
	 */
	public function redirect($url, $status=302){
		return $this->response->withRedirect($url)->withStatus($status); 
	}
	
	public function get_referer(){
		return $_SERVER['HTTP_REFERER'];
	}
}
