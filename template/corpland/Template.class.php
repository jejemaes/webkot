<?php

class Template extends AbstractTemplate implements iTemplate{
	
	private $pageTitle;
	private $pageSubtitle;
	
	private $cssTags;
	private $jsHeaderTags;
	private $jsFooterTags;
	
	private $layout;
	private $options;
	private $menuContent;
	private $content;
	
	private $widgetsSitebar;
	private $widgetsFooter;
	
	
	/**
	 * 
	 * @param array $options
	 */
	public function __construct($options){
		$this->setPageTitle($options['site-title']);
		$this->setPageSubtitle("");
		
		$this->setCssTags(array());
		$this->setJsHeaderTags(array());
		$this->setJsFooterTags(array());
		
		$this->setLayout("layout2col");
		$this->setOptions($options);
		$this->setSlides(array());
		$this->setMenuContent(array());
		
		$this->setWidgetSidebar(array());
		$this->setWidgetFooter(array());
	}
	
	
	/**
	 * built the html code of the complete page and display it
	 */
	public function render(){
		$options = $this->getOptions();
		
		
		$html = '<!DOCTYPE html>
					<html lang="en">';
			$html .= '<meta charset="UTF-8">';
			$html .= '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
			$html .= '<title>'.$options['site-title'] . ' : '.$this->getPageTitle().'</title>	';
		
		//###### HEADER
		$html .= '<head>';
			$html .= $options['site-metatags'];
			$html .= '  <!-- GOOGLE FONT-->
						<link href="http://fonts.googleapis.com/css?family=Roboto:400,300,700italic,700,500&subset=latin,latin-ext" rel="stylesheet" type="text/css">
						<!-- Bootstrap core CSS -->
    					<link href="'.DIR_TEMPLATE.'corpland/css/bootstrap.css" rel="stylesheet">
    					<link href="'.DIR_TEMPLATE.'corpland/css/custom.css" rel="stylesheet">
    					<link href="'.DIR_TEMPLATE.'modern-business/css/bootstrap_2.3.2_form.css" rel="stylesheet">
    							
    					<link href="http://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css" rel="stylesheet">
    					<!--[if lt IE 7]>
						<link href="http://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome-ie7.min.css" rel="stylesheet">
						<![endif]-->
					    <!-- Fav and touch icons -->
    					
						<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
						<!--[if lt IE 9]>
						      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js" type="text/javascript"></script>
						<![endif]-->
    							
    					<!-- Bootstrap core JS -->
    					<script src="'.DIR_TEMPLATE.'corpland/js/jquery.js"></script>
    				  	<script src="'.DIR_TEMPLATE.'corpland/js/bootstrap.min.js"></script>';
			$html .= $this->renderArray($this->getCssTags());
			$html .= "\n".'<!-- Additionnal JS Code Header-->'."\n";
			$html .= $this->renderArray($this->getJsHeaderTags());
		$html .= '</head>';
		
		//###### BODY
		$html .= '<body>';	
		
		$html .= '<div class="wrap">';
		$html .= '<section>';
			//menu
			$html .= '
				<nav id="topnav" class="navbar navbar-fixed-top navbar-inverse" role="navigation">
				      <div class="container">
				        <div class="navbar-header">
				          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
				            <span class="sr-only">'.$options['site-title'].'</span>
				            <span class="icon-bar"></span>
				            <span class="icon-bar"></span>
				            <span class="icon-bar"></span>
				          </button>
				          <a class="navbar-brand" href="'.URL.'">'.$options['site-title'].'</a>
				        </div>
					    <div class="collapse navbar-collapse navbar-ex1-collapse">
					        '.$this->getMenuHTML(). $this->getWidgetNavbarHtml() . '
						</div><!-- /.navbar-collapse -->
	      			</div><!-- /.container -->
	    		</nav>';
		$html .= '</section>';
		
		$html .= '<section><div class="text-left col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">';
		$html .= $this->getHtmlSlides();
		$html .= '</div></section>';
		// header page
		$html .= '<section>
			<div class="jumbotron">
				<div class="container">
					<div class="row">
						<div style="height:200px;">
						<div class="text-left col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">
							
							<h2>
								This template built with Bootstrap 3.0. version.
							</h2> <a class="btn btn-primary btn-lg" href="index.html#">SIGN UP NOW!</a>
						</div>
						</div>
					</div>
				</div>
			</div>
		</section>';
		//Content
		$html .= '<section class="content-box">';
		$html .= '<div class="container">';
		
		//layout2col
		if($this->getLayout() == "layout2col"){	
			$html .= '<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<div class="page-header">
						<h3>
							'.$this->getPageTitle().' <small>'.$this->getPageSubtitle().'</small>
						</h3>
					</div>
				</div>
			</div>';
			$html .= '<div class="row">
				<div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
					<div style="margin: 20px">'.$this->getContent().'</div>
				</div>
				<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
					<div class="">'.$this->getSidebarHtml().'</div>
				</div>
			</div>';
			
		}
		
		//layout1col
		if($this->getLayout() == "layout1col"){
			$html .= '<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<div class="page-header">
						<h3>
							'.$this->getPageTitle().' <small>'.$this->getPageSubtitle().'</small>
						</h3>
					</div>
				</div>
			</div>';
			$html .= '<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div style="margin: 20px">'.$this->getContent().'</div>
				</div>
			</div>';
		}
		
		// portfolio
		if($this->getLayout() == "layoutportfolio"){
			$html .= $this->getContent();
			/*$htmlZ .= '<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					'.$this->getContent().'
				</div>
			</div>';*/
		}
				
		$html .= '</div>';
		$html .= '</section>';
	// footer
	$html .= '<section class="custom-footer">
		<div class="container">
			<div class="row">
				<div class="col-xs-6 col-sm-6 col-md-8 col-lg-8">
					<div class="row">
						<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
							<ul class="list-unstyled">
								<li>
									 <a>Link anchor</a>
								</li>
								<li>
									 <a>Link anchor</a>
								</li>
								<li>
									 <a>Link anchor</a>
								</li>
								<li>
									 <a>Link anchor</a>
								</li>
								<li>
									 <a>Link anchor</a>
								</li>
							</ul>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
							<ul class="list-unstyled">
								<li>
									 <a>Link anchor</a>
								</li>
								<li>
									 <a>Link anchor</a>
								</li>
								<li>
									 <a>Link anchor</a>
								</li>
								<li>
									 <a>Link anchor</a>
								</li>
								<li>
									 <a>Link anchor</a>
								</li>
							</ul>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
							<ul class="list-unstyled">
								<li>
									 <a>Link anchor</a>
								</li>
								<li>
									 <a>Link anchor</a>
								</li>
								<li>
									 <a>Link anchor</a>
								</li>
								<li>
									 <a>Link anchor</a>
								</li>
								<li>
									 <a>Link anchor</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
				<div class="col-xs-6 col-sm-6 col-md-4 col-lg-4 text-right">
					 <address> <strong>Twitter, Inc.</strong><br /> 795 Folsom Ave, Suite 600<br /> San Francisco, CA 94107<br /> <abbr title="Phone">P:</abbr> (123) 456-7890</address> <address> <strong>Full Name</strong><br /> <a href="mailto:#">first.last@example.com</a></address>
				</div>
			</div>
		</div>
	</section>
</div>';
		
		
			/*
			 
			 //menu
			$html .= '
			<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
			      <div class="container">
			        <div class="navbar-header">
			          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
			            <span class="sr-only">'.$options['site-title'].'</span>
			            <span class="icon-bar"></span>
			            <span class="icon-bar"></span>
			            <span class="icon-bar"></span>
			          </button>
			          <a class="navbar-brand" href="'.URL.'">'.$options['site-title'].'</a>
			        </div>
				    <div class="collapse navbar-collapse navbar-ex1-collapse">
				        '.$this->getMenuHTML(). $this->getWidgetNavbarHtml() . '    
					</div><!-- /.navbar-collapse -->
      			</div><!-- /.container -->
    		</nav>';
			
			
			
			//content frame & layout
			if($this->getLayout() == "layout2col" || $this->getLayout() == "layout1col"){
				$html .= '<div class="template-header template-img-center">';
				$html .= '<img src="'.DIR_TEMPLATE . 'modern-business/img/dessinLogo.png">';
				$html .= '</div>';
				
				$html .= '<div class="container">';
				//layout2col
				if($this->getLayout() == "layout2col"){	
					$html .= '<div class="row">';
						$html .= '<div class="col-lg-12">';
						$html .= '<h1 class="page-header">'.$this->getPageTitle().'  <small>  '.$this->getPageSubtitle().'</small></h1>';
						$html .= '</div>';
					$html .= '</div><!-- /.row -->';
					$html .= '<div class="row">';
						$html .= '<div class="col-lg-8">';
						$html .= '<div class="well">';
						$html .= $this->getContent();
						$html .= '</div>';
						$html .= '</div>';
						$html .= '<div class="col-lg-4">';
		   				$html .= $this->getSidebarHtml();
						$html .= '</div>';
					$html .= '</div><!-- /.row -->';
				}
				
				//layout1col
				if($this->getLayout() == "layout1col"){
					$html .= '<div class="row">';
						$html .= '<div class="col-lg-12">';
						$html .= '<h1 class="page-header">'.$this->getPageTitle().'  <small>  '.$this->getPageSubtitle().'</small></h1>';
						$html .= '</div>';
					$html .= '</div><!-- /.row -->';
					$html .= '<div class="row">';
						$html .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
						$html .= '<div class="well">';
						$html .= $this->getContent();
						$html .= '</div>';
						$html .= '</div>';	
					$html .= '</div><!-- /.row -->';
					$html .= '<div class="row">';
						$html .= $this->getBottombarHtml();
					$html .= '</div><!-- /.row -->';
				}
				$html .= '</div><!-- /.container -->';
			}
			
			
			if($this->getLayout() == "layoutportfolio"){
				$html .= $this->getContent();
			}
			
			
			//footer
			$html .= '<div class="col-lg-12 template-footer">';
			$html .= '<div class="container">';
				// footer widgets
				$this->getWidgetFooter();
				if(count($this->getWidgetFooter()) <= 4){
					$n = (int)(12 / count($this->getWidgetFooter()));
					for($i=0 ; $i< count($this->getWidgetFooter()) ; $i++){
						$tmp = $this->getWidgetFooter();
						$w = $tmp[$i];
						$html .= '<div class="col-lg-'.$n.'">';
						$html .= '<h4>'.$w->getName().'</h4>';
						$html .= $w;
						$html .= '</div>';
					}
				}else{	
					for($i=0 ; $i<3 ; $i++){
						$tmp = $this->getWidgetFooter();
						$w = $tmp[$i];
						$html .= '<div class="col-lg-3">';
						$html .= '<h4>'.$w->getName().'</h4>';
						$html .= $w;
						$html .= '</div>';
					}
				}
			$html .= '<div class="clearfix"></div>';
			$html .= '<hr class="clearer-visible">';
			$html .= '	<footer>
					        <div class="row">
					            <div class="col-lg-12">
					                <p class="text-center">
					                    '.$options["site-footer"].'
					                </p>
					            </div>
					        </div>
					 	</footer>';
			$html .= '</div>';
			$html .= '</div>';
		
			
			$html .= '<!-- Bootstrap edge JavaScript -->
    				  <!-- <script src="'.DIR_TEMPLATE.'modern-business/js/modern-business.js"></script>-->
					  <script src="'.DIR_TEMPLATE.'modern-business/js/bootstrap-modalmanagerKK.js"></script>
					  <script src="'.DIR_TEMPLATE.'corpland/js/bootstrap.js"></script>';
		*/
		
			$html .= '<!-- Additionnal JS Code Footer -->';
			$html .= $this->renderArray($this->getJsFooterTags());
		$html .= '</body>';
		
		$html .= '</html>';
		echo $html;
	}
	
	
	/**
	 * built the html code of the complete page and display it
	 */
	public function renderClosed(){
		$options = $this->getOptions();
	
		$html = '<!DOCTYPE html>
					<html lang="en">';
		$html .= '<meta charset="UTF-8">';
		$html .= '<meta name=viewport content="width=device-width, initial-scale=1">';
		$html .= '<title>'.$options['site-title'] . ' : '.$this->getPageTitle().'</title>	';
		//###### HEADER
		$html .= '<head>';
		$html .= '	<!-- Bootstrap core CSS -->
    					<link href="'.DIR_TEMPLATE.'modern-business/css/bootstrap.css" rel="stylesheet">
    					<link href="'.DIR_TEMPLATE.'modern-business/css/bootstrap_2.3.2_form.css" rel="stylesheet">
    					<link href="'.DIR_TEMPLATE.'modern-business/css/login.css" rel="stylesheet">
	
    					<!-- Bootstrap core JS -->
    					<script src="'.DIR_TEMPLATE.'modern-business/js/jquery.js"></script>
    				  	<script src="'.DIR_TEMPLATE.'modern-business/js/bootstrap.js"></script>
	
    					<!-- CSS Template -->
    					<link href="'.DIR_TEMPLATE.'modern-business/css/modern-business.css" rel="stylesheet">
    					<link href="http://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css" rel="stylesheet">
    					<link href="'.DIR_TEMPLATE.'modern-business/font-awesome/css/font-awesome.min.css" rel="stylesheet">'."\n";
		
		$html .= $this->renderArray($this->getCssTags());
		$html .= "\n".'<!-- Additionnal JS Code Header-->'."\n";
		$html .= $this->renderArray($this->getJsHeaderTags());
		$html .= '</head>';
	
		//###### BODY
		$html .= '<body>';
		
		$html .= '<div id="wrap">';
	
		$html .= '<div class="container">
    <div class="content">
      <div class="row">
        <div class="login-form">
          <h2>Login</h2>
          <form method="post">
            <fieldset>
              <div class="clearfix">
                <input type="text" placeholder="Username" name="form-login-input-username" id="form-login-input-username">
              </div>
              <div class="clearfix">
                <input type="password" placeholder="Password" name="form-login-input-password" id="form-login-input-password">
              </div>
          		<input type="hidden" id="form-login-sended" name="form-login-sended" value="fromform" />
              <button class="btn btn-success" type="submit">Login</button>
            </fieldset>
          </form>
        </div>
      </div>
    </div>
  </div> <!-- /container -->';
	
		$html .= '<div class="container-message">'.$options['site-closed-message'].'</div>';
	    $html .= '<div id="push"></div>
	    	</div>';
	
	    $html .= '<div id="footer">
	      <div class="container3">
	        <p class="text-center" style="padding-top:10px;">'.$options["site-footer"].'</p>
	      </div>
	    </div>';
			
		//footer
		/*$html .= '<div class="col-lg-12 template-footer">';
		$html .= '<div class="container">';
		
		$html .= '<div class="clearfix"></div>';
		$html .= '<hr class="clearer-visible">';
		$html .= '	<footer>
					        <div class="row">
					            <div class="col-lg-12">
					                <p class="text-center">
					                    '.$options["site-footer"].'
					                </p>
					            </div>
					        </div>
					 	</footer>';
		$html .= '</div>';
		$html .= '</div>';
			
	*/
			
		$html .= '<!-- Bootstrap edge JavaScript -->
    				  <script src="'.DIR_TEMPLATE.'modern-business/js/modern-business.js"></script>
					  <script src="'.DIR_TEMPLATE.'modern-business/js/bootstrap-modalmanager.js"></script>
					  <script src="'.DIR_TEMPLATE.'modern-business/js/bootstrap-modal.js"></script>';
		$html .= "\n".'<!-- Additionnal JS Code Footer -->'."\n";
		$html .= $this->renderArray($this->getJsFooterTags());
		$html .= '</body>';
	
		$html .= '</html>';
		echo $html;
	}
	
	
	
	/**
	 * built the html code of an array containing the tags
	 * @param array $import
	 * @return string
	 */
	private function renderArray(array $import){
		$string = '';
		foreach ($import as $value){
			$string .= $value;
		}
		return $string;
	}
	
	/**
	 * built the html code of the Main Menu
	 * @return the html code of the Menu
	 */
	private function getMenuHTML(){
		$items = $this->getMenuContent();
		$menu ='<ul class="nav navbar-nav">';
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
	
	/**
	 * built the html code of the widget for the sitebar
	 * @return string
	 */
	private function getSidebarHtml(){
		$code = '';
		$i=0;
		foreach ($this->getWidgetSidebar() as $widget){
			if($i != 0){	
				$str = $widget->__toString();
				$code .= '<aside>
								<div class="well">
									<div class="SiteBarBoxContainerHead"><h4>'.$widget->getName().'</h4></div>
									<div class="SiteBarBoxContainer">
											'.$str.'
									</div>
								</div>
							</aside>';
			}
			$i++;
		}
		return $code;
	}
	
	/**
	 * built the html code of the widget for the bottom of container
	 * @return string
	 */
	private function getBottombarHtml(){
		$code = '';
		if((count($this->getWidgetSidebar())-1) <= 4 && (count($this->getWidgetSidebar())-1) > 0){
			$n = (int)(12 / (count($this->getWidgetSidebar())-1));
		}else{
			$n = 3;
		}
		$i = 0;
		foreach ($this->getWidgetSidebar() as $widget){
			if($i != 0){
				$str = $widget->__toString();
				$code .= '<div class="col-lg-'.$n.' col-md-'.$n.' col-sm-12 col-xs-12">
								<div class="template-widget-bottom">
									<div class="SiteBarBoxContainerHead"><h4>'.$widget->getName().'</h4></div>
									<div class="SiteBarBoxContainer">
											'.$str.'
									</div>
								</div>
							</div>';
			}
			$i++;
		}
		return $code;
	}
	
	
	private function getWidgetNavbarHtml(){
		$widgets = $this->getWidgetSidebar();
		if(count($widgets) > 0){
			$widget = $widgets[0];
			$str = $widget->__toString();
			$html = '<ul class="nav navbar-nav navbar-right">
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">'.$widget->getName().' <b class="caret"></b></a>
							<div class="dropdown-menu template-dropdown-div-navbar">
								'.$str.'
							</div>
						</li>
					</ul>';
			return $html;
		}
		return "";
	}
	
	
	
	

	private function getHtmlSlides(){
		$html = "";
		$slides = $this->getSlides();
		if(count($slides) > 0){
			$html .= '<div id="accueil-carousel" class="carousel slide">';
			$html .= '<ol class="carousel-indicators">';
			for($i=0 ; $i<count($slides) ; $i++){
				$class = "";
				if($i == 0){
					$class = ' class="active"';
				}
				$html .= '<li data-target="#accueil-carousel" data-slide-to="'.($i+1).'" '.$class.'></li>';
			}
			$html .= '</ol>';
	
			$html .= '<!-- Wrapper for slides --><div class="carousel-inner">';
			for($i=0 ; $i<count($slides) ; $i++){
				$slide = $slides[$i];
				$class = "";
				if($i == 0){
					$class = ' active';
				}
				$html .= '<div class="item '.$class.'">';
				$html .= '<div class="fill" style="background-image:url(\'' . $slide->getPathimg().'\');"></div>';
				$html .= '<div class="carousel-caption">';
				$html .= '<h1>' . $slide->getTitle() . '</h1>';
				$html .= '<p>' . $slide->getDescription() . '</p>';
				$html .= '</div>';
				$html .= '</div>';
			}
			$html .= '</div>';
	
			$html .= '<!-- Controls -->
	        <a class="left carousel-control" href="#accueil-carousel" data-slide="prev">
	          <span class="icon-prev"></span>
	        </a>
	        <a class="right carousel-control" href="#accueil-carousel" data-slide="next">
	          <span class="icon-next"></span>
	        </a>';
	
			$html .= '</div>';
		}
		return $html;
	}
	
}
