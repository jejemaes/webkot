<?php


class WidgetPub extends Widget implements iWidget{

	/**
	 * (non-PHPdoc)
	 * @see iWidget::__toString()
	 */
	public function __toString(){
		try{	
			$omanager = OptionManager::getInstance();
			$pub = $omanager->getOption('site-pub');	
			
			return $pub;
		}catch (Exception $e){
			return '<p class="text-danger">Erreur interne du Widget.</p>';
		}
	}

}