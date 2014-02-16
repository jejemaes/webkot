<?php
/**
 * Class describing a Template for the Frontend
 *
 * @author jeromemaes
 * 16 fŽvr. 2014
 */

interface iTemplate extends iGeneralTemplate{
	
	
	public function setLayout($layout);
	
	
	public function setOptions($options);
	
	/**
	 * set the slides for the page
	 * @param array $slides : array of slide Objects
	 */
	public function setSlides(array $slides);
	
	
	public function setWidgetSidebar(array $widgets);
	
	
	public function setWidgetFooter(array $widgets);
	
	
	/**
	 * TODO : remove this !
	 * @param String $layoutName
	 */
	public function initLayout($layoutName, $isHomepage = 0);
	

	
}