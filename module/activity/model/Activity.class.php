<?php
/*
 * Created on 13 Aug. 2013
 *
 * MAES Jerome, Webkot 2011-2012
 * Class description : an activity is an event cover by the Webkot. This is the Object.
 *
 * Convention : the setters & getters are in lowercase, except the first letter of the attribute (important for hydratation)
 * 				attributes have the same name as the column in the database
 * Required : 	ActivityPicture, AbstractPicture
 */

class Activity {
	
	private $_id;
	private $_title;
	private $_description;
	private $_date;
	private $_directory;
	private $_level;
	private $_viewed;
	private $_ismodified = false;
	
	private $_ispublished;
	
	// the list of ActivityPicture of the activity : can not be add with the construtor
	private $pictures;
	
	//the number of picture : will be hydrated, but can be null too
	private $nbrpictures;
	
	// the list of Activity Authors (User object) of the activity : can not be add with the construtor
	private $authors;
	
	/**
	 * Constructor
	 * @param array $data : the data from the file, in a key-array
	 */
	public function __construct(array $donnees = array()){
		$this->pictures = array();
		$this->authors = array();
		$this->hydrate($donnees);
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
	
	
	/**
	 * @return 
	 */
	public function __toString(){
		//return sprintf("ID : %s <br> Titre : %s <br>Descr : %s <br>Viewed : %s <br>", $this->getId(),$this->getTitle(),$this->getDescription(),$this->getDate(), $this->getViewed());
		return "ACTIVITY OBJECT";

	}
	
	
    
    // SETTERS & GETTERS
	public function setId($id){
		$id = (int) $id;
     	if ($id > 0){
          $this->_id = $id;
          $this->_ismodified = true;
		}
 	}
 	
 	public function setTitle($value){
     	if (is_string($value)){
          $this->_title = $value;
          $this->_ismodified = true;
		}
 	}
 	
 	public function setDescription($value){
    	$this->_description = $value;
        $this->_ismodified = true;
 	}
 	
 	public function setDate($value){
    	$this->_date = $value;
        $this->_ismodified = true;
 	}
 	
 	public function setDirectory($value){
     	if (is_string($value)){
          $this->_directory = $value;
          $this->_ismodified = true;
		}
 	}
 	
 	public function setLevel($value){
    	$this->_level = $value;
        $this->_ismodified = true;
 	}
 	
 	public function setViewed($value){
		$value = (int) $value;
     	if ($value > 0){
          $this->_viewed = $value;
          $this->_ismodified = true;
		}
 	}
	
	public function setIspublished( $_ispublished ){
		$this->_ispublished = $_ispublished;
	}
	
	
 	
 	public function getId(){
		return $this->_id;
	}
 	
 	public function getTitle(){
		return $this->_title;
	}
	
	public function getDescription(){
		return $this->_description;
	}
	
	public function getDate(){
		return $this->_date;
	}
	
	public function getDirectory(){
		return $this->_directory;
	}
	
	public function getLevel(){
		return $this->_level;
	}
	
	public function getViewed(){
		return $this->_viewed;
	}
	
 	public function getIsmodified(){
		return $this->_ismodified;
	}
	
	public function getIspublished(){
	 	return $this->_ispublished;
	}
 	
	


	public function setPictures( $pictures ){
		$this->pictures = $pictures;
	}
	
	public function getPictures(){
		return $this->pictures;
	}
	
	public function setAuthors( $authors ){
		$this->authors = $authors;
	}
	
	public function getAuthors(){
		return $this->authors;
	}
	


	public function setNbrpictures($nbrpictures){
		$this->nbrpictures = $nbrpictures;
	}
	
	public function getNbrpictures(){
		return $this->nbrpictures;
	}
	
	
	public function getCountPictures(){
		if(!empty($this->getNbrpictures())){
			return $this->getNbrpictures();
		}else{
			return count($this->getPictures());
		}
	}
 		
}
?>