<?php


class Media{
	
	private $_id;
	private $_name;
	private $_filename;
	private $_addeddate;
	private $_category;
	
	
	
	/**
	 * Constructor
	 */
	public function __construct(array $data = array()){
		$this->hydrate($data);
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
	
	
	public function getExtension(){
		$splits = preg_split("/[.]+/", $this->getFilename());
		$ext = $splits[count($splits)-1];
		return $ext;
	}
	

	
	
	// SETTERS & GETTERS
	
	public function setId( $_id ){
		$this->_id = $_id;
	}
	
	public function setName( $_name ){
		$this->_name = $_name;
	}
	
	public function setFilename( $_filename ){
		$this->_filename = $_filename;
	}
	
	public function setAddeddate( $_addeddate ){
		$this->_addeddate = $_addeddate;
	}
	
	public function setCategory( $_category ){
		$this->_category = $_category;
	}
	
	public function getId(){
		return $this->_id;
	}
	
	public function getName(){
		return $this->_name;
	}
	
	public function getFilename(){
		return $this->_filename;
	}
	
	public function getAddeddate(){
		return $this->_addeddate;
	}
	
	public function getCategory(){
		return $this->_category;
	}
	
}