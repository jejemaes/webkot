<?php

class Option{
	
	
	private $_key;
	private $_value;
	private $_type;
	private $_description;
	

	
	public function __construct(array $data = array()){
		$this->hydrate($data);
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

	public function setKey( $_key ){
		$this->_key = $_key;
	}
	
	public function setValue( $_value ){
		$this->_value = $_value;
	}
	
	public function setType( $_type ){
		$this->_type = $_type;
	}
	
	public function setDescription( $_descri ){
		$this->_description = $_descri;
	}
	
	public function getKey(){
		return $this->_key;
	}
	
	public function getValue(){
		return $this->_value;
	}
	
	public function getType(){
		return $this->_type;
	}
	
	public function getDescription(){
		return $this->_description;
	}
}