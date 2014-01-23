<?php


class WidgetLastActivities extends Widget implements iWidget{

	/**
	 * (non-PHPdoc)
	 * @see iWidget::__toString()
	 */
	public function __toString(){
		try{	
			$omanager = OptionManager::getInstance();
			$nbr = $omanager->getOption('activity-widget-lastactivity');
			$m = ActivityManager::getInstance();
			$activities = $m->getLastActivity($nbr);
			
			$list = array();
			for($i=0 ; $i<count($activities) ; $i++){
				$acti = $activities[$i];
				$list[$acti->getTitle()] = URLUtils::generateURL($this->getModuleName(),array("p" => "activity", "id" => $acti->getId()));
			}
			return system_html_action_list($list);
		}catch (Exception $e){
			return '<p class="text-danger">Erreur interne du Widget.</p>';
		}
	}

}