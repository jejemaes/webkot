<?php


class StatActivity{
	
	private $year;
	private $activities;
	private $pictures;
	private $comments;
	
	
	
	/**
	 * Constructor
	 * @param array $data : the data from the file, in a key-array
	 */
	public function __construct(array $data = array()){
		$this->hydrate($data);
	}
	
	
	/**
	 * Hydrate : fill the field with the array
	 * @param array $data : the data from the file, in a key-array
	 */
	public function hydrate(array $data){
		foreach ($data as $key => $value){
			$method = 'set'.ucfirst($key);
			if (method_exists($this, $method)){
				$this->$method($value);
			}
		}
	}
	


	public function setYear( $year ){
		$this->year = $year;
	}
	
	public function setActivities( $activities ){
		$this->activities = $activities;
	}
	
	public function setPictures( $pictures ){
		$this->pictures = $pictures;
	}
	
	public function setComments( $comments ){
		$this->comments = $comments;
	}
	
	public function getYear(){
		return $this->year;
	}
	
	public function getActivities(){
		return $this->activities;
	}
	
	public function getPictures(){
		return $this->pictures;
	}
	
	public function getComments(){
		return $this->comments;
	}
		
	
}