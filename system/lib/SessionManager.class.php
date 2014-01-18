<?php

class SessionManager{
	
	protected static $_instance;
	const SESSION_VAR = "sessioninfo"; 
	
	/**
	 * getInstance
	 * @return SessionManager $instance : the instance of SessionManager
	 */
	public static function getInstance(){
		if (!isset(self::$_instance)) {
			$c = __CLASS__;
			self::$_instance = new $c;
			self::$_instance->__construct();
		}
		//var_dump($_SESSION);
		return self::$_instance;
	}
	
	/**
	 * Constructor
	 */
	public function __construct(){
		if(!session_id()){
			session_start();
		}
			if($this->existsSession()){
				if($this->generateCheck() != $this->getSession()->getCheck()){
					// throw new exception !!
					echo "Session ERROR, line30, sessionManager";
				}
			}else{
				// create a Visitor session
				$role = RoleManager::getInstance()->getMinRole()->getRole();
				
				$mmanager = ModuleManager::getInstance();
				$capabilities = $mmanager->getUserRoleCapabilities($role);
				
				$session = new Session(null, $capabilities, $this->generateCheck(), $role);
				$this->setSession($session);
			}
		//}
	
	}
	
	
	public function initializeSession(User $user = null, $capabilities = array(), $role = null){
		
		$role = ($role == null ? Role::getMinRole() : $role);
		
		if(!$this->existsUserSession()){
			// create a session
			$session = new Session($user, $capabilities, $this->generateCheck(), $role);
			$this->setSession($session);
		}else{
			if($this->generateCheck() != $this->getSession()->getCheck()){
				// throw new exception !!
				echo "Session ERROR, line54, sessionManager";
			}
		}
	}
	
	
	/**
	 * Set the User Session
	 * @param User $user
	 * @param String $role
	 */
	public function setUserSession(User $user, $role){
		$session = new Session($user, array(), $this->generateCheck(), $role);
		$this->setSession($session);
	}
	
	
	public function getSessionRole(){
		$session = $this->getSession();
		return $session->getRole();
	}
	
	
	/**
	 * check if a session already exists (even a Visitor one)
	 * @return boolean $b : true if a session is already defined in the $_SESSION var. False otherwise.
	 */
	public function existsSession(){
	//	echo self::SESSION_VAR;
		return isset($_SESSION[self::SESSION_VAR]);
	}
	
	
	/**
	 * check if a User Session is defined 
	 * @return boolean $b : true if a User Session exists in $_SESSION. False otherwise.
	 * @tutorial : DON'T PUT A session_write_close(); IN THIS METHOD
	 */
	public function existsUserSession(){
		if($this->existsSession()){
			$session = $this->getSession();
			return ($session->getUserprofile() != null);
		}
		return false;
	}
	
	
	/**
	 * set other capabilities to the Session Object, according to the session role
	 * @param array $modCapabilities : the capabilities of the module to associate to the Object
	 */
	public function setCapabilities(array $modCapabilities){
		$session = $this->getSession();
		$role = $session->getRole();
		$sessionCapabilities = $modCapabilities[$role];
		if($sessionCapabilities == null){
			$sessionCapabilities = array();
		}	
		$session->setCapabilities($sessionCapabilities);
		$this->setSession($session);
	}
	
	/**
	 * get the session Capabilities
	 * @return array $capabilities : the Capabilities of the Session
	 */
	public function getCapabilities(){
		$session = $this->getSession();
		session_write_close();
		return $session->getCapabilities();
	}
	
	
	public function getUserprofile(){
		if($this->existsUserSession()){
			$session = $this->getSession();
			session_write_close();
			return $session->getUserprofile();
		}else{
			return false;
		}
	}
	
	public function setUserprofile($profile){
		$session = $this->getSession();
		$session->setUserprofile($profile);
		$this->setSession($session);
	}
	
	public function destroySession(){
		session_destroy();
		$role = $rmanager = RoleManager::getInstance()->getMinRole();
		$session = new Session(null, array(), $this->generateCheck(), $role->getLevel());
		$this->setSession($session);
	}
	
	/**
	 * built the check code
	 * @return string $md5 : the check code 
	 */
	private function generateCheck(){
		return md5('hdslfsdj345df' . $_SERVER["HTTP_USER_AGENT"]);
	}
	
	
	/**
	 * set the Session Object in the $_SESSION variable
	 * @param Session $session : the Session to save
	 */
	private function setSession(Session $session){
		$_SESSION[self::SESSION_VAR] = serialize($session);
		session_write_close();
	}
	
	/**
	 * get back the Session Object stocked in the $_SESSION var
	 * @return Session $session : the Session saved in the $_SESSION variable
	 */
	private function getSession(){
		$session = $_SESSION[self::SESSION_VAR];
		return unserialize($session);
	}
	
	
}