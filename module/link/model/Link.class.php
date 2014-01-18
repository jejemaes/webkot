<?php
/*
 * Created on 12 avr. 2012
 *
 * MAES Jerome, Webkot 2011-2012
 * Class description : representing the link
 *
 * Convention : setters & getters begin with a capital letter (important for hydratate)
 * 				same attribute names as in the DB
 */
 
class Link {

	private $_id;
	private $_category;
	private $_url;
	private $_name;
	
	/**
	 * constructor
	 * @param array containing the data (attributes) from the DB
	 */
	public function __construct(array $donnees = array()){
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

	public function __toString(){
	return sprintf("<br>ID : %s  %s %s <br>", $this->getCategory(), $this->getUrl(), $this->getName());
		
	}	
	
	
	
	
	// SETTERS & GETTERS
	
	 
	
	public function setId( $_id ){
		$this->_id = $_id;
	}
	
	public function getId(){
		return $this->_id;
	}

	public function setCategory($value){
		$this->_category = $value;
	}
	
	public function setUrl($value){
		$this->_url = $value;
	}
	
	public function setName($value){
		$this->_name = $value;
	}
	
	public function getCategory(){
		return $this->_category;
	}
	
	public function getUrl(){
		return $this->_url;
	}
	
	public function getName(){
		return $this->_name;
	}
    

}
?>