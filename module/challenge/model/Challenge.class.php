<?php

class Challenge {
	
	private $_id;
	private $_description;
	private $_question;
	private $_answer;
	private $_path_picture;
	private $_publication_date;
	private $_end_date;
	private $_winnerid;
	
	
	/**
	 * Constructor
	 */
	public function __construct(array $data = array()){
		$this->hydrate($data);
	}
	
	/**
	 * 
	 * @param array $data : the data from the DB
	 */
	public function hydrate(array $donnees){       
		foreach ($donnees as $key => $value){
			$method = 'set'.ucfirst($key);             
			if (method_exists($this, $method)){
				$this->$method($value);
			}
		}
	}
	
	
	public function setId( $_id ){
		$this->_id = $_id;
	}
	
	public function setDescription( $_description ){
		$this->_description = $_description;
	}
	
	public function setQuestion( $_question ){
		$this->_question = $_question;
	}
	
	public function setPath_picture( $_path_picture ){
		$this->_path_picture = $_path_picture;
	}
	
	public function setPublication_date( $_ation_date ){
		$this->_publication_date = $_ation_date;
	}
	
	public function setEnd_date( $_end_date ){
		$this->_end_date = $_end_date;
	}
	
	public function setWinnerid( $value){
		$this->_winnerid = $value;
	}
	
	public function getId(){
		return $this->_id;
	}
	
	public function getDescription(){
		return $this->_description;
	}
	
	public function getQuestion(){
		return $this->_question;
	}
	
	public function getPath_picture(){
		return $this->_path_picture;
	}
	
	public function getPublication_date(){
		return $this->_publication_date;
	}
	
	public function getEnd_date(){
		return $this->_end_date;
	}
	
	public function getWinnerid(){
		return $this->_winnerid;
	}
	
	
	public function setAnswer( $_answer )
		{
		$this->_answer = $_answer;
		}
	
	public function getAnswer()
		{
		return $this->_answer;
		}
	
}
?>