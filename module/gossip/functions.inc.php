<?php


function gossip_html_list(array $gossips, $numpage, $modname){
	//$html = '<a id="page-'.$numpage.'"></a>';
	$html = '<div id="gossip-message"></div>';
	foreach ($gossips as $gossip){
		$html .= gossip_htm_gossip($gossip,$modname);
	}
	//$html .= "<script>$('.btn-popover').popover();</script>";
	return $html;
}




function gossip_htm_gossip(Gossip $gossip, $modname){
	$html = '<div id="gossip-'.$gossip->getId().'">';
	if($gossip->getCensure()){
		$content = "Le potin a &eacute;t&eacute; censur&eacute;.";
		if(RoleManager::getInstance()->hasCapabilitySession('gossip-read-censure')){
			$content = $gossip->getContent();
		}
		$html .= '<h4><span class="text-muted">Il parait que ... </span><span id="gossip-content-'.$gossip->getId().'" class="text-danger">' .$content  . '</span></h4>';
	}else{
		$html .= '<h4><span class="text-muted">Il parait que ... </span><span id="gossip-content-'.$gossip->getId().'">' .$gossip->getContent() . '</span></h4>';
	}
	$html .= '<div class="row">';
	$html .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
	$html .= '<i class="fa fa-user"></i> Publi&eacute; par <a href="'.URLUtils::getUserPageURL($gossip->getUser()).'">'.$gossip->getUser().'</a>';
	$html .= ' | <i class="fa fa-calendar"></i> le ' . ConversionUtils::timestampUnixToDatetime($gossip->getTimestamp());
	$html .= ' | <div class="btn-group btn-group-xs">';
	$html .= '<button id="gossip-liker-popover-'.$gossip->getId().'" type="button" class="btn btn-default" data-toggle="popover" data-container="body" data-original-title="Likers" title="Likers"><i class="fa fa-thumbs-up"></i> <span id="gossip-liker-'.$gossip->getId().'">'.count($gossip->getLiker()).'</span> </button>';
	$html .= "<script>$('#gossip-liker-popover-".$gossip->getId()."').popover({content : function(){return gossipGetCommenter('".URL."server.php?module=gossip&action=getliker',".$gossip->getId().");},placement : 'top', html : true, trigger:'click'});</script>";
	$html .= '<button id="gossip-disliker-popover-'.$gossip->getId().'" type="button" class="btn btn-default" data-toggle="popover" data-container="body" data-original-title="Dislikers" title="Dislikers"><i class="fa fa-thumbs-down"></i> <span id="gossip-disliker-'.$gossip->getId().'">'.count($gossip->getDisliker()).'</span> </button>';
	$html .= "<script>$('#gossip-disliker-popover-".$gossip->getId()."').popover({content : function(){return gossipGetCommenter('".URL."server.php?module=gossip&action=getdisliker',".$gossip->getId().");},placement : 'top', html : true, trigger:'click'});</script>";
	$html .= '</div>';
	$html .= '</div>';
	$html .= '</div>';
	
	$profile = $smanager = SessionManager::getInstance()->getUserprofile();
	if($profile){
		$html .= '<div class="pull-right" style="margin-top:15px;">';
		$html .= ' <div class="btn-group btn-group-xs">';
		if(in_array($profile->getUsername(),$gossip->getLiker())){
			$html .= '<button id="gossip-like-button-'.$gossip->getId().'" type="button" class="btn btn-info" onclick="gossipComment(\''.URL.'server.php?module=gossip&action=\', '.$gossip->getId().', \''.$profile->getUsername().'\',\'unlike\')"><i class="fa fa-white fa fa-thumbs-up"></i> Je n\'aime plus</button>';
		}else{
			$html .= '<button id="gossip-like-button-'.$gossip->getId().'" type="button" class="btn btn-info" onclick="gossipComment(\''.URL.'server.php?module=gossip&action=\', '.$gossip->getId().', \''.$profile->getUsername().'\',\'like\')"><i class="fa fa-white fa fa-thumbs-up"></i> J\'aime </button>';
		}
		if(in_array($profile->getUsername(),$gossip->getDisliker())){
			$html .= '<button id="gossip-like-button-'.$gossip->getId().'" type="button" class="btn btn-primary" onclick="gossipComment(\''.URL.'server.php?module=gossip&action=\', '.$gossip->getId().', \''.$profile->getUsername().'\',\'undislike\')"><i class="fa fa-white fa fa-thumbs-up"></i> Je n\'aime pas plus </button>';
		}else{
			$html .= '<button id="gossip-dislike-button-'.$gossip->getId().'" type="button" class="btn btn-primary" onclick="gossipComment(\''.URL.'server.php?module=gossip&action=\', '.$gossip->getId().', \''.$profile->getUsername().'\',\'dislike\')"><i class="fa fa-white fa fa-thumbs-down"></i> Je n\'aime pas</button>';
		}
		$html .= '</div> ';
		
		if(RoleManager::getInstance()->hasCapabilitySession('gossip-censure-gossip')){
			if($gossip->getCensure()){
				$html .= ' <button id="gossip-censure-button-'.$gossip->getId().'" type="button" class="btn btn-xs btn-danger" onclick="gossipCensureAction(\''.URL.'server.php?module=gossip\', '.$gossip->getId().',\'uncensure\')"><i class="fa fa-circle-o"></i> D&eacute;censurer</button> ';
			}else{
				$html .= ' <button id="gossip-censure-button-'.$gossip->getId().'" type="button" class="btn btn-xs btn-danger" onclick="gossipCensureAction(\''.URL.'server.php?module=gossip\', '.$gossip->getId().',\'censure\')"><i class="fa fa-ban"></i> Censurer</button> ';	
			}
		}
		
		if(RoleManager::getInstance()->hasCapabilitySession('gossip-delete-gossip')){
			$html .= ' <button type="button" alt="Supprimer" class="btn btn-xs btn-danger" onclick="gossipDeleteAction(\''.URL.'server.php?module=gossip\', '.$gossip->getId().',\'delete\')"><i class="fa fa-trash-o"></i> </button> ';
		}
		
		$html .= '</div> ';
		
	}
	$html .= '<div class="clearfix"></div>';
	$html .= system_load_plugin(array('social-ring' => array("level" => 0, "appId" => OptionManager::getInstance()->getOption("facebook-appid"), "url" => URL . URLUtils::generateURL($modname, array("id"=>$gossip->getId())))));
	$html .= '<div id="gossip-message-'.$gossip->getId().'"></div>';
	$html .= '</div>';
	$html .= '<hr>';
	return $html;
}
