<?php


interface iView{
	
	/**
	 * Constructor : set the 2 variables in the Object
	 * @param iTemplate $template : the template Object of the current html page
	 * @param Module $module : the current module
	 */
	public function __construct(iTemplate $template, Module $module);
	
	/**
	 * method call to modify the template in the case of error
	 * @param Error $error : the error to display
	 */
	public function error(Error $error);
	
	
	
	
	public function setTemplate($t);
	
	public function getTemplate();
	
	public function getModule();
}