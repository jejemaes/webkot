<?php
/**
 * Created on 12 avr. 2012
 *
 * MAES Jerome, Webkot 2011-2012
 */
namespace module\link\model;


class LinkCategory {

  	public $description;
	public $place;
	public $id;
	
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
		 
	
	public function setDescription( $description ){
		$this->description = $description;
	}
	
	public function setPlace( $place ){
		$this->place = $place;
	}
	
	public function setId( $id ){
		$this->id = $id;
	}
	
	public function getDescription(){
	 	return $this->description;
	}
	
	public function getPlace(){
	 	return $this->place;
	}
	
	public function getName(){
	 	return $this->name;
	}
}
?>