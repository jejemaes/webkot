<?php


class Session {
	
	private $_userprofile;
	private $_capabilities;
	private $_check;
	private $_start_date;
	private $_role;
	
	
	public function __construct($userprofile, array $capabilities, $check, $role){
		$this->_userprofile = $userprofile;
		$this->_capabilities = $capabilities;
		$this->_check = $check;
		$this->_start_date = time();
		$this->_role = $role;
	}


	public function setUserprofile( $_userprofile )
	{
		$this->_userprofile = $_userprofile;
	}
	
	public function setCapabilities( $_capabilities )
	{
		$this->_capabilities = $_capabilities;
	}
	
	public function setCheck( $_check )
	{
		$this->_check = $_check;
	}
	
	public function setStartDate( $_start_date )
	{
		$this->_start_date = $_start_date;
	}
	
	public function setRole( $_role )
	{
		$this->_role = $_role;
	}
	
	public function getUserprofile()
	{
		return $this->_userprofile;
	}
	
	public function getCapabilities()
	{
		return $this->_capabilities;
	}
	
	public function getCheck()
	{
		return $this->_check;
	}
	
	public function getStartDate()
	{
		return $this->_start_date;
	}
	
	public function getRole()
	{
		return $this->_role;
	}
	
}