<?php
namespace module\gossip\model;

class Gossip {

   	public $id;
	public $content;
	public $userid;
	public $user; // carefull, the fiel is not the same as in the DB : Manager must get directly the username
	public $timestamp;
	public $censure;
	
	public $liker;
	public $disliker;
	
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
    	return array_key_exists($uid, $this->liker);
    }
    
    
     /**
     * get true if the user is a liker
     * @param int $uid : the identifier of the user
     * @return boolean $b : true if the $uid is in the LikeList of the Gossip Object, false otherwise
     */
    public function isDisliker($uid){
    	return array_key_exists($uid, $this->disliker);
    }
    
	
	public function setId($value){
		 $this->id = $value;
	}
 	
 	public function setContent($value){
		 $this->content = $value;
	}
	
	public function setUser($value){
		 $this->user = $value;
	}
	
	public function setUserid($value){
		 $this->userid = $value;
	}
	
	public function setTimestamp($value){
		 $this->timestamp = $value;
	}
	
	public function setCensure($value){
		 $this->censure = $value;
	}
	
	public function setLiker($value){
		 $this->liker = $value;
	}
	
	public function setDisliker($value){
		 $this->disliker = $value;
	}
	
	public function getId(){
		return $this->id;
	}
 	
 	public function getContent(){
		return $this->content;
	}
	
	public function getUser(){
		return $this->user;
	}
	
	public function getUserid(){
		return $this->userid;
	}
	
	public function getTimestamp(){
		return $this->timestamp;
	}
	
	public function getCensure(){
		return $this->censure;
	}
	
	public function getLiker(){
		return $this->liker;
	}
	
	public function getDisliker(){
		return $this->disliker;
	}
	
}
?>