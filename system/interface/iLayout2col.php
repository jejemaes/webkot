<?php


interface iLayout2col extends iLayout{
	
	/**
	 * Set the content of the menu
	 * @param array $menu
	 */
	public function setMenuContent(array $menu);

	
	/**
	 * add content in the Footer of the layout (widgets, ....)
	 * @param unknown $content
	 */
	public function addFooterContent($content);
	
	
	public function addSidebarContent(array $content);
	
	
}