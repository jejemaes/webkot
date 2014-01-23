<?php

class WidgetNextEvent extends Widget implements iWidget{
	
	/**
	 * (non-PHPdoc)
	 * @see iWidget::__toString()
	 */
	public function __toString(){
		try{	
			$emanager = EventManager::getInstance();
			$list = $emanager->getEventsAfter(date('Y-m-d'), false, 0, 5);
			
			$html = "<ul>";
			for($i=0 ; $i<count($list) ; $i++){
				$event = $list[$i];
				$html .= "<li><a href=\"".URLUtils::generateURL($this->getModuleName(),array('p' => 'event', "id"=>$event->getId()))."\">" .$event->getName() . "</a>, le ".ConversionUtils::transformDate($event->getStart_time(),"d/m/Y").".</li>";
			}
			$html .= "</ul>";
			
			return $html;	
		}catch (Exception $e){
			return "Erreur interne du Widget ou du Module.";
		}
	}
	
}