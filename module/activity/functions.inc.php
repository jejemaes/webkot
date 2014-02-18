<?php


/**
 * built the html code for the given Activity
 * @param Activity $activity : the Activity to display (it contains the list of the Picture for the gallery)
 * @param Module $module : the current Module
 * @return string : the html code
 */
function activity_html_page_activity(Activity $activity, Module $module, iGeneralTemplate $template, $jsactive = false){
	//$HTML = '<div>';
	$HTML .= '<h4>'.$activity->getTitle().'</h4>';

	// infos
	$nbrCom = activity_count_comment_actipict($activity->getPictures());
	$nbrPict = count($activity->getPictures());
	$comment = ($nbrCom <= 1 ) ? "commentaire" : "commentaires";
	$picture = ($nbrPict <= 1 ) ? "photo" : "photos";
	$view = ($activity->getViewed() <= 1 ) ? "vue" : "vues";
	$HTML .= '<small>';
	$HTML .= '<i class="fa fa-calendar"></i> Le '.ConversionUtils::dateToDateFr($activity->getDate());
	$HTML .= ' | <i class="fa fa-comment"></i> '.$nbrCom.' '.$comment;
	$HTML .= ' | <i class="fa fa-camera"></i> '.$nbrPict.' '.$picture;
	$HTML .= ' | <i class="fa fa-eye"></i> '.$activity->getViewed().' '.$view;
	$HTML .= '</small>';

	$HTML .= '<p>'.$activity->getDescription().'</p>';
	
	//SocialRing plugin
	$HTML .= system_load_plugin(array('social-ring' => array("level" => $activity->getLevel(), "template" => $template, "appId" => OptionManager::getInstance()->getOption("facebook-appid"))));

	$jsclass = '';
	if($jsactive){
		$jsclass = ACTIVITY_JS_CLASS_CALL_ANCHOR;
	}
	
	$HTML .= '<table class="activity-table-center">';
	$size = count($activity->getPictures());
	for($i = 0; $i < $size;){
		$HTML .= '<tr>';
		for($j=0; $j<5; $j++, $i++){
			$HTML .= '<td>';
			if($i<$size){
				$currentPict = $activity->getPictures()[$i];
				$href = URLUtils::generateURL($module->getName(), array('p' => 'picture', 'id' => $currentPict->getId()));
				//activity_path_thumbnail($module->getLocation(), $activity->getDirectory(),$currentPict->getFilename());
				$path = URLUtils::builtServerUrl($module->getName(), array('action' => 'getimage', 'type' => 'small', 'id' => $currentPict->getId()));
				if($currentPict->getIscensured()){
					$HTML .= '<a href="'.$href.'" class="'.$jsclass.'"><img class="img-responsive activity-img-censured activity-img-hover" src="'.DIR_MODULE . $module->getLocation() .'/view/img/censure-small.jpg" alt="Photo '.($i+1) . '/'. $size .'" title="'.($i+1) . '/'. $size .'"/></a>';
				}else{
					if($currentPict->getNbcomments() > 0){
						$HTML .= '<a href="'.$href.'" class="'.$jsclass.'"><img class="img-responsive activity-img-commented activity-img-hover" src="'.$path.'" alt="Photo '.($i+1) . '/'. $size .'" title="'.($i+1) . '/'. $size .'"/></a>';
					}else{
						$HTML .= '<a href="'.$href.'" class="'.$jsclass.'"><img class="img-responsive activity-img-hover" src="' . $path .'" alt="Photo '.($i+1) . '/'. $size .'" title="'.($i+1) . '/'. $size .'" /></a>';
					}
				}
			}
			$HTML .= '</td>';
		}
		$HTML .= '</tr>';
	}
	$HTML .= '</table>';
	
	//$HTML .= '</div>';

	return $HTML;
}


/**
 * built the html code for the page displaying the Top10's of the given Year
 * @param string $modloc : the location of the current module
 * @param array $mostView : array of Picture Object containing the most View Picture for the given period
 * @param array $mostCommented : array of Picture Object containing the most commented Picture for the given period
 * @param string $year : the desired year
 * @return string : the html code of the page
 */
function activity_html_page_top10($module, $mostView, $mostCommented,$year = "ever"){
	$html = '<div class="row">';
	$html .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
	$html .= '<h4>Top10 des plus vues</h4>';
	$html .= '<div class="row" style="margin-top:15px;margin-bottom:15px;">';
	for($i=0 ; $i<count($mostView) ; $i++){
		$p = $mostView[$i];
		if($i % 4 == 0){
			$html .= '</div>';
			$html .= '<div class="row" style="margin-top:15px;margin-bottom:15px;">';
		}
		$class ="";
		if($i == 8){
			$class = "col-lg-offset-3 col-md-offset-3";
		}
		//$path = activity_path_thumbnail($module->getLocation(), $p->getDirectory(), $p->getFilename());
		$path = URLUtils::builtServerUrl($module->getName(), array('action' => 'getimage', 'type' => 'small', 'id' => $p->getId()));	
		$html .= '<div class="col-lg-3 col-md-3 col-sm-4 col-xs-6 '.$class.'" style="text-align:center;">';
		$html .= '<a href="'.URLUtils::generateURL($module->getName(),array("p" => "top10","type" => "view", "year" => $year, "index" => ($i+1))).'" class="'.ACTIVITY_JS_CLASS_CALL_ANCHOR.'"><img src="'.$path.'" class="img-responsive activity-img-hover activity-img-center"></a>';
		$html .= '<p class="text-center">' . $p->getViewed() . ' Vues</p>';
		$html .= '</div>';
	}
	$html .= '</div>';
	$html .= '</div>';
	$html .= '</div>';
	$html .= '<div class="row">';
	$html .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
	$html .= '<h4>Top10 des plus comment&eacute;es</h4>';
	$html .= '<div class="row" style="margin-top:15px;margin-bottom:15px;">';
	for($i=0 ; $i<count($mostCommented) ; $i++){
		$p = $mostCommented[$i];
		if($i % 4 == 0){
			$html .= '</div>';
			$html .= '<div class="row" style="margin-top:15px;margin-bottom:15px;">';
		}
		$class ="";
		if($i == 8){
			$class = "col-lg-offset-3 col-md-offset-3";
		}
		//$path = activity_path_thumbnail($module->getLocation(), $p->getDirectory(), $p->getFilename());
		$path = URLUtils::builtServerUrl($module->getName(), array('action' => 'getimage', 'type' => 'small', 'id' => $p->getId()));
		$html .= '<div class="col-lg-3 col-md-3 col-sm-4 col-xs-6 '.$class.'"  style="text-align:center;">';
		$html .= '<a href="'.URLUtils::generateURL($module->getName(),array("p" => "top10","type" => "commented", "year" => $year, "index" => ($i+1))).'" class="'.ACTIVITY_JS_CLASS_CALL_ANCHOR.'"><img src="'.$path.'" class="img-responsive activity-img-hover activity-img-center"></a>';
		$html .= '<p class="text-center">' . $p->getNbcomments() . ' Commentaires</p>';
		$html .= '</div>';
	}
	$html .= '</div>';
	$html .= '</div>';
	$html .= '</div>';
	return $html;
}

/**
 * built the html code of a list of Activity, in the media format
 * @param array $list : the Activity list to display
 * @return string : the html code
 */
function activity_html_page_media_activity(array $list, $module){
	$html = "";
	for($i=0 ; $i < count($list) ;$i++){
		$act = $list[$i];
		// thumbnail
		$html .= '<div class="row">';	
	        $html .= '<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">';
	        $html .= '<br>';
				$html .= '<a class="text-center" href="'.URLUtils::generateURL($module->getName(), array("p" => "activity", "id" => $act->getId())).'">';
				$pict = activity_get_random_picture($act);
				$path = URLUtils::builtServerUrl($module->getName(), array('action' => 'getimage', 'type' => 'small', 'id' => $pict->getId()));
				$html .= '<img class="img-rounded img-responsive activity-img-hover activity-img-center" src="'.$path.'">';
				$html .= '</a>';   
	        $html .= '</div>';
		// activity informations
        $html .= '<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">';
        	//title
        	$html .= '<h4>'.$act->getTitle().'</h4>';  		
			// infos
        	$nbrCom = activity_count_comment_actipict ( $act->getPictures () );
	    	$nbrPict = count ( $act->getPictures () );
			$comment = ($nbrCom <= 1 ) ? "commentaire" : "commentaires";
			$picture = ($nbrPict <= 1 ) ? "photo" : "photos";
			$view = ($act->getViewed() <= 1 ) ? "vue" : "vues";
	        $html .= '<small>';
			$html .= '<i class="fa fa-calendar"></i> Le '.ConversionUtils::dateToDateFr($act->getDate());
			$html .= ' | <i class="fa fa-comment"></i> '.$nbrCom.' '.$comment;
			$html .= ' | <i class="fa fa-camera"></i> '.$nbrPict.' '.$picture;
			$html .= ' | <i class="fa fa-eye"></i> '.$act->getViewed().' '.$view;
			$html .= '</small>';
			// description
	        $html .= '<p>'.$act->getDescription().'</p>';
        	//button
        	$html .= '<a class="btn btn-primary pull-right" href="'.URLUtils::generateURL($module->getName(), array("p" => "activity", "id" => $act->getId())).'">Voir <i class="fa fa-angle-right"></i></a>';
        $html .= '</div>
		<div class="clearfix"></div>	
      </div><hr>';
	}
	return $html;
}


/**
 * built hte html code of a Picture page
 * @param Module $module : the current module 
 * @param Activity $activity : the Activity related to the Picture
 * @param Picture $picture : the Picture Object to display
 * @param mixed $profile : false if there is not profile, a User Object otherwise
 * @param array $album : key array containing informations about the album ('title' is the title of the album, 'href' is the link to this album)
 * @param array $orders : 	$orders[next] : the URL to the next picture in the list of the current album (null if there not)
 * 							$orders[order] : the number (place) of the current picture in the list of the album
 * 							$orders[previous] : the URL to the previous picture in the list of the current album (null if there not)
 * @param array $actions : the action that can be made on the current Picture
 * @return string : the html code of the Page where the Picture is displayed
 */
function activity_html_page_picture(Module $module, Activity $activity, Picture $picture, $profile, array $album, array $orders, array $actions, $socialring = false, $jsactive = false){
	$HTML = '<div class="row">' . "\n";
	// ##### PICTURE DIV
	$HTML .= '<div class="col-lg-9">';
		$HTML .= '<div class="activity-picture-content" id="photo">';
		$HTML .= '<div class="activity-picture-inner-content carousel img-responsive">';
			// the picture
			$path = activity_path_picture($module->getLocation(), $activity->getDirectory(), $picture, 'medium');
			$class = "img-polaroid";
			if(count($picture->getComments()) > 0){
				$class = "activity-img-commented";
			}
			if($picture->getIscensured()){
				$class = "activity-img-censured";
			}
			$url = URLUtils::builtServerUrl($module->getName(), array('action' => 'getimage', 'type' => 'medium', 'id' => $picture->getId()));
			$HTML .=  "\n\t" . '<img src="'.$url.'" alt="Photo" id="activity-the-picture" class="img-responsive '.$class.'" style="margin:auto"/>';
			
			//the pager of the picture
			$jsclass = '';
			if($jsactive){
				$jsclass = ACTIVITY_JS_CLASS_CALL_ANCHOR;
			}
			if($orders["next"]){
				$HTML .= '<a class="carousel-control right '.$jsclass.'" href="'.$orders["next"].'#photo"><span class="icon-next"></span></a>';
			}
			if($orders["previous"]){
				$HTML .= '<a class="carousel-control left '.$jsclass.'" href="'.$orders["previous"].'#photo"><span class="icon-prev"></span></a>';
			}
		$HTML .= '</div>';
		$HTML .= '</div>';
		
		
		//SocialRing plugin
		if($socialring){		
			$HTML .= system_load_plugin(array('social-ring' => array("level" => $activity->getLevel(), "appId" => OptionManager::getInstance()->getOption("facebook-appid"), "url" => URL . URLUtils::generateURL($module->getName(), array("id"=>$picture->getId(), "p"=>"picture")))));
		}
	$HTML .= '</div><!-- end of col-lg-9 -->' . "\n";
	
	// ##### INFOS & COMMENTS DIV
	$HTML .= '<div class="col-lg-3">';
		$HTML .= '<div>';
			// Infos
			$HTML .= '<div class="activity-content-box">
						<h4><i class="fa fa-info-circle"></i> Infos</h4>
						<b>Id : </b>'.$picture->getId() . '<br>';
						$HTML .= '<b>Activit&eacute; : </b><a href="'.URLUtils::generateURL($module->getName(), array("p"=>"activity", "id" => $activity->getId())).'">' . $activity->getTitle() . '</a><br>';
						if($album['title'] !== $activity->getTitle()){
							$HTML .= '<b>Num&eacute;ro : </b>'.($orders["order"]+1).' sur '.$album['count'].' dans <i><a href="'.$album['href'].'">'.$album['title'].'</a></i><br>';
						}else{
							$HTML .= '<b>Num&eacute;ro : </b>'.($orders["order"]+1).' sur '.count($activity->getPictures()).'<br>';
						}
			  $HTML .= '<b>Prise : </b> le '. ConversionUtils::dateToDateFr($activity->getDate()) . ' &agrave; '.ConversionUtils::timeToTimefr($picture->getTime()).'<br>
						<b>Commentaires : </b>'.count($picture->getComments()).'<br>';
			$HTML .= '</div>';
			// Actions
			$HTML .= '<div>';
			foreach ($actions as $name => $options){
				if(!empty($options['actions'])){			
					$HTML .= '  <div class="btn-group">';
					$HTML .= '<a class="btn '.$options['class'].' dropdown-toggle" data-toggle="dropdown" href="#">'.$name.' <span class="caret"></span></a>';
					$HTML .= '<ul class="dropdown-menu">';
					foreach($options['actions'] as $title => $href){
						$HTML .= '<li><a tabindex="-1" href="'.$href.'">'.$title.'</a></li>';
					}
					$HTML .= '</ul>';
					$HTML .= '</div>  ';
				}
			}
			$HTML .= '</div>';
			// Message div
			$HTML .= '<div id="activity-modal-message"></div>';
			// Comments
			$HTML  .= '<div class="activity-content-box  activity-modal-infos">';
				$HTML  .= '<div id="activity-modal-comments">';
				if(count($picture->getComments()) > 0){
					$HTML  .= '<h4><i class="fa fa-comments"></i> Commentaires</h4>';	
					$actions = array();
					if(RoleManager::getInstance()->hasCapabilitySession('activity-delete-comment')){
						$actions[] = array("title" => "Supprimer", "href"=>"javascript:activityDeleteComment('server.php?module=".$module->getName()."&action=delcomment&id=%comid',%comid);", "param" => array("%comid"=>"getId"));
					}
					$listCom = $picture->getComments();
					for($i=0 ; $i<count($picture->getComments()) ; $i++){
						$currentComment = $listCom[$i];
						$HTML  .= activity_html_modal_comment($currentComment,$actions);
					}
				}else{
					$HTML  .= "Il n'y a pas de commentaire sur cette photo.";
				}
				$HTML .= '</div>';
				
				if(RoleManager::getInstance()->hasCapabilitySession('activity-add-comment') && $profile){
					$HTML .= '<form method="post" id="activity-comment-form" class="activity-comment-form"><span id="activity-modal-loading-comment"></span>Ajouter votre commentaire
		 								<textarea id="activity-comment-textarea" name="activity-input-comment" class="activity-comment-textarea"></textarea>
		 						</form>';
					$HTML .= '<script>
						 var isMAJ = false;
						 $("#activity-comment-textarea").keyup(function(event) {
						 	if(event.keyCode == 16){ isMAJ = false}
						 }).keydown(function(event){
						    if(event.keyCode == 16){ isMAJ = true}
						    if(event.keyCode == 13 && isMAJ == false){
						   		activitySendComment("'.URLUtils::builtServerUrl($module->getName(), array("action"=>"sendcomment")).'",'.$picture->getId().', '.$profile->getId().');
						   	}
						 });
					</script>';
				}
			$HTML .= '</div><!-- end of activity-content-box -->';		
		$HTML .= '</div>';
	$HTML .= '</div><!-- end of col-lg-3 -->' . "\n";
	$HTML .= '</div><!-- end of row-fluid -->';
	
	// Censure submittion Form Modal
	$email = "";
	if(SessionManager::getInstance()->existsUserSession()){
		$profile = $smanager->getUserprofile();
		$email = $profile->getMail();
	}
	$HTML .= activity_html_modal_censure($picture->getId(), "", $email);
	
	return $HTML;
	
}


/**
 * get the html code of the list of activity
 * @param array $list : the activity to display
 * @param String $modulename : the name of the current module
 * @return string $html : the html code
 */
function activity_html_page_activity_list(array $list, $modulename){
	$nommois = activity_get_month_table();
	$count = count($list);
	
	$activities_to_display = array();
	$i = 0;
	$html = "";
	$month = "";
	while($i < $count){
		$activity = $list[$i];
		$current = $nommois[(int)ConversionUtils::getDateMonth($activity->getDate())]. ' ' . ConversionUtils::getDateYear($activity->getDate());
		if($month != $current){
			if($month != ""){	
				$html .= '<div class="row">';
				$html .= '<div class="col-lg-12">';
				$html .= '<h4><i class="fa fa-camera"></i> ' . $month . '</h4>';
				$html .= '</div>';
				$html .= '</div>';
	
				$nb = (int) (count($activities_to_display) / 2);
				$nb = $nb + (count($activities_to_display) % 2);
				$html .= '<div class="row">';
				$html .= '<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">';
				$html .= activity_html_mini_list(array_slice($activities_to_display, 0, $nb), $modulename, "list-unstyled");
				$html .= '</div>';
				$html .= '<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">';
				$html .= activity_html_mini_list(array_slice($activities_to_display, $nb, (count($activities_to_display) - $nb)), $modulename, "list-unstyled");
				$html .= '</div>';
				$html .= '</div>';
			}	
			$month = $current;
			$activities_to_display = array();
		}
		$activities_to_display[] = $activity;
		
		$i++;
	}
	return $html;
}

/**
 * 
 * @param unknown $modulename
 * @param unknown $listComm
 * @return string
 */
function activity_html_page_lastcomm($modulename, $listComm){
	if(!empty($listComm)){
		$HTML = '<div class="row">';
		$i=1;
		foreach ($listComm as $commented){
			if(!$commented->getIscensured()){
				if(file_exists(DIR_PICTURES . $commented->getDirectory(). '/small/'. $commented->getFilename())){
					$path = DIR_PICTURES . $commented->getDirectory(). '/small/'. $commented->getFilename();
				}else{
					$path = DIR_MODULE . 'activity/view/img/missing-small.jpg';
				}
			}else{
				$path = DIR_MODULE . 'activity/view/img/censure-small.jpg';
			}
			$HTML .= '<div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">';
			$HTML .= '<a href="'.URLUtils::generateURL($modulename,array("p"=>"lastcomm","index"=>$i)).'" class="'.ACTIVITY_JS_CLASS_CALL_ANCHOR.'"><img class="img-responsive img-thumbnail accueil-portfolio-picture accueil-img-hover" src="'. $path .'"></a>';
			$HTML .= '</div>';
				
			if($i % 6 == 0){
				$HTML .= '<div class="clearfix"></div>';
			}
			$i++;
		}
	
		$HTML .= '</div><!-- /.row -->';
		 
		$HTML .= activity_get_js_page_overlay('Derni&egrave;res photos comment&eacute;es', 'activity', true);
	
	}
	return $HTML;
}


/**
 * built the code of the Asking Censure Modal
 * @param int $pid : the integer of the Picture the asking is about
 * @param string $buttonClass : the class of the button
 * @param string $email : the email of the current user (if someone is conencted)
 * @return string : the html code and the js code
 */
function activity_html_modal_censure($pid, $buttonClass, $email){
	//$HTML .= '<a  href="#" class="'.$buttonClass.'" data-toggle="modal" data-target="#activity-censure-modal"><i class="icon-ban-circle"></i> Demande de censure</a>';
	$HTML = '<!-- Modal -->
<div class="modal fade" id="activity-censure-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
	<div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Demande de censure pour la photo <i>'.$pid.'</i></h4>
      </div>
      <div class="modal-body">
        	<div id="activity-message"></div>
       		<form class="form-horizontal" role="form" id="activity-censure-form">
				  <div class="form-group">
				    <div class="col-sm-10">
				      <input type="email" class="form-control" id="activity-censure-email" name="activity-censure-email" placeholder="Email" value="'.$email.'">
					  <input type="hidden" class="form-control" id="activity-censure-pid" name="activity-censure-pid" placeholder="pid" value="'.$pid.'">
				    </div>
				  </div>
				  <div class="form-group">
				    <div class="col-sm-10">
					  <textarea class="form-control" rows="3" id="activity-censure-comment" name="activity-censure-comment" placeholder="Pourquoi je veux censurer cette photo"></textarea>
				    </div>
				  </div>
				  <div class="form-group">
				    <div class="col-sm-offset-2 col-sm-10">
				      <button type="submit" class="btn btn-default">Envoyer</button>
				    </div>
				  </div>
				</form>	
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->';
	
	$HTML .= '<script>
			$( "#activity-censure-form" ).submit(function( event ) {
				var email = $("#activity-censure-email").val();
				var pid = $("#activity-censure-pid").val();
				var comment = $("#activity-censure-comment").val();
		
				if(email && pid && comment){
					// send = call function
					activityAskCensure(pid, email, comment, \''.URLUtils::builtServerUrl("activity", array("action" => "askcensure")).'\');
				}else{
					$("#activity-message").html(\'<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Au moins un des champs est vide.</div>\');
				}
  				return false;
			});
			</script>';
	
	return $HTML;
}



/**
 * generate the div htl code for the given Comment
 * @param unknown $currentComment
 * @return string
 */
function activity_html_modal_comment($currentComment, $actions){
	$comm = '<div id="activity-comment-'.$currentComment->getId().'">';
	$comm .= '<b><a href="'.URLUtils::getUserPageURL($currentComment->getUserid()).'">'.$currentComment->getUserid() . '</a></b>, le <i>' . ConversionUtils::timestampToDatetime($currentComment->getDate()) . '</i> ';
	for($i=0 ; $i<count($actions) ; $i++){
		$action = $actions[$i];

		$href = $action["href"];
		foreach ($action["param"] as $key => $value){
			$href = str_replace($key, $currentComment->$value(), $href);
		}
		//$comm .= '<a href="'.$action["url"]. $currentComment->$action["function"]() .'" class="btn btn-danger btn-mini">'.$action["name"].'</a>  ';
		$comm .= ' - <a href="'.$href .'" class="btn btn-danger btn-xs">'.$action["title"].'</a>  ';
	}
	$comm .= '<br>';
	$commentText = ConversionUtils::smiley(ConversionUtils::decoding($currentComment->getComment()));
	$commentText = preg_replace("
	  	#((http|https|ftp)://(\S*?\.\S*?))(\s|\;|\)|\]|\[|\{|\}|,|\"|'|:|\<|$|\.\s)#i", 
	  	"'<a href=\"$1\" target=\"_blank\">$3</a>$4'",
		$commentText
	);
	$comm .= str_replace("&lt;br /&gt;", "<br/>", $commentText);
	$comm .= '<hr class="activity-hr-style">';
	$comm .= '</div>';
	return $comm;
}

/**
 * get the code of a list (html li tag) of a list of Activity
 * @param array $list
 * @param unknown $classTag
 * @return string
 */
function activity_html_mini_list(array $list, $moduleName, $classTag){
	$html = "<ul class=\"".$classTag."\">";
	foreach ($list as $activity){
		$html .= "<li>";
		$html .= '<strong><small>' . ConversionUtils::dateToDateshort($activity->getDate()) . '</small> &middot; </strong>';
		$html .= "<a href=\"".URLUtils::generateURL($moduleName, array('p' => 'activity', 'id' => $activity->getId()))."\">".$activity->getTitle()."</a>";
		$html .= "</li>";
	}
	$html .= "</ul>";
	return $html;
}


/*
 DEPRECIATED
function activity_path_thumbnail($moduleloc, $directory, $filename){
	if(file_exists(DIR_PICTURES . $directory . "/small/" .$filename)){
		return DIR_PICTURES . $directory . "/small/" .$filename;
	} 
	return DIR_MODULE . $moduleloc . "view/img/missing-small.jpg";
}
*/
/**
 * return the path of a given picture (file exists or is censured)
 * @param String $moduleloc : the directory of the Activity Module
 * @param String $directory : the directory of the Actvity Object of the given picture
 * @param Picture $picture : the Picture Object
 * @param string $type : the type of the Picture (hd, small, medium)
 * @return string $path : the path to the file the user can see
 */
function activity_path_picture($moduleloc, $directory, Picture $picture, $type = "small"){
	if($type == 'small'){	
		$path = DIR_PICTURES . $directory ."/small/". $picture->getFilename();

		if($picture->getIscensured()){
			if(RoleManager::getInstance()->hasCapabilitySession('activity-read-censured')){
				if(!file_exists($path)){
					$path = DIR_MODULE . $moduleloc . "view/img/missing-small.jpg";
				}
			}else{
				$path = DIR_MODULE . $moduleloc ."view/img/censure-small.jpg";
			}
		}else{
			if(!file_exists($path)){
				$path = DIR_MODULE . $moduleloc . "view/img/missing-small.jpg";
			}
		}
	}else{	
		switch ($type){
			case 'hd':
				$path = DIR_HD_PICTURES . $directory ."/". $picture->getFilename();
				break;
			case 'medium':
			default:
				$path = DIR_PICTURES . $directory ."/". $picture->getFilename();
				break;
		}
		if($picture->getIscensured()){
			if(RoleManager::getInstance()->hasCapabilitySession('activity-read-censured')){
				if(!file_exists($path)){
					$path = DIR_MODULE . $moduleloc . "view/img/missing.jpg";
				}
			}else{
				$path = DIR_MODULE . $moduleloc ."view/img/censure.jpg";
			}
		}else{
			if(!file_exists($path)){
				$path = DIR_MODULE . $moduleloc . "view/img/missing.jpg";
			}
		}
	}
	return $path;
}


/**
 * return the path of a given picture (file exists or is censured)
 * @param String $moduleloc : the directory of the Activity Module
 * @param String $directory : the directory of the Actvity Object of the given picture
 * @param Picture $picture : the Picture Object
 * @return string $path : the path to the file the user can see
 */
/*
function activity_path_picture_hd($moduleloc, $directory, Picture $picture){
	if($picture->getIscensured()){
		if(RoleManager::getInstance()->hasCapabilitySession('activity-read-censured')){
			if(file_exists(DIR_HD_PICTURES . $directory ."/". $picture->getFilename())){
				$path = DIR_HD_PICTURES . $directory ."/". $picture->getFilename();
			}else{
				$path = DIR_MODULE . $moduleloc . "view/img/missing.jpg";
			}
		}else{
			$path = DIR_MODULE . $moduleloc ."view/img/censure.jpg";
		}
	}else{
		if(file_exists(DIR_HD_PICTURES . $directory ."/". $picture->getFilename())){
			$path = DIR_HD_PICTURES . $directory ."/". $picture->getFilename();
		}else{
			$path = DIR_MODULE . $moduleloc . "view/img/missing.jpg";
		}
	}
	return $path;
}

*/

/**
 * Compute the number of comment by adding the number of every given ActivityPicture
 * @param array $apicts : the ActivityPicture to count the comments
 * @return number : the number of comments
 */
function activity_count_comment_actipict(array $apicts){
	$nb = 0;
	for($i=0 ; $i<count($apicts) ; $i++){
		$ap = $apicts[$i];
		$nb = $nb + $ap->getNbcomments();
	}
	return $nb;
}

/**
 * built the javascript code (with tag or not) for the overlay page
 * @param string $modaltitle : the title of the modal (overlay)
 * @param string $modulename : the name of the current module
 * @param boolean $withtag : true if tags are required, false otherwise
 * @return string : the js code
 */
function activity_get_js_page_overlay($modaltitle, $modulename, $withtag = true){
	$js = "";
	if($withtag){
		$js .= "<script>";
	}
	$js .= "$( document ).ready(function() {
					$(document).on('click','.".ACTIVITY_JS_CLASS_CALL_ANCHOR."',function(e)  { 
					    var href = ($(this).attr('href'));
						activityOverlayPage('".URLUtils::builtServerUrl($modulename, array())."', href, '".$modaltitle."');
						e.preventDefault();
					});	
				});";
	if($withtag){
		$js .= "</script>";
	}
	return $js;
}

/**
 * get the name of the months of the year
 * @return multitype:string
 */
function activity_get_month_table(){
	//Init tableau des mois
	$nommois = array();
	$nommois[0] = "Inconnu";
	$nommois[1] = "Janvier";
	$nommois[2] = "F&eacute;vrier";
	$nommois[3] = "Mars";
	$nommois[4] = "Avril";
	$nommois[5] = "Mai";
	$nommois[6] = "Juin";
	$nommois[7] = "Juillet";
	$nommois[8] = "Aout";
	$nommois[9] = "Septembre";
	$nommois[10] = "Octobre";
	$nommois[11] = "Novembre";
	$nommois[12] = "D&eacute;cembre";
	return $nommois;
}


/**
 * return a informations table with the order of the given picture, in the list, the next picture and the previous picture
 * @param array $pictures : the list of Picture
 * @param Picture $picture : the current Picture to analyse
 * @return array $links : "order","next","previous" are the keys
 */
function activity_get_neighbor_pictures(array $pictures, Picture $picture){
	$found = false;
	$i=0;
	while(!$found && $i < (count($pictures)-1)){
		$tmp = $pictures[$i];
		if($tmp->getId() == $picture->getId()){
			$found = true;
		}else{
			$i++;
		}
	}
	// $i contain the correct index
	$links = array("previous" => null, "next" => null, "order" => $i);
	if($i > 0){
		$p = $pictures[$i-1];
		$links["previous"] = $p->getId();
	}
	if(($i+1) < count($pictures)){
		$p = $pictures[$i+1];
		$links["next"] = $p->getId();
	}
	return $links;
}




/**
 * select a random not-cesored picture of an given activity
 * @param Activity $activity
 * @return ActivityPicture $pict : the random picture
 */
function activity_get_random_picture(Activity $activity){
	if(count($activity->getPictures()) > 0){
		$count = count($activity->getPictures());
		$time = 0;
		while($time < 3){
			$i = rand(0,($count-1));
			$pict = $activity->getPictures()[$i];
			if(!$pict->getIscensured()){
				return $pict;
			}else{
				$time++;
			}
		}
		return $activity->getPictures()[0];
	}else{
		return new Picture();
	}
}



function activity_admin_html_table_activity_list(array $list, $modname){

	$HTML = '<table class="table table-striped tablesorter">';
	$HTML .= '<thead>
		<tr>
			<th>Id</th>
			<th>Titre</th>
			<th>Description</th>
			<th>Infos</th>
			<th>Publi&eacute;</th>
			<th><u>Actions</u></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th>Id</th>
			<th>Titre</th>
			<th>Description</th>
			<th>Infos</th>
			<th>Publi&eacute;</th>
			<th><u>Actions</u></th>
		</tr>
	</tfoot>';

	$HTML .= '<tbody>';
	for ($i = 0; $i < count($list); $i++) {
		$a = $list[$i];
		$HTML .= '<tr>';
		$statut = ($a->getIspublished() == '1' ? '<span class="label label-success">Oui</span>' : '<span class="label label-danger">Non</span>');
		$HTML .= '<td>'.$a->getId().'</td><td>'.($a->getTitle()).'</td><td>'.($a->getDescription()).'</td>';
		$HTML .= '<td>';
		$HTML .= '<strong>Date :</strong> '.($a->getDate()).'<br>';
		$HTML .= '<strong>R&eacute;pertoire :</strong> '.($a->getDirectory()).'<br>';
		$HTML .= '<strong>Photos :</strong> '.$a->getCountPictures().'<br>';
		$HTML .= '<strong>Auteurs :</strong> <ol><li>'.str_replace(",", "</li><li>", $a->getAuthors()) . '</li></ol>';
		$HTML .= '</td>';
		//$HTML .= '<td>'.str_replace(",", "<br>", $a->getAuthors()).'</td>';
		$HTML .= '<td><div id="activity-statut-'.$a->getId().'">'.$statut.'</div></td>';

		$HTML .= '<td><div class="btn-group">
    <a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#">Actions <span class="caret"></span></a>
    <ul class="dropdown-menu">
    	<li><a href="'.URLUtils::generateURL($modname, array('action' => 'edit', 'id' => $a->getId())).'"><i class="fa fa-pencil"></i> Editer</a></li>
    	<li><a href="'.URLUtils::generateURL($modname, array('action' => 'delete', 'id' => $a->getId())).'" onclick=\'return(confirm("Etes vous certain de vouloir supprimer cet event ?"));\'"><i class="fa fa-trash-o"></i> Supprimer</a></li>';
		$HTML .= '<li><a href="'.URLUtils::generateURL($modname, array('action' => 'managepicture', 'id' => $a->getId())).'"><i class="fa fa-picture-o"></i> Gestion des photos</a></li>';
		if(!$a->getIspublished()){
			$HTML .= '<li><a id="activity-action-publish-'.$a->getId().'" href="'.URLUtils::generateURL($modname, array('action' => 'publish', 'id' => $a->getId())).'" ><i class="fa fa-leaf"></i> Publier</a></li>';
		}else{
			$HTML .= '<li><a id="activity-action-publish-'.$a->getId().'" href="'.URLUtils::generateURL($modname, array('action' => 'unpublish', 'id' => $a->getId())).'" ><i class="fa fa-fire"></i> Depublier</a></li>';
		}
		$HTML .= '</ul>
    </div>';
		$HTML .= '<img src="'.DIR_MODULE.$modname.'/view/img/loader.gif" alt="loader" style="visibility:hidden;" id="activity-loader-'.$a->getId().'">';
		$HTML .= '</td>';
		$HTML .= '</tr>';
	}
	$HTML .= '</tbody></table>';

	return $HTML;
}




function activity_admin_html_activity_form($action, $modname, Activity $activity, $potentialAuthors, $roles){

	if($action == 'add'){
		$label = 'Ajouter';
		$url = URLUtils::generateURL($modname, array('action' => 'add'));
		$date = date('Y-m-d');
		$readonly = "";
	}else{
		$label = 'Modifier';
		$url = URLUtils::generateURL($modname, array('action' => 'edit', 'id' => $activity->getId()));
		$date = $activity->getDate();
		$readonly = "readonly";
	}
	
	// level options
	$options = '';
	foreach($roles as $role){
		$value = $role->getLevel();
		$key = $role->getRole();
		if($value == $activity->getLevel()){
			$options .= '<option value="'.$role->getId().'" selected="selected">'.$key.'</option>';
		}else{
			$options .= '<option value="'.$role->getId().'">'.$key.'</option>';
		}
	}

	// authors possibilities
	$authorsCheckboxes = '<div class="control-group">
			 <label class="control-label" for="acitivity-input-authors">Auteurs</label>
    <div class="controls">';
	foreach($potentialAuthors as $author){
		$checked = "";
		$authors = $activity->getAuthors();
		foreach($authors as $a){
			if($a->getId() == $author->getId()){
				$checked = "checked";
			}
		}

		$authorsCheckboxes .= '<label class="checkbox inline">
  									<input type="checkbox" id="inlineCheckbox'.$author->getId().'" value="'.$author->getId().'" name="activity-input-authors[]" '.$checked.'> '.$author->getFirstname().' '.$author->getName().' ('.$author->getUsername().')
							  </label>';
	}
	$authorsCheckboxes .= '</div></div>';


	// create the form
	$HTML = '<form class="form-horizontal" method="post" action="'. $url .'">
<fieldset>

<!-- Form Name -->
<legend>Formulaire</legend>

<!-- Text input-->
<div class="control-group">
  <label class="control-label" for="activity-input-title">Titre</label>
  <div class="controls">
    <input id="activity-input-title" name="activity-input-title" type="text" placeholder="titre" class="input-xlarge" value="'.$activity->getTitle().'">

  </div>
</div>

<!-- Textarea -->
<div class="control-group">
  <label class="control-label" for="acitivty-input-description">Description</label>
  <div class="controls">
    <textarea id="acitivty-input-description" name="activity-input-description" class="bootstrap-editor input-xlarge" style="width: 80%; height: 300px">'.$activity->getDescription().'</textarea>
  </div>
</div>
		
<!-- Text input-->
<div class="control-group">
  <label class="control-label" for="activity-input-date">Date</label>
  <div class="controls">
   	<div class="input-append">
    	<input id="activity-input-date" name="activity-input-date" type="text" data-date-format="yyyy-mm-dd" class="input-xlarge" value="'.$date.'" readonly>
    	<span class="add-on"><i class="fa fa-calendar"></i></span>
    </div>
  </div>
</div>

<!-- Text input-->
<div class="control-group">
  <label class="control-label" for="activity-input-directory">Dossier</label>
  <div class="controls">
    <input id="activity-input-directory" name="activity-input-directory" type="text" placeholder="yyyy-mm-jj_activity_name" class="input-xlarge" value="'.$activity->getDirectory().'" '.$readonly.'>
    <p class="help-block">Surtout : ne pas mettre d\'accents dans le nom du dossier, ni d\'autre caractere special !</p>
  </div>
</div>

<!-- Select Basic -->
<div class="control-group">
  <label class="control-label" for="activity-input-level">Level</label>
  <div class="controls">
    <select id="activity-input-level" name="activity-input-level" class="input-xlarge">
      '.$options.'
    </select>
  </div>
</div>
      		'.$authorsCheckboxes.'

<div class="control-group">
    <div class="controls">
      <button type="submit" class="btn">'.$label.'</button>
    </div>
</div>

</fieldset>
</form>';

	return $HTML;
}



function activity_admin_html_list_directories($list){
	$HTML = '<h3>Liste des r&eacute;pertoires non utilis&eacute;s dans '.DIR_HD_PICTURES.' (HD directory)</h3>';
	$HTML .= '<table class="table table-striped tablesorter">';
	$HTML .= '<thead>
		<tr>
			<th>Repertoire</th>
			<th><u>Action</u></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th>Repertoire</th>
			<th><u>Action</u></th>
		</tr>
	</tfoot>';
	
	$HTML .= '<tbody>';
	for ($i = 0; $i < count($list); $i++) {
		$a = $list[$i];
		$HTML .= '<tr>';
		$HTML .= '<td>'; 
		$HTML .= $a;
		$HTML .= '</td>';
		$HTML .= '<td>';
		$HTML .= '<a href="#" class="btn btn-primary">Voir contenu</a>  ';
		$HTML .= '<a href="#" class="btn btn-danger" onclick=\'return(confirm("Etes vous certain de vouloir supprimer ce dossier ?"));\'">Supprimer</a>  ';
		$HTML .= '<a href="#" class="btn">Utiliser dans une activite</a>  ';
		$HTML .= '</td>';
		$HTML .= '</tr>';
	}
	$HTML .= '</tbody></table>';
	return $HTML;
}


/**
 * 
 * @param unknown $year
 * @param unknown $stats
 * @return string
 */
function activity_admin_html_stat_table($year, $stats){
	$HTML = '<div class="col-lg-3 col-md-3">';
	$HTML .= '<h4 class="text-center">Ann&eacute;e '.$year.'</h4>';
	$HTML .= '<table class="table table-striped tablesorter">';
	$HTML .= '<thead>
		<tr>
			<th>Nom</th>
			<th>Username</th>
			<th>Stats</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th>Nom</th>
			<th>Username</th>
			<th>Stats</th>
		</tr>
	</tfoot>';
	
	$HTML .= '<tbody>';
	for ($i = 0; $i < count($stats); $i++) {
		$s = $stats[$i];
		$HTML .= '<tr>';
		$HTML .= '<td>';
		$HTML .= ucfirst($s->getFirstname()) . " " . ucfirst($s->getName());
		$HTML .= '</td>';
		$HTML .= '<td>';
		$HTML .= $s->getUsername();
		$HTML .= '</td>';
		$HTML .= '<td>';
		$HTML .= $s->getStat();
		$HTML .= '</td>';
		$HTML .= '</tr>';
	}
	$HTML .= '</tbody></table>';
	$HTML .= '</div>';
	return $HTML;
	
}


function activity_admin_html_stat_acti_table($statActi, $total){
	$HTML = '<div class="">';
	$HTML .= '<table class="table table-striped tablesorter">';
	$HTML .= '<thead>
		<tr>
			<th>Ann&eacute;e</th>
			<th>#Activit&eacute;s</th>
			<th>#Photos</th>
			<th>#Commentaires</th>
			<th>Photos/activit&eacute;</th>
			<th>Comms/activit&eacute;</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th>Ann&eacute;e</th>
			<th>#Activit&eacute;s</th>
			<th>#Photos</th>
			<th>#Commentaires</th>
			<th>Photos/activit&eacute;</th>
			<th>Comms/activit&eacute;</th>
		</tr>
	</tfoot>';
	
	$HTML .= '<tbody>';
	$totalActi = 0;
	$totalPict = 0;
	$totalComm = 0;
	foreach($statActi as $stat){
		$totalActi += $stat->getActivities();
		$totalPict += $stat->getPictures();
		$totalComm += $stat->getComments();
		$HTML .= '<tr>';
			$HTML .= '<td>' .$stat->getYear(). '-' . ($stat->getYear()+1). '</td>';
			$HTML .= '<td>' .$stat->getActivities(). '</td>';
			$HTML .= '<td>' .$stat->getPictures(). '</td>';
			$HTML .= '<td>' .$stat->getComments(). '</td>';
			$HTML .= '<td>' .($stat->getPictures() / $stat->getActivities()). '</td>';
			$HTML .= '<td>' .($stat->getComments() / $stat->getActivities()) . '</td>';
		$HTML .= '</tr>';
	}
	if($total){		
		$HTML .= '<tr class="warning">';
			$HTML .= '<td>TOTAL</td>';
			$HTML .= '<td>' .$totalActi. '</td>';
			$HTML .= '<td>' .$totalPict. '</td>';
			$HTML .= '<td>' .$totalComm. '</td>';
			$HTML .= '<td>' .($totalPict / $totalActi). '</td>';
			$HTML .= '<td>' .($totalComm / $totalActi) . '</td>';
		$HTML .= '</tr>';
	}
	$HTML .= '</tbody></table>';
	$HTML .= '</div>';
	return $HTML;
	
}




function activity_admin_html_table_censures_list(array $list, $modname){

	$HTML = '<table class="table table-striped tablesorter">';
	$HTML .= '<thead>
		<tr>
			<th>Id</th>
			<th>Comment</th>
			<th>PID</th>
			<th>Email</th>
			<th>Date</th>
			<th><u>Actions</u></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th>Id</th>
			<th>Comment</th>
			<th>PID</th>
			<th>Email</th>
			<th>Date</th>
			<th><u>Actions</u></th>
		</tr>
	</tfoot>';

	$HTML .= '<tbody>';
	for ($i = 0; $i < count($list); $i++) {
		$a = $list[$i];
		$HTML .= '<tr>';
		$HTML .= '<td>'.$a->getId().'</td><td>'.($a->getComment()).'</td><td>'.($a->getPictureid()).'</td><td>'.($a->getEmail()).'</td><td>'.($a->getDate()).'</td>';
		$HTML .= '<td>';
		$HTML .= '<div class="btn-group">
				<a href="'.URL.URLUtils::generateURL($modname, array('p' => 'picture', 'id' => $a->getPictureid())).'" target="_blank" class="btn btn-default"><i class="fa fa-eye"></i> Voir</a>
  				<a class="btn btn-danger" href="'.URLUtils::generateURL($modname, array("action" => "deletecensure", "id" => $a->getId())).'" onclick=\'return(confirm("Etes vous certain de vouloir supprimer cette demande de censure ?"));\'"><i class="fa fa-trash-o"></i> Rejeter</a>
			</div>';
		$HTML .= '</td>';
		$HTML .= '</tr>';
	}
	$HTML .= '</tbody></table>';
	return $HTML;
}




//######### RSS


//FUNCTIONS RSS
function &init_news_rss(&$xml_file){
	$root = $xml_file->createElement("rss"); // création de l'élément
	$root->setAttribute("version", "2.0"); // on lui ajoute un attribut
	$root = $xml_file->appendChild($root); // on l'insère dans le nœud parent (ici root qui est "rss")

	$channel = $xml_file->createElement("channel");
	$channel = $root->appendChild($channel);

	$desc = $xml_file->createElement("description");
	$desc = $channel->appendChild($desc);
	$text_desc = $xml_file->createTextNode("Restez informés de toutes les activités couvertes par le Webkot sur le Campus Namurois !"); // on insère du texte entre les balises <description></description>
	$text_desc = $desc->appendChild($text_desc);

	$link = $xml_file->createElement("link");
	$link = $channel->appendChild($link);
	$text_link = $xml_file->createTextNode("http://www.webkot.be");
	$text_link = $link->appendChild($text_link);

	$title = $xml_file->createElement("title");
	$title = $channel->appendChild($title);
	$text_title = $xml_file->createTextNode("Webkot.be : Les activités");
	$text_title = $title->appendChild($text_title);

	return $channel;
}

function add_news_node(&$parent, $root, $id, $titre, $contenu, $date){
	$item = $parent->createElement("item");
	$item = $root->appendChild($item);

	$title = $parent->createElement("title");
	$title = $item->appendChild($title);
	$text_title = $parent->createTextNode($titre);
	$text_title = $title->appendChild($text_title);

	$link = $parent->createElement("link");
	$link = $item->appendChild($link);
	$text_link = $parent->createTextNode(URL . URLUtils::generateURL("activity", array("p"=>"activity", "id"=>$id)));
	$text_link = $link->appendChild($text_link);

	$desc = $parent->createElement("description");
	$desc = $item->appendChild($desc);
	$text_desc = $parent->createTextNode($contenu);
	$text_desc = $desc->appendChild($text_desc);

	$pubdate = $parent->createElement("pubDate");
	$pubdate = $item->appendChild($pubdate);
	$text_date = $parent->createTextNode($date);
	$text_date = $pubdate->appendChild($text_date);

	$guid = $parent->createElement("guid");
	$guid = $item->appendChild($guid);
	$text_guid = $parent->createTextNode(URL . URLUtils::generateURL("activity", array("p"=>"activity", "id"=>$id)));
	$text_guid = $guid->appendChild($text_guid);

	$src = $parent->createElement("source");
	$src = $item->appendChild($src);
	$text_src = $parent->createTextNode(URL);
	$text_src = $src->appendChild($text_src);
}

function rebuild_rss(){
	$manager = ActivityManager::getInstance();
	$activities = $manager->getLastActivity(30,0);

	$xml_file = new DOMDocument("1.0");

	// on initialise le fichier XML pour le flux RSS
	$channel = init_news_rss($xml_file);

	foreach($activities as $activity){
		add_news_node($xml_file, $channel, $activity->getId(), $activity->getTitle(), $activity->getDescription(), ConversionUtils::dateToDateRSS($activity->getDate()));
	}
	$xml_file->save(RSS_FILE);
}