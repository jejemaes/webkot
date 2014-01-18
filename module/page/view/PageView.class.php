<?php

class PageView extends View implements iView{

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
		$template = $this->getTemplate();
		$template->setPageSubtitle($this->getModule()->getDisplayedName());
	}


	
	public function pagePage(Page $page, Message $message, $additionnalcontent){	
		$HTML .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
		$HTML .= $page->getContent();
		$HTML .= $message;
		$HTML .= $additionnalcontent;
		$HTML .= '</div>';
		$this->configureLayout('page-page',$HTML);
		$this->getTemplate()->setPageSubtitle($page->getTitle());
	}
	
	
	public function pageListPage(array $pages){
		$HTML = '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
		$HTML .= '<ul>';
		foreach ($pages as $page){
			$HTML .= '<li><a href="'.URLUtils::generateURL($this->getModule()->getName(), array("id" => $page->getId())).'">'.$page->getTitle().'</a></li>';
		}
		$HTML .= '</ul>';
		$HTML .= '</div>';
		
		$this->configureLayout('page-list',$HTML);
		$this->getTemplate()->setPageSubtitle("Liste des pages");
	}

	
}
