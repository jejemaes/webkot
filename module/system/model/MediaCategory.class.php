<?php


class MediaCategory{
	
	private $_id;
	private $_name;
	private $_description;
	private $_directory;
	
	private $medias;
	
	/**
	 * Constructor
	 */
	public function __construct(array $data = array()){
		$this->hydrate($data);
		$this->medias = array();
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
	
	public function setName( $_name ){
		$this->_name = $_name;
	}
	
	public function setDescription( $_description ){
		$this->_description = $_description;
	}
	
	public function setDirectory( $_directory ){
		$this->_directory = $_directory;
	}
	
	public function setMedias( $value ){
		$this->medias = $value;
	}
	
	public function getId(){
		return $this->_id;
	}
	
	public function getName(){
		return $this->_name;
	}
	
	public function getDescription(){
		return $this->_description;
	}
	
	public function getDirectory(){
		return $this->_directory;
	}
	
	public function getMedias(){
		return $this->medias;
	}
	
}