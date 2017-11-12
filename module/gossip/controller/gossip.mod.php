<?php


$view = new GossipView($template, $module);

$gmanager = GossipManager::getInstance();

if(isset($_GET['action']) && !empty($_GET['action'])){
	if($_GET['action'] == 'add'){
		if(isset($_POST['gossip-input-content'])){
			$message = new Message(3);
			if(RoleManager::getInstance()->hasCapabilitySession('gossip-add-gossip')){
				if(!empty($_POST['gossip-input-content'])){
					$profile = $smanager = SessionManager::getInstance()->getUserprofile();
					if($profile){
						$gmanager->add($_POST['gossip-input-content'], $profile->getId());
						$message->addMessage("Votre potin a &eacute;t&eacute; ajout&eacute; avec succ&egrave;s.");
						$message->setType(1);
						
						$SMM = SessionMessageManager::getInstance();
						$SMM->setSessionMessage($message);
						URLUtils::redirection(URLUtils::generateURL($module->getName(),array()));
					}else{
						$message->addMessage("Aucune session utilisateur n'existe pour l'instant.");
					}		
				}else{
					$message->addMessage("Le potin soumis est vide.");
				}
			}else{
				throw new AccessRefusedException("Vous n'avez pas les autorisations d'ajouter de potins.");
			}
			unset($_POST);
		}	
	}
}


if(isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id'])){
	// particular gossip
	$gossip = $gmanager->getGossip($_GET['id']);
	$view->pagePotin($gossip);
}else{
	// list
	$count = $gmanager->getCountGossip();
	$nbrpage = (int)($count / NBR_DEFAULT);
	if($count % NBR_DEFAULT != 0){
		$nbrpage++;
	}
	$page = 1;
	$list = $gmanager->getListGossip(($page-1), NBR_DEFAULT);								
	
	$SMM = SessionMessageManager::getInstance();
	if(!$message){
		$message = $SMM->getSessionMessage();
	}
	
	$view->pageList($list, $nbrpage, $page, $message);
}