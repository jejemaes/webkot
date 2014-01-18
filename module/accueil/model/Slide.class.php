<?php


class Slide{
	
	private $_id;
	private $_title;
	private $_description;
	private $_pathimg;
	private $_isactive;
	
	
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
	



	public function setId( $_id )
	{
		$this->_id = $_id;
	}
	
	public function setTitle( $_title )
	{
		$this->_title = $_title;
	}
	
	public function setDescription( $_description )
	{
		$this->_description = $_description;
	}
	
	public function setPathimg( $_pathimg )
	{
		$this->_pathimg = $_pathimg;
	}
	
	public function setIsactive( $_isactive )
	{
		$this->_isactive = $_isactive;
	}
	
	public function getId()
	{
		return $this->_id;
	}
	
	public function getTitle()
	{
		return $this->_title;
	}
	
	public function getDescription()
	{
		return $this->_description;
	}
	
	public function getPathimg()
	{
		return $this->_pathimg;
	}
	
	public function getIsactive()
	{
		return $this->_isactive;
	}
}