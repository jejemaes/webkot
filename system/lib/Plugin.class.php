<?php


abstract class Plugin{
	
	private $options;
	

	public function setOptions( $value ){
		$this->options = $value;
	}
	
	public function getOptions(){
		return $this->options;
	}
	
	
	
}