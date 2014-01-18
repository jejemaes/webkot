<?php



class WebkotView extends View implements iView{
	
	
	
	
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
		//$template->addStyle('<link href="'.$viewdirectory.'css/style.css" rel="stylesheet"/>');
	
		$template->setPageTitle($this->getModule()->getDisplayedName());
	}
	
	
	
	public function pageActualWebkotTeam(array $team,$texts){
		$HTML = '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
		foreach ($texts as $key => $value){
			$HTML .= '<h4>'.$key.'</h4>';
			$HTML .= '<div style="margin-left:20px;">'.$value.'</div>';
		}
		$HTML .= '</article>';
		$HTML .= '<h4>Qui sommes-nous ?</h4>';
		$HTML .= webkot_html_team($team);
		$HTML .= '</div>';
		
		$this->configureLayout('page-actual',$HTML);
		$this->getTemplate()->setPageSubtitle("L'&eacute;quipe actuelle");
	}
	
	
	public function pageOldWebkotTeam(array $oldteams){
		$HTML = "";
		$i = 0 ;
		while($i<(count($oldteams)-1)){
			$mem = $oldteams[$i];
			$yearBeg = $mem->getYear();
			$yearEnd = $mem->getYear();
			$j = $i;
			//$HTML .= '<br>'.'== i='.$i . '   j=' . $j . '  '.$yearBeg . '  /'.count($oldteams).'<br>';
			while($yearBeg == $yearEnd && (($j)<(count($oldteams)-1))){
				$j++;
				$mem = $oldteams[$j];
				$yearEnd = $mem->getYear();
			}
		
			$p = $j-$i;
			if($j == (count($oldteams)-1)){
				$p = $j-$i+1;
			}
			$HTML .= '<div class="content-box"><a id="team'.$yearBeg.'"></a><h4>La Team '.$yearBeg.'</h4>';
			$HTML .= '<hr>';
			$HTML .= webkot_html_team(array_slice($oldteams,$i,($p)));
			$HTML .= '</div>';
				
			$i = $j;
		}
		$this->configureLayout('page-old',$HTML);
		$this->getTemplate()->setPageSubtitle("Les Vieux du Webkot");
	}
	

}
	
