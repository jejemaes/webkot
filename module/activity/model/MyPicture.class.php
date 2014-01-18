<?php
/*
 * Created on 14 mai 2012
 *
 * MAES Jerome, Webkot 2011-2012
 * Class description : representing the link between a user and a picture (Mes Photos)
 *
 * Convention : setters & getters begin with a capital letter (important for hydratate)
 * 				same attribute names as in the DB
 */
 

 
 class MyPicture extends Picture{
 	
 	private $_userid;
	private $_pictureid;
	
	private $_addeddate;	
	private $_directory;
	
	private $_title;
	private $_date;

	
	
	/**
	 * Constructor
	 */
	public function __construct(array $donnees){
		$this->hydrate($donnees);
    }
	
	/**
	 * Fonction Hydrate : fill the attribute of the object from an array containing the values
	 * @param : $donnees contains all the attribute (the values)
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
	 * Create the string of the object
	 * @return : string describing the object
	 */
	public function __toString(){
		return sprintf("<br>ID : %s <br> idActi : %s <br>Filename : %s <br>Censured : %s <br>", $this->getId(), $this->getIdactivity(), $this->getFilename(), $this->getTime());
	}
	
	
	// SETTERS & GETTERS
	public function setUserid($id){
		$id = (int) $id;
     	if ($id > 0){
          $this->_userid = $id;
		}
 	}
 	
 	public function setPictureid($id){
		$id = (int) $id;
     	if ($id > 0){
          $this->_pictureid = $id;
		}
 	}
 	
 	 

	public function setAddeddate( $_addeddate ){
		$this->_addeddate = $_addeddate;
	}
	
	public function setDirectory( $_directory ){
		$this->_directory = $_directory;
	}
	
	public function getAddeddate(){
	 	return $this->_addeddate;
	}
	
	public function getDirectory(){
	 	return $this->_directory;
	}
 	
 	
	
	public function getUserid(){
		return $this->_userid;
	}
	
	public function getPictureid(){
		return $this->_pictureid;
	}
	
	
	 
	
	public function setTitle( $_title ){
		$this->_title = $_title;
	}
	
	public function getTitle(){
	 	return $this->_title;
	}
	
	 

	public function setDate( $_date ){
		$this->_date = $_date;
	}
	
	public function getDate(){
	 	return $this->_date;
	}
	
 	
 }
 
 
?>
