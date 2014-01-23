<?php
/**
 * 
 *
 * @author jeromemaes
 * 22 janv. 2014
 */

class Widget {
	private $_id;
	private $_name;
	private $_isactive;
	private $_infooter;
	private $_module_name;
	private $_module_location;
	private $_classname;

	private $_place;
	
	/**
	 * Constructor
	 * 
	 * @param int $id        	
	 * @param String $name        	
	 * @param boolean $allpage        	
	 * @param boolean $isactive        	
	 * @param String $modulename        	
	 * @param String $modulelocation        	
	 */

	public function __construct(array $data){
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
	
	
	
	public function setId($_id) {
		$this->_id = $_id;
	}
	public function setName($_name) {
		$this->_name = $_name;
	}

	public function setIsActive($_isactive) {
		$this->_isactive = $_isactive;
	}
	public function setModuleName($_module_name) {
		$this->_module_name = $_module_name;
	}
	public function setModuleLocation($_module_location) {
		$this->_module_location = $_module_location;
	}
	public function setInfooter( $_infooter ){
		$this->_infooter = $_infooter;
	}
	public function setPlace( $_place ){
		$this->_place = $_place;
	}
	public function setClassname($_classname){
		$this->_classname = $_classname;
	}
	

	
	public function getId() {
		return $this->_id;
	}
	public function getName() {
		return $this->_name;
	}
	public function getIsActive() {
		return $this->_isactive;
	}
	public function getModuleName() {
		return $this->_module_name;
	}
	public function getModuleLocation() {
		return $this->_module_location;
	}
	public function getInfooter(){
		return $this->_infooter;
	}
	public function getPlace(){
		return $this->_place;
	}
	public function getClassname(){
		return $this->_classname;
	}
}