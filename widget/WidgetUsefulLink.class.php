<?php

class WidgetUsefulLink extends Widget implements iWidget{
	
	/**
	 * (non-PHPdoc)
	 * @see iWidget::__toString()
	 */
	public function __toString(){
		$tab = array ();
		$tab ["L'AGE Namur"] = "http://www.age-namur.be";
		$tab ["L'Echogito"] = "https://www.facebook.com/echogito?fref=ts";
		$tab ["L'AKAP"] = "#";
		$tab ["Contactez-nous !"] = "index.php?mod=page&id=contact";
		return system_html_action_list ( $tab );
	}
	
}