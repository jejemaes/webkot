<?php


interface iWidget{
	
	
	
	//public function __construct($id, $name, $allpage, $isactive, $modulename, $modulelocation);
	public function __construct(array $data);
	
	
	public function __toString();
	
	
	public function setId( $_id );
	
	public function setName( $_name );
	
	public function setIsactive( $_isactive );
	
	public function setModuleName( $_module_name );
	
	public function setModuleLocation( $_module_location );
	
	public function getId();
	
	public function getName();
	
	public function getIsactive();
	
	public function getModuleName();
	
	public function getModuleLocation();
	
}