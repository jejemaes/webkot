<?php


class VideoView extends View implements iView{

	
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
		$this->getTemplate()->setLayout($lname);
		$this->getTemplate()->setContent($content);
	}
	
	/**
	 * Set some parameters for the Template : add css style, js code, ...
	 */
	private function configureTemplate(){
		$viewdirectory = DIR_MODULE . $this->getModule()->getLocation() . 'view/';
		// add module css
		$template = $this->getTemplate();
		$template->addStyle('<link href="'.$viewdirectory.'css/style.css" rel="stylesheet"/>');
	
		$template->setPageTitle($this->getModule()->getDisplayedName());
	}
	
	
	public function pageList(array $videos, $count, $page, $desc){
		$HTML = '<div class="row">';
		$HTML .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
		$HTML .= video_html_list_video($this->getModule()->getName(), $videos);
		$HTML .= system_html_pagination($this->getModule()->getName(), array(),$count,$desc,$page, "vid&eacute;os");
		$HTML .= '</div>';
		$HTML .= '</div>';
		$this->configureLayout('page-list',$HTML);
		$this->getTemplate()->setPageSubtitle("Liste des vid&eacute;os");
	}
	
	
	
	public function pageVideo(Video $video){
		$HTML = '<div class="row">';
		$HTML .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
		$HTML .= '<h4>'.$video->getTitle().'</h4>';
		$HTML .= '<iframe class="video-youtube-iframe" width="640" height="480" src="//www.youtube.com/embed/'.$video->getId().'" frameborder="0" allowfullscreen></iframe>';
		$HTML .= '<div class="row">';
        $HTML .= '<div class="col-lg-5 col-md-offset-2">
          <h5><i class="fa fa-align-right"></i> Description</h5>
	          <p>'.$video->getDescription().'</p>
	        </div>
	        <div class="col-lg-3">
	          <h5><i class="fa fa-info-circle"></i> Informations</h5>
	          <p>Duree : '.$video->getDuration().' secondes</p>
	          <p>Vues : '.$video->getView().'</p>
	          <p>Date de publication : '.$video->getPublishedDate().'</p>
	        </div>';
     	$HTML .= '</div>';
		$HTML .= '</div>';
		$HTML .= '</div>';
		
     	$this->getTemplate()->setPageSubtitle($video->getTitle());
		$this->configureLayout('page-video',$HTML);
	}
	
	
}
