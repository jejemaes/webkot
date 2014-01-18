
<?php

class Template implements iTemplate{
	
	private $layout;
	private $pageTitle;
	private $pageSubtitle;
	private $footerWidgets;
	private $cssTags;
	private $jsHeaderTags;
	private $jsFooterTags;
	private $content;
	private $menu;
	
	private $options;
	
	
	public function __construct($options){
		$this->pageTitle = $options['site_title'];	
		$this->cssTags = array();
		$this->jsHeaderTags = array();
		$this->jsFooterTags = array();
		$this->menu = array();
		$this->footerWidgets = array();
		$this->setOptions($options);
	}
	
	
	public function initLayout($layoutName, $isHomepage = 0){
		if(empty($layoutName) or $layoutName == null){
			$layoutName = "Layout2col";
		}
		$layoutName = ucfirst($layoutName);
		$this->layout = new $layoutName();
		$this->layout->setIsHomePage($isHomepage);
		return $this->getLayout();
	}
	
	
	public function setWidgetSidebar(array $widgets){
		if($this->getLayout() instanceof iLayout2col){
			$this->getLayout()->addSidebarContent($widgets);
		}
		
		if($this->getLayout() instanceof iLayout1col){
			$this->getLayout()->addWidgets($widgets);
		}
	}
	
	public function setWidgetFooter(array $widgets){
		$this->footerWidgets = $widgets;
	}
	
	public function setMenuContent(array $menu){
		$this->setMenu($menu);
		$this->getLayout()->setMenuContent($menu);
	}
	
	public function setPageSubtitle($subtitle){
		$this->pageSubtitle = $subtitle;
	}
	
	/**
	 * Add additional CSS tag (import other CSS than those by default, ei. the views of modules)
	 * @param String $cssTag
	 */
	public function addStyle($cssTag){
		$tmp = $this->getCssTags();
		$tmp[] = $cssTag;
		$this->setCssTags($tmp);
	}
	
	
	
	/**
	 * Add additional JS tag (import other JS Files or add JS code) in the header
	 * @param String $jsCode
	 */
	public function addJSHeader($jsCode){
		$tmp = $this->getJsHeaderTags();
		$tmp[] = $jsCode;
		$this->setJsHeaderTags($tmp);
	}
	
	
	/**
	 * Add additional JS tag (import other JS Files or add JS code) in the footer of the HTML page
	 * @param String $jsCode
	 */
	public function addJSFooter($jsCode){
		$tmp = $this->getJsFooterTags();
		$tmp[] = $jsCode;
		$this->setJsFooterTags($tmp);
	}
	
	
	public function render(){
	
		$this->getLayout()->setTitle($this->getPageSubtitle());
		if($this->getContent() != null){
			$this->getLayout()->setContent($this->getContent());
		}
		
		$html = '<!DOCTYPE HTML>
<html lang="fr">
	<head>
		<meta charset="UTF-8">
		<title>'.$this->getPageTitle().' : '.$this->getPageSubtitle().'</title>			

		'.$this->getOptions()["site-metatags"].'
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="//vjs.zencdn.net/4.1/video-js.css" rel="stylesheet">
<script src="//vjs.zencdn.net/4.1/video.js"></script>				
		<!-- Style css -->
  		<link href="'.DIR_TEMPLATE.'default/css/bootstrap.css" rel="stylesheet"/>
	    <link href="'.DIR_TEMPLATE.'default/css/bootstrap-responsive.css" rel="stylesheet"/>
		<link href="'.DIR_TEMPLATE.'default/css/bootstrap-modal.css" rel="stylesheet"/>
	    <link href="'.DIR_TEMPLATE.'default/css/design.css" rel="stylesheet"/>
		 '.$this->renderImport($this->getCssTags()).'
	    
<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
 <!--[if lt IE 9]>
     <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
 <![endif]-->

		 		
	   <!-- JavaScript Tags -->
	   <!-- <script src="'.DIR_TEMPLATE.'default/js/jquery-1.7.2.min.js" type="text/javascript"></script>-->
	   		
		'.$this->renderImport($this->getJsHeaderTags()).'
	</head>  
			
	<body>
			<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/fr_FR/all.js#xfbml=1&appId=516806348339496";
  fjs.parentNode.insertBefore(js, fjs);
}(document, "script", "facebook-jssdk"));</script>
		
		'.$this->getLayout()->getHTMLCode().'
	    				
			<section class="dark footer">
			    <div class="container ">
			        <div class="row">';
		
		$this->getFooterWidgets();
		if(count($this->getFooterWidgets()) <= 4){
			$n = (int)(12 / count($this->getFooterWidgets()));
			for($i=0 ; $i< count($this->getFooterWidgets()) ; $i++){
				$w = $this->getFooterWidgets()[$i];
				$html .= '<div class="span'.$n.'">';
				$html .= '<h4>'.$w->getName().'</h4>';
				$html .= $w;
				$html .= '</div>';
			}
		}else{
			
			for($i=0 ; $i<3 ; $i++){
				$w = $this->getFooterWidgets()[$i];
				$html .= '<div class="span3">';
				$html .= '<h4>'.$w->getName().'</h4>';
				$html .= $w;
				$html .= '</div>';
			}
		}
		
		$html .= '</div>';

		$html .=  '<div class="row">
			        	<div class="span12">
			            	<hr class="style1">
			            	<footer><p class="center">
			                2011-2012 / <a href="http://www.webkot.be">www.webkot.be</a> <br clear>Site optimis&eacute; pour <a href="http://www.google.be">Firefox</a> et <a href="http://www.google.be">Safari</a></p>
			                </footer>
			            </div>

			        </div>
			    </div>
			</section>

			<script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.js"></script>
			<!--<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>-->
    		<script src="'.DIR_TEMPLATE.'default/js/bootstrap.js" type="text/javascript"></script>
    		<script src="'.DIR_TEMPLATE.'default/js/bootstrap-modal.js"></script>
			<script src="'.DIR_TEMPLATE.'default/js/bootstrap-modalmanager.js"></script>
					
			'.$this->renderImport($this->getJsFooterTags()).'
					
	</body>
</html>';
				
		echo $html;
		
	}
	
	
	private function renderImport(array $import){
		$string = '';
		foreach ($import as $value){
			$string .= $value;
		}
		return $string;
	}
	
	
	
	public function setLayout($layout){
		if(!is_object($layout)){
			$this->initLayout($layout);
		}else{
			$this->layout = $layout;
		}
	}
	public function getLayout(){
		return $this->layout;
	}
	public function getPageTitle(){
		return $this->pageTitle;
	}
	public function getPageSubtitle(){
		return $this->pageSubtitle;
	}
	public function setPageTitle($title){
		$this->pageTitle = $title;
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
	
	public function setContent( $content ){
		$this->content = $content;
	}

	public function setFooterWidgets( $footerWidgets ){
		$this->footerWidgets = $footerWidgets;
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
	
	public function getContent(){
		return $this->content;
	}
	
	public function setMenu( $menu ){
		$this->menu = $menu;
	}
	
	public function getMenu(){
		return $this->menu;
	}
	
	public function getFooterWidgets(){
		return $this->footerWidgets;
	}


	public function setOptions( $options ){
		$this->options = $options;
	}
	
	public function getOptions(){
		return $this->options;
	}
}