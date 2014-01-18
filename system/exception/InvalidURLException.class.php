<?php

class InvalidURLException extends Exception{
	
	
	private $_description;
	private $_url;
	
	/**
	 * Constructor
	 */
	function __construct($descri){ 
		parent::__construct(); 
		
		$this->_description = $descri;
		$this->_url = $this->generateURL();
	} 
	
	
	
	public function toJSON(){
		return "{message : {type : 3 ; content : '".addslashes($this->getDescription())."'}}";
	}
	
	/**
	 * toString
	 * @return string
	 */
	public function __toString(){
		$html .= '<strong>Invalid URL</strong><br>L\'URL introduite est mauvaise :' . $this->_url;
		$html .= '<br>DATE : ' . date('Y-m-d H:i');
		$html .= '<br>MESSAGE : ' . $this->getMessage();
		$html .= '<br>DESCRIPTION : ' . $this->getDescription();
		if(system_session_privilege() >= 5){
			$html .= '<br>CODE : ' . $this->getCode();
			$html .= '<br>FILE : ' . $this->getFile();
			$html .= '<br>LINE : ' . $this->getLine();
			$html .= '<br>URL : ' . $this->getUrl();
			$html .= '<br>TRACE : ' . $this->getTraceAsString();
		}
		return $html;
	}
	
	
	public function getDescription(){
		return $this->_description;
	}
	
	public function getUrl(){
		return $this->_url;
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
?>