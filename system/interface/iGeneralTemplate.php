<?php
interface iGeneralTemplate {
	
	
	/**
	 * Set the main title of the page (in the title html tag, or others)
	 * @param String $pageTitle
	 */
	public function setPageTitle( $pageTitle );
	
	
	/**
	 * Set the subtitle of the page (a precision for the title)
	 * @param String $pageSubtitle
	 */
	public function setPageSubtitle( $pageSubtitle );
	
	/**
	 * Set the content of the menu
	 * @param array $menu        	
	 */
	public function setMenuContent(array $menu);
	
	
	/**
	 * Set the content of the page
	 * @param String $html : the html code
	 */
	public function setContent($html);
	
	/**
	 * Add additional CSS tag (import other CSS than those by default, ei.
	 * the views of modules)
	 * @param String $cssTag        	
	 */
	public function addStyle($cssTag);
	
	/**
	 * Add additional JS tag (import other JS Files or add JS code) in the header
	 * @param String $jsCode        	
	 */
	public function addJSHeader($jsCode);
	
	/**
	 * Add additional JS tag (import other JS Files or add JS code) in the footer of the HTML page
	 * @param String $jsCode        	
	 */
	public function addJSFooter($jsCode);
	
	
	/**
	 * built the html code of the complete page and display it
	 */
	public function render();
	
	
	/**
	 * built the html code of the page when the site is locked/closed
	 */
	public function renderClosed();
	
}