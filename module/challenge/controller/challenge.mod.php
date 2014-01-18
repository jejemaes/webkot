<?php
/*
 * Created on 10 nov. 2012
*
* MAES Jerome, Webkot 2012-2013
* Class Description :
*
*/

$view = new ChallengeView($template, $module);


if(isset($_POST['challenge-input-answer'])){
	if(!empty($_POST['challenge-input-answer']) && (isset($_POST['challenge-input-challengeid'])) && !empty($_POST['challenge-input-challengeid']) && is_numeric($_POST['challenge-input-challengeid'])){
		if (RoleManager::getInstance ()->hasCapabilitySession ( 'challenge-answer-challenge' )) {
			try{
				$ms = SessionManager::getInstance();

				$manager = ChallengeManager::getInstance();
				$rep = $manager->addAnswer($_POST['challenge-input-challengeid'], $ms->getUserprofile()->getId(), $_POST['challenge-input-answer']);
				
				$message = new Message(1);
				$message->addMessage("Votre reponse a bien ete prise en compte.");
				
			}catch(SQLException $sqle){
				$message = new Message(3);
				$message->addMessage("Votre reponse n'a pas ete prise en compte.");
			}catch(DatabaseExcetion $dbe){
				$message = new Message(3);
				$message->addMessage("Votre reponse n'a pas ete prise en compte suite a un probleme de base de donnŽes.");
			}
			// avoid to send the request again if the user actualize the page
			unset($_POST);
		}else{
			$message = new Message(3);
			$message->addMessage("Vous ne pouvez pas participer par manque de privileges.");
		}
	}else{
		$message = new Message(3);
		$message->addMessage("Le champ est vide !");
	}
}else{
 	//nothing
	$message = new Message(0);
}



if (RoleManager::getInstance()->hasCapabilitySession ( 'challenge-read-challenge' )) {
	
	try {
		$manager = ChallengeManager::getInstance ();
		$challenge = $manager->getLastChallenge ();
		
		if($challenge->getEnd_date() <= date('Y-m-d H:i:s')){
			if($challenge->getWinnerid() != null){
				$managerU = UserManager::getInstance();
				$user = $managerU->getUserById($challenge->getWinnerid());
				
				$text = "<p>D&eacute;sol&eacute;, mais il est trop tard. <br />Le concours est termin&eacute;. La bonne r&eacute;ponse &eacute;tait : <strong>'".$challenge->getAnswer()."'</strong>. Le grand gagnant de ce concours est <strong><a href=\"".URLUtils::getUserLinkId($user->getId())."\">".$user->getUsername()."</a></strong>. Prenez contact avec nous pour discuter de la r&eacute;compense. Pour les autres, &agrave; la prochaine ;)<br />Le Webkot</p>";
			}else{
				$text = "Le concours est terminŽ, mais le gagnant n'a pas encore ŽtŽ tirŽ au sort. Un peu de patience ...";
			}
			$view->pageChallengeText($challenge,$text, $message);
		}else{
			if (RoleManager::getInstance ()->hasCapabilitySession ( 'challenge-answer-challenge' )) {
				$managerSess = SessionManager::getInstance();
				if($managerSess->existsUserSession()){
		
					if ($manager->existsAnswer($managerSess->getUserprofile()->getId(),$challenge->getId())){
						$view->pageChallengeText($challenge, "Vous avez deja repondu a ce concours !", $message);
					}else{
						$view->pageChallengeForm($challenge, $message);
					}
				}else{
					$view->pageChallengeText($challenge, "Vous devez absolument etre connecte pour repondre a ce challenge.", $message);
				}
			} else {
				$view->pageChallengeText($challenge, "Vous devez etre connecte pour repondre a ce challenge.",$message);
			}
		}
		
	} catch ( DatabaseException $dbe ) {
		$logger->logwarn ( "Connection impossible ˆ la Base de donnees." );
		$view->error ( new Error ( "Erreur de BD", "Connection impossible ˆ la Base de donnees" ) );
	} catch ( SQLException $sqle ) {
		$logger->logwarn ( "Erreur SQL : " . $sqle->getDescription () );
		$view->error ( new Error ( "Erreur SQL", $sqle->getDescription () ) );
	} catch ( NullObjectException $ne ) {
		$logger->logwarn ( "Erreur NullException : pas de profile pour ce numero" );
		$view->error ( new Error ( "Erreur de profile", "Il n'y a pas de User Profile ici. Circulez !" ) );
	}
} else {
	throw new AccessRefusedException ( "Vous ne pouvez pas consulter cette page." );
}




?>
