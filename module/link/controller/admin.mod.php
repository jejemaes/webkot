<?php

$view = new LinkAdminView($template, $module);

if(isset($_GET['part']) && !empty($_GET['part']) && ($_GET['part'] == 'link')){
	if(isset($_GET['action']) && !empty($_GET['action'])){
		switch($_GET['action']){
			case 'add':
				if (RoleManager::getInstance ()->hasCapabilitySession ( 'link-add-link' )) {
					$message = new Message();
					$link = new Link();
					if(isset($_POST['link-input-name']) && isset($_POST['link-input-url']) && isset($_POST['link-input-category'])){
						$link->setName($_POST['link-input-name']);
						$link->setCategory($_POST['link-input-category']);
						$link->setUrl($_POST['link-input-url']);
						if((!empty($_POST['link-input-name'])) && (!empty($_POST['link-input-url'])) &&  (!empty($_POST['link-input-category']))){
							// check if the URL is correct
							$pattern = "/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i";
							$pattern_1 = "/^(http|https|ftp):\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+.(com|org|net|be|at|us|tv|info|uk|co.uk|biz|fr)$)(:(\d+))?\/?/i";
							$pattern_2 = "/^(www)((\.[A-Z0-9][A-Z0-9_-]*)+.(com|org|net|dk|at|us|tv|info|uk|co.uk|biz|se)$)(:(\d+))?\/?/i";
							if(!preg_match($pattern, $_POST['link-input-url'])){
								$message->setType(3);
								$message->addMessage("L'URL introduite n'est pas au bon format : http://www.monsite.domaine/");
							}			
							if($message->isEmpty()){
								$manager = LinkManager::getInstance();
								$rep = $manager->addLink($_POST['link-input-name'],$_POST['link-input-category'],$_POST['link-input-url']);
								if($rep){
									$message->setType(1);
									$message->addMessage("Mise a jour effectuee avec succes.");
									$SMM = SessionMessageManager::getInstance();
									$SMM->setSessionMessage($message);
									URLUtils::redirection(URLUtils::generateURL($module->getName(), array('part' => 'link')));
								}else{
									$message->setType(3);
									$message->addMessage("Une erreur SQL s'est produite.");
								}
							}
						}else{
							$message->setType(3);
							$message->addMessage("Au moins un des champs requis est vide !");
						}
					}
					$manager = LinkManager::getInstance();
					$categories = $manager->getListCategory();
					$view->pageLinkForm('add',$message,$categories, $link);
				}else{
					throw new AccessRefusedException("Vous ne pouvez pas ajouter de lien.");
				}
				break;
			case 'edit':
				if (RoleManager::getInstance ()->hasCapabilitySession ( 'link-edit-link' )) {
					$message = new Message();
					if(isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id'])){
						$manager = LinkManager::getInstance();
						$link = $manager->getLink($_GET['id']);
						if(isset($_POST['link-input-name']) && isset($_POST['link-input-url']) && isset($_POST['link-input-category'])){
							$link->setName($_POST['link-input-name']);
							$link->setCategory($_POST['link-input-category']);
							$link->setUrl($_POST['link-input-url']);
							if((!empty($_POST['link-input-name'])) && (!empty($_POST['link-input-url'])) &&  (!empty($_POST['link-input-category']))){
								// check if the URL is correct
								$pattern = "/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i";
								$pattern_1 = "/^(http|https|ftp):\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+.(com|org|net|be|at|us|tv|info|uk|co.uk|biz|fr)$)(:(\d+))?\/?/i";
								$pattern_2 = "/^(www)((\.[A-Z0-9][A-Z0-9_-]*)+.(com|org|net|dk|at|us|tv|info|uk|co.uk|biz|se)$)(:(\d+))?\/?/i";
								if(!preg_match($pattern, $_POST['link-input-url'])){
									$message->setType(3);
									$message->addMessage("L'URL introduite n'est pas au bon format : http://www.monsite.domaine/");
								}
								if($message->isEmpty()){
									$manager = LinkManager::getInstance();
									//$rep = $manager->addLink($_POST['link-input-name'],$_POST['link-input-category'],$_POST['link-input-url']);
									$rep = $manager->updateLink($_GET['id'], $_POST['link-input-name'], $_POST['link-input-category'], $_POST['link-input-url']);
									if($rep){
										$message->setType(1);
										$message->addMessage("Mise a jour effectuee avec succes.");
										$SMM = SessionMessageManager::getInstance();
										$SMM->setSessionMessage($message);
										URLUtils::redirection(URLUtils::generateURL($module->getName(), array('part' => 'link')));
									}else{
										$message->setType(3);
										$message->addMessage("Une erreur SQL s'est produite.");
									}
								}
							}else{
								$message->setType(3);
								$message->addMessage("Au moins un des champs requis est vide !");
							}
						}
						$manager = LinkManager::getInstance();
						$categories = $manager->getListCategory();
						$view->pageLinkForm('edit', $message, $categories, $link);
					}else{
						$message->setType(3);
						$message->addMessage("L'identifiant du lien est absent.");
					}
				}else{
					throw new AccessRefusedException("Vous ne pouvez pas editer de lien.");
				}
				break;
			case 'delete':
				if (RoleManager::getInstance ()->hasCapabilitySession ( 'link-delete-link' )) {
					$message = new Message();
					if(isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id'])){
						$manager = LinkManager::getInstance();
						$rep = $manager->deleteLink($_GET['id']);
						if($rep){
							$message->setType(1);
							$message->addMessage("Suppression effectuee avec succes.");
						}else{
							$message->setType(3);
							$message->addMessage("Une erreur SQL s'est produite.");
						}
						$SMM = SessionMessageManager::getInstance ();
						$SMM->setSessionMessage ( $message );
						URLUtils::redirection(URLUtils::generateURL($module->getName(), array ('part' => 'link')));
					}
				}else{
					throw new AccessRefusedException("Vous ne pouvez pas supprimer de lien.");
				}
				break;
			default:
				break;
		}
	}else{
		// list
		$manager = LinkManager::getInstance();
		$list = $manager->getListLink();
		$view->pageLinkTable($list);
	}
}else{
	if(isset($_GET['action']) && !empty($_GET['action'])){
	
		switch($_GET['action']){
			case 'add':
				if (RoleManager::getInstance ()->hasCapabilitySession ( 'link-add-category' )) {
					$message = new Message();
					$category = new LinkCategory();
					if(isset($_POST['link-input-catid']) && isset($_POST['link-input-catdescri']) && isset($_POST['link-input-catplace'])){
						$category->setName($_POST['link-input-catid']);
						$category->setDescription($_POST['link-input-catdescri']);
						$category->setPlace($_POST['link-input-catplace']);
						if((!empty($_POST['link-input-catid'])) && (!empty($_POST['link-input-catdescri'])) &&  (!empty($_POST['link-input-catplace'])) && is_numeric($_POST['link-input-catplace'])){
							$manager = LinkCategoryManager::getInstance();
							$rep = $manager->addCategory($_POST['link-input-catid'], $_POST['link-input-catdescri'], $_POST['link-input-catplace']);
							if($rep){
								$message->setType(1);
								$message->addMessage("Ajout de CatŽgorie ŽffectuŽ avec succes.");
								$SMM = SessionMessageManager::getInstance();
								$SMM->setSessionMessage($message);
								URLUtils::redirection(URLUtils::generateURL($module->getName(), array('part' => 'category')));
							}else{
								$message->setType(3);
								$message->addMessage("Une erreur SQL s'est produite.");
							}
						}else{
							$message->setType(3);
							$message->addMessage("Au moins un des champs requis est vide !");
						}
					}
					$view->pageCategoryForm('add',$message, $category);
				}else{
					throw new AccessRefusedException("Vous ne pouvez pas editer de catŽgorie de lien.");
				}
				break;
			case 'edit':
				if (RoleManager::getInstance ()->hasCapabilitySession ( 'link-edit-category' )) {
					if(isset($_GET['name']) && !empty($_GET['name'])){
						$manager = LinkCategoryManager::getInstance();		
						$message = new Message();
						$category = $manager->getCategory($_GET['name']);
						if(isset($_POST['link-input-catid']) && isset($_POST['link-input-catdescri']) && isset($_POST['link-input-catplace'])){
							$category->setDescription($_POST['link-input-catdescri']);
							$category->setPlace($_POST['link-input-catplace']);
							if((!empty($_POST['link-input-catid'])) && (!empty($_POST['link-input-catdescri'])) &&  (!empty($_POST['link-input-catplace'])) && is_numeric($_POST['link-input-catplace'])){
								$manager = LinkCategoryManager::getInstance();
								$rep = $manager->updateCategory($_GET['name'], $_POST['link-input-catdescri'], $_POST['link-input-catplace']);
								if($rep){
									$message->setType(1);
									$message->addMessage("Mise a jour de CatŽgorie ŽffectuŽ avec succes.");
									$SMM = SessionMessageManager::getInstance();
									$SMM->setSessionMessage($message);
									URLUtils::redirection(URLUtils::generateURL($module->getName(), array('part' => 'category')));
								}else{
									$message->setType(3);
									$message->addMessage("Une erreur SQL s'est produite.");
								}
							}else{
								$message->setType(3);
								$message->addMessage("Au moins un des champs requis est vide !");
							}
						}
					}else{
						$message->setType(3);
						$message->addMessage("L'identifiant de la categorie est absent.");
					}
					$view->pageCategoryForm('edit',$message, $category);
				}else{
					throw new AccessRefusedException("Vous ne pouvez pas editer de catŽgorie de lien.");
				}
				break;
			case 'delete':
				if (RoleManager::getInstance ()->hasCapabilitySession ( 'link-delete-category' )) {
					$message = new Message();
					if(isset($_GET['name']) && !empty($_GET['name'])){
						$manager = LinkCategoryManager::getInstance();
						$rep = $manager->deleteCategory($_GET['name']);
						if($rep){
							$message->setType(1);
							$message->addMessage("Suppression ŽffectuŽe avec succes.");
						}else{
							$message->setType(3);
							$message->addMessage("Une erreur SQL s'est produite.");
						}
						$SMM = SessionMessageManager::getInstance ();
						$SMM->setSessionMessage ( $message );
						URLUtils::redirection(URLUtils::generateURL($module->getName(), array ('part' => 'category')));
					}
				}else{
					throw new AccessRefusedException("Vous ne pouvez pas supprimer de lien.");
				}
				break;
			default:
				break;
		}
	}else{
		// list
		$manager = LinkManager::getInstance();
		$list = $manager->getListCategory();
		$view->pageLinkCategoryTable($list);
	}
}
