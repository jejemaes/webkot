<?php


class Event{
	
	private $id;
	private $name;
	private $description;
	private $start_time;
	private $location;
	private $facebookid;
	private $isapproved;
	
	private $categoryid;
	private $categoryname;
	private $categorycolor;
	
	
	
	/**
	 * Constructor
	 * @param array $data : the data from the file, in a key-array
	 */
	public function __construct(array $donnees = array()){
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
	
	


	public function setId( $id )
	{
		$this->id = $id;
	}
	
	public function setName( $name )
	{
		$this->name = $name;
	}
	
	public function setDescription( $description )
	{
		$this->description = $description;
	}
	
	public function setStart_time( $start_time )
	{
		$this->start_time = $start_time;
	}
	
	public function setLocation( $location )
	{
		$this->location = $location;
	}
	
	public function setFacebookid( $facebookid )
	{
		$this->facebookid = $facebookid;
	}
	public function setIsapproved( $isapproved ){
		$this->isapproved = $isapproved;
	}
	
	public function getId()
	{
		return $this->id;
	}
	
	public function getName()
	{
		return $this->name;
	}
	
	public function getDescription()
	{
		return $this->description;
	}
	
	public function getStart_time()
	{
		return $this->start_time;
	}
	
	public function getLocation()
	{
		return $this->location;
	}
	
	public function getFacebookid()
	{
		return $this->facebookid;
	}
	public function getIsapproved(){
		return $this->isapproved;
	}
	
	

	
	 
	
	public function setCategoryid( $categoryid )
	{
		$this->categoryid = $categoryid;
	}
	
	public function setCategoryname( $categoryname )
	{
		$this->categoryname = $categoryname;
	}
	
	public function setCategorycolor( $categorycolor )
	{
		$this->categorycolor = $categorycolor;
	}
	
	public function getCategoryid()
	{
	 	return $this->categoryid;
	}
	
	public function getCategoryname()
	{
	 	return $this->categoryname;
	}
	
	public function getCategorycolor()
	{
	 	return $this->categorycolor;
	}
	
}