<?php

$view = new WebkotAdminView($template, $module);

if(isset($_GET['part']) && !empty($_GET['part'])){
	
	// Part for the TEAM management
	if($_GET['part'] == 'team'){
		if(isset($_GET['action']) && !empty($_GET['action'])){
			switch ($_GET['action']) {
				// Add a post
				case "add":
					if (RoleManager::getInstance ()->hasCapabilitySession ( 'webkot-add-membership' )) {		
						if(isset($_GET['step']) && !empty($_GET['step'])){
							switch ($_GET['step']) {
								
								case "2":
									if(isset($_POST['webkot-input-year']) && !empty($_POST['webkot-input-year'])){ // chekc format iiii-iiii
										$man = WebkotteurManager::getInstance();
										$list = $man->getAllExceptYear($_POST['webkot-input-year']);
										
										$view->pageAddTeamStep2($module->getName(),$_POST['webkot-input-year'],$list);
									}else{
										$message = new Message(3);
										$message->addMessage("L'annee de l'equipe n'a pas ete choisie.");
										$SMM = SessionMessageManager::getInstance();
										$SMM->setSessionMessage($message);
										URLUtils::redirection(URLUtils::generateURL($module->getName(), array('part' => 'team', 'action' => 'add', 'step' => '1')));
									}
									break;
								case "3" :
									if(isset($_POST['webkot-input-webkotteurs']) && !empty($_POST['webkot-input-webkotteurs']) && isset($_POST['webkot-input-year']) && !empty($_POST['webkot-input-year'])){
										if(count($_POST['webkot-input-webkotteurs']) >= 1){			
											$manager = WebkotteurManager::getInstance();
											$webkotteur = array();
											foreach ($_POST['webkot-input-webkotteurs'] as $value){
												$webkotteur[] = $manager->getWebkotteur($value);
											}
											$view->pageAddTeamStep3($module->getName(),$webkotteur,$_POST['webkot-input-year']);
										}
										
									}else{
										$message = new Message(3);
										$message->addMessage("Aucun webkotteur ne sera ajoutŽ a l'equipe.");
										$SMM = SessionMessageManager::getInstance();
										$SMM->setSessionMessage($message);
										URLUtils::redirection(URLUtils::generateURL($module->getName(), array('part' => 'team', 'action' => 'add', 'step' => '2')));
									}
									break;
								case "4" :
									if(isset($_POST['webkot-input-year']) && !empty($_POST['webkot-input-year']) && isset($_POST['webkot-input-id']) && !empty($_POST['webkot-input-id']) && isset($_POST['webkot-input-age']) && !empty($_POST['webkot-input-age']) && isset($_POST['webkot-input-function']) && !empty($_POST['webkot-input-function']) && isset($_POST['webkot-input-img']) && !empty($_POST['webkot-input-img']) && isset($_POST['webkot-input-study']) && !empty($_POST['webkot-input-study']) && isset($_POST['webkot-input-order']) && !empty($_POST['webkot-input-order'])){		
										if((!system_is_item_array_empty($_POST['webkot-input-id'])) && (!system_is_item_array_empty($_POST['webkot-input-age'])) && (!system_is_item_array_empty($_POST['webkot-input-order']))){
											$year = $_POST['webkot-input-year'];
												
											$listId = $_POST['webkot-input-id'];
											$listFct = $_POST['webkot-input-function'];
											$listStudies = $_POST['webkot-input-study'];
											$listImg = $_POST['webkot-input-img'];
											$listAge = $_POST['webkot-input-age'];
											$listOrder = $_POST['webkot-input-order'];			
											
											$error = new Message(3);
											$succes = new Message(1);
											
											for($i=0 ; $i<count($listId) ; $i++){
												if((!empty($listId[$i])) && (!empty($listAge[$i])) && (!empty($listOrder[$i])) && (is_numeric($listOrder[$i]))  && (is_numeric($listAge[$i]))){
													$succes->addMessage('Ajout correct de Id=' . $listId[$i] . ' Annee=' . $year .' Etude='. $listStudies[$i] .' Fonction='. $listFct[$i]);
													$man = WebkotteurManager::getInstance();
													$man->addMember($listId[$i],$year,($listStudies[$i]),($listFct[$i]),($listImg[$i]),($listAge[$i]),($listOrder[$i]));
												}else{
													$error->addMessage('Erreur pour Id='.$listId[$i] . '. Il faut recommencer !');
												}
											}
											$SMM = SessionMessageManager::getInstance();
											if(!$error->isEmpty()){
												$SMM->setSessionMessage($error);
												URLUtils::redirection(URLUtils::generateURL($module->getName(), array('part' => 'team', 'action' => 'add', 'step' => '3')));
											}else{					
												$SMM->setSessionMessage($succes);
												URLUtils::redirection(URLUtils::generateURL($module->getName(), array('part' => 'team')));											
											}
										}else{
											$message = new Message(3);
											$message->addMessage("Au moins une information est manquante.");
											$SMM = SessionMessageManager::getInstance();
											$SMM->setSessionMessage($message);
											URLUtils::redirection(URLUtils::generateURL($module->getName(), array('part' => 'team', 'action' => 'add', 'step' => '3')));
										}
									}else{
										$message = new Message(3);
										$message->addMessage ( "Au moins une information est manquante ou vide pour tout les utilisateurs." );
										$SMM = SessionMessageManager::getInstance ();
										$SMM->setSessionMessage ( $message );
										URLUtils::redirection ( URLUtils::generateURL ( $module->getName (), array (
												'part' => 'team',
												'action' => 'add',
												'step' => '3' 
										) ) );
									}
									break;
							}
						}else{
							$view->pageAddTeamStep1($module->getName());
						}
					}else{
						throw new AccessRefusedException("Vous ne pouvez pas ajouter de webkotteur a une equipe.");
					}	
					break;
					
				case 'edit':
					if (RoleManager::getInstance ()->hasCapabilitySession ( 'webkot-edit-membership' )) {
						list($message, $webkotteur) = WebkotteurController::editMembershipAction($_REQUEST);
						if($message->isSuccess()){
							$SMM = SessionMessageManager::getInstance();
							$SMM->setSessionMessage($message);
							URLUtils::redirection(URLUtils::generateURL($module->getName(), array('part' => 'team')));
						}else{
							$view->pageTeamForm($module->getName(), $webkotteur, $message);
						}
					}else{
						throw new AccessRefusedException("Vous ne pouvez pas editer l'appartenance d'un webkotteur a une equipe.");
					}
					break;
					
				case 'delete':
					if (RoleManager::getInstance ()->hasCapabilitySession ( 'webkot-delete-membership' )) {
						$message = WebkotteurController::deleteMembershipAction($_GET);
						$SMM = SessionMessageManager::getInstance();
						$SMM->setSessionMessage($message);
						URLUtils::redirection(URLUtils::generateURL($module->getName(), array("part" => "team")));
					}else{
						throw new AccessRefusedException("Vous ne pouvez pas supprimer l'appartenance d'un webkotteur a une Žquipe.");
					}
					break;
				default:
					echo "switch default : action inconnue.";
			}
		}else{
			// list
			$manager = WebkotteurManager::getInstance();
			$list = $manager->getAllMemberShip();
			$view->pageListTeam($list);
		}
	}
	
	// Part for the WEBKOTTEUR management
	if($_GET['part'] == 'webkotteur'){
		if(isset($_GET['action']) && !empty($_GET['action'])){
			switch ($_GET['action']) {
				// Add a profile
				case "add":
					if (RoleManager::getInstance ()->hasCapabilitySession ( 'webkot-add-webkotteur' )) {		
						$webkotteur = new Webkotteur(array());
						$error = new Message();
						if(isset($_POST['webkot-input-name']) && isset($_POST['webkot-input-firstname']) && isset($_POST['webkot-input-userid']) && isset($_POST['webkot-input-mail'])){	
							if((!empty($_POST['webkot-input-name'])) && (!empty($_POST['webkot-input-firstname'])) &&  (!empty($_POST['webkot-input-userid'])) && (!empty($_POST['webkot-input-mail']))){
								
								if(!is_numeric($_POST['webkot-input-userid'])){
									$error->setType(3);
									$error->addMessage("Le UserId n'est pas un chiffre.");
								}
								
								if((!preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $_POST['webkot-input-mail'])) || (strstr($_POST['webkot-input-mail'], '@') != '@webkot.be')){
									$error->setType(3);
									$error->addMessage("Le format de l'adresse mail n'est pas valide. Elle doit etre telle que xxxxxx@webkot.be");
								}
								if($error->isEmpty()){
									//add
									$man = WebkotteurManager::getInstance();
									$man->addWebkotteur($_POST['webkot-input-name'], $_POST['webkot-input-firstname'], $_POST['webkot-input-surname'], $_POST['webkot-input-mail'],$_POST['webkot-input-userid']);
									$message = new Message(1);
									$message->addMessage("Webkotteur ajoute avec succes.");
									$SMM = SessionMessageManager::getInstance();
									$SMM->setSessionMessage($message);
									URLUtils::redirection(URLUtils::generateURL($module->getName(), array('part' => 'webkotteur')));
								}
							}else{
								$error->setType(3);
								$error->addMessage("Au moins un des champs est vide !");
							}
							
							$webkotteur->setName($_POST['webkot-input-name']);
							$webkotteur->setFirstname($_POST['webkot-input-firstname']);
							$webkotteur->setNickname($_POST['webkot-input-surname']);
							$webkotteur->setUserId($_POST['webkot-input-userid']);
							$webkotteur->setMail($_POST['webkot-input-mail']);
							
						}else{
							$error = new Message();
							if(isset($_GET['uid']) && !empty($_GET['uid'])){
								$umanager = UserManager::getInstance();
								$user = $umanager->getUserById($_GET['uid']);
								if($user != null){
									$webkotteur->setName($user->getName());
									$webkotteur->setFirstname($user->getFirstname());
									$webkotteur->setNickname();
									$webkotteur->setUserId($user->getId());
								}
							}
						}
						$view->pageWebkotteurForm('add', $error, $webkotteur);
					}else{
						throw new AccessRefusedException("Vous ne pouvez pas ajouter de webkotteur.");
					}
					break;
				// Edit a profile
				case "edit":
					if (RoleManager::getInstance ()->hasCapabilitySession ( 'webkot-edit-webkotteur' )) {
						if(isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id'])){
							$manager = WebkotteurManager::getInstance();
							$webkotteur = $manager->getWebkotteur($_GET['id']);
							if(isset($_POST['webkot-input-name']) && isset($_POST['webkot-input-firstname']) && isset($_POST['webkot-input-userid']) && isset($_POST['webkot-input-mail'])){	
								if((!empty($_POST['webkot-input-name'])) && (!empty($_POST['webkot-input-firstname'])) &&  (!empty($_POST['webkot-input-userid'])) && (!empty($_POST['webkot-input-mail']))){
					
									try{
										$manager = WebkotteurManager::getInstance();
										$rep = $manager->updateKoteur($_GET['id'],$_POST['webkot-input-name'], $_POST['webkot-input-firstname'], $_POST['webkot-input-surname'], $_POST['webkot-input-mail'],$_POST['webkot-input-userid']);
											
										if($rep){
											$message = new Message(1);
											$message->addMessage("Votre webkotteur a bien ete ajoute avec succes.");
											$SMM = SessionMessageManager::getInstance();
											$SMM->setSessionMessage($message);
											URLUtils::redirection(URLUtils::generateURL($module->getName(), array('part' => 'webkotteur')));
										}else{
											$message = new Message(3);
											$message->addMessage("Une erreur s'est produite, la mise a jour de votre webkotteur a echoue.");
										}
									}catch(SQLException $sqle){
										$message = new Message(3);
										$message->addMessage("Une erreur s'est produite, la mise a jour de votre webkotteur a echoue.");
										$message->addMessage($sqle->getMessage());
									}catch(DatabaseExcetion $dbe){
										$message = new Message(3);
										$message->addMessage("Une erreur s'est produite, la mise a jour de votre webkotteur a echoue.");
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
							$view->pageWebkotteurForm('edit', $message, $webkotteur);
						}else{
							$message = new Message(3);
							$message->addMessage("Edition impossible, il manque l'id du webkotteur.");
							$SMM = SessionMessageManager::getInstance();
							$SMM->setSessionMessage($message);
							URLUtils::redirection(URLUtils::generateURL($module->getName(), array('part' => 'webkotteur')));
						}
							
					}else{
						throw new AccessRefusedException("Vous ne pouvez pas editer de webkotteur.");
					}
					break;
				// Delete a profile
				case "delete" :
					// DO NOTHING : soit supprimer les membership avant, soit apres ?
					echo "soit supprimer les membership avant, soit apres ?<br>ligne 284, admin.mod.php";
					break;
				default:
					echo "switch default : action inconnue.";
			}
		}else{
			// list webkotteur
			$desc = system_get_desc_pagination();
			$page = (system_get_page_pagination()-1);
			$limit = ($page*$desc);
			
			$manager = WebkotteurManager::getInstance();
			$list = $manager->getListWebkotteur("",$limit,$desc);
			$count = $manager->getCountWebkotteur("");
			
			$view->pageListWebkotteur($list, $count,$desc,($page+1));
		}
	}
}else{
	// list webkotteur
	$desc = system_get_desc_pagination();
	$page = (system_get_page_pagination()-1);
	$limit = ($page*$desc);

	$manager = WebkotteurManager::getInstance();
	$list = $manager->getListWebkotteur("",$limit,$desc);	
	$count = $manager->getCountWebkotteur("");
	
	$view->pageListWebkotteur($list, $count,$desc,$page);
}