<?php

class Gossip {

   	private $_id;
	private $_content;
	private $_userid;
	private $_user; // carefull, the fiel is not the same as in the DB : Manager must get directly the username
	private $_timestamp;
	private $_censure;
	
	private $_liker;
	private $_disliker;
	
	/**
	 * Constructeur prenant en paramtre le resulset de la requete ˆ la BD
	 */
	public function __construct(array $donnees){
		$this->hydrate($donnees);
    }
    
  
    
    /**
	 * Fonction Hydrate : remplis les attributs selon le tableau
	 * @param : $donnees contient des champs de mmes nom que les attributs (et donc que la BD)
	 * @return les champs de $this sont remplis
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
     * get true if the user is a liker
     * @param int $uid : the identifier of the user
     * @return boolean $b : true if the $uid is in the LikeList of the Gossip Object, false otherwise
     */
    public function isLiker($uid){
    	return array_key_exists($uid, $this->_liker);
    }
    
    
     /**
     * get true if the user is a liker
     * @param int $uid : the identifier of the user
     * @return boolean $b : true if the $uid is in the LikeList of the Gossip Object, false otherwise
     */
    public function isDisliker($uid){
    	return array_key_exists($uid, $this->_disliker);
    }
    
	
	public function setId($value){
		 $this->_id = $value;
	}
 	
 	public function setContent($value){
		 $this->_content = $value;
	}
	
	public function setUser($value){
		 $this->_user = $value;
	}
	
	public function setUserid($value){
		 $this->_userid = $value;
	}
	
	public function setTimestamp($value){
		 $this->_timestamp = $value;
	}
	
	public function setCensure($value){
		 $this->_censure = $value;
	}
	
	public function setLiker($value){
		 $this->_liker = $value;
	}
	
	public function setDisliker($value){
		 $this->_disliker = $value;
	}
	
	public function getId(){
		return $this->_id;
	}
 	
 	public function getContent(){
		return $this->_content;
	}
	
	public function getUser(){
		return $this->_user;
	}
	
	public function getUserid(){
		return $this->_userid;
	}
	
	public function getTimestamp(){
		return $this->_timestamp;
	}
	
	public function getCensure(){
		return $this->_censure;
	}
	
	public function getLiker(){
		return $this->_liker;
	}
	
	public function getDisliker(){
		return $this->_disliker;
	}
	
}
?>