<?php


class LinkView extends View implements iView{

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
		$template->setPageTitle($this->getModule()->getDisplayedName());
	}



	public function pageLink(array $links){
		$HTML = '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
		$i=0;
		while($i < count($links)){
			$link = $links[$i];
			$category = $link->getCategory();
			$cat = $link->getCategory();
			
			$HTML .= '<h4>' . $category . '</h4>';
			$HTML .= '<table class="table table-hover">';
			while($category == $cat && ($i < count($links))){
				$HTML .= '<tr><td>'.$link->getName().'</td><td><a href="'.$link->getUrl().'" target="_blank">'.$link->getUrl().'</a></td></tr>';
				$i++;
				if($i < count($links)){
					$link = $links[$i];
					$cat = $link->getCategory();
				}
			}
			$HTML .= '</table>';
		}
		$HTML .= '</div>';
		$this->configureLayout('page-link',$HTML);
		$this->getTemplate()->setPageSubtitle("La liste des liens");
	}

}

