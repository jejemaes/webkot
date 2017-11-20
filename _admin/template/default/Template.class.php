<?php


class Template implements iAdminTemplate{
	
	private $content;
	private $menu;
	
	private $jsCodeHeader;
	private $jsCodeFooter;
	private $cssStyle;
	
	
	/**
	 * Constructor
	 */
	public function __construct(){
		$this->content = '';
		$this->menu = array();
		$this->cssStyle = array();
		$this->jsCodeHeader = array();
		$this->jsCodeFooter = array();
	}
	
	public function setMenuContent(array $menu){
		$this->menu = $menu;
	}
	

	
	public function setContent($content){
		$this->content = $content;
	}
	
	
	/**
	 * set the title page
	 * @param String $title
	*/
	public function setPageTitle($title){
		
	}
	
	public function setPageSubtitle( $pageSubtitle ){
		
	}
	
	
	/**
	 * Add additional CSS tag (import other CSS than those by default, ei.
	 * the views of modules)
	 *
	 * @param String $cssTag
	 */
	public function addStyle($cssTag){
		$tags = $this->getCssStyle();
		if(!in_array($cssTag, $tags)){
			$tags[] = $cssTag;
			$this->setCssStyle($tags);
		}
	}
	
	/**
	 * Add additional JS tag (import other JS Files or add JS code) in the header
	 *
	 * @param String $jsCode
	*/
	public function addJSHeader($jsCode){
		$tags = $this->getJsCodeHeader();
		if(!in_array($jsCode, $tags)){
			$tags[] = $jsCode;
			$this->setJsCodeHeader($tags);
		}
	}
	
	/**
	 * Add additional JS tag (import other JS Files or add JS code) in the footer of the HTML page
	 *
	 * @param String $jsCode
	*/
	public function addJSFooter($jsCode){
		$tags = $this->getJsCodeFooter();
		if(!in_array($jsCode, $tags)){
			$tags[] = $jsCode;
			$this->setJsCodeFooter($tags);
		}
	}
	
	/**
	 * built the html code of the complete page and display it
	*/
	public function render(){
		
		$html = '<!DOCTYPE HTML>
<html lang="fr">
	<head>
		<meta charset="UTF-8">
		
	<meta http-equiv="Content-Language" content="fr-be">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
				
	<title>Webkot.be :: Admin Panel??</title>
	
	<!-- Bootstrap -->
	<script src="http://code.jquery.com/jquery.js"></script>
	<script src="'.DIR_TEMPLATE.'default/js/bootstrap.js"></script>
    <!--<script src="'.DIR_TEMPLATE.'default/js/jquery.js"></script>-->
    		
    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
			
	<link href="'.DIR_TEMPLATE.'default/css/bootstrap.css" rel="stylesheet">
    <link href="'.DIR_TEMPLATE.'default/css/style.css" rel="stylesheet">
    <style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 40px;
      }
    </style>
    		
   <!-- Additionnal CSS -->
   '.system_render_tag($this->getCssStyle()) . '
    		
   	<!-- Additionnal JS header -->
   '.system_render_tag($this->getJsCodeHeader()) . '
  
    </head>

  <body>';
		
		if($this->getMenuContent()){	
			$html .= '<div class="navbar navbar-inverse navbar-fixed-top">
		      <div class="navbar-inner">
		        <div class="container">
		          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
		            <span class="icon-bar"></span>
		            <span class="icon-bar"></span>
		            <span class="icon-bar"></span>
		          </a>
		          <a class="brand" href="../index.php">Webkot.be</a>
		          <div class="nav-collapse collapse">
		            <ul class="nav">
		              '.$this->renderMenuContent().'
		            </ul>
		          </div><!--/.nav-collapse -->
		        </div>
		      </div>
		    </div>';
		}
  
    		
	$html .= $this->getContent();
    
    
      $html .= '<hr>

      <footer>
        <p>&copy; Webkot.be / Administration </p>
      </footer>

	<!-- Additionnal JS footer -->
   '.system_render_tag($this->getJsCodeFooter()) . '
    
   </body>
</html>
				';
		
		echo $html;
		
	}
	
	
	
	public function getContent(){
		return $this->content;
	}
	


	public function setJsCodeHeader( $jsCodeHeader ){
		$this->jsCodeHeader = $jsCodeHeader;
	}
	
	public function setJsCodeFooter( $jsCodeFooter ){
		$this->jsCodeFooter = $jsCodeFooter;
	}
	
	public function setCssStyle( $cssStyle ){
		$this->cssStyle = $cssStyle;
	}
	
	public function getJsCodeHeader(){
		return $this->jsCodeHeader;
	}
	
	public function getJsCodeFooter(){
		return $this->jsCodeFooter;
	}
	
	public function getCssStyle(){
		return $this->cssStyle;
	}
	
	public function getMenuContent(){
		return $this->menu;
	}
	
	
	
	private function renderMenuContent(){
		$code = "";
		for($i=0 ; $i<count($this->getMenuContent()) ; $i++){
			$mod = $this->getMenuContent()[$i];
			$value = $mod->getAdminUrl();
			$key = $mod->getDisplayedName();
		/*foreach($this->getMenuContent() as $key => $value ){*/
			if($value != null){		
				if(!system_array_sub_array($value)){
					$code .= '<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">'.$key.' <b class="caret"></b></a>';
					$code .= '<ul class="dropdown-menu">';
					foreach($value as $subkey => $subval){
						if(!system_array_sub_array($subval)){
							$code .= '<li><a href="'.URLUtils::generateURL($mod->getName(), $subval).'">'.$subkey.'</a></li>';
						}
					}
					$code .= '</ul>';
					$code .= '</li>';
				}else{
					$code .= '<li><a href="'.URLUtils::generateURL($mod->getName(), $value).'">'.$key.'</a></li>';
				}
			}else{
				$code .= '<li><a href="'.URLUtils::generateURL($mod->getName(), array()).'">'.$key.'</a></li>';
			}
		}
		return $code;
	}
	
}