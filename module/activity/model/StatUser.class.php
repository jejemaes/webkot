<?php


class StatUser{
	
	private $year;
	private $username;
	private $name;
	private $firstname;
	private $stat;
	private $userid;
	
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


	public function setUsername( $username )
	{
		$this->username = $username;
	}
	
	public function setName( $name )
	{
		$this->name = $name;
	}
	
	public function setFirstname( $firstname )
	{
		$this->firstname = $firstname;
	}
	
	public function setStat( $stat )
	{
		$this->stat = $stat;
	}
	public function setYear( $year )
	{
		$this->year = $year;
	}
	
	public function setUserid( $userid )
	{
		$this->userid = $userid;
	}
	
	public function getUsername()
	{
		return $this->username;
	}
	
	public function getName()
	{
		return $this->name;
	}
	
	public function getFirstname()
	{
		return $this->firstname;
	}
	
	public function getStat()
	{
		return $this->stat;
	}
	
	public function getUserid()
	{
		return $this->userid;
	}
	
	public function getYear()
	{
		return $this->year;
	}
}