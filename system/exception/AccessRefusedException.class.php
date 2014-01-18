<?php


class AccessRefusedException extends Exception{
	
	private $_comment;
	private $_description;
	private $_url;
	
	/**
	 * Constructor
	 */
	function __construct($comment){
		parent::__construct();
	
		$this->_comment = $comment;
		$this->_description = "Vous n'avez pas les autorisations requises pour acc&eacute;der &agrave; la page : ";
		$this->_url = $this->generateURL();
	}
	
	
	public function toJSON(){
		return '{"message" : {"type" : "error" , "content" : "'.($this->getDescription() . $this->getComment()).'"}}';
	}
	
	
	/**
	 * toString
	 * @return string
	 */
	public function __toString(){
		return '<strong>Refused Access</strong><br>' . $this->_description . ' ' . $this->_url . '<br><br>' .$this->_comment;
	}
	
	public function getDescription(){
		return $this->_description;
	}
	
	public function getUrl(){
		return $this->_url;
	}
	
	public function getComment(){
		return $this->_comment;
	}
	
	
	
	
	/**
	 * generate the Absolute URL of the page when the Exception is raised
	 * @@return string $url;
	 */
	private function generateURL(){
		$pageURL = 'http';
		if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
	
		return $pageURL;
	}
	
}