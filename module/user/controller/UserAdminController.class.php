<?php


class UserController{
	
	/**
	 * edit a User Profile with the Admin Right
	 * @param array $request : the GET and POST variables to identify and modify the Profile
	 * @return array $arr : containing a Message and a User Objects
	 */
	public static function editAdminAction(array $request){
		$message = new Message(3);
		$umanager = UserManager::getInstance();
		$user = $umanager->getUserById($request['id']);
		if (isset ( $request ['user-input-username'] ) && isset ( $request ['user-input-password'] ) && isset ( $request ['user-input-email'] )) {
			$user->setMail(ConversionUtils::encoding (($request ['user-input-email'])));
			$user->setName(ConversionUtils::encoding (($request ['user-input-name'])));
			$user->setFirstname((($request ['user-input-firstname'])));
			$user->setAddress(ConversionUtils::encoding (($request ['user-input-local'])));
			$user->setSchool(ConversionUtils::encoding (($request ['user-input-school'])));
			$user->setSection(ConversionUtils::encoding (($request ['user-input-section'])));
			if(!empty($request['user-input-username']) && ! empty($request['user-input-password']) && ! empty ( $request ['user-input-email'])){
				
				$message->setType(1);
				
				if (! preg_match ( "#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $request ['user-input-email'] )) {
					$message->setType ( 3 );
					$message->addMessage ( "L'adresse mail ne respecte pas le format suivant xxxxxx@xxxxx.xx" );
				}
			
				// Login ne contient que des lettres et des chiffres et ._-
				if (preg_match ( "[^0-9a-zA-Z._-]+", $request ['user-input-username'] )) {
					$message->setType ( 3 );
					$message->addMessage ( "Votre <b>login</b> contient d'autres caractres que lettres et chiffres." );
				}
			
				// the lenght of the password and the login must be > 4
				if ((strlen ( $request ['user-input-password'] ) < 4) || (strlen ( $request ['user-input-username'] ) < 4)) {
					$message->setType ( 3 );
					$message->addMessage("Votre login et/ou mot de passe sont <b>trop court(s)</b>.");
				}
				
				// PROCESS if ok
				if ($message->isEmpty ()) {
					try {
						// add in the DB
						if(isset($request['input-passmd5']) && $request['input-passmd5']=='ok'){
							$user->setPassword(md5($request ['user-input-password']));
						}else{
							$user->setPassword($request ['user-input-password']);
						}
						
						$mv = 0;
						if (! empty ( $request ['user-input-mailwatch'] )) {
							$mv = 1;
						}
						$user->setMailwatch($mv);
						
						$dv = 0;
						if (! empty ( $request ['user-input-detview'] )) {
							$dv = 1;
						}
						$user->setViewdet($dv);
						
						$rep = $umanager->update($user->getId(), $user->getPassword(),$user->getMail(), $user->getName(), $user->getFirstname(), $user->getSchool(), $user->getSection(), $user->getAddress(), $user->getMailwatch(), $user->getViewdet(),$user->getIsAdmin(),$user->getIsWebkot());
			
						if ($rep) {
							$message->setType ( 1 );
							$message->addMessage ( "Profile utilisateur mis a jour avec succes." );
						} else {
							$message->setType ( 3 );
							$message->addMessage ( "Une erreur est suvenue, votre inscription n'a pas ete prise en compte.'" );
						}
					}catch(SQLException $sqle){
						$message = new Message(3);
						$message->addMessage("Une erreur s'est produite, l'ajout de votre profile a echoue.");
					}catch(DatabaseExcetion $dbe){
						$message = new Message(3);
						$message->addMessage("Une erreur s'est produite, l'ajout de votre profile a echoue.");
					}
				}
			}else{
				$message->setType(3);
				$message->addMessage("Un des champs requis est vide.");
			}	
		}	
		return array($message, $user);
	}
	
	
	/**
	 * remove a user profile
	 * @param array $request : the GET varibles
	 * @return Message $message : the return Message Object
	 */
	public static function deleteAdminAction($request){
		if(isset($request['id']) && !empty($request['id']) && is_numeric($request['id'])){
			try{
				$managerU = UserManager::getInstance();
				$managerU->delete($request['id']);
		
				$message = new Message(1);
				$message->addMessage("Le suppression de l'utilisateur ".$request['id']." a &eacute;t&eacute; ex&eacute;cut&eacute;e avec succes !");
				$SMM = SessionMessageManager::getInstance();
				$SMM->setSessionMessage($message);
				URLUtils::redirection(URLUtils::getPreviousURL());
			}catch(SQLException $sqle){
				$message = new Message(3);
				$message->addMessage("Une erreur s'est produite, l'ajout de votre profile a echoue.");
			}catch(DatabaseExcetion $dbe){
				$message = new Message(3);
				$message->addMessage("Une erreur s'est produite, l'ajout de votre profile a echoue.");
			}
		}else{
			$message = new Message(3);
			$message->addMessage("Edition impossible, il manque l'identifiant de l'utilisateur.");
		}
		return $message;
	}
	
	/**
	 * Modify the Privilege of a given User Profile
	 * @param array $request : 	$request[uid] : the id of the User
	 * 							$request[rid] : the id of the Privilege
	 * @return array $arr : an array containing a Message and a Role Object
	 */
	public static function grantedAdminAction(array $request){
		$message = new Message(1);
		$role = new Role();
		if(isset($request['uid']) && !empty($request['uid']) && is_numeric($request['uid']) && isset($request['rid']) && !empty($request['rid']) && is_numeric($request['rid'])){
			try{
				$managerU = UserManager::getInstance();
				$user = $managerU->getUserById($request['uid']);
				
				$rmanager = RoleManager::getInstance();
				if($rmanager->existsRole($request['rid'])){
					$role = $rmanager->getRole($request['rid']);
				
					if($rmanager->getMinRole()->getLevel() != $role->getLevel()){
						$managerU->updatePrivilege($request['uid'],$request['rid']);					
						$message = new Message(1);
						$message->addMessage("Le modification du Role de l'utilisateur ".$request['uid']." a &eacute;t&eacute; ex&eacute;cut&eacute;e avec succes ! Son role est maitenant " . $role->getRole());
					}else{
						$message = new Message(3);
						$message->addMessage("Le role minimum d'un utilisateur enregistr&eacute; ne peut etre <i>".$rmanager->getMinRole()->getRole()."</i>");
					}	
				}else{
					$message = new Message(3);
					$message->addMessage("Le Role donn n'existe pas. L'opration est donc impossible.");
				}
			}catch(SQLException $sqle){
				$message = new Message(3);
				$message->addMessage("Une erreur s'est produite, la mise a jour du Role du profile a choue.");
			}catch(DatabaseExcetion $dbe){
				$message = new Message(3);
				$message->addMessage("Une erreur s'est produite, la mise a jour du Role du profile a choue.");
			}
		}else{
			$message = new Message(3);
			$message->addMessage("Modification impossible, il manque l'identifiant de l'Utilisateur ou celui du Role.");
		}
		return array($message, $role);
	}
	
	
	
	/**
	 * Modify the Mailwatch of a given User Profile
	 * @param array $request
	 * @return Message
	 */
	public static function editMailwatchAction(array $request){
		$message = new Message(1);
		if(isset($request['id']) && !empty($request['id']) && is_numeric($request['id']) && isset($request['value']) && !empty($request['value'])){
			try{
				if($request['value'] == 'true' || $request['value'] == 'false'){
					$mailwatch = ($request['value'] == 'true' ? '1' : '0');
						
					$managerU = UserManager::getInstance();
					$managerU->updateMailwatch($request['id'], $mailwatch);
					$message = new Message(1);
					if($mailwatch == '0'){
						$message->addMessage("Le d&eacute;sabonnement de l'utilisateur ".$request['id']." a &eacute;t&eacute; ex&eacute;cut&eacute;e avec succes !");
					}else{
						$message->addMessage("L'abonnement de l'utilisateur ".$request['id']." a &eacute;t&eacute; ex&eacute;cut&eacute;e avec succes !");
					}
				}else{
					$message = new Message(3);
					$message->addMessage("La valeur du Mailwatch n'est pas valide.");
				}	
			}catch(SQLException $sqle){
				$message = new Message(3);
				$message->addMessage("Une erreur s'est produite, la mise a jour du Mailwatch du profile a choue.");
			}catch(DatabaseExcetion $dbe){
				$message = new Message(3);
				$message->addMessage("Une erreur s'est produite, la mise a jour du Mailwatch du profile a choue.");
			}
		}else{
			$message = new Message(3);
			$message->addMessage("Modification impossible, il manque l'identifiant de l'Utilisateur.");
		}
		return $message;
	}
	
	
}