<?php

/*
 * Created on 12 avr. 2012
 *
 * MAES Jerome, Webkot 2011-2012
 * Class description : representing the link
 *
 * Convention : setters & getters begin with a capital letter (important for hydratate)
 * 				same attribute names as in the DB
 */
 
class User {
	
	private $_id;
	private $_username;
	private $_password;
	private $_mail;
	private $_name;
	private $_firstname;
	private $_school;
	private $_section;
	private $_address;
	private $_isAdmin;
	private $_isWebkot;
	private $_mailwatch; // ask for an email when new activity
	private $_lastLogin;
	private $_subscription;
	private $_viewdet;// public profil or not
	private $_facebookid;
	
	private $_role;
	private $_level;
	
	private $_nbrcomment;
	
	// indicate if modification
	private $_ismodified = false;
	
	
	
 	/**
 	 * Constructor
 	 */
	public function __construct(array $donnees = array()){
		$this->hydrate($donnees);
    }
    
    
    /**
	 * Fonction Hydrate : fill the attribute of the object from an array containing the values
	 * @param : $donnees contains all the attribute (the values)
	 */
	public function hydrate(array $donnees){
		//var_dump($donnees);        
    	foreach ($donnees as $key => $value){
    		$method = 'set'.ucfirst($key);  
			//var_dump($key);           
        	if (method_exists($this, $method)){
            	$this->$method($value);
            }
        }
	}
	
	/**
	 * Create the string of the object
	 * @return : string describing the object
	 */
	public function __toString(){
		return sprintf("<br>ID : %s <br> Username : %s <br>isAdmin : %s <br>Mail : %s <br>", $this->getId(), $this->getUsername(), $this->getIsAdmin(), $this->getMail());
	}
	
	
	
	// SETTERS & GETTERS
	public function setId($id){
		$id = (int) $id;
     	if ($id > 0){
          $this->_id = $id;
          $this->_ismodified = true;
		}
 	}
 	
 	public function setUsername($value){
		if (is_string($value)){
          $this->_username = $value;
          $this->_ismodified = true;
		}
 	}
 	
 	public function setPassword($value){
     	if (is_string($value)){
          $this->_password = $value;
          $this->_ismodified = true;
		}
 	}
 	
 	public function setName($value){
		if (is_string($value)){
          $this->_name = $value;
          $this->_ismodified = true;
		}
 	}
 	
 	public function setMail($value){
     	if (is_string($value)){
          $this->_mail = $value;
          $this->_ismodified = true;
		}
 	}
 	
 	public function setFirstname($value){
          $this->_firstname = $value;
 	}
 	
 	public function setSchool($value){
     	if (is_string($value)){
          $this->_school = $value;
          $this->_ismodified = true;
		}
 	}
 	
 	public function setSection($value){
     	if (is_string($value)){
          $this->_section = $value;
          $this->_ismodified = true;
		}
 	}
 	
 	public function setAddress($value){
     	if (is_string($value)){
          $this->_address = $value;
          $this->_ismodified = true;
		}
 	}
 	
 	public function setIsAdmin($value){
 		$value = (int) $value;
        $this->_isAdmin = $value;
        $this->_ismodified = true;
 	}
 	
 	public function setIswebkot($value){
 		$value = (int) $value;
        $this->_isWebkot = $value;
        $this->_ismodified = true;
     }
 	
 	public function setMailwatch($value){
 		$value = (int) $value;
        $this->_mailwatch = $value;
        $this->_ismodified = true;
 	}
 	
 	public function setLastLogin($Value){
		$this->_lastLogin = $Value;
		$this->_ismodified = true;
	}
 	
 	public function setSubscription($Value){
		$this->_subscription = $Value;
		$this->_ismodified = true;
	}
	
	public function setViewdet($value){
		$value = (int) $value;
     	$this->_viewdet = $value;
		$this->_ismodified = true;
 	}
 	
 	public function setNbrcomment($value){
 		$this->_nbrcomment = $value;
 	}
	
	
	
	public function getId(){
		return $this->_id;
	}
	
	public function getUsername(){
		return $this->_username;
	}
	
	public function getPassword(){
		return $this->_password;
	}
	
	public function getMail(){
		return $this->_mail;
	}
	
	public function getName(){
		return $this->_name;
	}
	
	public function getFirstname(){
		return $this->_firstname;
	}
	
	public function getSchool(){
		return $this->_school;
	}
	
	public function getSection(){
		return $this->_section;
	}
	
	public function getAddress(){
		return $this->_address;
	}
	
	public function getIsAdmin(){
		return $this->_isAdmin;
	}
	
	public function getIsWebkot(){
		return $this->_isWebkot;
	}
	
	public function getMailwatch(){
		return $this->_mailwatch;
	}
	
	public function getLastLogin(){
		return $this->_lastLogin;
	}
	
	public function getSubscription(){
		return $this->_subscription;
	}
	
	public function getViewdet(){
		return $this->_viewdet;
	}
	
	public function getNbrcomment(){
		return $this->_nbrcomment;
	}
	
	
	


	public function setFacebookid( $_facebookid ){
		$this->_facebookid = $_facebookid;
	}
	
	public function getFacebookid(){
		return $this->_facebookid;
	}
	
	


	public function setRole( $_role ){
		$this->_role = $_role;
	}
	
	public function setLevel( $_level ){
		$this->_level = $_level;
	}
	
	public function getRole(){
		return $this->_role;
	}
	
	public function getLevel(){
		return $this->_level;
	}
	
}
?>