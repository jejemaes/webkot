<?php


class ActivityController{
	
	
	/**
	 * add an Activity
	 * @param array $request : the REQUEST variables
	 * @return multitype:Message Activity : array containing the return Message and Activity that was added
	 */
	public static function addAction(array $request){
		$activity = new Activity();
		$message = new Message();
		if(isset($request['activity-input-title']) && isset($request['activity-input-description']) && isset($request['activity-input-date']) && isset($request['activity-input-directory']) && isset($request['activity-input-level'])){
			if(!empty($request['activity-input-title']) && !empty($request['activity-input-description']) && !empty($request['activity-input-date']) && !empty($request['activity-input-directory'])  && !empty($request['activity-input-authors'])){
		
				// check the directory name
				if (! preg_match ( "#^[0-9]{4}-[0-9]{2}-[0-9]{2}_[a-zA-Z0-9_-]+$#", $request ['activity-input-directory'] )) {
					$message->setType(3);
					$message->addMessage( "Le repertoire indique doit au format <i>YYYY-MM-DD_activity_name</i> et ne doit pas contenir de caractere speciaux ou accentues." );
				}
		
				if($message->isEmpty()){
					try{
						// creation of directory
						$managerA = ActivityManager::getInstance();
						$actiUsingDir = array();
						if(!is_dir(DIR_HD_PICTURES . $request['activity-input-directory'] . "/")){
							if(mkdir(DIR_HD_PICTURES . $request['activity-input-directory'] . "/", CHMOD, true)){
								$message->setType(1);
								$message->addMessage("Le dossier ".DIR_HD_PICTURES . $request['activity-input-directory']." cree !");
							}else{
								$message->setType(3);
								$message->addMessage("ERREUR : le dossier n'a PAS ete cree !");
							}
						}else{
							$message->setType(2);
							$message->addMessage("Le repertoire existait déja (".DIR_HD_PICTURES . $request['activity-input-directory'].").");
								
							$actiUsingDir = $managerA->getListActivityForDirectory($request['activity-input-directory']);
						}
		
						if(count($actiUsingDir) == 0){
							//creation of the activity, and add the authors
							$aid = $managerA->add($request['activity-input-title'],$request['activity-input-description'],$request['activity-input-date'],$request['activity-input-directory'],$request['activity-input-level']);
							$message->addMessage("L'activite a ete ajoutée avec succes, et porte l'identifiant " . $aid);
		
							$authors = $request['activity-input-authors'];
							for($i=0 ; $i<count($authors) ; $i++){
								try{
									$managerA->addAuthors($aid,$authors[$i]);
									$message->addMessage($authors[$i] . ' est auteur.');
								}catch(Exception $e){
									$message->addMessage($authors[$i] . ' N\'A PAS ETE AJOUTE comme auteur.');
									$message->setType(3);
								}
							}
						}else{
							$message->setType(3);
							$message->addMessage("Les activit&eacute;s ");
							for($i=0 ; $i<count($actiUsingDir) ; $i++){
								$tmp = $actiUsingDir[$i];
								$message->addMessage("           - " .$tmp->getId() . " : " . $tmp->getTitle());
							}
							$message->addMessage("utilisent le dossier ".DIR_HD_PICTURES . $request['activity-input-directory']. " ! Votre nouvelle activit&eacute; n'a donc pas ete cr&eacute;&eacute;e.");
							$message->addMessage("Veuillez rem&eacute;dier au probleme le plus rapidement possible.");
						}
					}catch(SQLException $sqle){
						$message = new Message(3);
						$message->addMessage("Une erreur s'est produite, l'ajout de votre activity a echoue.");
						$message->addMessage($sqle->getMessage());
					}catch(DatabaseExcetion $dbe){
						$message = new Message(3);
						$message->addMessage("Une erreur s'est produite, l'ajout de votre activity a echoue.");
						$message->addMessage($dbe->getMessage());
					}
				}
		
			}else{
				$message->setType(3);
				$message->addMessage("Au moins un des champs requis est vide !");
			}
		}
		return array($message, $activity);
	}
	
	
	/**
	 * Edit a given Activity
	 * @param array $request : the REQUEST variable
	 * @return array Message Activity : array containing the Message Object with the result of the action, and the edited Activity  
	 */
	public static function editAction(array $request){
		$activity = new Activity();
		$message = new Message();
		if(isset($request['id']) && !empty($request['id']) && is_numeric($request['id'])){
			$manager = ActivityManager::getInstance();
			$level = system_session_privilege();
			$activity = $manager->getActivity($request['id'], $level);
			if(isset($request['activity-input-title']) && isset($request['activity-input-description']) && isset($request['activity-input-date']) && isset($request['activity-input-directory']) && isset($request['activity-input-level'])){
				$activity->setTitle($request['activity-input-title']);
				$activity->setDescription($request['activity-input-description']);
				$activity->setDate($request['activity-input-date']);
				$activity->setDirectory($request['activity-input-directory']);
				$activity->setLevel($request['activity-input-level']);
				if(!empty($request['activity-input-title']) && !empty($request['activity-input-description']) && !empty($request['activity-input-date']) && !empty($request['activity-input-directory']) && !empty($request['activity-input-authors'])){
					try{
						//creation of the activity, and ad the authors
						$managerA = ActivityManager::getInstance();
						$activity = $managerA->getActivity($request['id']);
						//$aid = $managerA->add($request['activity-input-title'],$request['activity-input-description'],$request['activity-input-date'],$request['activity-input-directory'],$request['activity-input-level']);
						$manager->update($request['id'],$request['activity-input-title'],$request['activity-input-description'],$request['activity-input-date'],$request['activity-input-level']);
						$message->setType(1);
						$message->addMessage("L'activit&eacute; a &eacute;t&eacute; mis a jour avec succes.");
							
						//deleteAuthor
						$managerA->deleteAllAuthor($request['id']);
						$authors = $request['activity-input-authors'];
						for($i=0 ; $i<count($authors) ; $i++){
							try{
								$managerA->addAuthors($request['id'],$authors[$i]);
								$message->addMessage($authors[$i] . ' est auteur.');
							}catch(Exception $e){
								$message->addMessage($authors[$i] . ' N\A PAS ETE AJOUTE comme auteur.');
								$message->setType(3);
							}
						}
					}catch(SQLException $sqle){
						$message->setType(3);
						$message->addMessage("Une erreur s'est produite, l'ajout de votre activity a echoue.");
						$message->addMessage($sqle->getMessage());
					}catch(DatabaseExcetion $dbe){
						$messagesetType(3);
						$message->addMessage("Une erreur s'est produite, l'ajout de votre activity a echoue.");
						$message->addMessage($dbe->getMessage());
					}
				}else{
					$message->setType(3);
					$message->addMessage("Au moins un des champs requis est vide !");
				}
			}
		}else{
			$message->setType(3);
			$message->addMessage("L'identifiant est manquant.");
		}
		return array($message, $activity);
	}
	
	
	
	
	
	/**
	 * delete a given Activity
	 * @param array $request : the REQUEST variables
	 * @return Message $message : Message Object containing the result of the action 
	 */
	public static function deleteAction($request){
		$message = new Message(1);
		if(isset($request['id']) && !empty($request['id']) && is_numeric($request['id'])){
			$amanager = ActivityManager::getInstance();
			$level = system_session_privilege();
			$activity = $amanager->getActivity($request['id'],$level);
				
			//delete the activity in the database (cascade : so the pictures and comments are removed too)
			try{
				$amanager->delete($activity->getId());
				$message->addMessage("L'activite a ete supprimee avec succes.");
			}catch(SQLException $sqle){
				$message->addMessage("Une erreur s'est produite, l'activity et les photos n'ont pu etre supprimee dans la database.");
				$message->addMessage($sqle->getMessage());
				$messag->setType(3);
			}catch(DatabaseExcetion $dbe){
				$message->addMessage("Une erreur s'est produite, l'activity et les photos n'ont pu etre supprimee dans la database.");
				$message->addMessage($dbe->getMessage());
				$messag->setType(3);
			}
			//delete the file in the medium directory
			if(system_remove_directory(DIR_PICTURES . $activity->getDirectory() . "/")){
				$message->addMessage("Le dossier (medium picture) " . DIR_PICTURES . $activity->getDirectory() . " a ete supprime avec succes.");
			}else{
				$message->addMessage("Le dossier (medium picture)" . DIR_PICTURES . $activity->getDirectory() . " n'a pu etre vide. Les medium pictures sont donc toujours pr&eacute;sentes sur le disque.");
				$message->setType(2);
			}
				
			//delete the file in the HD directory
			if(system_remove_directory(DIR_HD_PICTURES . $activity->getDirectory() . "/")){
				$message->addMessage("Le dossier (HD picture) " . DIR_HD_PICTURES . $activity->getDirectory() . " a ete supprime avec succes.");
			}else{
				$message->addMessage("Le dossier (HD picture)" . DIR_HD_PICTURES . $activity->getDirectory() . " n'a pu etre vide. Les HD pictures sont donc toujours pr&eacute;sentes sur le disque.");
				$message->setType(2);
			}
		}else{
			$message->setType(3);
			$message->addMessage("L'identifiant est manquant.");
		}
		return $message;
	}
	
	
	/**
	 * add picture Action 
	 * @param ActivityAdminView $view : the Admin View
	 * @param array $request : the $_GET and $_POST variables
	 * @return Message $message : Message Object containing the result of the action 
	 */
	public static function addPictureActionKKK(ActivityAdminView $view, array $request){
		if(isset($request['id']) && !empty($request['id']) && is_numeric($request['id'])){
			$manager = ActivityManager::getInstance();
			$activity = $manager->getActivity($request['id']);
			if(!$activity->getIspublished()){
				$directory = $activity->getDirectory();
				$view->pageFormAddPicture($directory);
			}else{
				$message = new Message(3);
				$message->addMessage("L'activite est deja publiee : vous ne pouvez y rajouter des photos !");
			}
		}
		return $message;
	}
	
	
	/**
	 * unpublish a given Activity
	 * @param array $request : the REQUEST variables
	 * @return Message $message : Message Object containing the result of the action 
	 */
	public static function unpublishAction($request){
		$message = new Message(1);
		if(isset($request['id']) && !empty($request['id']) && is_numeric($request['id'])){
			$amanager = ActivityManager::getInstance();
			$level = system_session_privilege();
			$activity = $amanager->getActivity($request['id'], $level);
				
			if($activity->getIspublished()){
		
				//delete the picture in the database
				try{
					$pmanager = PictureManager::getInstance();
					$pmanager->deleteActivityPictures($activity->getId());
				}catch(SQLException $sqle){
					$message->addMessage("Une erreur s'est produite, les photos n'ont pu etre supprimee dans la database.");
					$message->addMessage($sqle->getMessage());
					$messag->setType(3);
				}catch(DatabaseExcetion $dbe){
					$message->addMessage("Une erreur s'est produite, les photos n'ont pu etre supprimee dans la database.");
					$message->addMessage($dbe->getMessage());
					$messag->setType(3);
				}
					
				//delete the file in the medium directory
				if(system_remove_directory(DIR_PICTURES . $activity->getDirectory() . "/")){
					$message->addMessage("Le dossier " . DIR_PICTURES . $activity->getDirectory() . " a ete supprime avec succes.");
				}else{
					$message->addMessage("Le dossier " . DIR_PICTURES . $activity->getDirectory() . " n'a pu etre vide. Les medium pictures sont donc toujours pr&eacute;sentes sur le disque.");
					$message->setType(2);
				}
			
				//change the status of the activity
				$amanager->updatePublish($activity->getId(), '0');
				$message->addMessage("L'activite a ete depubliee avec succes.");
				$message->addMessage("Vous pouvez maintenant ajouter de novuelles photos, et republie l'activite.");
		
				rebuild_rss();
			}else{
				$message = new Message(3);
				$message->addMessage("L'activite est non publiee, vous ne pouvez donc pas la depublier ! Banane va :p");
			}
		}else{
			$message = new Message(3);
			$message->addMessage("L'identifiant de l'activit&eacute; est manquant. On ne peut donc rien faire pour l'instant !");
		}
		return $message;
	}
	
}