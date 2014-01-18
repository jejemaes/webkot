<?php


interface iTemplate extends iGeneralTemplate{
	
	
	public function setIsHomepage($isHomepage);
	
	
	public function setLayout($layout);
	
	
	public function setOptions($options);
	
	
	public function setWidgetSidebar(array $widgets);
	
	
	public function setWidgetFooter(array $widgets);
	
	
	/**
	 * TODO : remove this !
	 * @param String $layoutName
	 */
	public function initLayout($layoutName, $isHomepage = 0);
	

	
}