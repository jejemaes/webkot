<?php



class EventView extends View implements iView{

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
		$template->setPageTitle($this->getModule()->getDisplayedName());
	}
	
	/**
	 * display the page for the given Event
	 * @param Event $event : the Event Object to display
	 */
	public function pageEvent(Event $event){
		$HTML .= $this->headerView();
		$HTML .= echogito_html_page_event($this->getModule()->getName(),$event);
		$this->getTemplate()->setPageSubtitle($event->getName());
		$this->configureLayout('page-event',$HTML);
	}
	
	public function pageWeekEvents(array $events, $message){
		$HTML = $message;
		
		$HTML .= $this->headerView();
		
		$HTML .= '<br><br>';
		$HTML .= '<ul class="nav nav-tabs" id="echogito-tabpanel">';
		foreach ($events as $key => $value){
			$HTML .= '<li><a href="#'.$key.'" data-toggle="tab">'.echogito_translate_day_name($key).'</a></li>';
		}
  		$HTML .= '</ul>';

		$HTML .= '<div class="tab-content" style="min-height:400px;">';
		foreach ($events as $key => $value){
			$HTML .= '<div class="tab-pane" id="'.$key.'">';
				$HTML .= '<div class="row">';
				if(count($value) > 0){
					//foreach ($value as $event){
					for($i=0 ; $i<count($value) ; $i++){
						$event = $value[$i];
						$HTML .= echogito_html_media_event($this->getModule()->getName(),$event, "col-lg-6 col-md-6 col-sm-12 col-xs-12");
						if($i % 2 != 0){
							$HTML .= '</div><hr><div class="row">';
						}
					}
				}else{
					$HTML .= '<div class="col-lg12 col-md-12 col-sm-12 col-xs-12">Il n\'y a pas d\'&eacute;v&eacute;venement ce jour-la. On va donc devoir "&eacute;tudier" ...</div>';
				}
				$HTML .= '</div>';
			$HTML .= '</div>';
		}
		$HTML .= '</div>';

		$HTML .= '<script>
		  $(function () {
		    $(\'#echogito-tabpanel a[href="#'.date('l').'"]\').tab(\'show\')
		  })
		</script>';
		$this->getTemplate()->setPageSubtitle("Cette semaine");
		$this->configureLayout('page-echogito',$HTML);
	}
	
	/**
	 * built the html code for the list of coming Event
	 * @param array $events : array of array of Event Object (sorted by echogito_sort_by_month())
	 */
	public function pageLaterEvents(array $list, $count, $desc, $page){
		
		$HTML .= $this->headerView();
		
		$HTML .= '<br>';
		$HTML .= '<div class="col-lg-10 col-lg-offset-1">';
		foreach ($list as $key => $events){
			$HTML .= '<h4>'.echogito_translate_month_name(ConversionUtils::transformDate($key, "F"))." ".substr($key,0,4).'</h4>';
			//$HTML .= '<table class="table table-hover">';
			$HTML .= '<div class="col-lg-12">';
			for($i=0 ; $i<count($events) ; $i++){
				$event = $events[$i];
				//$HTML .= '<tr>';
				$HTML .= '<div class="row">';
				$HTML .= '<div class="col-lg-3"><span class="text-muted">'.ConversionUtils::transformDate($event->getStart_time(),"l d/m/Y").'</span></div>';
				$HTML .= '<div class="col-lg-8">'.$event->getName();
				if($event->getCategoryid()){
					$HTML .= '<br><span style="color:'.$event->getCategorycolor().'"><i class="fa fa-folder-open"></i> '.$event->getCategoryname().'</span>';
				}
				$HTML .= '</div>';
				$HTML .= '<div class="col-lg-1"><a class="btn btn-primary btn-sm" href="'.URLUtils::generateURL($this->getModule()->getName(), array("p"=>"event", "id" => $event->getId())).'">&#187; Lire plus</a></div>';
				//$HTML .= '</tr>';
				$HTML .= '</div><hr>';
			}
			$HTML .= '</div>';	
		}
		$HTML .= '</div>';
		
		$HTML .= system_html_pagination($this->getModule()->getName(), array("p"=>"later"),$count,$desc,$page, "&eacute;v&eacute;nements");
		
		$this->getTemplate()->setPageSubtitle("Ev&eacute;nements &agrave; venir");
		$this->configureLayout('page-echogito',$HTML);
	}
	
	
	public function pageForm($message){
		$HTML .= $message;
		$HTML .= echogito_html_double_form($this->getModule()->getName(), $this->getTemplate());
		$this->getTemplate()->setPageSubtitle("Ajouter un &eacute;v&eacute;nement");
		$this->configureLayout('page-form',$HTML);
	}
	
	
	public function pageCalendarAge(){
		$HTML .= $this->headerView();
		$HTML .= '<br>';
		$HTML .= '<iframe src="http://www.google.com/calendar/embed?showTitle=0&amp;height=800&amp;mode=MONTH&amp;wkst=2&amp;bgcolor=%23FFFFFF&amp;src=age%40fundp.ac.be&amp;color=%23A32929&amp;src=ffialf5uitb78e3a4ailq2u51o%40group.calendar.google.com&amp;color=%23AB8B00&amp;src=r1bunu9fqk7trrk2c27dg2e4a0%40group.calendar.google.com&amp;color=%23B1440E&amp;src=regionales.age%40gmail.com&amp;color=%230D7813&amp;src=laobrn2mf85ilagvbbnn34fklg%40group.calendar.google.com&amp;color=%23705770&amp;src=1cs2q4j7qpg78ok2dup7jqu5m4%40group.calendar.google.com&amp;color=%232952A3&amp;src=3krdj656r7h5dlkgi7eja9oqo0%40group.calendar.google.com&amp;color=%232952A3&amp;src=ihfd4oa9i3fam6jcllphem5bvg%40group.calendar.google.com&amp;color=%232952A3&amp;src=bf804v20e8jr95q6v8chlm4i6s%40group.calendar.google.com&amp;color=%232952A3&amp;src=8n0fsgvlfcdfp80gbdifv176hk%40group.calendar.google.com&amp;color=%232952A3&amp;src=8alnmb61lbslviob94okv28v6k%40group.calendar.google.com&amp;color=%232952A3&amp;src=nn6l6pi6ja7b50q5g60rrbjpsc%40group.calendar.google.com&amp;color=%232952A3&amp;src=cbvipid1pghp5v5ucc2tdukdno%40group.calendar.google.com&amp;color=%232952A3&amp;src=2le23msratjh6j79tmmksj3nm4%40group.calendar.google.com&amp;color=%232952A3&amp;src=u1c3or7lp6rgr41a7dm2fqolgo%40group.calendar.google.com&amp;color=%232952A3" style="border-width:0" width="100%" height="800" frameborder="0" scrolling="no"></iframe>';
		$this->getTemplate()->setPageSubtitle("Calendrier AGE");
		$this->configureLayout('page-echogito',$HTML);
	}
	
	/**
	 * built the header of the view (image + buttons)
	 * @return string : html code of the header of the view
	 */
	private function headerView(){
		$HTML .= '<br>';
		$HTML .= '<div class="row">';
		$HTML .= '<div class="col-lg-8">';
		$HTML .= '<img src="'.DIR_MODULE.$this->getModule()->getLocation().'view/img/echogito.png" alt="Logo" class="img-responsive" style="margin:auto;">';
		$HTML .= '</div>';
		$HTML .= '<div class="col-lg-4">';
		$HTML .= '<p class="text-center"><a href="'.URLUtils::generateURL($this->getModule()->getName(), array()).'" class="btn btn-default"><i class="fa fa-home"></i> Home</a> ';
		$HTML .= ' <a href="'.URLUtils::generateURL($this->getModule()->getName(), array("action" => "submit")).'" class="btn btn-primary"><i class="fa fa-plus"></i> Ajouter</a></p>';
		$HTML .= '<p class="text-center"><a href="'.URLUtils::generateURL($this->getModule()->getName(), array("p" => "later")).'" class="btn btn-success"><i class="fa fa-list"></i> Ev&eacute;nements &agrave; venir</a></p>';
		$HTML .= '<p class="text-center"><a href="'.URLUtils::generateURL($this->getModule()->getName(), array("p" => "calendar")).'" class="btn btn-info"><i class="fa fa-calendar"></i> Calendrier AGE</a></p>';
		$HTML .= '</div>';
		$HTML .= '</div>';
		return $HTML;
	}

}