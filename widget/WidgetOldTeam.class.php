<?php

class WidgetOldTeam extends Widget implements iWidget{
	
	/**
	 * (non-PHPdoc)
	 * @see iWidget::__toString()
	 */
	public function __toString(){		
		try{
			// create the liste of old team
			$managerW = WebkotteurManager::getInstance();
			$listYears = $managerW->getListYear();
		}catch(SQLException $sqle){
			$listYears = array();
		}catch(DatabaseException $dbe){
			$listYears = array();
		}
		$list = array();
		for($i=0 ; $i<count($listYears) ; $i++){
			$ans = $listYears[$i];
			$list['Team ' . $ans] = URLUtils::generateURL($this->getModulename(), array("p" => "vieux#team".$ans));
		}
		return system_html_action_list($list);	
	}
	
}