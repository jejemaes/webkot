<?php

class BlogPost {

   private $_id;
   private $_author;
   private $_title;
   private $_content;
   private $_date;
   
   private $_nbrcomment;
   private $comments;
   
   
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
	
	public function setAuthor( $_author ){
		$this->_author = $_author;
	}
	
	public function setTitle( $_title ){
		$this->_title = $_title;
	}
	
	public function setContent( $_content ){
		$this->_content = $_content;
	}
	
	public function setDate( $_date ){
		$this->_date = $_date;
	}
	
	public function setNbrcomment( $value ){
		$this->_nbrcomment = $value;
	}
	
	public function getId(){
	 	return $this->_id;
	}
	
	public function getAuthor(){
	 	return $this->_author;
	}
	
	public function getTitle(){
	 	return $this->_title;
	}
	
	public function getContent(){
	 	return $this->_content;
	}
	
	public function getDate(){
	 	return $this->_date;
	}
	
	public function getNbrcomment(){
	 	return $this->_nbrcomment;
	}
	


	public function setComments( $comments )
	{
		$this->comments = $comments;
	}
	
	public function getComments()
	{
		return $this->comments;
	}
    
}
?>