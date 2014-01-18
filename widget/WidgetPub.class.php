<?php


class WidgetPub extends Widget implements iWidget{

	/**
	 * (non-PHPdoc)
	 * @see iWidget::__toString()
	 */
	public function __toString(){
		
		$omanager = OptionManager::getInstance();
		$pub = $omanager->getOption('site-pub');	
		
		return $pub;
	}

}