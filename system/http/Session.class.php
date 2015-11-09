<?php
/**
 * Session Helper class
 *
 * This is a general-purpose class that allows to manage PHP built-in sessions
 * and the session variables passed via $_SESSION superglobal.
 *
 */
namespace system\http;
use system\interfaces\iSession as iSession;

class Session implements iSession {
	
	protected static $_instance;
	
	public static function getInstance(){
		if (!isset(self::$_instance)) {
			$c = __CLASS__;
			self::$_instance = new $c;
			self::$_instance->__construct();
		}
		return self::$_instance;
	}
	
	private function __construct(){
		
	}
	
	public function __toString(){
		return 'Session(login='.$this->get('login').' uid='.$this->get('uid').');';
	}
	
	public function authenticate($user_id, $login, $crypted_password){
		$this->set('uid', $user_id);
		$this->set('login', $login);
		$this->set('password', $crypted_password);
	}
	
	public function is_empty(){
		return !empty($_SESSION);
	}
	
    /**
     * Get a session variable.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get($key, $default = null){
        return $this->exists($key)
            ? $_SESSION[$key]
            : $default;
    }

    /**
     * Set a session variable.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value){
        $_SESSION[$key] = $value;
    }

    /**
     * Delete a session variable.
     *
     * @param string $key
     */
    public function delete($key){
        if ($this->exists($key)) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Clear all session variables.
     */
    public function clear(){
        $_SESSION = array();
    }

    /**
     * Check if a session variable is set.
     *
     * @param string $key
     *
     * @return bool
     */
    protected function exists($key){
        return array_key_exists($key, $_SESSION);
    }

    /**
     * Get or regenerate current session ID.
     *
     * @param bool $new
     *
     * @return string
     */
    public static function id($new = false){
        if ($new && session_id()) {
            session_regenerate_id(true);
        }

        return session_id() ?: '';
    }

    /**
     * Destroy the session.
     */
    public static function destroy(){
        if (self::id()) {
            session_unset();
            session_destroy();
            session_write_close();

            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 4200,
                    $params['path'],
                    $params['domain'],
                    $params['secure'],
                    $params['httponly']
                );
            }
        }
    }

    /**
     * Magic method for get.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key){
        return $this->get($key);
    }

    /**
     * Magic method for set.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function __set($key, $value){
        $this->set($key, $value);
    }

    /**
     * Magic method for delete.
     *
     * @param string $key
     */
    public function __unset($key){
        $this->delete($key);
    }

    /**
     * Magic method for exists.
     *
     * @param string $key
     *
     * @return bool
     */
    public function __isset($key){
        return $this->exists($key);
    }
}
