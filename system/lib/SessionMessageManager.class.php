<?php


class SessionMessageManager{
	
	protected static $_instance;
	const SESSION_MESSAGE = "sessionmessage";
	
	/**
	 * getInstance
	 * @return SessionMessageManager $instance : the instance of SessionMessageManager
	 */
	public static function getInstance(){
		if (!isset(self::$_instance)) {
			$c = __CLASS__;
			self::$_instance = new $c;
			self::$_instance->__construct();
		}
		return self::$_instance;
	}
	
	/**
	 * Constructor
	 */
	public function __construct(){
		session_start();
	}
	
	public function existsSessionMessage(){
		return isset($_SESSION[self::SESSION_MESSAGE]);
	}
	
	
	public function setSessionMessage(Message $message){
		$_SESSION[self::SESSION_MESSAGE] = serialize($message);
	}
	
	
	public function getSessionMessage(){
		if($this->existsSessionMessage()){
			$message = unserialize($_SESSION[self::SESSION_MESSAGE]);
			$this->eraseSessionMessage();
			return $message;
		}else{
			return new Message();
		}
	}
	
	
	private function eraseSessionMessage(){
		unset($_SESSION[self::SESSION_MESSAGE]);
	}
	
	
	
	
}