<?php


class Censure{
	
	private $_id;
	private $_pictureid;
	private $_comment;
	private $_email;
	private $_date;
	
	public function __construct(array $donnees){
		$this->hydrate($donnees);
	}
	
	
	public function hydrate(array $donnees){
		foreach ($donnees as $key => $value){
			$method = 'set'.ucfirst($key);
			if (method_exists($this, $method)){
				$this->$method($value);
			}
		}
	}


	public function setId( $_id ){
		$this->_id = $_id;
	}
	
	public function setPictureid( $_pictureid ){
		$this->_pictureid = $_pictureid;
	}
	
	public function setComment( $_comment ){
		$this->_comment = $_comment;
	}
	
	public function setEmail( $_email ){
		$this->_email = $_email;
	}
	
	public function setDate( $_date ){
		$this->_date = $_date;
	}
	
	public function getId(){
		return $this->_id;
	}
	
	public function getPictureid(){
		return $this->_pictureid;
	}
	
	public function getComment(){
		return $this->_comment;
	}
	
	public function getEmail(){
		return $this->_email;
	}
	
	public function getDate(){
		return $this->_date;
	}
	
}