<?php

class ChallengeAnswer {
	
	private $_id;
	private $_userid;
	private $_challengeid;
	private $_answer;
	private $_date;
	private $_iscorrect;
	
	
	/**
	 * Constructor
	 */
	public function __construct(array $data){
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
	
	public function setUserid( $_userid ){
		$this->_userid = $_userid;
	}
	
	public function setChallengeid( $_challengeid ){
		$this->_challengeid = $_challengeid;
	}
	
	public function setAnswer( $_answer ){
		$this->_answer = $_answer;
	}
	
	public function setDate( $_date ){
		$this->_date = $_date;
	}
	
	public function setIscorrect( $_iscorrect ){
		$this->_iscorrect = $_iscorrect;
	}
	
	public function getId(){
		return $this->_id;
	}
	
	public function getUserid(){
		return $this->_userid;
	}
	
	public function getChallengeid(){
		return $this->_challengeid;
	}
	
	public function getAnswer(){
		return $this->_answer;
	}
	
	public function getDate(){
		return $this->_date;
	}
	
	public function getIscorrect(){
		return $this->_iscorrect;
	}
	
}
?>