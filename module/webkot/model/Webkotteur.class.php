<?php

namespace module\webkot\model;

use \SQLException as SQLException;
use \PDOException as PDOException;
use \DatabaseException as DatabaseException;


class Webkotteur {

   	public $id;
	public $name;
	public $firstname;
	public $nickname;
	public $age;
	public $function;
	public $img;
	public $mail;
	public $studies;
	public $userid;
	public $valuetolike;
	
	// only for membership profil
	public $place;
	
	// used only for the old team
	public $year; 
	
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
		return $this->id;
	}
 	
 	public function getName(){
		return $this->name;
	}
	
	public function getFirstname(){
		return $this->firstname;
	}
	
	public function getNickname(){
		return $this->nickname;
	}
	
	public function getAge(){
		return $this->age;
	}
	
	public function getFunction(){
		return $this->function;
	}
	
	public function getImg(){
		return $this->img;
	}
	
	public function getMail(){
		return $this->mail;
	}
	
	public function getYear(){
		return $this->year;
	}

	public function getStudies(){
		return $this->studies;
	}
	
	public function getUserid(){
		return $this->userid;
	}
	
	public function getValuetolike(){
		return $this->valuetolike;
	}
	
	public function getPlace(){
		return $this->place;
	}
	
	public function setId($value){
		 $this->id = $value;
	}
 	
 	public function setName($value){
		 $this->name = $value;
	}
	
	public function setFirstname($value){
		 $this->firstname = $value;
	}
	
	public function setNickname($value){
		 $this->nickname = $value;
	}
	
	public function setAge($value){
		 $this->age = $value;
	}
	
	public function setFunction($value){
		 $this->function = $value;
	}
	
	public function setImg($value){
		 $this->img = $value;
	}
	
	public function setMail($value){
		 $this->mail = $value;
	}

	public function setStudies($value){
		 $this->studies = $value;
	}
	
	public function setYear($value){
		 $this->year = $value;
	}
	
	public function setUserid($value){
		 $this->userid = $value;
	}
	
	public function setValuetolike($value){
		 $this->valuetolike = $value;
	}
	
	public function setPlace($value){
		 $this->place = $value;
	}
	
}
?>