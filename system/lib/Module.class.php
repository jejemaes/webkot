<?php

/*
 * Created on 9 Aug. 2013
 *
 * MAES Jerome, Webkot 2012-2013
 * Class description : manage the Module Object (bridge with the DB). Only for the frontend
 *
 * Convention : the setters & getters are lowercase, but their first letter is a capital letter
 * 				
 */
 
class Module {
	
	private $_id;
	private $_name;
	private $_displayed_name;
	private $_location;
	private $_isActive;
	private $_inMenu;
	private $_loader;
	private $_place;
	
	private $_config;
	
	/**
	 * Constructor : fill the fields of the objects, and initialize the param config
	 * @param array $data : the data from the file, in a key-array
	 */
	public function __construct(array $data = array()){
		$this->hydrate($data);
		
		//$content = file_get_contents(DIR_MODULE . $this->getLocation() . "config.json");
		//$this->_config = json_decode($content, true);
		//var_dump($this->_config);
	}
	
	
	
	/**
	 * Hydrate : fill the field with the array
	 * @param array $data : the data from the file, in a key-array
	 */
	public function hydrate(array $donnees){
		foreach ($donnees as $key => $value){
			$method = 'set'.ucfirst($key);
			if (method_exists($this, $method)){
				$this->$method($value);
			}
		}
	}
	
	/**
	 * get the capabilities per role of the current Module
	 * @return array $cap : a key-array where the keys are the role names, and the values are the capabilites
	 */
	public function getCapabilities(){
		return $this->_config['capabilities'];
	}
	
	/**
	 * TODO : check si y a une key pour tout les roles
	 * @param array $capabilities
	 */
	public function setCapabilities(array $capabilities){
		if($capabilities != null){
			$this->_config['capabilities'] = $capabilities;
		}else{
			$this->_config['capabilities'] = array();
		}
	}
	
	/**
	 * get the capabilities available for this module
	 * @return array $cap : an array containing the capabilites
	 */
	public function getAvailableCapabilities(){
		return $this->_config['parameters']['capabilities'];
	}
	
	/**
	 * get the admin Url of the current Module
	 * @return array $au : a key-array where the keys are the name, and the values are the GET parameter values
	 */
	public function getAdminUrl(){
		$params = $this->getParameters();
		return $params['admin-url'];
	}
	
	/**
	 * get the parameters of the current Module
	 * @return array $param : a key-array where the keys are the param names
	 */
	public function getParameters(){
		return $this->_config["parameters"];
	}
	
	public function getLayout($state){
		$t = $this->getParameters();
		$t = $t["layout"];
		if($t[$state]){
			return $t[$state];
		}
		return "layout2col";
	}
	
	
	
	public function setId($value){
		$this->_id = $value;
	}
	public function setName($value){
		$this->_name = $value;
	}
	public function setDisplayedName($value){
		$this->_displayed_name = $value;
	}
	public function setLocation($value){
		$this->_location = $value;
	}
	public function setIsActive($value){
		$this->_isActive = $value;
	}
	public function setInMenu($value){
		$this->_inMenu = $value;
	}
	public function setLoader( $value ){
		$this->_loader = $value;
	}
	public function setConfig( $_config ){
		$this->_config = json_decode($_config,true);
	}
	
	public function getId(){
		return $this->_id ;
	}
	public function getName(){
		return $this->_name ;
	}
	public function getDisplayedName(){
		return $this->_displayed_name ;
	}
	public function getLocation(){
		return $this->_location ;
	}
	public function getIsActive(){
		return $this->_isActive ;
	}
	public function getInMenu(){
		return $this->_inMenu ;
	}
	public function getLoader(){
		return $this->_loader ;
	}
	public function getConfig(){
		return $this->_config;
	}

	public function setPlace( $_place ){
		$this->_place = $_place;
	}
	
	public function getPlace(){
		return $this->_place;
	}
	
}