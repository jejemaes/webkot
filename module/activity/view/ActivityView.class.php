<?php


class ActivityView extends View implements iView{
	
	/**
	 * Constructor : set the 2 variables in the Object
	 * @param iTemplate $template : the template Object of the current html page
	 * @param Module $module : the current module
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
	
	
	
	/**
	 * built the page for the list of activities
	 * @param array $list
	 */
	public function pageList(array $list){
		$HTML = '<div class="row">';
		$HTML .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
		$HTML .= activity_html_page_activity_list($list, $this->getModule()->getName());
		$HTML .= '</div>';
		$HTML .= '</div>';
		
		$this->configureLayout('page-list',$HTML);
		$this->getTemplate()->setPageSubtitle("Liste des activit&eacute;s");
	}
	
	
	/**
	 * built the page for the given activity
	 * @param Activity $activity
	 */
	public function pageActivity(Activity $activity){
		$HTML = '<div class="row">';
		$HTML .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
		
		$HTML .= activity_html_page_activity($activity, $this->getModule(), $this->getTemplate(), true);	
		$HTML .= '</div>';
		$HTML .= '</div>';
		
		//$js = activity_get_js_page_overlay($activity->getTitle(), 'activity', true);
		//$this->getTemplate()->addJsFooter($js);
		
		$this->getTemplate()->setPageSubtitle($activity->getTitle());
		$this->configureLayout('page-activity',$HTML);
	}
	
	

	/**
	 * built the page for the given picture
	 * @param Module $module : the current module
	 * @param Activity $activity : the Activity related to the Picture
	 * @param Picture $picture : the Picture which must be exposed
	 * @param User $profile : User Object or null (if no one is connected)
	 * @param array $album : the informations about the collection the given Picture belong (not necessarily the activity)
	 * @param array $orders : the url of the current, next and previous picture of the album
	 * @param array $actions : the action and the url allowed on this picture
	 */
	public function pagePicture(Module $module, Activity $activity, Picture $picture, $profile, array $album, array $orders, array $actions){
		system_load_plugin(array('social-ring' => array("template"=>$this->getTemplate(), "level" => $activity->getLevel(), "appId" => OptionManager::getInstance()->getOption("facebook-appid"), "url" => URL . URLUtils::generateURL($module->getName(), array("p"=>"picture", "id"=>$picture->getId())))));
		
		$HTML .= activity_html_page_picture($module, $activity, $picture, $profile, $album, $orders, $actions, true);
		
		$this->configureLayout('page-picture',$HTML);
		$this->getTemplate()->setPageSubtitle($album['title']);

	}
	
	
	public function pageTop10(array $mostView, array $mostCommented, $year){
		$HTML = '<div class="row">';
		$HTML .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
		$HTML .= activity_html_page_top10($this->getModule(), $mostView, $mostCommented, $year);
		$HTML .= '</div>';
		$HTML .= '</div>';
		$HTML .= activity_get_js_page_overlay('Top10 des photos de ' .$year, $this->getModule()->getName(), true);
		
		$this->configureLayout('page-archive', $HTML);
		$this->getTemplate()->setPageSubtitle("Top10 de " . $year);
	}
	
	/**
	 * built the archive page for the given list
	 * @param array $list : list of Activity to display
	 * @param int $count : the total number of Activity for these archive
	 * @param int $page : the actual page (in the pagination)
	 * @param int $desc : the offset (number of past activity)
	 */
	public function pageArchive($list, $count, $page, $desc, $year){
		$HTML = '<div class="row">';
		$HTML .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
		$HTML .= activity_html_page_media_activity($list, $this->getModule());
		$HTML .= system_html_pagination($this->getModule()->getName(), array("p" => "archive", "year" => $year),$count,$desc,$page, "activit&eacute;");
		$HTML .= '</div>';
		$HTML .= '</div>';
		
		$this->configureLayout('page-archive', $HTML);
		$this->getTemplate()->setPageSubtitle("Archives " . $year . "-" . ($year+1));
	}
	
	/**
	 * built the archive page for the given list
	 * @param array $list : list of Activity to display
	 * @param int $count : the total number of Activity for these archive
	 * @param int $page : the actual page (in the pagination)
	 * @param int $desc : the offset (number of past activity)
	 */
	public function pageArchiveOld(){
		$HTML = '<div class="row">';
		$HTML .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
		//$HTML .= '<h3>Les activit&eacute;s 2002-2003</h3>';
		$HTML .= '<img src="'.DIR_MODULE.$this->getModule()->getLocation().'view/img/webkot_logo_v2.png" alt="Logo v2" class="img-center" />';
		$HTML .= '<br><br>';
		$HTML .= '<h4><a href="'.ACTIVITY_DIR_ARCHIVE2002.'rep6/index.html">Partie 1 <small> (de Ouverture des régionales à 12h de la BDThèque)</a></a></small></h4><hr>';
		$HTML .= '<h4><a href="'.ACTIVITY_DIR_ARCHIVE2002.'rep5/index.html">Partie 2 <small> (de Roi des bleus BW à Concours dessin au NDLR)</a></small></h4><hr>';
		$HTML .= '<h4><a href="'.ACTIVITY_DIR_ARCHIVE2002.'rep4/index.html">Partie 3 <small> (de Bunker Chimie à Cortège de la Saint Nicolas)</a></small></h4><hr>';
		$HTML .= '<h4><a href="'.ACTIVITY_DIR_ARCHIVE2002.'rep3/index.html">Partie 4 <small> (de Bal de la Saint Nicolas à Souper Clôture Festival)</a></small></h4><hr>';
		$HTML .= '<h4><a href="'.ACTIVITY_DIR_ARCHIVE2002.'rep2/index.html">Partie 5 <small> (de Soirée Vice Versa Bio à Souper de Passation Info)</a></small></h4><hr>';
		$HTML .= '<h4><a href="'.ACTIVITY_DIR_ARCHIVE2002.'rep1/index.html">Partie 6 <small> (de Bunker Médecine à Bunker Forfaitaire CIR)</a></small></h4><hr>';
		$HTML .= '</div>';
		$HTML .= '</div>';
	
		$year = 2002;
		$this->configureLayout('page-archive', $HTML);
		$this->getTemplate()->setPageSubtitle("Archives " . $year . "-" . ($year+1));
	}
	
	
	/**
	 * My Picture page : display the list of My Picture
	 * @param unknown $list
	 * @param unknown $count
	 * @param unknown $desc
	 * @param unknown $page
	 */
	public function pageListMyPicture($list, $count, $desc, $page){	
		$HTML = '<div class="row">';
		$HTML .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
		$HTML .= '<div id="activity-mypicture-message"></div>';
		$HTML .= '<table class="table">';
		for($i=0 ; $i<count($list) ; $i++){
			$pict = $list[$i];
			$HTML .= '<tr id="activity-mypicture-'.$pict->getId().'">';
			$class = ($pict->getIscensured() ? "activity-img-censured" : "img-polaroid");
			//$HTML .= '<td><a href="javascript:activityMakeModal('.$pict->getId().')" class="'.ACTIVITY_JS_CLASS_CALL_ANCHOR.'"><img src="'.activity_path_thumbnail($this->getModule()->getName()."/", $pict->getDirectory(), $pict->getFilename()).'" class="'.$class.' activity-img-hover"></a></td>';
			$HTML .= '<td><a href="'.URLUtils::generateURL($this->getModule()->getName(), array("p"=>"mypicture","id"=>$pict->getId())).'" class="'.ACTIVITY_JS_CLASS_CALL_ANCHOR.'"><img src="'.activity_path_thumbnail($this->getModule()->getName()."/", $pict->getDirectory(), $pict->getFilename()).'" class="'.$class.' activity-img-hover"></a></td>';
				
			$HTML .= '<td><b>'.$pict->getTitle().'</b>, le '.ConversionUtils::dateToDateFr($pict->getDate());
			if($pict->getAddeddate()){
				$HTML .= '<br><i>Ajout&eacute;e le '.ConversionUtils::timestampToDatetime($pict->getAddeddate()).'</i></td>';
			}
			$HTML .= '<td><a href="javascript:activityDeleteFavorite(\'server.php?module='.$this->getModule()->getName().'&action=delfav\','.$pict->getId().');" class="btn btn-danger"><i class="fa fa-trash-o"></i> Supprimer</a>  ';
			$HTML .= '<a target="_blank" href="'.URLUtils::builtServerUrl($this->getModule()->getName(),array("action" => "download", "id" => $pict->geTId())).'" class="btn btn-default"><i class="fa fa-download"></i> Download</a>';
				
			$HTML .= '</td>' ;
			$HTML .= '</tr>';
		}
		$HTML .= '</table>';
		$HTML .= system_html_pagination($this->getModule()->getName(), array("p" => "mypictures"),$count,$desc,$page, "photos");
			
		$HTML .= '</div>';
		$HTML .= '</div>';
		// built the js code for the overlay
		$HTML .= activity_get_js_page_overlay('Mes Photos', $this->getModule()->getName(), true);
		
		$this->configureLayout('page-archive',$HTML);
		$this->getTemplate()->setPageSubtitle("Mes Photos");
	}
	
	/**
	 * display the page with the given pictures (not related to a particular activity)
	 * @param array $censures : array of Picture Object
	 */
	public function pageListCensured(array $censures){
		$HTML = '<div class="row">';
		$HTML .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
		
		$HTML .= '<h4>Liste des photos censur&eacute;es</h4>';
		
		// infos
		$nbrPict = count($censures);
		$picture = ($nbrPict == 0 ) ? "photo" : "photos";
		$HTML .= '<small>';
		$HTML .= '<i class="fa fa-calendar"></i> Le '.ConversionUtils::dateToDateFr(date('Y-m-d'));
		$HTML .= ' | <i class="fa fa-camera"></i> '.$nbrPict.' '.$picture;
		$HTML .= '</small>';
		
		$HTML .= '<p>Il est interdit de partager cette page ; on est peut-etre pas cens&eacute; les garder. Mais il n\'est pas interdit de rire un peu ;)<br></p>';
		$HTML .= '</div>';
		
		$HTML .= '<table class="activity-table-center">';
		$size = count($censures);
		for($i = 0; $i < $size;){
			$HTML .= '<tr>';
			for($j=0; $j<5; $j++, $i++){
				$HTML .= '<td>';
				if($i<$size){
					$currentPict = $censures[$i];
					$path = activity_path_thumbnail($this->getModule()->getLocation(), $currentPict->getDirectory(),$currentPict->getFilename());
					
					$href = URLUtils::generateURL($this->getModule()->getName(), array("p" => "censures", "index" => ($i+1)));
					
					$HTML .= '<a href="'.$href.'" class="'.ACTIVITY_JS_CLASS_CALL_ANCHOR.'"><img class="img-responsive activity-img-hover" src="' . $path .'" alt="Photo '.($i+1) . '/'. $size .'" title="'.($i+1) . '/'. $size .'" /></a>';
				}
				$HTML .= '</td>';
			}
			$HTML .= '</tr>';
		}
		$HTML .= '</table>';
		
		$HTML .= '</div>';
		$HTML .= '</div>';
		
		$HTML .= activity_get_js_page_overlay("Les censur&eacute;es", $this->getModule()->getName(), true);
		
		$this->getTemplate()->setPageSubtitle("Censures");
		$this->configureLayout('page-activity',$HTML);
	}
	
	
	
	public function pageLastComm(array $pictures){
		$HTML = '<div class="row">';
		$HTML .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
		$HTML .= '<h4>Liste des dernieres photos comment&eacute;es</h4>';
				
		$HTML .= activity_html_page_lastcomm($this->getModule()->getName(), $pictures);

		$HTML .= '</div>';
		$HTML .= '</div>';
		
		$this->getTemplate()->setPageSubtitle("Derniers commentaires");
		$this->configureLayout('page-archive',$HTML);
	}
	
}