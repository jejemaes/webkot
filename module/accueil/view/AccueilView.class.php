<?php


class AccueilView extends View implements iView{


	/**
	 * Constructor
	 * @param iTemplate $template
	 */
	public function __construct(iTemplate $template, Module $module){
		parent::setTemplate($template);
		parent::setModule($module);
		$this->configureTemplate();
	}
	
	/**
	 * Set up the Layout according to the config file of the module, and init its content
	 * @param String $state : the state of the module which define the layout
	 * @param String $content : the html code of the content
	 */
	private function configureLayout($state, $content){
		$lname = $this->getModule()->getLayout($state);
		$this->getTemplate()->addJSFooter('<script src="'.DIR_MODULE.$this->getModule()->getName().'/view/js/home.js"></script>');
		$this->getTemplate()->setLayout($lname);
		$this->getTemplate()->setContent($content);
		$this->getTemplate()->setPageTitle($this->getModule()->getDisplayedName());
	}
	
	/**
	 * Set some parameters for the Template : add css style, js code, ...
	 */
	private function configureTemplate(){
		$viewdirectory = DIR_MODULE . $this->getModule()->getLocation() . 'view/';
		// add module css
		$template = $this->getTemplate();
		$template->addStyle('<link href="'.$viewdirectory.'css/style.css" rel="stylesheet"/>');
	
		$template->setPageSubtitle($this->getModule()->getDisplayedName());
	}
	
	
	public function pageHome(array $slides, array $activities, array $listComm, array $posts, Video $video){
		
		if(count($slides) > 0){		
			$HTML = '<div id="accueil-carousel" class="carousel slide">';
			$HTML .= '<ol class="carousel-indicators">';
			for($i=0 ; $i<count($slides) ; $i++){
				$class = "";
				if($i == 0){
					$class = ' class="active"';
				}
				$HTML .= '<li data-target="#accueil-carousel" data-slide-to="'.($i+1).'" '.$class.'></li>';
			}
			$HTML .= '</ol>';
			
			$HTML .= '<!-- Wrapper for slides --><div class="carousel-inner">';
			for($i=0 ; $i<count($slides) ; $i++){
				$slide = $slides[$i];
				$class = "";
				if($i == 0){
					$class = ' active';
				}
				$HTML .= '<div class="item '.$class.'">';
	            $HTML .= '<div class="fill" style="background-image:url(\'' . $slide->getPathimg().'\');"></div>';
	            $HTML .= '<div class="carousel-caption">';
	            $HTML .= '<h1>' . $slide->getTitle() . '</h1>';
	            $HTML .= '<p>' . $slide->getDescription() . '</p>';
	            $HTML .= '</div>';
	          	$HTML .= '</div>';
			}
			$HTML .= '</div>';
			
			$HTML .= '<!-- Controls -->
	        <a class="left carousel-control" href="#accueil-carousel" data-slide="prev">
	          <span class="icon-prev"></span>
	        </a>
	        <a class="right carousel-control" href="#accueil-carousel" data-slide="next">
	          <span class="icon-next"></span>
	        </a>';
			
			$HTML .= '</div>';
		}

		
		if(count($activities) > 0){	
			// first activity
			$activity = $activities[0];
			$picture = accueil_utils_get_random_picture($activity);
			// infos
			$nbrCom = accueil_count_comment_actipict($activity->getPictures());
			$nbrPict = count($activity->getPictures());
			$comment = ($nbrCom <= 1 ) ? "commentaire" : "commentaires";
			$pictword = ($nbrPict <= 1 ) ? "photo" : "photos";
			$view = ($activity->getViewed() <= 1 ) ? "vue" : "vues";
			$infos = '<small>';
			$infos .= '<i class="fa fa-calendar"></i> Le '.ConversionUtils::dateToDateFr($activity->getDate());
			$infos .= ' | <i class="fa fa-comment"></i> '.$nbrCom.' '.$comment;
			$infos .= ' | <i class="fa fa-camera"></i> '.$nbrPict.' '.$pictword;
			$infos .= ' | <i class="fa fa-eye-open"></i> '.$activity->getViewed().' '.$view;
			$infos .= '</small>';
			
			$HTML .= '  <div class="section well">
	      <div class="container">
	        <div class="row">
	          <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
	            <h2>'.$activity->getTitle().'</h2>
	            '.$infos.'
	            <p>'.$activity->getDescription().'</p>
	            <a class="btn btn-primary pull-right" href="'.URLUtils::generateURL('activity', array("p" => "activity", "id" => $activity->getId())).'">Voir <i class="fa fa-angle-right"></i></a>
	            <div class="clearfix"></div><br>
	          </div>
	          <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
	            <a href="'.URLUtils::generateURL('activity', array("p" => "activity", "id" => $activity->getId())).'"><img class="img-responsive accueil-img-hover" src="'.DIR_PICTURES . $activity->getDirectory() . "/" .$picture->getFilename().'"></a>
	          </div>
	        </div><!-- /.row -->
	      </div><!-- /.container -->
	    </div><!-- /.section-colored -->';
			
			
			$HTML .= '<div class="section">
	      <div class="container">
	        <div class="row">';
			
			for($i=1 ; $i < count($activities) ; $i++){
				$activity = $activities[$i];
				$picture = accueil_utils_get_random_picture($activity);
				// infos
				$nbrCom = accueil_count_comment_actipict($activity->getPictures());
				$nbrPict = count($activity->getPictures());
				$comment = ($nbrCom <= 1 ) ? "commentaire" : "commentaires";
				$pictword = ($nbrPict <= 1 ) ? "photo" : "photos";
				$view = ($activity->getViewed() <= 1 ) ? "vue" : "vues";
				$infos = '<small>';
				$infos .= '<i class="fa fa-calendar"></i> Le '.ConversionUtils::dateToDateFr($activity->getDate());
				$infos .= ' | <i class="fa fa-comment"></i> '.$nbrCom.' '.$comment;
				$infos .= ' | <i class="fa fa-camera"></i> '.$nbrPict.' '.$pictword;
				$infos .= ' | <i class="fa fa-eye"></i> '.$activity->getViewed().' '.$view;
				$infos .= '</small>';
				
				$HTML .= '<div class="col-lg-4 col-md-4 col-xs-12">';
	            //$HTML .= '<a href="'.URLUtils::generateURL('activity', array("p" => "activity", "id" => $activity->getId())).'"><img class="img-responsive img-rounded" style="margin:auto;" src="'.DIR_PICTURES . $activity->getDirectory() . "/small/" .$picture->getFilename().'"></a>';
	            $HTML .= '<h3><i class="fa fa-camera"></i> '.$activity->getTitle().'</h3>';
	            $HTML .= $infos;
	            $HTML .= '<p>'.substr($activity->getDescription(),0,300).'...</p>';
	            $HTML .= '<a class="btn btn-primary pull-right" href="'.URLUtils::generateURL('activity', array("p" => "activity", "id" => $activity->getId())).'">Voir <i class="fa fa-angle-right"></i></a>';
	            $HTML .= '<div class="clearfix"></div>';
	          	$HTML .= '</div>';
			}
	
	        $HTML .= '</div><!-- /.row -->
	      </div><!-- /.container -->
	    </div><!-- /.section -->';
		}
		
		if(!empty($listComm)){		
			$HTML .= '<div class="section well">
	      <div class="container">
	        <div class="row">
	          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
	            <h2>Les derni&egrave;res photos comment&eacute;es</h2>
	            <hr>
	          </div>';
			
			$HTML .= activity_html_page_lastcomm('activity', $listComm);
				
	         
	        $HTML .= '</div><!-- /.row -->
	      </div><!-- /.container -->
	    </div><!-- /.section -->';
	        
	        $HTML .= activity_get_js_page_overlay('Dernieres photos comment&eacute;es', 'activity', true);
	        	  
		}
		
		
        if($video->getId()){    	
			$HTML .= '  <div class="section">
	      <div class="container">
	        <div class="row">
	          <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
	            <h2>'.$video->getTitle().'</h2>
	            <p>'.$video->getDescription().'</p>
	            <ul>
	              <li>Dur&eacute;e : '.$video->getDuration().'</li>
	              <li>Publi&eacute;e le '.$video->getPublishedDate().'</li>
	              <li>Vues : '.$video->getView().'</li>
	            </ul>
	          </div>
	          <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
	            <iframe width="100%" height="360" src="//www.youtube.com/embed/'.$video->getId().'" frameborder="0" allowfullscreen></iframe>
	          </div>
	        </div><!-- /.row -->
	      </div><!-- /.container -->
	    </div><!-- /.section-colored -->';
        }
		
		$this->configureLayout('page-home',$HTML);
	}
	
	
	
}
