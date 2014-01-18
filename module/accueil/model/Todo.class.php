<?php

class Todo {

    private $_id;
	private $_title;
	private $_description;
	private $_creation_date;
	private $_accomplishment_date;
	private $_author;
	private $_executor;
	
	
	/**
	 * Constructor
	 */
	public function __construct(array $donnees = array()){
		$this->hydrate($donnees);
    }
    
    /**
	 * Fonction Hydrate : fill the attributes of the object with the array
	 * @param : $donnees array contening the data (generally from the DB)
	 */
    public function hydrate(array $donnees){       
    	foreach ($donnees as $key => $value){
    		$method = 'set'.ucfirst($key);             
        	if (method_exists($this, $method)){
            	$this->$method($value);
            }
        }
	}
	
	
	public function getId(){
		return $this->_id;
	}
 	
 	public function getTitle(){
		return $this->_title;
	}
	
	public function getDescription(){
		return stripslashes($this->_description);
	}
	
	public function getCreation_date(){
		return $this->_creation_date;
	}
	
	public function getAccomplishment_date(){
		return $this->_accomplishment_date;
	}
	
	public function getAuthor(){
		return $this->_author;
	}
	
	public function getExecutor(){
		return $this->_executor;
	}
	
	
	
	
	public function setId($value){
		$this->_id = $value;
	}
 	
 	public function setTitle($value){
		$this->_title = $value;
	}
	
	public function setDescription($value){
		$this->_description = $value;
	}
	
	public function setCreation_date($value){
		$this->_creation_date = $value;
	}
	
	public function setAccomplishment_date($value){
		$this->_accomplishment_date = $value;
	}
	
	public function setAuthor($value){
		$this->_author = $value;
	}
	
	public function setExecutor($value){
		$this->_executor = $value;
	}
	
}
?>