<?php


abstract class View{
	
	private $template;
	private $module;
	
	
	public function error(Error $error){
		$lname = $this->getModule()->getLayout("page-error");
		$layout = $this->getTemplate()->initLayout($lname);
		$layout->setContent($error->__toString());
	}
	
	public function setTemplate($t){
		$this->template = $t;
	}
	
	public function setModule($m){
		$this->module = $m;
	}
	
	public function getTemplate(){
		return $this->template;
	}
	
	public function getModule(){
		return $this->module;
	}
}