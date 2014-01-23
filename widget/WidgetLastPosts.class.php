<?php

class WidgetLastPosts extends Widget implements iWidget{
	
	/**
	 * (non-PHPdoc)
	 * @see iWidget::__toString()
	 */
	public function __toString(){
		try{			
			$omanager = OptionManager::getInstance();
			$nbr = $omanager->getOption('blog-widget-lastpost');
			
			$manager = BlogManager::getInstance();
			$list = $manager->getLastListPost($nbr);
			
			$html = "<ul>";
			for($i=0 ; $i<count($list) ; $i++){
				$post = $list[$i];
				$html .= "<li><a href=\"".URLUtils::generateURL($this->getModuleName(),array("post"=>$post->getId()))."\">" .$post->getTitle() . "</a></li>";
			}
			$html .= "</ul>";
			return $html;	
		}catch (Exception $e){
			return '<p class="text-danger">Erreur interne du Widget.</p>';
		}
	}
	
}