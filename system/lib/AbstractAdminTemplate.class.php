<?php


abstract class AbstractAdminTemplate implements iAdminTemplate{
	
	private $pageTitle;
	private $pageSubtitle;
	
	private $options;

	private $cssTags;
	private $jsHeaderTags;
	private $jsFooterTags;
	
	private $menuContent;
	private $content;
	
	
	
	/**
	 * Add additional CSS tag (import other CSS than those by default, ei. the views of modules)
	 * @param String $cssTag
	 */
	public function addStyle($cssTag){
		$tmp = $this->getCssTags();
		if(!in_array($cssTag, $tmp)){	
			$tmp[] = $cssTag;
			$this->setCssTags($tmp);
		}
	}
	
	
	
	/**
	 * Add additional JS tag (import other JS Files or add JS code) in the header
	 * @param String $jsCode
	 */
	public function addJSHeader($jsCode){
		$tmp = $this->getJsHeaderTags();
		if(!in_array($jsCode, $tmp)){
			$tmp[] = $jsCode;
			$this->setJsHeaderTags($tmp);
		}
	}
	
	
	/**
	 * Add additional JS tag (import other JS Files or add JS code) in the footer of the HTML page
	 * @param String $jsCode
	 */
	public function addJSFooter($jsCode){
		$tmp = $this->getJsFooterTags();
		if(!in_array($jsCode, $tmp)){
			$tmp[] = $jsCode;
			$this->setJsFooterTags($tmp);
		}
	}
	
	
	/**
	 * built the html code of the complete page and display it
	*/
	public function render(){ 
		
	}


	



	public function setPageTitle( $pageTitle ){
		$this->pageTitle = $pageTitle;
	}
	
	public function setPageSubtitle( $pageSubtitle ){
		$this->pageSubtitle = $pageSubtitle;
	}
	
	public function setCssTags( $cssTags ){
		$this->cssTags = $cssTags;
	}
	
	public function setJsHeaderTags( $jsHeaderTags ){
		$this->jsHeaderTags = $jsHeaderTags;
	}
	
	public function setJsFooterTags( $jsFooterTags ){
		$this->jsFooterTags = $jsFooterTags;
	}
	
	public function setMenuContent(array $menuContent ){
		$this->menuContent = $menuContent;
	}
	
	public function setContent( $content ){
		$this->content = $content;
	}
	
	public function setOptions( $options ){
		$this->options = $options;
	}
	
	
	
	public function getPageTitle(){
		return $this->pageTitle;
	}
	
	public function getPageSubtitle(){
		return $this->pageSubtitle;
	}
	
	public function getCssTags(){
		return $this->cssTags;
	}
	
	public function getJsHeaderTags(){
		return $this->jsHeaderTags;
	}
	
	public function getJsFooterTags(){
		return $this->jsFooterTags;
	}
	
	public function getMenuContent(){
		return $this->menuContent;
	}
	
	public function getContent(){
		return $this->content;
	}
	
	public function getOptions(){
		return $this->options;
	}
	
}