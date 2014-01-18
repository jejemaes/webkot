<?php
/*
 * Created on 13 aug. 2013
 *
 * MAES Jerome, Webkot 2012-2013
 * Class description : representing a picture
 *
 * Convention : setters & getters begin with a capital letter (important for hydratate)
 * 				same attribute names as in the DB
 */
 
 abstract class AbstractPicture{
 	
 	private $_id;
	private $_idactivity;
	private $_filename;
	private $_time;
	private $_iscensured;
	private $_viewed;
	private $_isvideo;
	private $_level;
	
	// indique si la photo a ��t� modifie (changement de date, de nom, ...)
	private $_ismodified = false;

	
	/**
	 * Constructor
	 */
	public function __construct(array $data = array()){
		$this->hydrate($data);
    }
	
	/**
	 * Fonction Hydrate : fill the attribute of the object from an array containing the values
	 * @param : $donnees contains all the attribute (the values)
	 */
	public function hydrate(array $donnees){
		//var_dump($donnees);        
    	foreach ($donnees as $key => $value){
    		$method = 'set'.ucfirst($key);            
        	if (method_exists($this, $method)){
            	$this->$method($value);
            }
        }
	}
	
	
	/**
	 * Create the string of the object
	 * @return : string describing the object
	 */
	public function __toString(){
		return "<br>ID : " . $this->getId() . "<br>Filename : " . $this->getFilename() . "<br>Id Activity : " . $this->getIdactivity() . "<br>Time : " + $this->getTime();
	}
	
	
	// SETTERS & GETTERS
	public function setId($id){
		$id = (int) $id;
     	if ($id > 0){
          $this->_id = $id;
          $this->_ismodified = true;
		}
 	}
 	
 	public function setIdactivity($idact){
		$idact = (int) $idact;
     	if ($idact > 0){
          $this->_idactivity = $idact;
          $this->_ismodified = true;
		}
 	}
 	
 	public function setFilename($filename){
     	if (is_string($filename)){
          $this->_filename = $filename;
          $this->_ismodified = true;
		}
 	}
 	
 	public function setTime($time){
        $this->_time = $time;
        $this->_ismodified = true;
 	}
 	
 	public function setIscensured($value){
		$this->_iscensured = $value;
		$this->_ismodified = true;
	}
	
	public function setViewed($value){
		$value = (int) $value;
     	if ($value > 0){
          $this->_viewed = $value;
          $this->_ismodified = true;
		}
 	}
 	
 	public function setIsvideo($value){
		$this->_isvideo = $value;
		$this->_ismodified = true;
	}
	public function setLevel($value){
		$this->_level = $value;
	}
	
	
	public function getId(){
		return $this->_id;
	}
	
	public function getIdactivity(){
		return $this->_idactivity;
	}
	
	public function getFilename(){
		return $this->_filename;
	}
	
	public function getTime(){
		return $this->_time;
	}
	
	public function getIscensured(){
		return $this->_iscensured;
	}
	
	public function getViewed(){
		return $this->_viewed;
	}
	
	public function getIsvideo(){
		return $this->_isvideo;
	}
	
	public function getLevel(){
		return $this->_level;
	}

 	
 }
 
 
?>
