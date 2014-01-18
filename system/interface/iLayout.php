<?php

interface iLayout{
	

	/**
	 * Set the content of the page
	 * @param String $html : the html code
	 */
	public function setContent($html);
	
	public function setIsHomePage($isHomepage);
	
	public function getHTMLCode();
	
	
	public function setHeroUnit($html);
	
	
}

