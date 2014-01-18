<?php


class Video{
	
	private $_id;
	private $_title;
	private $_description;
	private $_duration;
	private $_view;
	private $_published_date;
	private $_thumbnail;
	
   /**
	* Constructor
	* @param array $data : containing the data (attributes) from the DB
	*/
	public function __construct(array $data = array()){
		$this->hydrate($data);
	}
	
	
	/**
	 * Fonction Hydrate : fill the attribute of the object from an array containing the values
	 * @param array $data : contains all the attribute (the values)
	 */
	public function hydrate(array $data){
		foreach ($data as $key => $value){
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
	
	public function setDuration( $_duration )
	{
		$this->_duration = $_duration;
	}
	
	public function setView( $_view )
	{
		$this->_view = $_view;
	}
	
	public function setPublishedDate( $_published_date )
	{
		$this->_published_date = $_published_date;
	}
	
	public function setThumbnail( $_thumbnail )
	{
		$this->_thumbnail = $_thumbnail;
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
	
	public function getDuration()
	{
		return $this->_duration;
	}
	
	public function getView()
	{
		return $this->_view;
	}
	
	public function getPublishedDate()
	{
		return $this->_published_date;
	}
	
	public function getThumbnail()
	{
		return $this->_thumbnail;
	}
	
}