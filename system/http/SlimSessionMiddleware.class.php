<?php

namespace system\http;


class SlimSessionMiddleware extends \Slim\Middleware
{
	/**
	 * @var array
	 */
	protected $settings;
	protected $app;

	/**
	 * Constructor
	 *
	 * @param array $settings
	 */
	public function __construct($settings = array())
	{
		$defaults = array(
				'expires' => '20 minutes',
				'path' => '/',
				'domain' => null,
				'secure' => false,
				'httponly' => false,
				'name' => 'slim_session',
				'autorefresh' => false,
		);
		$this->settings = array_merge($defaults, $settings);
		if (is_string($this->settings['expires'])) {
			$this->settings['expires'] = strtotime($this->settings['expires']);
		}
		
		$this->app = Slim::getInstance();
	
		//ini_set('session.use_cookies', 0);
		session_cache_limiter(false);
		/*
		session_set_save_handler(
			array($this, 'open'),
			array($this, 'close'),
			array($this, 'read'),
			array($this, 'write'),
			array($this, 'destroy'),
			array($this, 'gc')
		);
		*/
	}

	/**
	 * Call
	 */
	public function call()
	{
		$this->loadSession();
		$this->next->call();
		$this->saveSession();
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
	 * Save session
	 */
	protected function saveSession()
	{
		
	}

	/********************************************************************************
	 * Session Handler
	*******************************************************************************/

	/**
	 * @codeCoverageIgnore
	 */
	public function open($savePath, $sessionName)
	{
		return true;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function close()
	{
		return true;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function read($id)
	{
		return '';
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function write($id, $data)
	{
		return true;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function destroy($id)
	{
		return true;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function gc($maxlifetime)
	{
		return true;
	}
}
