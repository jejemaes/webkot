<?php

$view = new ChallengeAdminView($template,$module);


if(isset($_GET['action']) && !empty($_GET['action'])){
	
	switch ($_GET['action']) {
		// add a challenge
		case "add":
			if (RoleManager::getInstance()->hasCapabilitySession ( 'challenge-add-challenge' )) {
				$message = new Message();
				$challenge = new Challenge();
				if(isset($_POST['challenge-input-question']) && isset($_POST['challenge-input-answer']) && isset($_POST['challenge-input-description']) && isset($_POST['challenge-input-path']) && isset($_POST['challenge-input-date'])){
					if(!empty($_POST['challenge-input-question']) && !empty($_POST['challenge-input-answer']) && !empty($_POST['challenge-input-description']) && !empty($_POST['challenge-input-path']) && !empty($_POST['challenge-input-date'])){
						try{
							$manager = ChallengeManager::getInstance();
							$rep = $manager->add($_POST['challenge-input-question'], $_POST['challenge-input-answer'],$_POST['challenge-input-description'], $_POST['challenge-input-path'], $_POST['challenge-input-date']);
							if($rep){
								$message = new Message(1);
								$message->addMessage("Votre concours a bien ete ajoute avec succes.");
						
								$SMM = SessionMessageManager::getInstance();
								$SMM->setSessionMessage($message);
								URLUtils::redirection(URLUtils::generateURL($module->getName(), array()));
							}else{
								$message = new Message(3);
								$message->addMessage("Une erreur s'est produite, l'ajout de votre concours a echoue.");
							}
						}catch(SQLException $sqle){
							$message = new Message(3);
							$message->addMessage("Une erreur s'est produite, l'ajout de votre concours a echoue.");
							$message->addMessage($sqle->getMessage());
						}catch(DatabaseExcetion $dbe){
							$message = new Message(3);
							$message->addMessage("Une erreur s'est produite, l'ajout de votre concours a echoue.");
							$message->addMessage($dbe->getMessage());
						}
					}else{
						$message = new Message(3);
						$message->addMessage("Au moins un des champs est vide.");
					}
				}
				$view->pageFormChallenge('add', $message, $challenge);
			}else{
				throw new AccessRefusedException("Vous ne pouvez pas ajouter de concours.");
			}
			break;
		//edit a challenge
		case "edit":
			if (RoleManager::getInstance()->hasCapabilitySession ( 'challenge-edit-challenge' )) {
				$message = new Message();
				if(isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id'])){
					$manager = ChallengeManager::getInstance();
					$challenge = $manager->getChallenge($_GET['id']);
					if(isset($_POST['challenge-input-question']) && isset($_POST['challenge-input-answer']) && isset($_POST['challenge-input-description']) && isset($_POST['challenge-input-path']) && isset($_POST['challenge-input-date'])){
						if(!empty($_POST['challenge-input-question']) && !empty($_POST['challenge-input-answer']) && !empty($_POST['challenge-input-description']) && !empty($_POST['challenge-input-path']) && !empty($_POST['challenge-input-date'])){
							$challenge->setQuestion($_POST['challenge-input-question']);
							$challenge->setAnswer($_POST['challenge-input-answer']);
							$challenge->setDescription($_POST['challenge-input-description']);
							$challenge->setPath_picture($_POST['challenge-input-path']);
							$challenge->setEnd_date($_POST['challenge-input-date']);
							try{
								$manager = ChallengeManager::getInstance();
								$manager->update($_GET['id'],$_POST['challenge-input-question'], $_POST['challenge-input-answer'],$_POST['challenge-input-description'], $_POST['challenge-input-path'], $_POST['challenge-input-date']);
								
								$message = new Message(1);
								$message->addMessage("Votre concours a bien ete ajoute avec succes.");
								$SMM = SessionMessageManager::getInstance();
								$SMM->setSessionMessage($message);
								URLUtils::redirection(URLUtils::generateURL($module->getName(), array()));	
							}catch(SQLException $sqle){
								$message = new Message(3);
								$message->addMessage("Une erreur s'est produite, l'ajout de votre concours a echoue.");
								$message->addMessage($sqle->getMessage());
							}catch(DatabaseExcetion $dbe){
								$message = new Message(3);
								$message->addMessage("Une erreur s'est produite, l'ajout de votre concours a echoue.");
								$message->addMessage($dbe->getMessage());
							}
						}else{
							$message = new Message(3);
							$message->addMessage("Au moins un des champs est vide.");
						}
					}		
					$view->pageFormChallenge('edit', $message, $challenge);
				}else{
					$message = new Message(3);
					$message->addMessage("Supression impossible, car il manque l'id du concours a supprimer.");
				}
			}else{
				throw new AccessRefusedException("Vous ne pouvez pas editer de concours.");
			}
			break;
			// delete a challenge
		case "delete" :
			if (RoleManager::getInstance ()->hasCapabilitySession ( 'challenge-delete-challenge' )) {
				if(isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id'])){
					try{
						$manager = ChallengeManager::getInstance();
						$manager->delete($_GET['id']);
						
						$message = new Message(1);
						$message->addMessage("Votre concours a bien ete supprime a jour avec succes.");
					}catch(SQLException $sqle){
						$message = new Message(3);
						$message->addMessage("Une erreur s'est produite, la suppression de votre concours a echoue.");
						$message->addMessage($sqle->getMessage());
					}catch(DatabaseExcetion $dbe){
						$message = new Message(3);
						$message->addMessage("Une erreur s'est produite,la suppression de votre concours a echoue.");
						$message->addMessage($dbe->getMessage());
					}
				}else{
					$message = new Message(3);
					$message->addMessage("Supression impossible, car il manque l'id du concours a supprimer.");
				}
				$SMM = SessionMessageManager::getInstance();
				$SMM->setSessionMessage($message);
				URLUtils::redirection(URLUtils::generateURL($module->getName(), array()));
			} else {
				throw new AccessRefusedException ( "Vous ne pouvez pas supprimer de concours." );
			}
			break;
		// validate an answer
		case "validate":
			if (RoleManager::getInstance()->hasCapabilitySession ( 'challenge-check-answer' )) {
				$message = new Message();
				if(isset($_GET['cid']) && (!empty($_GET['cid'])) && is_numeric($_GET['cid'])){
					if(isset($_GET['aid']) && (!empty($_GET['aid'])) && is_numeric($_GET['aid'])){
						try{
							$manager = ChallengeManager::getInstance();
							$rep = $manager->updateCorrect($_GET['aid'],true);
							if($rep){
								$message->setType(1);
								$message->addMessage("La reponse a ete validee avec sucess.");
							}else{
								$message->setType(3);
								$message->addMessage("une erreur SQL s est produite");
							}
						}catch(SQLException $sqle){
							$message = new Message(3);
							$message->addMessage("Une erreur s'est produite, la suppression de votre concours a echoue.");
							$message->addMessage($sqle->getMessage());
						}catch(DatabaseExcetion $dbe){
							$message = new Message(3);
							$message->addMessage("Une erreur s'est produite,la suppression de votre concours a echoue.");
							$message->addMessage($dbe->getMessage());
						}
					}else{
						$message->setType(3);
						$message->addMessage("L'identifiant de la reponse est vide ou inexistante.");
					}		
				}else{
					$message->setType(3);
					$message->addMessage("L'identifiant du challenge est vide ou inexistante.");
				}
				$SMM = SessionMessageManager::getInstance();
				$SMM->setSessionMessage($message);
				URLUtils::redirection(URLUtils::generateURL($module->getName(), array("detail" => $_GET['cid'])));
			}else{
				throw new AccessRefusedException("Vous ne pouvez pas corriger les reponses.");
			}
			break;
		// invalidate an answer
		case "invalidate" :
			if (RoleManager::getInstance()->hasCapabilitySession ( 'challenge-check-answer' )) {
				$message = new Message();
				if(isset($_GET['cid']) && (!empty($_GET['cid'])) && is_numeric($_GET['cid'])){
					if(isset($_GET['aid']) && (!empty($_GET['aid'])) && is_numeric($_GET['aid'])){
						try{
							$manager = ChallengeManager::getInstance();
							$rep = $manager->updateCorrect($_GET['aid'],false);
							if($rep){
								$message->setType(1);
								$message->addMessage("La reponse a ete invalidee avec sucess.");
							}else{
								$message->setType(3);
								$message->addMessage("une erreur SQL s est produite");
							}
						}catch(SQLException $sqle){
							$message = new Message(3);
							$message->addMessage("Une erreur s'est produite, la suppression de votre concours a echoue.");
							$message->addMessage($sqle->getMessage());
						}catch(DatabaseExcetion $dbe){
							$message = new Message(3);
							$message->addMessage("Une erreur s'est produite,la suppression de votre concours a echoue.");
							$message->addMessage($dbe->getMessage());
						}
					}else{
						$message->setType(3);
						$message->addMessage("L'identifiant de la reponse est vide ou inexistante.");
					}
				}else{
					$message->setType(3);
					$message->addMessage("L'identifiant du challenge est vide ou inexistante.");
				}
				$SMM = SessionMessageManager::getInstance();
				$SMM->setSessionMessage($message);
				URLUtils::redirection(URLUtils::generateURL($module->getName(), array("detail" => $_GET['cid'])));
			}else{
				throw new AccessRefusedException("Vous ne pouvez pas corriger les reponses.");
			}
			break;
		// generate the winner of a challenge
		case "genwinner" :
			if (RoleManager::getInstance()->hasCapabilitySession ( 'challenge-check-answer' )) {
				$message = new Message();
				$manager = ChallengeManager::getInstance();
					
				if(ConversionUtils::timestampTotimestampUnix($manager->getChallenge($_GET['id'])->getEnd_date()) <= time()){
					$list = $manager->getListUnCorrectAnswer($_GET['id']);
					if(count($list) > 0){
						$message->setType(3);
						$message->addMessage("Un gagnant ne sera designe que lorsque toutes les reponses auront ete corrigees.");
					}else{
						$list = $manager->getListCorrectAnswer($_GET['id']);
						if(count($list) > 0){
							$i = rand(0, count($list)-1);
							$ans = $list[$i];
							$manager->setWinner($_GET['id'],$ans->getUserid());
				
							$managerU = UserManager::getInstance();
							$user = $managerU->getUserById($ans->getUserid());
				
							$text = "Le gagnant pour le concours " . $_GET['id'] . " est le user " . $ans->getUserid() . " ( <a href=\"#\">".$user->getUsername()."</a> ).<br /> Il faut maintenant lui envoyer un mail a son adresse : " . $user->getMail() . ".<br /> Le resultat est deja publie sur le site, si le module concours est actif.";
				
							$message->setType(1);
							$message->addMessage($text);
						}else{
							$message->setType(3);
							$message->addMessage("Non mais en fait, pour avoir un gagnant, il faut au moins 1 bonne reponse dans le concnours, banane !");
						}
					}
				}else{
					$message->setType(3);
					$message->addMessage("Le concours n'est pas finis. Le gagnant ne peut etre designer que lorsque le concours est finis.");
				}
				$SMM = SessionMessageManager::getInstance();
				$SMM->setSessionMessage($message);
				URLUtils::redirection(URLUtils::generateURL($module->getName(), array("detail" => $_GET['id'])));			
			}else{
				throw new AccessRefusedException("Vous ne pouvez pas corriger les reponses.");
			}
			break;
		default :
			echo "action inconnue !!";
	}
}else{
	
	// particular challenge
	if(isset($_GET['detail']) && !empty($_GET['detail']) && is_numeric($_GET['detail'])){
		if (RoleManager::getInstance()->hasCapabilitySession ( 'challenge-read-detail' )) {			
			if(isset($_GET['detail']) && !empty($_GET['detail'])){
				$manager = ChallengeManager::getInstance();
				$challenge = $manager->getChallenge($_GET['detail']);
				$answers = $manager->getListAnswer($_GET['detail']);
				
				$view->pageDetailChallenge($challenge, $answers);
			}else{
				$message = new Message(3);
				$message->addMessage ( "L'identifiant du concours est mauvais/manquant/pas au bon format." );
				
				$SMM = SessionMessageManager::getInstance();
				$SMM->setSessionMessage($message);
				URLUtils::redirection ( URLUtils::generateURL ( $module->getName (), array () ) );
			}
		}else{
			throw new AccessRefusedException("Vous ne pouvez pas lire les details des concours.");
		}
	}else{
		//list of challenge
		if (RoleManager::getInstance()->hasCapabilitySession ( 'challenge-read-list' )) {
			$manager = ChallengeManager::getInstance();
			$list = $manager->getListChallenge();
			$view->pageListChallenge($list);
		}else{
			throw new AccessRefusedException("Vous ne pouvez pas lire la liste de concours.");
		}
	}
	
}