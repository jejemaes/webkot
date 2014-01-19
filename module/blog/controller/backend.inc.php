<?php


$view = new BlogAdminView($template, $module);


if(isset($_GET['action']) && !empty($_GET['action'])){
	switch ($_GET['action']) {
		// Add a post
		case "add":
			if (RoleManager::getInstance ()->hasCapabilitySession ( 'blog-add-post' )) {				
				$post = new BlogPost(array());
				$managerSession = SessionManager::getInstance();
				$post->setAuthor($managerSession->getUserprofile()->getUsername());
				
				if(isset($_POST['blog-input-title']) && isset($_POST['blog-input-content']) && isset($_POST['blog-input-author'])){
					//fill the post object
					$post->setTitle($_POST['blog-input-title']);
					$post->setContent($_POST['blog-input-content']);
					
					if(!empty($_POST['blog-input-title']) && !empty($_POST['blog-input-content']) && !empty($_POST['blog-input-author'])){				
						try{
							$manager = BlogManager::getInstance();
							$rep = $manager->addPost($_POST['blog-input-title'], $_POST['blog-input-content'], $managerSession->getUserprofile()->getId());
							if($rep){
								$message = new Message(1);
								$message->addMessage("Votre article a bien ete ajoute avec succes.");
								
								$SMM = SessionMessageManager::getInstance();
								$SMM->setSessionMessage($message);
								URLUtils::redirection(URLUtils::generateURL($module->getName(), array()));
							}else{
								$message = new Message(3);
								$message->addMessage("Une erreur s'est produite, l'ajout de votre article a echoue.");
							}
						}catch(SQLException $sqle){
							$message = new Message(3);
							$message->addMessage("Une erreur s'est produite, l'ajout de votre article a echoue.");
							$message->addMessage($sqle->getMessage());
						}catch(DatabaseExcetion $dbe){
							$message = new Message(3);
							$message->addMessage("Une erreur s'est produite, l'ajout de votre article a echoue.");
							$message->addMessage($dbe->getMessage());
						}
					}else{
						$message = new Message(3);
						$message->addMessage("Au moins un des champs est vide.");
					}
				}else{
					$message = new Message();
				}
				
				$view->pageFormPost('add', $message, $post);
			}else{
				throw new AccessRefusedException("Vous ne pouvez pas ajouter d'article.");
			}
			break;
		//edit a post
		case "edit":
			if (RoleManager::getInstance ()->hasCapabilitySession ( 'blog-edit-post' )) {
				if(isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id'])){
					$manager = BlogManager::getInstance();
					$post = $manager->getPost($_GET['id']);
					if(isset($_POST['blog-input-title']) && isset($_POST['blog-input-content']) && isset($_POST['blog-input-id'])){
						if(!empty($_POST['blog-input-title']) && !empty($_POST['blog-input-content']) && !empty($_POST['blog-input-id'])){
							$post->setTitle($_POST['blog-input-title']);
							$post->setContent($_POST['blog-input-content']);
							try{
								$manager = BlogManager::getInstance();
								$rep = $manager->updatePost($_POST['blog-input-id'], $_POST['blog-input-title'], $_POST['blog-input-content']);
								if($rep){
									$message = new Message(1);
									$message->addMessage("Votre article a bien ete ajoute avec succes.");
									$SMM = SessionMessageManager::getInstance();
									$SMM->setSessionMessage($message);
									URLUtils::redirection(URLUtils::generateURL($module->getName(), array()));
								}else{
									$message = new Message(3);
									$message->addMessage("Une erreur s'est produite, la mise a jour de votre article a echoue.");
								}
							}catch(SQLException $sqle){
								$message = new Message(3);
								$message->addMessage("Une erreur s'est produite, la mise a jour de votre article a echoue.");
								$message->addMessage($sqle->getMessage());
							}catch(DatabaseExcetion $dbe){
								$message = new Message(3);
								$message->addMessage("Une erreur s'est produite, la mise a jour de votre article a echoue.");
								$message->addMessage($dbe->getMessage());
							}
						}else{
							$message = new Message(3);
							$message->addMessage("Au moins un des champs est vide.");
						}
					}else{
						$message = new Message();
						//$message->addMessage("Au moins un des champs n'existe pas !");
					}
					$view->pageFormPost('edit', $message, $post);
				}else{
					$message = new Message(3);
					$message->addMessage("Edition impossible, il manque l'id du post.");
					$SMM = SessionMessageManager::getInstance();
					$SMM->setSessionMessage($message);
					URLUtils::redirection(URLUtils::generateURL($module->getName(), array()));
				}
			
			}else{
				throw new AccessRefusedException("Vous ne pouvez pas editer d'article.");
			}
			break;
		case "delete":
			if (RoleManager::getInstance ()->hasCapabilitySession ( 'blog-delete-post' )) {
				
				if(isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id'])){			
					try{
						$manager = BlogManager::getInstance();
						$rep = $manager->deletePost($_GET['id']);
						if($rep){
							$message = new Message(1);
							$message->addMessage("Votre article a bien ete supprime a jour avec succes.");
						}else{
							$message = new Message(3);
							$message->addMessage("Une erreur s'est produite, la suppression de votre article a echoue.");
						}
					}catch(SQLException $sqle){
						$message = new Message(3);
						$message->addMessage("Une erreur s'est produite, la suppression de votre article a echoue.");
						$message->addMessage($sqle->getMessage());
					}catch(DatabaseExcetion $dbe){
						$message = new Message(3);
						$message->addMessage("Une erreur s'est produite,la suppression de votre article a echoue.");
						$message->addMessage($dbe->getMessage());
					}
				}else{
					$message = new Message(3);
					$message->addMessage("Supression impossible, car il manque l'id du post a supprimer.");
				}
				$SMM = SessionMessageManager::getInstance();
				$SMM->setSessionMessage($message);
				URLUtils::redirection(URLUtils::generateURL($module->getName(), array()));
			}else{
				throw new AccessRefusedException("Vous ne pouvez pas supprimer d'article.");
			}
			break;
		default:
			echo "switch default : action inconnue.";
	}
}else{
	// list
	$manager = BlogManager::getInstance();
	$list = $manager->getListPost();
	$view->pageListPost($list);
}