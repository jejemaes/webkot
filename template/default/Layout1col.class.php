<?php


class Layout1col implements iLayout1col{

	private $content;
	private $menu_content;
	private $title;
	
	private $herounit;
	private $widgets;
	private $widget_footer;
	
	private $_isHomePage;
	
	
	public function __construct(){
		$this->menu_content = array();
		$this->title = "Titre";
		$this->widget_footer = array();
		$this->herounit = null;
		$this->widgets = array();
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
	

	public function addWidgets( $widgets ){
		$this->widgets = $widgets;
	}
	
	/**
	 * Set the content of the page
	 * @param String $html : the html code
	 */
	public function setContent($html){
		$this->content = $html;
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
				                <img src="'.DIR_TEMPLATE.'default/img/dessinLogo.png" id="logo-imgM">
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
		
		
		$html .= '<div class="container frame">';

		//var_dump($this->getWidgets());
		
		if($this->getHeroUnit()){
			$html .= '<div class="row">
						<div class="span12 page">
							<div class="row">
								<div class="span3">';
				
			
			if(count($this->getWidgets()) >= 1){
				$html .= $this->getSidebarHtml($this->getWidgets()[0]);
			}

			$html .= '
								</div>
								<div class="span9">
									<div class="page-content">
										<div class="content-box">
											'.$this->getHeroUnit().'
										</div>
									</div>
								</div>
							</div>
						</div>
				
			</div>';
		}
		
		
		if($this->getIsHomePage()){	
		$html .= '<div class="row">
		    	<div class="span12 spacer"></div>
			</div>';
			$html .=  $this->getContent();
		}else{
			$html .= '<div class="row page">
		    	<div class="span12">'.$this->getContent().'</div>
			</div>';
		}
		
			/*' . $this->getContent().'
					
			<div class="row">
				
						<div class="span12 page">
							<div class="page-content">
								<div class="content-box">
									<h3>'.$this->getTitle(). '</h3>' . $this->getContent().'
								</div>
							</div>
						</div>
				
			</div>
		
			<div class="row">
		    	<div class="span12 spacer"></div>
			</div>
	*/
		
		$html .= '</div><!-- end of container -->';
	
		return $html;
	}
	
	
	public function setTitle($title){
		$this->title = $title;
	}
	public function getTitle(){
		return $this->title;
	}
	
	public function getHeroUnit(){
		return $this->herounit;
	}
	
	public function getContent(){
		return $this->content;
	}
	
	public function setWidgetFooter( $widget_footer ){
		$this->widget_footer = $widget_footer;
	}
	
	public function getWidgetFooter(){
		return $this->widget_footer;
	}
	
	public function getMenuContent(){
		return $this->menu_content;
	}


	public function setIsHomePage( $_isHomePage ){
		$this->_isHomePage = $_isHomePage;
	}
	
	public function getIsHomePage(){
		return $this->_isHomePage;
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
	
	
	private function getSidebarHtml($widget){
		$code = '';
		// ($this->getWidgetSidebar() as $widget){
			$str = $widget->__toString();
			$code .= '<aside>
							<div class="SiteBarBox">
								<div class="SiteBarBoxContainerHead"><h4>'.$widget->getName().'</h4></div>
								<div class="SiteBarBoxContainer">
										'.$str.'
								</div>
							</div>
						</aside>';
		//}
		return $code;
	}
	
	


	
	public function getWidgets(){
		return $this->widgets;
	}
	

}