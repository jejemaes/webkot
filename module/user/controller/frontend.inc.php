<?php

$view = new UserView($template,$module);


if(isset($_GET['action']) && !empty($_GET['action'])){
	
	switch ($_GET['action']) {
	    case "edit":
	        if (RoleManager::getInstance()->hasCapabilitySession('user-edit-profile')) {
	        	$logger->loginfo ( "Edit user own profile action" );
	        	$message = new Message();
	        	
	        	$smanager = SessionManager::getInstance();
	        	$profile = $smanager->getUserprofile();
	        	$UserManager = UserManager::getInstance();
	        	$user = $UserManager->getUserById($profile->getId());
	        	
	        	if (isset ( $_POST ['send'] ) && ! empty ( $_POST ['send'] ) && ($_POST ['send'] == 'yes')) {
	        		$data ['id'] = $profile->getId();
	        		$data ['username'] = ConversionUtils::encoding ( ($_POST ['user-input-username']) );
	        		$data ['password'] = md5 ( $_POST ['user-input-password'] );
	        		$data ['mail'] = ConversionUtils::encoding ( ($_POST ['user-input-mail']) );
	        		$data ['name'] = ConversionUtils::encoding ( ($_POST ['user-input-name']) );
	        		$data ['firstname'] = ConversionUtils::encoding ( ($_POST ['user-input-firstname']) );
	        		$data ['address'] = ConversionUtils::encoding ( ($_POST ['user-input-local']) );
	        		$data ['school'] = ConversionUtils::encoding ( ($_POST ['user-input-school']) );
	        		$data ['section'] = ConversionUtils::encoding ( ($_POST ['user-input-section']) );
	        		$data ['mailwatch'] = 0;
	        		if (! empty ( $_POST ['user-input-mailwatch'] )) {
	        			$data ['mailwatch'] = 1;
	        		}
	        		$data ['detview'] = 0;
	        		if (! empty ( $_POST ['user-input-detview'] )) {
	        			$data ['detview'] = 1;
	        		}
	        		$user = new User($data);
	        			
	        		if (isset ( $_POST ['user-input-username'] ) && ! empty ( $_POST ['user-input-username'] ) && isset ( $_POST ['user-input-password'] ) && ! empty ( $_POST ['user-input-password'] ) && isset ( $_POST ['user-input-mail'] ) && ! empty ( $_POST ['user-input-mail'] ) && isset ( $_POST ['user-input-password-confirm'] ) && ! empty ( $_POST ['user-input-password-confirm'] )) {
	        	
	        			if (! preg_match ( "#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $_POST ['user-input-mail'] )) {
	        				$message->setType ( 3 );
	        				$message->addMessage ( "L'adresse mail ne respecte pas le format suivant xxxxxx@xxxxx.xx" );
	        			}
	        	
	        			// Login ne contient que des lettres et des chiffres ?;
	        			if (preg_match ( "[^0-9a-zA-Z]+", $_POST ['user-input-username'] )) {
	        				$message->setType ( 3 );
	        				$message->addMessage ( "Votre <b>login</b> contient d'autres caract�res que lettres et chiffres." );
	        			}
	        	
	        			// Mot de passe correctement confirm&eacute; ?;
	        			if ($_POST ['user-input-password'] != $_POST ['user-input-password-confirm']) {
	        				$message->setType ( 3 );
	        				$message->addMessage ( "Votre confirmation de <b>mot de passe</b> est erron&eacute;e." );
	        			}
	        	
	        			// the lenght of the password and the login must be > 4
	        			if ((strlen ( $_POST ['user-input-password'] ) < 4) || (strlen ( $_POST ['user-input-username'] ) < 4)) {
	        				$message->setType ( 3 );
	        				$message->addMessage ( "Votre login et/ou mot de passe sont <b>trop court(s)</b>." );
	        			}
	        			
	        			// PROCESS if ok
	        			if ($message->isEmpty ()) {
	        				try {
        					
        						// add in the DB
        						$data ['username'] = ConversionUtils::encoding ( ($_POST ['user-input-username']) );
        						$data ['password'] = md5 ( $_POST ['user-input-password'] );
        						$data ['mail'] = ConversionUtils::encoding ( ($_POST ['user-input-mail']) );
        						$data ['name'] = ConversionUtils::encoding ( ($_POST ['user-input-name']) );
        						$data ['firstname'] = ConversionUtils::encoding ( ($_POST ['user-input-firstname']) );
        						$data ['address'] = ConversionUtils::encoding ( ($_POST ['user-input-local']) );
        						$data ['school'] = ConversionUtils::encoding ( ($_POST ['user-input-school']) );
        						$data ['section'] = ConversionUtils::encoding ( ($_POST ['user-input-section']) );
        							
        						$data ['mailwatch'] = 0;
        						if (! empty ( $_POST ['user-input-mailwatch'] )) {
        							$data ['mailwatch'] = 1;
        						}
        						$data ['detview'] = 0;
        						if (! empty ( $_POST ['user-input-detview'] )) {
        							$data ['detview'] = 1;
        						}
        						$user = $UserManager->getUserById($profile->getId());
        						$rep = $UserManager->update($user->getId(), md5($_POST ['user-input-password']),$data['mail'], $data ['name'], $data ['firstname'], $data ['school'], $data ['section'], $data ['address'], $data ['mailwatch'], $data ['detview'],$user->getIsAdmin(),$user->getIsWebkot());
        						
        						// update the session var too
        					//	$smanager = SessionManager::getInstance();
        					//	$smanager->setUserprofile($user);
        						if ($rep) {
        							/*$TextConnected = '<div>Inscription accomplie avec succes !</div><br>Un comtpe vient d\'etre cree avec les informations suivantes ;<ul>
										<li>Login : ' . addslashes ( $_POST ['user-input-username'] ) . '</li>
										<li>eMail : ' . addslashes ( $_POST ['user-input-mail'] ) . '</li>
										<li>Nom : ' . addslashes ( $_POST ['user-input-name'] ) . '</li>
										<li>Prenom : ' . addslashes ( $_POST ['user-input-firstname'] ) . '</li>
										<li>Adresse : ' . addslashes ( $_POST ['user-input-local'] ) . '</li>
										<li>Ecole : ' . addslashes ( $_POST ['user-input-school'] ) . ' </li>
										<li>Section : ' . addslashes ( $_POST ['user-input-section'] ) . '</li></ul><br>
									Un email de confirmation vous a ete envoye. Vous pouvez maintenant vous connecter avec le mot de passe y figurant (celui que vous avez introduit).<center><a href="index.php">Cliquez ici pour vous connecter</a></center>';
        	*/
        							$user = $UserManager->getUserById($profile->getId());
        							$smanager->setUserprofile($user);
        							
        							$message->setType ( 1 );
        							$message->addMessage ( "Profile mis a jour avec succes." );
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
	        		} else {
	        			$message->setType ( 3 );
	        			$message->addMessage ( "Au moins une des variables requises est vide." );
	        		}
	        	}
	        	$view->pageInscription('edit', $user, $message);
	        } else {
				throw new AccessRefusedException ( "Vous ne pouvez pas consulter cette page." );
			}
	        break;
	        
	    case "inscription":
	    	if (RoleManager::getInstance ()->hasCapabilitySession ( 'user-inscription' )) {
				$logger->loginfo ( "Display the inscription form" );
				
				$message = new Message ();
				$user = new User();
				
				if (isset ( $_POST ['send'] ) && ! empty ( $_POST ['send'] ) && ($_POST ['send'] == 'yes')) {
						$data ['username'] = ConversionUtils::encoding ( ($_POST ['user-input-username']) );
						$data ['password'] = md5 ( $_POST ['user-input-password'] );
						$data ['mail'] = ConversionUtils::encoding ( ($_POST ['user-input-mail']) );
						$data ['name'] = ConversionUtils::encoding ( ($_POST ['user-input-name']) );
						$data ['firstname'] = ConversionUtils::encoding ( ($_POST ['user-input-firstname']) );
						$data ['address'] = ConversionUtils::encoding ( ($_POST ['user-input-local']) );
						$data ['school'] = ConversionUtils::encoding ( ($_POST ['user-input-school']) );
						$data ['section'] = ConversionUtils::encoding ( ($_POST ['user-input-section']) );	
						$data ['mailwatch'] = 0;
						if (! empty ( $_POST ['user-input-mailwatch'] )) {
							$data ['mailwatch'] = 1;
						}
						$data ['detview'] = 0;
						if (! empty ( $_POST ['user-input-detview'] )) {
							$data ['detview'] = 1;
						}
						$user = new User($data);
					
					if (isset ( $_POST ['user-input-username'] ) && ! empty ( $_POST ['user-input-username'] ) && isset ( $_POST ['user-input-password'] ) && ! empty ( $_POST ['user-input-password'] ) && isset ( $_POST ['user-input-mail'] ) && ! empty ( $_POST ['user-input-mail'] ) && isset ( $_POST ['user-input-password-confirm'] ) && ! empty ( $_POST ['user-input-password-confirm'] ) && isset ( $_POST ["recaptcha_response_field"] ) && (! empty ( $_POST ["recaptcha_response_field"] ))) {
						
						if (! preg_match ( "#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $_POST ['user-input-mail'] )) {
							$message->setType ( 3 );
							$message->addMessage ( "L'adresse mail ne respecte pas le format suivant xxxxxx@xxxxx.xx" );
						}
						
						// Login ne contient que des lettres et des chiffres et ._-
						if (preg_match ( "[^0-9a-zA-Z._-]+", $_POST ['user-input-username'] )) {
							$message->setType ( 3 );
							$message->addMessage ( "Votre <b>login</b> contient d'autres caract�res que lettres et chiffres." );
						}
						
						// Mot de passe correctement confirm&eacute; ?;
						if ($_POST ['user-input-password'] != $_POST ['user-input-password-confirm']) {
							$message->setType ( 3 );
							$message->addMessage ( "Votre confirmation de <b>mot de passe</b> est erron&eacute;e." );
						}
						
						// the lenght of the password and the login must be > 4
						if ((strlen ( $_POST ['user-input-password'] ) < 4) || (strlen ( $_POST ['user-input-username'] ) < 4)) {
							$message->setType ( 3 );
							$message->addMessage ( "Votre login et/ou mot de passe sont <b>trop court(s)</b>." );
						}
						
						// check the CAPTCHA
						$resp = recaptcha_check_answer ( CAPTCHA_PRIVATE_KEY, $_SERVER ["REMOTE_ADDR"], $_POST ["recaptcha_challenge_field"], $_POST ["recaptcha_response_field"] );
						
						if (! $resp->is_valid) {
							$message->setType ( 3 );
							$message->addMessage ( "Le Captcha est <b>mauvais</b>." );
						}
						
						// PROCESS if ok
						if ($message->isEmpty ()) {
							try {
								$manager = UserManager::getInstance ();
								$used = $manager->loginAlreadyUsed ( ConversionUtils::encoding ( $_POST ['user-input-username'] ) );
								
								if (! $used) {
									// add in the DB
									$data ['username'] = ConversionUtils::encoding ( ($_POST ['user-input-username']) );
									$data ['password'] = md5 ( $_POST ['user-input-password'] );
									$data ['mail'] = ConversionUtils::encoding ( ($_POST ['user-input-mail']) );
									$data ['name'] = ConversionUtils::encoding ( ($_POST ['user-input-name']) );
									$data ['firstname'] = ConversionUtils::encoding ( ($_POST ['user-input-firstname']) );
									$data ['address'] = ConversionUtils::encoding ( ($_POST ['user-input-local']) );
									$data ['school'] = ConversionUtils::encoding ( ($_POST ['user-input-school']) );
									$data ['section'] = ConversionUtils::encoding ( ($_POST ['user-input-section']) );
									
									$data ['mailwatch'] = 0;
									if (! empty ( $_POST ['user-input-mailwatch'] )) {
										$data ['mailwatch'] = 1;
									}
									$data ['detview'] = 0;
									if (! empty ( $_POST ['user-input-detview'] )) {
										$data ['detview'] = 1;
									}
									
									$role = RoleManager::getInstance()->getMinSubscriberRole();
									$rep = $manager->add($data, $role->getId());
									if ($rep) {
										// send confirmation mail
										$headers  = 'MIME-Version: 1.0' . "\r\n";
										$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
										system_send_mail("Inscription sur le Webkot", "Confirmation de votre inscription sur le site du Webkot avec le login : " . $_POST ['user-input-username'] . ".\n Votre mot de passe est : " . $_POST ['user-input-password'] . ".\n Si vous REFUSEZ cette inscription, r&eacute;pondez � cet e-mail en laissant le texte tel qu'il est.", $_POST ['user-input-mail'], "admin@webkot.be", $headers);
										
										$TextConnected = '<div>Inscription accomplie avec succes !</div><br>Un comtpe vient d\'etre cree avec les informations suivantes ;<ul>
											<li>Login : ' . addslashes ( $_POST ['user-input-username'] ) . '</li>
											<li>eMail : ' . addslashes ( $_POST ['user-input-mail'] ) . '</li>
											<li>Nom : ' . addslashes ( $_POST ['user-input-name'] ) . '</li>
											<li>Prenom : ' . addslashes ( $_POST ['user-input-firstname'] ) . '</li>
											<li>Adresse : ' . addslashes ( $_POST ['user-input-local'] ) . '</li>
											<li>Ecole : ' . addslashes ( $_POST ['user-input-school'] ) . ' </li>
											<li>Section : ' . addslashes ( $_POST ['user-input-section'] ) . '</li></ul><br>
										Un email de confirmation vous a ete envoye. Vous pouvez maintenant vous connecter avec le mot de passe y figurant (celui que vous avez introduit).<center><a href="index.php">Cliquez ici pour vous connecter</a></center>';
										
										$message->setType ( 1 );
										$message->addMessage ( $TextConnected );
									} else {
										$message->setType ( 3 );
										$message->addMessage ( "Une erreur est suvenue, votre inscription n'a pas ete prise en compte.'" );
									}
								} else {
									$message->setType ( 3 );
									$message->addMessage ( "Le login que vous avez choisi est d&eacutej&agrave; utilis&eacute;. Veuillez en prendre un autre." );
								}
							}catch(SQLException $sqle){
								$message = new Message(3);
								$message->addMessage("Une erreur s'est produite, l'ajout de votre profile a echoue."); 
							}catch(DatabaseExcetion $dbe){
								$message = new Message(3);
								$message->addMessage("Une erreur s'est produite, l'ajout de votre profile a echoue.");
							}
						}
					} else {
						$message->setType ( 3 );
						$message->addMessage ( "Au moins une des variables requises est vide." );
					}
				}
	    		$view->pageInscription('add',$user, $message);
			} else {
				$user = SessionManager::getInstance()->getUserprofile();
				if($user){
					URLUtils::redirection(URLUtils::getUserPageURL($user->getId()));
				}else{
					URLUtils::redirection(URL);
				}
				//throw new AccessRefusedException ( "Vous ne pouvez pas consulter cette page." );
			}
	        break;
	   	case "lostpassword":
	   		if (RoleManager::getInstance ()->hasCapabilitySession ( 'user-inscription' )) {
		   		$message = new Message ();
		   		if(isset($_POST['user-input-lostpassword']) && !empty($_POST['user-input-lostpassword'])){
		   			
		   			if (!preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $_POST['user-input-lostpassword'])){
		   				$message->setType(3);
		   				$message->addMessage("L'adresse mail ne respecte pas le format suivant xxxxxx@xxxxx.xxx");
		   			}else{	
			   			$password = system_generate_password();
			   			$umanager = UserManager::getInstance();
			   			$rep = $umanager->updatePassword($_POST['user-input-lostpassword'], md5($password));
			   			if($rep){	
			   				$profils = $umanager->getUserByMail($_POST['user-input-lostpassword']);
			   				if(count($profils) == 1){
			   					$u = $profils[0];
				   				$text = "Bonjour ".$u->getUsername().",<br> votre mot de passe a &eacute;t&eacute; r&eacute;initialis&eacute; pour votre/vos compte(s) Webkot utilisant l'adresse mail <i>" . $_POST['user-input-lostpassword'] . "</i>.";
				   				$text .= "<br>Le nouveau mot de passe est : " . $password . ".";
				   				$text .= "<br>Vous pouvez vous connecter et modifier votre mot de passe dans 'Editer Profil' sur " . URL . ".<br><br>Le Webkot.";
			   				}else{
			   					$list = "";
			   					for($i = 0 ; $i<count($profils) ; $i++){
			   						$u = $profils[$i];
			   						$list .= "<i>".$u->getUsername()."</i>";
			   						if($i < (count($profils)-1)){
			   							$list .= ", ";
			   						}
			   					}
			   					$text = "Bonjour,<br> votre mot de passe a &eacute;t&eacute; r&eacute;initialis&eacute; pour votre/vos compte(s) Webkot utilisant l'adresse mail <i>" . $_POST['user-input-lostpassword'] . "</i>.";
			   					$text .= "<br>Le nouveau mot de passe est : " . $password . ".";
			   					$text .= "<br>Les comptes concern&eacute;s sont " . $list;
			   					$text .= "<br>Vous pouvez vous connecter et modifier votre mot de passe dans 'Editer Profil' sur " . URL . ".<br><br>Le Webkot.";
			   				}

			   				$headers  = 'MIME-Version: 1.0' . "\r\n";
				   			$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
				   			system_send_mail("Lost Password", $text, $_POST['user-input-lostpassword'], "admin@webkot.be", $headers);
				   				
			   				$message->addMessage("Un mail a &eacute;t&eacute; envoyer &agrave; " . $_POST['user-input-lostpassword'] . " avec un nouveau mot de passe.");
			   				$message->setType(1);
			   			}else{
				   			$message->addMessage("Aucun mot de passe n'a &eacute;t&eacute; modifi&eacute;, car l'adresse mail n'est utilis&eacute;e par aucun compte Webkot.");
				   			$message->setType(2);
			   			}
		   			}
		   		}
		      	$view->pageLostPassword("form", $message);			
	   		}else{
	   			URLUtils::redirection(URL);
	   		}
	      	break;
	      	
	    case "fb-connect":
	    	if(!SessionManager::getInstance()->existsUserSession()){
	    		$message = new Message();
	    		
	    		// check Facebook connection
	    		$facebook = new Facebook(array(
	    				'appId'  => FACEBOOK_APPID,
	    				'secret' => FACEBOOK_SECRET,
	    		));
	    		
	    		// Get User ID
	    		$fb_id = $facebook->getUser();
	    		
	    		if ($fb_id) {
	    			try {
	    				// Proceed knowing you have a logged in user who's authenticated.
	    				$fbuser = $facebook->api('/me');
	    				
	    				// Process for submitting the forms
	    				if((isset($_POST['input-fb-username']) && isset($_POST['input-fb-password'])) || (isset($_POST['input-wk-username']) && isset($_POST['input-wk-password']))){
	    					if((!empty($_POST['input-fb-username']) && !empty($_POST['input-fb-password'])) || (!empty($_POST['input-wk-username']) && !empty($_POST['input-wk-password']))){
	    						$umanager = UserManager::getInstance();
	    						if(isset($_POST['input-fb-username'])){
	    							//Facebook
	    							$data = array();
	    							$data["username"] = $fbuser["username"];
	    							$data["password"] = md5($_POST['input-fb-password']);
	    							$data["mail"] =$fbuser["username"] . '@facebook.com';
	    							$data["name"] = $fbuser["last_name"];
	    							$data["firstname"] = $fbuser["first_name"];
	    							$data["mailwatch"] = '0';
	    							$data["detview"] = '0';
	    							$data["facebookid"] = $fb_id;
	    						
	    							$rmanager = RoleManager::getInstance();
	    							$role = $rmanager->getMinSubscriberRole()->getId();
	    						
	    							try{
		    							$umanager->add($data, $role);
		    							URLUtils::redirection(URLUtils::generateURL('user', array('profile' =>$fbuser["username"])));
	    							}catch(SQLException $sqle){
	    								$message = new Message(3);
	    								$message->addMessage("Une erreur s'est produite, le nouveau compte n'a pu etre creer.");
	    							}catch(DatabaseExcetion $dbe){
	    								$message = new Message(3);
	    								$message->addMessage("Une erreur s'est produite, le nouveau compte n'a pu etre creer.");
	    							}
	    						}else{
	    							//Webkot
	    							if($umanager->exists($_POST['input-wk-username'], md5($_POST['input-wk-password']))){
	    								$user = $umanager->getUserByLogin($_POST['input-wk-username']);
	    								try{
	    									$umanager->updateFacebookId($user->getId(), $fbuser['id']);	
		    								URLUtils::redirection(URLUtils::generateURL('user', array('profile' => $_POST['input-wk-username'])));
	    								}catch(SQLException $sqle){
											$message = new Message(3);
											$message->addMessage("Une erreur s'est produite, le matching n'a pu etre fait."); 
										}catch(DatabaseExcetion $dbe){
											$message = new Message(3);
											$message->addMessage("Une erreur s'est produite, le matching n'a pu etre fait.");
										}
		    							
	    							}else{
	    								$message->sestType(3);
	    								$message->addMessage("Aucun compte Webkot ne contient ce login/password.");
	    							}
	    						}
	    					}else{
	    						$message->sestType(3);
	    						$message->addMessage("Au moins un des champs requis est vide.");
	    					}
	    				}else{
		    				$fbuser["password"] = system_generate_password();	
	    				}
		    			$view->pageFacebookConnect($fbuser, $message);
	    			} catch (FacebookApiException $e) {
	    				throw new NullObjectException("Les donn&eacute;es de Facebook n'ont pu �tre r&eacute;cup&eacute;r&eacute;es.");
	    			}
	    		}else{throw new InvalidURLException("Aucun compte Facebook n'a &eacute;t&eacute; d&eacute;t&eacute;ct&eacute;.");
	    		}
	    	}else{
	    		throw new InvalidURLException("Vous ne pouvez etre connect&eacute; pour arriver sur cette adresse. Allez, salut !");
	    	}
	    	break;
	    default:
       		echo "switch default : action inconnue.";
	}
	
}else{
	
	if (isset ( $_GET['profile'] )) {
		// display a particular profile
		if(!empty($_GET['profile'])){
			if(RoleManager::getInstance()->hasCapabilitySession('user-read-profile')){
				$logger->loginfo ( "Display the list of public profile" );	
				try{
					// get the manager
					$manager = UserManager::getInstance();
						
					// get the User Object
					if(is_numeric($_GET['profile'])){
						$user = $manager->getUserById($_GET['profile']);
					}else{
						$user = $manager->getUserByLogin($_GET['profile']);
					}
					
					$profile = SessionManager::getInstance()->getUserprofile();
					$isMyProfile = false;
					if($profile){
						$isMyProfile = ($profile->getId() == $user->getId() ? true : false);
					}
					
					$view->UserProfilePage($user,$isMyProfile);
				}catch(DatabaseException $dbe){
					$logger->logwarn("Connection impossible a la Base de donnees.");
 					$view->error(new Error("Erreur de BD", "Connection impossible a la Base de donnees"));
				}catch(SQLException $sqle){
					$logger->logwarn("Erreur SQL : " . $sqle->getDescription());
 					$view->error(new Error("Erreur SQL", $sqle->getDescription()));
				}catch(NullObjectException $ne){
					$logger->logwarn("Erreur NullException : pas de profile pour ce numero");
 					$view->error(new Error("Erreur de profile", "Il n'y a pas de User Profile ici. Circulez !"));
				}
	
			} else {
				throw new AccessRefusedException ( "Vous devez etre connecté pour pouvoir voir le profil des autres utilisateurs." );
			}
	
		}else{
			throw new InvalidURLException("Il n'y a pas de profile a cette adresse.");
		}
	} else {
	
		// display the list of all public profile
		if(RoleManager::getInstance()->hasCapabilitySession('user-read-profile')){
			$logger->loginfo ( "Display the list of public profile" );
			try {
				$desc = system_get_desc_pagination();
				$page = (system_get_page_pagination()-1);
				$limit = ($page*$desc);
	
				$manager = UserManager::getInstance();
				$listUser = $manager->getListPublicUser($limit, $desc);
	
				$count = $manager->getCountUsers("viewdet = '1'");
	
				$view->ListProfilePage($listUser, $count, ($page+1), $desc);
	
			} catch ( DatabaseException $dbe ) {
				$logger->logwarn("Connection impossible � la Base de donnees.");
 				$view->error(new Error("Erreur de BD", "Connection impossible � la Base de donnees"));
			} catch ( SQLException $sqle ) {
				$logger->logwarn("Erreur SQL : " . $sqle->getDescription());
 				$view->error(new Error("Erreur SQL", $sqle->getDescription()));
			}
		}else{
			throw new AccessRefusedException("Vous ne pouvez pas regarder la page des utilisateurs publique sans etre connecte.");
		}
	}
}

