<?php
/**
 * Maes Jerome
 * SessionMiddleware.class.php, created at Jun 3, 2016
 *
 */
namespace system\http;
use system\core\Environment as Env;


class SessionMiddleware {
	
	/**
	 * Constructor
	 *
	 * @param array $settings
	 */
	public function __construct($settings=[]) {
		$defaults = [
			'expires' => '20 minutes',
			'path' => '/',
			'domain' => null,
			'secure' => false,
			'httponly' => false,
			'name' => 'wk_session',
			'autorefresh' => false,
		];
		$this->settings = array_merge($defaults, $settings);
		if (is_string($this->settings['expires'])) {
			$this->settings['expires'] = strtotime($this->settings['expires']);
		}
		session_cache_limiter(false);
	}
	
	/**
	 * Middleware invocation : process the action before or after the other middleware
	 * @param unknown $request
	 * @param unknown $response
	 * @param unknown $next
	 * @return unknown
	 */
	public function __invoke($request, $response, $next){
		$this->loadSession();
		$this->authenticate();
		$response = $next($request, $response);
		$this->saveSession();
		return $response;
	}
	
	
	/**
	 * Load session
	 */
	protected function loadSession()
	{
		if (session_id() === '') {
			$settings = $this->settings;
			$name = $settings['name'];
				
			// custom sesion cookie
			session_set_cookie_params(
				$settings['expires'],
				$settings['path'],
				$settings['domain'],
				$settings['secure'],
				$settings['httponly']
			);
			session_name($name);
				
			// start the session
			session_start();
				
			// auto refresh
			if ($settings['autorefresh'] && isset($_COOKIE[$name])) {
				setcookie(
					$name,
					$_COOKIE[$name],
					time() + $settings['lifetime'],
					$settings['path'],
					$settings['domain'],
					$settings['secure'],
					$settings['httponly']
				);
			}
		}
	}
	
	/**
	 * Authenticate the user on the session
	 */
	public function authenticate(){
		// set user record on session
		$session = Session::getInstance();
		$session->set('user', null);
		$uid = $session->get('uid');
		if($uid){
			$env = Env::get();
			$user = $env['user']::browse($uid);
			$session->set('user', $user);
		}
	}
	
	/**
	 * Save session
	 */
	protected function saveSession() {
	
	}
}