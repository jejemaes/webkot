<?php


interface iAdminView{
	
	
	public function configureTemplate();
	
	
	public function getTemplate();	
	public function setTemplate($t);
	public function getModule();
	public function setModule($m);
	
}