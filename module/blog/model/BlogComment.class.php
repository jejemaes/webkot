<?php

class BlogComment {

   private $_id;
   private $_postid;
   private $_user;
   private $_comment;
   private $_date; 
   
     
	/**
	 * Constructor
	 */
	public function __construct(array $donnees){
		$this->hydrate($donnees);
    }
    
  
    
    /**
	 * Fill the attribute with the data from the DB
	 * @param array $data : the data
	 */
	public function hydrate(array $data){       
    	foreach ($data as $key => $value){
    		$method = 'set'.ucfirst($key);             
        	if (method_exists($this, $method)){
            	$this->$method($value);
            }
        }
	}
	   
   
	    
	
	public function setId( $_id ){
		$this->_id = $_id;
	}
	
	public function setPostid( $_postid ){
		$this->_postid = $_postid;
	}
	
	public function setUser( $_user ){
		$this->_user = $_user;
	}
	
	public function setComment( $_comment ){
		$this->_comment = $_comment;
	}
	
	public function setDate( $_date ){
		$this->_date = $_date;
	}
	
	public function getId(){
	 	return $this->_id;
	}
	
	public function getPostid(){
	 	return $this->_postid;
	}
	
	public function getUser(){
	 	return $this->_user;
	}
	
	public function getComment(){
	 	return $this->_comment;
	}
	
	public function getDate(){
	 	return $this->_date;
	}
	


}
?>