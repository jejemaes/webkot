<?php

class Webkotteur {

   	private $_id;
	private $_name;
	private $_firstname;
	private $_nickname;
	private $_age;
	private $_function;
	private $_img;
	private $_mail;
	private $_studies;
	private $_userid;
	private $_valuetolike;
	
	// only for membership profil
	private $_place;
	
	// used only for the old team
	private $_year; 
	
	/**
	 * Constructor 
	 * @param array $data : containing the values from the DB
	 */
	public function __construct(array $data = array()){
		$this->hydrate($data);
    }
    
  
    
    /**
	 * Function Hydrate : fill the attribute with the value extracted from the DB
	 * @param : $donnees contient des champs de mmes nom que les attributs (et donc que la BD)
	 */
	public function hydrate(array $donnees){       
    	foreach ($donnees as $key => $value){
    		$method = 'set'.ucfirst($key);             
        	if (method_exists($this, $method)){
            	$this->$method($value);
            }
        }
	}
	
	public function __toString(){
		return $this->getFirstname() . "  " . $this->getFunction();
	}
	
	public function getId(){
		return $this->_id;
	}
 	
 	public function getName(){
		return $this->_name;
	}
	
	public function getFirstname(){
		return $this->_firstname;
	}
	
	public function getNickname(){
		return $this->_nickname;
	}
	
	public function getAge(){
		return $this->_age;
	}
	
	public function getFunction(){
		return $this->_function;
	}
	
	public function getImg(){
		return $this->_img;
	}
	
	public function getMail(){
		return $this->_mail;
	}
	
	public function getYear(){
		return $this->_year;
	}

	public function getStudies(){
		return $this->_studies;
	}
	
	public function getUserid(){
		return $this->_userid;
	}
	
	public function getValuetolike(){
		return $this->_valuetolike;
	}
	
	public function getPlace(){
		return $this->_place;
	}
	
	public function setId($value){
		 $this->_id = $value;
	}
 	
 	public function setName($value){
		 $this->_name = $value;
	}
	
	public function setFirstname($value){
		 $this->_firstname = $value;
	}
	
	public function setNickname($value){
		 $this->_nickname = $value;
	}
	
	public function setAge($value){
		 $this->_age = $value;
	}
	
	public function setFunction($value){
		 $this->_function = $value;
	}
	
	public function setImg($value){
		 $this->_img = $value;
	}
	
	public function setMail($value){
		 $this->_mail = $value;
	}

	public function setStudies($value){
		 $this->_studies = $value;
	}
	
	public function setYear($value){
		 $this->_year = $value;
	}
	
	public function setUserid($value){
		 $this->_userid = $value;
	}
	
	public function setValuetolike($value){
		 $this->_valuetolike = $value;
	}
	
	public function setPlace($value){
		 $this->_place = $value;
	}
	
}
?>