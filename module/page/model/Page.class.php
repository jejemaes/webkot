<?php

class Page{
	
	private $_id;
	private $_title;
	private $_content;
	private $_isactive;
	private $_file;
	
	
	
	/**
	 * Constructor
	 */
	public function __construct(array $data = array()){
		$this->hydrate($data);
	}
	
	/**
	 *
	 * @param array $data : the data from the DB
	 */
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
	
	public function setTitle( $_title ){
		$this->_title = $_title;
	}
	
	public function setContent( $_content ){
		$this->_content = $_content;
	}
	
	public function setIsactive( $_isactive ){
		$this->_isactive = $_isactive;
	}
	
	public function setFile( $_file ){
		$this->_file = $_file;
	}
	
	public function getId(){
		return $this->_id;
	}
	
	public function getTitle(){
		return $this->_title;
	}
	
	public function getContent(){
		return $this->_content;
	}
	
	public function getIsactive(){
		return $this->_isactive;
	}
	
	public function getFile(){
		return $this->_file;
	}
	
}