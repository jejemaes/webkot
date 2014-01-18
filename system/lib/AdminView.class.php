<?php


abstract class AdminView{
	
	private $template;
	private $module;

	public function setTemplate( $template ){
		$this->template = $template;
	}
	
	public function setModule( $module ){
		$this->module = $module;
	}
	
	public function getTemplate(){
		return $this->template;
	}
	
	public function getModule(){
		return $this->module;
	}
}