<?php


class PictureController{
	
	/**
	 * get the Picture and the Activity Object corresponding to the given Picture Id.
	 * @param array $request : 	int $request[id] is the identifier of the Picture
	 * 							string $request[module] is the name of the current module
	 * @return array Message,Picture,Activity,array actions : the Message object decribing is the action is a success or not, the Picture and Activity desired, and the list of actions
	 */
	public static function getPicture($request){
		$message = new Message(1);
		$picture = new Picture();
		$activity = new Activity();
		$actions = array();
		
		if(isset($request['id']) && !empty($request['id']) && is_numeric($request['id']) && isset($request['module']) && !empty($request['module'])){
			try{
				$pmanager = PictureManager::getInstance();
				$picture = $pmanager->getPicture($request['id']);
				$pmanager->updateView($request['id']);
				$amanager = ActivityManager::getInstance();
				$level = system_session_privilege();
				$activity = $amanager->getActivity($picture->getIdactivity(),$level);

				// actions
				$actions = array();
				$actionCommon = array();
				$actionCommon['<i class="fa fa-download"></i> Download'] = URL.'server.php?module='.$request['module'].'&action=download&id='.$picture->getId();

				$smanager = SessionManager::getInstance();
				if($smanager->existsUserSession()){
					$actionCommon['<i class="fa fa-star"></i> Favoris'] = 'javascript:activityAddFavorite(\'server.php?module='.$request['module'].'&action=addfav\','.$picture->getId().');' ;
					$profile = $smanager->getUserprofile();
					$email = $profile->getMail();
				}
				//$label = activity_html_modal_censure($picture->getId(), "", $email);//URLUtils::generateURL('page', array("id"=>"contact")); 
				// --> the html code of the modal is added in the activity_html_page_picture function
				$actionCommon['<i class="fa fa-ban"></i> Demander censure'] = "javascript:activityShowModal('activity-censure-modal');";
				//$actionCommon['<button data-toggle="modal" data-target="#activity-censure-modal">Demande de censure</button>'] = "#"; //"javascript:activityShowModal('activity-censure-modal');";
			
				$adminActions = array();
				if(RoleManager::getInstance()->hasCapabilitySession("activity-can-censure")){
					if($picture->getIscensured()){
						$adminActions['D&eacute;censurer'] = 'javascript:activityChangeCensure(\'server.php?module='.$request['module'].'&action=censure\','.$picture->getId().',0);';
					}else{
						$adminActions['Censurer'] = 'javascript:activityChangeCensure(\'server.php?module='.$request['module'].'&action=censure\','.$picture->getId().',1);';
					}
				}
				if(RoleManager::getInstance()->hasCapabilitySession("activity-rotate-picture")){
					$adminActions['Rotation 90&ordm;'] = 'javascript:activityRotationPicture(\'server.php?module='.$request['module'].'&action=rotation\','.$picture->getId().',90);';
					$adminActions['Rotation 180&ordm;'] = 'javascript:activityRotationPicture(\'server.php?module='.$request['module'].'&action=rotation\','.$picture->getId().',180);';
					$adminActions['Rotation 270&ordm;'] = 'javascript:activityRotationPicture(\'server.php?module='.$request['module'].'&action=rotation\','.$picture->getId().',270);';
				}
					
				$actions['Actions'] = array('actions' => $actionCommon, 'class' => 'btn-default');
				$actions['Admin'] = array('actions' => $adminActions, 'class' => 'btn-danger');
					
				$message->addMessage("La recuperation de la photo est un succes.");
			}catch(SQLException $sqle){
				$message = new Message(3);
				$message->addMessage("Une erreur s'est produite, la recuperation de la photo a echou&eacute;.");
			}catch(DatabaseExcetion $dbe){
				$message = new Message(3);
				$message->addMessage("Une erreur s'est produite, la recuperation de la photo a echou&eacute;.");
			}
		}else{
			$message->setType(3);
			$message->addMessage("L'identifiant de la photo est manquant (ou le nom du module).");
		}
		return array($message, $picture, $activity, $actions);
	}
	
	/**
	 * add a Censure for a given Picture
	 * @param array $request :  int $request[pid] is the identifier of the Picture
	 * 							string $request[comment] is the justification
	 * 							string $request[email] is the email address of the asker
	 * @return Message $message : the Message Object
	 */
	public static function addCensure($request){
		$message = new Message(1);
		if(isset($request['pid']) && isset($request['email']) && isset($request['comment'])){
			if(is_numeric($request['pid']) && preg_match ( "#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $request ['email'] )) {
				try{
					
					$omanager = OptionManager::getInstance();
					$to = $omanager->getOption("activity-censure-mail");
					
					$m = "<html><head>
					          <title>Demande de censure</title>
					          </head>
					          <body>";
					$m .= "<br>Demande de censure pour une photo:";
					$m .= "<br><b>Demandeur : </b><i>" . $request['email'] . "</i>";
					$m .= "<br><b>Motif : </b><i>" . $request['comment'] . "</i>";
					$m .= '<br><b>Photo : </b><i><a href="'.URL.URLUtils::generateURL("activity",array("p" => "picture", "id" => $request['pid'])).'">' . $request['pid'] . '</a></i>';
					$m .= "<br><br>Voila ! Bisous l'equipe !";
					$m .= "</body></html>";
								
					system_send_mail('Demande de censure', $m, $to, 'Webkot (ne pas repondre) <noreply@webkot.be>', 'Reply-To: ' . $request['email']);				
					
					$cmanager = CensureManager::getInstance();
					$cmanager->add($request['pid'],$request['comment'],$request['email']);
					$message->setType(1);
					$message->addMessage("La demande de censure a bien &eacute;t&eacute; enregistr&eacute;e et sera examin&eacute;e le plus rapidement possible.");
				}catch(SQLException $sqle){
					$message = new Message(3);
					$message->addMessage("Une erreur s'est produite, l'ajout de votre demande de censure a echou&eacute;.");
				}catch(DatabaseExcetion $dbe){
					$message = new Message(3);
					$message->addMessage("Une erreur s'est produite, l'ajout de votre demande de censure a echou&eacute;.");
				}
			}else{
				$message->setType(3);
				$message->addMessage("L'identifiant est erron&eacute; ou l'adresse mail n'est pas au bon format.");
			}
		}else{
			$message->setType(3);
			$message->addMessage("Au moins une des arguments est manquant.");
		}
		return $message;
	}
	
	
	/**
	 * delete a Censure for a given Picture
	 * @param array $request :  int $request[id] is the identifier of the Censure
	 * @return Message $message : the Message Object
	 */
	public static function deleteCensure($request){
		$message = new Message(1);
		if(isset($request['id']) && !empty($request['id']) && is_numeric($request['id'])){
			try{
				$cmanager = CensureManager::getInstance();
				$cmanager->delete($request['id']);
			}catch(SQLException $sqle){
				$message = new Message(3);
				$message->addMessage("Une erreur s'est produite, l'ajout de votre demande de censure a echou&eacute;.");
			}catch(DatabaseExcetion $dbe){
				$message = new Message(3);
				$message->addMessage("Une erreur s'est produite, l'ajout de votre demande de censure a echou&eacute;.");
			}
		}else{
			$message->setType(3);
			$message->addMessage("Au moins une des arguments est manquant.");
		}
		return $message;
	}
	
	/**
	 * add a given Picture to the MyPicture of the connected User 
	 * @param array $request : the informations about the Picture to add
	 * @return Message $message : the return message
	 */
	public static function addFavoriteAction(array $request){
		$message = new Message(1);
		
		return $message;
	}
	
	
	/**
	 * delete a given Picture from the MyPicture of the connected User
	 * @param array $request : the informations about the Picture to remove
	 * @return Message $message : the return message
	 */
	public static function deleteFavoriteAction(array $request){
		$message = new Message(1);
	
		return $message;
	}
	
	
	
	/**
	 * censure or uncensure a given Picture
	 * @param array $request : the informations about the Picture to censure. 
	 * 				int $request[id] : the identifier of the Picture 
	 * 				int $request[value] : the value of the censure ('0' or '1')
	 * @return Message $message : the return message
	 */
	public static function censureAction(array $request){
		$message = new Message(1);
		if(isset($request['id']) && !empty($request['id']) && is_numeric($request['id']) && isset($request['value']) && is_numeric($request['value'])){
			try{
				$pmanager = PictureManager::getInstance();
				$pmanager->changeCensure($request['id'],$request['value']);
				if($request['value']){
					$message->addMessage("La photo a &eacute;t&eacute; censur&eacute;e avec succ&egrave;s.");
				}else{
					$message->addMessage("La photo a &eacute;t&eacute; d&eacute;censur&eacute;e avec succ&egrave;s.");
				}
			} catch ( DatabaseException $dbe ) {
				$message->addMessage("Une erreur est survenue avec la BD. Censure impossible.");
				$message->setType(3);
			} catch ( SQLException $sqle ) {
				$message->addMessage("Une erreur est survenue avec la BD. Censure impossible.");
				$message->setType(3);
			}
		}else{
			$message->addMessage("Un des arguments est manquant ou n'est pas bien typ&eacute;.");
			$message->setType(3);
		}
		return $message;
	}
	
	
	/**
	 * Make a rotation of a given Picture
	 * @param array $request : the informations about the Picture to rotate. Must contain the id of the Picture and the degree of the rotation
	 * @return Message $message : the return message
	 */
	public static function rotationAction(array $request){
		$message = new Message(1);
		
		return $message;
	}
	
}