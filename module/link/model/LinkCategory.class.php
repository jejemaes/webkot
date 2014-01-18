<?php

class LinkCategory {

  	private $_description;
	private $_place;
	private $_name;
	
	/**
	 * constructor
	 * @param array $data : containing the data (attributes) from the DB
	 */
	public function __construct(array $data = array()){
		$this->hydrate($data);
    }
    
    
    /**
	 * Fonction Hydrate : fill the attribute of the object from an array containing the values
	 * @param array $data : contains all the attribute (the values)
	 */
    public function hydrate(array $data){       
    	foreach ($data as $key => $value){
    		$method = 'set'.ucfirst($key);            
        	if (method_exists($this, $method)){
            	$this->$method($value);
            }
        }
	}
		 
	
	public function setDescription( $_description ){
		$this->_description = $_description;
	}
	
	public function setPlace( $_place ){
		$this->_place = $_place;
	}
	
	public function setName( $_name ){
		$this->_name = $_name;
	}
	
	public function getDescription(){
	 	return $this->_description;
	}
	
	public function getPlace(){
	 	return $this->_place;
	}
	
	public function getName(){
	 	return $this->_name;
	}
}
?>