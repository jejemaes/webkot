<?php

$view = new PageView($template, $module);

$message = new Message();

if(isset($_GET['id']) && !empty($_GET['id'])){
	if(RoleManager::getInstance()->hasCapabilitySession('page-read-page')){
		// a specified page
		$pmanager = PageManager::getInstance();
		$page = $pmanager->getPage($_GET['id']);
		
		if($page->getIsactive()){	
			$contentFromFile = "";
			if($page->getFile()){
				include  DIR_MODULE . $module->getName() . "/files/" . $page->getFile();
				//$message->addMessage(DIR_MODULE . $module->getName() . "/files/" . $page->getFile());
			}
			$view->pagePage($page, $message, $contentFromFile);
		}else{
			throw new InvalidURLException("La page est inactive pour l'instant. Revenez plus tard, ou contactez le webmaster.");
		}
	}else{
		throw new AccessRefusedException("Vous ne pouvez pas lire cette Page.");
	}
}else{
	// list of the pages
	$pmanager = PageManager::getInstance();
	$pages = $pmanager->getListPage();
	
	$view->pageListPage($pages);
}
