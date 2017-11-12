<?php


class Template extends AbstractAdminTemplate implements iAdminTemplate{
	
	/**
	 * Constructor
	 */
	public function __construct($options = array()){
		$this->setPageTitle($options["site-title"] . " / Admin");
		$this->setPageSubtitle("DEV");
		
		$this->setCssTags(array());
		$this->setJsHeaderTags(array());
		$this->setJsFooterTags(array());
		
		$this->setMenuContent(array());
		$this->setOptions($options);
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
				
	<title>'.$this->getPageTitle().' :: '.$this->getPageSubtitle().'</title>';
	$html .= '
	<!-- Bootstrap -->
	<script src="http://code.jquery.com/jquery.js"></script>
	<script src="'.DIR_TEMPLATE.'sb-admin/js/bootstrap.js"></script>
    <!--<script src="'.DIR_TEMPLATE.'sb-admin/js/jquery.js"></script>-->
    		
    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Bootstrap CSS -->
	<link href="'.DIR_TEMPLATE.'sb-admin/css/bootstrap.css" rel="stylesheet">
    <link href="'.DIR_TEMPLATE.'sb-admin/css/sb-admin.css" rel="stylesheet">
    <link href="'.DIR_TEMPLATE.'sb-admin/css/bootstrap_2.3.2_form.css" rel="stylesheet">
    
    <link href="http://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css" rel="stylesheet">
    					
   <!-- Additionnal CSS -->
   '.system_render_tag($this->getCssTags()) . '
    		
   	<!-- Additionnal JS header -->
   '.system_render_tag($this->getJsHeaderTags()) . '
  
    </head>

  <body>';
	if($this->getMenuContent()){
		$html .= '<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
		        <!-- Brand and toggle get grouped for better mobile display -->
		        <div class="navbar-header">
		          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
		            <span class="sr-only">Toggle navigation</span>
		            <span class="icon-bar"></span>
		            <span class="icon-bar"></span>
		            <span class="icon-bar"></span>
		          </button>
		          <a class="navbar-brand" href="'.URL.'">'.$this->getOptions()["site-title"].'</a>
		        </div>
		
		        <!-- Collect the nav links, forms, and other content for toggling -->
		        <div class="collapse navbar-collapse navbar-ex1-collapse">
		          	'.$this->renderMenuContent().'
		        </div><!-- /.navbar-collapse -->
		</nav>';
		
	}
	
  
	$html .= '<div id="page-wrapper">';
	$html .= $this->getContent();
    $html .= '</div><!-- page-wrapper -->';
    
      $html .= '<hr>

      <footer>
        <p>&copy; Webkot.be / Administration </p>
      </footer>

	<!-- Additionnal JS footer -->
   '.system_render_tag($this->getJsFooterTags()) . '
    
   </body>
</html>';
		
		echo $html;
		
	}
	
	
	
	public function renderClosed(){
		
		$html = '<!DOCTYPE HTML>
<html lang="fr">
	<head>
		<meta charset="UTF-8">
		
	<meta http-equiv="Content-Language" content="fr-be">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
		
	<title>'.$this->getPageTitle().' :: '.$this->getPageSubtitle().'</title>';
		$html .= '
	<!-- Bootstrap -->
	<script src="http://code.jquery.com/jquery.js"></script>
	<script src="'.DIR_TEMPLATE.'sb-admin/js/bootstrap.js"></script>
    <!--<script src="'.DIR_TEMPLATE.'sb-admin/js/jquery.js"></script>-->
		
    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
		
    <!-- Bootstrap CSS -->
	<link href="'.DIR_TEMPLATE.'sb-admin/css/bootstrap.css" rel="stylesheet">
    <link href="'.DIR_TEMPLATE.'sb-admin/css/sb-admin.css" rel="stylesheet">
    <link href="'.DIR_TEMPLATE.'sb-admin/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <link href="'.DIR_TEMPLATE.'sb-admin/css/closed.css" rel="stylesheet">
     
    <link href="http://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css" rel="stylesheet">
   
   <!-- Additionnal CSS -->
   '.system_render_tag($this->getCssTags()) . '
		
   	<!-- Additionnal JS header -->
   '.system_render_tag($this->getJsHeaderTags()) . '
		
    </head>
		
  <body>';
		
		$html .= '<div class="container">
		    <div class="row">
		        <div class="col-md-4 col-md-offset-7">
		            <div class="panel panel-default">
		                <div class="panel-heading">
		                    <span class="fa fa-lock"></span> Webkot Admin Panel</div>
		                <div class="panel-body">
		                    <form class="form-horizontal" role="form" method="POST">
		                    <div class="form-group">
		                        <label for="form-login-input-username" class="col-sm-3 control-label">
		                            Username</label>
		                        <div class="col-sm-9">
		                            <input type="text" class="form-control" id="form-login-input-username" name="form-login-input-username" placeholder="username" required>
		                        </div>
		                    </div>
		                    <div class="form-group">
		                        <label for="form-login-input-password" class="col-sm-3 control-label">
		                            Password</label>
		                        <div class="col-sm-9">
		                            <input type="password" class="form-control" id="form-login-input-password" name="form-login-input-password" placeholder="Password" required>
		                        </div>
		                    </div>
							<input type="hidden" id="form-login-sended" name="form-login-sended" value="fromform" />
		                    <div class="form-group last">
		                        <div class="col-sm-offset-3 col-sm-9">
		                            <button type="submit" class="btn btn-success btn-sm">
		                                Sign in</button>
		                                 <button type="reset" class="btn btn-default btn-sm">
		                                Reset</button>
		                        </div>
		                    </div>
		                    </form>
		                </div>
		                <div class="panel-footer">
		                	Admin only !
						</div>
		            </div>
		        </div>
		    </div>
		</div>';
				
		
		$html .= '</body>
		</html>';
		
		echo $html;
		
	}
	
	
	
	
	private function renderMenuContent(){
		$code = '<ul class="nav navbar-nav navbar-user">';
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
		$code .= '</ul>';
		return $code;
	}
	
}