<?php


class EventCategory{
	
	private $id;
	private $name;
	private $description;
	private $color;
	
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
	
	public function setColor( $color )
	{
		$this->color = $color;
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
	
	public function getColor()
	{
		return $this->color;
	}
}