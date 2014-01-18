<?php

class WidgetTop10 extends Widget implements iWidget{
	
	/**
	 * (non-PHPdoc)
	 * @see iWidget::__toString()
	 */
	public function __toString(){		
		$list = array();
		$dateY = system_get_begin_year();
		for($i=2002 ; $i<$dateY ; $i++){
			$ans =  $i . '-' . ($i+1) ;
			$list['Top10 de ' . $ans] = URLUtils::generateURL($this->getModuleName(),array("p" => "top10", "year" => $i));
		}
		$list['Top10 de Toujours'] = URLUtils::generateURL($this->getModuleName(),array("p" => "top10", "year" => "ever"));
			
		if(count($list) >= 10){
			$nb = (int) (count($list) / 2);
			$nb = $nb + (count($list) % 2);
			$html = '<div class="row">';
			$html .= '<div class="col-lg-6">';
			$html .= system_html_action_list(array_slice($list, 0, $nb),"list-unstyled");
			$html .= '</div>';
			$html .= '<div class="col-lg-6">';
			$html .= system_html_action_list(array_slice($list, $nb, (count($list) - $nb)), "list-unstyled");
			$html .= '</div>';
			$html .= '</div>';
		}else{
			$html = system_html_action_list($list);
		}
		
		return 	$html;
	}
	
}