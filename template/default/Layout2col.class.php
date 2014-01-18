<?php


class Layout2col implements iLayout2col{
	
	private $content;
	private $menu_content;
	private $title;
	
	private $herounit;
	
	private $widget_sidebar;
	private $widget_footer;
	private $_isHomePage;
	
	
	public function __construct(){
		$this->menu_content = array();
		$this->title = "Titre";
		$this->widget_sidebar = array();
		$this->widget_footer = array();
		$this->_isHomePage = 0;
	}

	/**
	 * Set the content of the menu
	 * @param array $menu
	 */
	public function setMenuContent(array $menu){
		$this->menu_content = $menu;
	}
	
	public function setHeroUnit($html){
		$this->herounit = $html;
	}
	
	
	/**
	 * Set the content of the page
	 * @param String $html : the html code
	 */
	public function setContent($html){
		$this->content = $html;
	}
	
	

	public function setIsHomePage( $_isHomePage ){
		$this->_isHomePage = $_isHomePage;
	}
	
	public function getIsHomePage(){
		return $this->_isHomePage;
	}
	
	
	/**
	 * add content in the sidebar (widgets, ....)
	 * @param array $content
	*/
	public function addSidebarContent(array $content){
		$this->setWidgetSidebar($content);
	}
	
	
	/**
	 * add content in the Footer of the layout (widgets, ....)
	 * @param unknown $content
	*/
	public function addFooterContent($content){
		
	}
	
	
	public function getHTMLCode(){
		
		$html = '
				<div class="navbar navbar-inverse navbar-fixed-top">
			      <div class="">
			        <div class="container menu-navbar-inner">
			        	<div class="menu-margin">
				          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				            <span class="icon-bar"></span>
				            <span class="icon-bar"></span>
				            <span class="icon-bar"></span>
				          </a>
				          <a class="brand" href="'.URL.'">Webkot.be</a>
				          <div class="nav-collapse collapse">
				            '.$this->getMenuHTML().'			          
						  </div>
			          	</div>
			        </div>
			      </div>
			    </div>';

					    
		if($this->getIsHomePage()){
				$html .= '<div class="header-homepage">
					    <div class="container">
					        <div class="row">				  
					            <div class="span6">
					            	<div class="span3 header-content offset1">
					                <h2>Edito</h2>
					                <p>Bienvenue sur le Webkot, le guilleret kot &agrave; projet namurois qui vous diffuse quotidiennement les photos de vos derni&egrave;res guindailles.<br>Bonne visite !</p>
					                </div>
					            </div>
					            <div class="span6 logo">
					                <img src="'.DIR_TEMPLATE.'default/img/dessinLogo.png" id="logo-img">
					            </div>
					        </div>
					    </div>
					</div>';
				
			}else{
				$html .= '<div class="header-classicpage">
				<div class="container">
					<div class="row">
						<div class="span4 offset4 header-box"><img src="'.DIR_TEMPLATE.'default/img/dessinLogo.png" id="logo-img"></div>
			        </div>
				</div>
			</div>	';
			}	   
			
		$html .= '<div class="container frame">
			<div class="row">
				<div class="span12">
					<div class="row">
						<div class="span3 page">
							'.$this->getSidebarHtml().'
						</div>
	
	
						<div class="span9 page">
							<div class="page-content">
									<h3>'.$this->getTitle(). '</h3>
								<div class="content-box">
									' . $this->getContent().'
								</div><!-- End of Content-box -->
							</div>
						</div>
					</div><!-- end of 2e row -->
				</div><!-- end of span12 -->	               
				        
			</div>
			
			<div class="row">
		    	<div class="span12 spacer"></div>
			</div>
				    
		</div><!-- end of container -->';
		return $html;
	}
	
	
	public function getHeroUnit(){
		return $this->herounit;
	}
	
	public function setTitle($title){
		$this->title = $title;
	}
	public function getTitle(){
		return $this->title;
	}
	
	public function getContent(){
		return $this->content;
	}
	
	public function setWidgetSidebar( $widget_sidebar ){
		$this->widget_sidebar = $widget_sidebar;
	}
	
	public function setWidgetFooter( $widget_footer ){
		$this->widget_footer = $widget_footer;
	}
	
	public function getWidgetSidebar(){
		return $this->widget_sidebar;
	}
	
	public function getWidgetFooter(){
		return $this->widget_footer;
	}

	public function getMenuContent(){
		return $this->menu_content;
	}
	
	
	
	private function getSidebarHtml(){
		$code = '';
		foreach ($this->getWidgetSidebar() as $widget){
			$str = $widget->__toString();
			$code .= '<aside>
							<div class="SiteBarBox">
								<div class="SiteBarBoxContainerHead"><h4>'.$widget->getName().'</h4></div>
								<div class="SiteBarBoxContainer">
										'.$str.'
								</div>
							</div>
						</aside>';
		}
		return $code;
	}
	
	/**
	 * built the html code of the Main Menu
	 */
	private function getMenuHTML(){
		$items = $this->getMenuContent();
		$menu ='<ul class="nav">';
		$l = count($items);
	
		$i=0;
		foreach($items as $key => $value ){
	
			if(!empty($value)){
				$class ='';
				if(strlen(strstr(strtolower(URLUtils::getCompletePageURL()),strtolower($value)))>0){
					$class = 'class="active"';
				}
				if($i == ($l-1)){
					$menu .= '<li '.$class.'><a href="'.$value.'" >'.$key.'</a></li>';
				}else{
					$menu .= '<li '.$class.'><a href="'.$value.'">'.$key.'</a></li>';
				}
			}
			$i++;
		}
		$menu .= '</ul>';
		
		return $menu;
	
	}
	
	
}