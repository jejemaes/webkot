<?php

class Comment {

    private $_id;
	private $_userid;
	private $_pictureid;
	private $_comment;
	private $_rank;
	private $_ip;
	private $_date;

	private $_ismodified = false;
	
	
	/*
	 * Constructeur prenant en paramtre le resulset de la requete ˆ la BD
	 */
	public function __construct(array $donnees){
		$this->hydrate($donnees);
    }
    
    /*
	 * Fonction Hydrate : remplis les attributs selon le tableau
	 * PRE : $donnees contient des champs de mmes nom que les attributs (et donc que la BD)
	 * POST : les champs de $this sont remplis
	 */
	public function hydrate(array $donnees){        
    	foreach ($donnees as $key => $value){
    		$method = 'set'.ucfirst($key);              
        	if (method_exists($this, $method)){
            	$this->$method($value);
            }
        }
	}
	
	// SETTERS & GETTERS
	public function setId($id){
		$id = (int) $id;
     	if ($id > 0){
          $this->_id = $id;
          $this->_ismodified = true;
		}
 	}
 	
 	public function setUserid($value){	
          $this->_userid = $value;
          $this->_ismodified = true;
 	}
 	
 	public function setPictureid($value){
		$value = (int) $value;
     	if ($value > 0){
          $this->_pictureid = $value;
          $this->_ismodified = true;
		}
 	}
 	
 	public function setComment($value){
    	$this->_comment = $value;
        $this->_ismodified = true;
 	}
 	
 	public function setRank($value){
    	$this->_rank = $value;
        $this->_ismodified = true;
 	}
 	
 	public function setIp($value){
    	$this->_ip = $value;
        $this->_ismodified = true;
 	}
 	
 	public function setDate($value){
    	$this->_date = $value;
        $this->_ismodified = true;
 	}
 	
 	
 	public function getId(){
		return $this->_id;
	}
 	
 	public function getUserid(){
		return $this->_userid;
	}
	
	public function getPictureid(){
		return $this->_pictureid;
	}
	
	public function getComment(){
		return $this->_comment;
	}
	
	public function getRank(){
		return $this->_rank;
	}
	
	public function getIp(){
		return $this->_ip;
	}
	
	public function getDate(){
		return $this->_date;
	}
}
?>