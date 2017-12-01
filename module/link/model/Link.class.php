<?php
/**
 * Created on 12 avr. 2012
 *
 * MAES Jerome, Webkot 2011-2012
 */
namespace module\link\model;


class Link {

	public $id;
	public $category;
	public $url;
	public $name;
	
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
	
	 
	
	public function setId( $id ){
		$this->id = $id;
	}
	
	public function getId(){
		return $this->id;
	}

	public function setCategory($value){
		$this->category = $value;
	}
	
	public function setUrl($value){
		$this->url = $value;
	}
	
	public function setName($value){
		$this->name = $value;
	}
	
	public function getCategory(){
		return $this->category;
	}
	
	public function getUrl(){
		return $this->url;
	}
	
	public function getName(){
		return $this->name;
	}
    

}
?>