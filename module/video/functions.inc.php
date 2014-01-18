<?php


function video_html_list_video($modname, array $videos){
	$html = '<div class="col-lg-12">';
	$html .= '<h4>Liste des vid&eacute;os</h4>';
	$nbr = 4;
	$i=0;
	$html.= '<div class="row">';
	foreach ($videos as $video){
		$html .= '<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 video-margin-bottom">';
			$html .= '<div class="thumbnail">';
			$html .= '<div class="video-div-thumbnail">';
				$html .= '<a href="'.URLUtils::generateURL($modname, array("id" => $video->getId())).'">';
			  	$html .= '<img src="'.$video->getThumbnail().'" alt="video-thumbnail">';
			  	$html .= '<img src="'.DIR_MODULE . $modname . '/view/img/play_logo.png" class="video-play">';
			  	$html .= '</a>';
		  	$html .= '</div>';
			$html .= '<div class="caption">';
				$html .= '<h4>'.$video->getTitle().'</h4>';
				$html .= '<p>Date : '.$video->getPublishedDate().'<br clear>';
				$html .= 'Dur&eacute;e : '.$video->getDuration().' secondes</p>';
			$html .= '</div>';
			$html .= '</div>';
		$html .= '</div>';
		$i++;
		if($i % $nbr == 0){
			$html.= '</div><!-- end of row -->';
			$html.= '<div class="row">';
		}
		
	}
	$html.= '</div><!-- end of Row -->';
	$html .= '</div>';
	return $html;
}