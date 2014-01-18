<?php


$view = new PageAdminView($template, $module);


if(isset($_GET['action']) && !empty($_GET['action'])){
	$message = new Message();
	$pmanager = PageManager::getInstance();
	
	switch ($_GET['action']) {
		// Add a page
		case "add":
			if(RoleManager::getInstance()->hasCapabilitySession('page-add-page')){
				$page = new Page();
				if(isset($_POST['page-input-id']) && isset($_POST['page-input-title']) && isset($_POST['page-input-content']) && isset($_POST['page-input-file'])){
					//fill the post object
					$page->setId($_POST['page-input-id']);
					$page->setTitle($_POST['page-input-title']);
					$page->setContent($_POST['page-input-content']);
					$page->setFile($_POST['page-input-file']);
					
					if(!empty($_POST['page-input-id']) && !empty($_POST['page-input-title']) && !empty($_POST['page-input-content'])){
						try{
							$statut = (($_POST['page-input-active'] == 'true' ? '1' : '0'));
							
							$rep = $pmanager->addPage($_POST['page-input-id'],$_POST['page-input-title'], $_POST['page-input-content'],$statut,$_POST['page-input-file']);
							if($rep){
								$message = new Message(1);
								$message->addMessage("Votre page a bien ete ajout&eacute;e avec succes.");
							}else{
								$message = new Message(3);
								$message->addMessage("Une erreur s'est produite, l'ajout de votre page a &eacute;chou&eacute;.");
							}
							$SMM = SessionMessageManager::getInstance();
							$SMM->setSessionMessage($message);
							URLUtils::redirection(URLUtils::generateURL($module->getName(), array()));
						}catch(SQLException $sqle){
							$message = new Message(3);
							$message->addMessage("Une erreur s'est produite, l'ajout de votre page a &eacute;chou&eacute;.");
							$message->addMessage($sqle->getMessage());
						}catch(DatabaseExcetion $dbe){
							$message = new Message(3);
							$message->addMessage("Une erreur s'est produite, l'ajout de votre page a &eacute;chou&eacute;.");
							$message->addMessage($dbe->getMessage());
						}
					}else{
						$message->setType(3);
						$message->addMessage("Au moins un des champs requis est vide.");
					}
				}
				$view->pageFormPage($_GET['action'], $message, $page);
			}else{
				throw new AccessRefusedException("Vous ne pouvez pas ajouter de Page.");
			}
			break;
		// Edit a page
		case "edit":
			if(RoleManager::getInstance()->hasCapabilitySession('page-edit-page')){	
				if(isset($_GET['id']) && !empty($_GET['id'])){	
					$page = $pmanager->getPage($_GET['id']);
					if(isset($_POST['page-input-id']) && isset($_POST['page-input-title']) && isset($_POST['page-input-content']) && isset($_POST['page-input-file'])){
						//fill the post object
						$page->setId($_POST['page-input-id']);
						$page->setTitle($_POST['page-input-title']);
						$page->setContent($_POST['page-input-content']);
						$page->setFile($_POST['page-input-file']);
							
						if(!empty($_POST['page-input-id']) && !empty($_POST['page-input-title']) && !empty($_POST['page-input-content'])){
							try{
								$statut = (($_POST['page-input-active'] == 'true' ? '1' : '0'));
									
								$rep = $pmanager->updatePage($_GET['id'], $_POST['page-input-title'], $_POST['page-input-content'], $statut, $_POST['page-input-file']);
								if($rep){
									$message = new Message(1);
									$message->addMessage("Votre page a bien ete ajout&eacute;e avec succes.");
								}else{
									$message = new Message(3);
									$message->addMessage("Une erreur s'est produite, l'ajout de votre page a &eacute;chou&eacute;.");
								}
								$SMM = SessionMessageManager::getInstance();
								$SMM->setSessionMessage($message);
								URLUtils::redirection(URLUtils::generateURL($module->getName(), array()));
							}catch(SQLException $sqle){
								$message = new Message(3);
								$message->addMessage("Une erreur s'est produite, l'ajout de votre page a &eacute;chou&eacute;.");
								$message->addMessage($sqle->getMessage());
							}catch(DatabaseExcetion $dbe){
								$message = new Message(3);
								$message->addMessage("Une erreur s'est produite, l'ajout de votre page a &eacute;chou&eacute;.");
								$message->addMessage($dbe->getMessage());
							}
						}else{
							$message->setType(3);
							$message->addMessage("Au moins un des champs requis est vide.");
						}
					}
					$view->pageFormPage($_GET['action'], $message, $page);
				}else{
					$message->setType(3);
					$message->addMessage("L'identifiant de la page est vide.");
					$SMM = SessionMessageManager::getInstance();
					$SMM->setSessionMessage($message);
					URLUtils::redirection(URLUtils::generateURL($module->getName(), array()));
				}
			}else{
				throw new AccessRefusedException("Vous ne pouvez pas editer de Page.");
			}
			break;
		case "delete":
			if(RoleManager::getInstance()->hasCapabilitySession('page-delete-page')){
				if(isset($_GET['id']) && !empty($_GET['id'])){
					try{
						
						$pmanager->delete($_GET['id']);
						
						$message = new Message(1);
						$message->addMessage("Votre page ".$_GET['id']." a bien ete ajout&eacute;e avec succes.");		
					}catch(SQLException $sqle){
						$message = new Message(3);
						$message->addMessage("Une erreur s'est produite, l'ajout de votre page a &eacute;chou&eacute;.");
						$message->addMessage($sqle->getMessage());
					}catch(DatabaseExcetion $dbe){
						$message = new Message(3);
						$message->addMessage("Une erreur s'est produite, l'ajout de votre page a &eacute;chou&eacute;.");
						$message->addMessage($dbe->getMessage());
					}
				}else{
					$message->setType(3);
					$message->addMessage("L'identifiant de la page est vide.");
				}
				$SMM = SessionMessageManager::getInstance();
				$SMM->setSessionMessage($message);
				URLUtils::redirection(URLUtils::generateURL($module->getName(), array()));
			}else{
				throw new AccessRefusedException("Vous ne pouvez pas editer de Page.");
			}
			break;
		default:
			throw new InvalidURLException("Operation inconnue sur une page.");	
	}
		
}else{
	if(RoleManager::getInstance()->hasCapabilitySession('page-read-page')){	
		// list of the pages
		$pmanager = PageManager::getInstance();
		$pages = $pmanager->getListPage();
		
		$SMM = SessionMessageManager::getInstance();
		$message = $SMM->getSessionMessage();
		
		$view->pageListPage($pages, $message);
	}else{
		throw new AccessRefusedException("Vous ne pouvez pas lire de Page.");
	}
}

