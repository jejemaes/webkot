<?php


$view = new ActivityAdminView($template, $module);

if(isset($_GET['action']) && !empty($_GET['action'])){
	
	switch ($_GET['action']) {
		
		case "add":
			if (RoleManager::getInstance ()->hasCapabilitySession ( 'activity-add-activity' )) {		
				list($message, $activity) = ActivityController::addAction($_POST);

				if($message){
					if(($message->getType() == 1) && (!$message->isEmpty())){
						$SMM = SessionMessageManager::getInstance();
						$SMM->setSessionMessage($message);
						URLUtils::redirection(URLUtils::generateURL($module->getName(), array()));
					}else{
						$rmanager = RoleManager::getInstance();
						$roles = $rmanager->getRoleList();
						$level = $rmanager->getLevel('Webkot');
						
						$umanager = UserManager::getInstance();
						$potentialAuthors = $umanager->getListUserLevel($level, ">=");
						
						$view->pageFormActivity('add', $message, $activity, $potentialAuthors, $roles);
					}	
				}else{
					$SMM = SessionMessageManager::getInstance();
					$SMM->setSessionMessage($message);
					URLUtils::redirection(URLUtils::generateURL($module->getName(), array()));
				}	
			}else{
				throw new AccessRefusedException("Vous ne pouvez pas ajouter d'activity.");
			}
			break;
			
		case "edit" :
			if (RoleManager::getInstance ()->hasCapabilitySession ( 'activity-edit-activity' )) {
				list($message, $activity) = ActivityController::editAction($_REQUEST);

				if($message){
					if(($message->getType() == 1) && (!$message->isEmpty())){
						$SMM = SessionMessageManager::getInstance();
						$SMM->setSessionMessage($message);
						URLUtils::redirection(URLUtils::generateURL($module->getName(), array()));
					}else{
						$rmanager = RoleManager::getInstance();
						$roles = $rmanager->getRoleList();
						$level = $rmanager->getLevel('Webkot');
						
						$umanager = UserManager::getInstance();
						$potentialAuthors = $umanager->getListUserLevel($level, ">=");
						
						$view->pageFormActivity('edit', $message, $activity,$potentialAuthors, $roles);
					}	
				}else{
					$SMM = SessionMessageManager::getInstance();
					$SMM->setSessionMessage($message);
					URLUtils::redirection(URLUtils::generateURL($module->getName(), array()));
				}	
			}else{
				throw new AccessRefusedException("Vous ne pouvez pas editer d'activity.");
			}
			break;
			
		case "delete" :
			if (RoleManager::getInstance ()->hasCapabilitySession ( 'activity-delete-activity' )) {
				$message = ActivityController::deleteAction($_GET);	
				$SMM = SessionMessageManager::getInstance();
				$SMM->setSessionMessage($message);
				URLUtils::redirection(URLUtils::generateURL($module->getName(), array()));
			}else{
				throw new AccessRefusedException("Vous ne pouvez pas supprimer d'activity.");
			}
			break;
			
		case "managepicture" :
			if (RoleManager::getInstance ()->hasCapabilitySession ( 'activity-manage-picture' ) || RoleManager::getInstance ()->hasCapabilitySession ( 'activity-add-picture' )) {
				if(isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id'])){
					$manager = ActivityManager::getInstance();
					$level = system_session_privilege();
					$activity = $manager->getActivity($_GET['id'], $level);

					$omanager = OptionManager::getInstance();
					$delay = $omanager->getOption('activity-lock-day');
					$diff = system_date_difference_day(date("Y-m-d H:i:s"), $activity->getDate());
					// Comments are waiting for the lock. @see TODO 76
					if($diff <= $delay || !$activity->getIspublished()){
						$view->pageFormManagePicture($activity);
					}else{
						$message = new Message(3);
						$message->addMessage("L'activit&eacute; <i>'".$activity->getTitle()."'</i> est d&eacute;ja publi&eacute;e depuis <strong>plus de ".$delay." jours</strong> : vous ne pouvez plus y ajouter de photos !");
						$SMM = SessionMessageManager::getInstance();
						$SMM->setSessionMessage($message);
						URLUtils::redirection(URLUtils::generateURL($module->getName(), array()));
					}
				}
			}else{
				throw new AccessRefusedException("Vous ne pouvez pas manager les photos de l'activit�.");
			}
			break;
		/*	
		case "publish" :
			if (RoleManager::getInstance ()->hasCapabilitySession ( 'activity-publish-activity' )) {
				if(isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id'])){
					if(activity_utils_is_publishing()){
						$message = new Message(3);
						$message->addMessage("Une publication est deja en cours. Veuillez reessayer dans quelques minutes. Merci.");
						$SMM = SessionMessageManager::getInstance();
						$SMM->setSessionMessage($message);
						URLUtils::redirection(URLUtils::generateURL($module->getName(), array()));
					}else{
						$manager = ActivityManager::getInstance();
						$level = system_session_privilege();
						$activity = $manager->getActivity($_GET['id'], $level);
						if(!$activity->getIspublished()){
							if(system_count_files_in_directory(DIR_HD_PICTURES . $activity->getDirectory() . "/", array("jpg", "jpeg", "JPG", "JPEG")) != 0){
								$view->pagePublishing($activity);
							}else{
								$message = new Message(3);
								$message->addMessage("Il n'y a aucune images dans le dossier ".DIR_HD_PICTURES . $activity->getDirectory() . "/. La publication est donc impossible Johnny !");
								$SMM = SessionMessageManager::getInstance();
								$SMM->setSessionMessage($message);
								URLUtils::redirection(URLUtils::generateURL($module->getName(), array()));
							}			
						}else{
							$message = new Message(3);
							$message->addMessage("L'activite est deja publiee, vous ne pouvez donc pas la republier ! Banane va :p");
							$SMM = SessionMessageManager::getInstance();
							$SMM->setSessionMessage($message);
							URLUtils::redirection(URLUtils::generateURL($module->getName(), array()));
						}	
					}
				}
			}else{
				throw new AccessRefusedException("Vous ne pouvez pas publier d'activity.");
			}
			break;
			*/
			case "publish" :
				if (RoleManager::getInstance ()->hasCapabilitySession ( 'activity-publish-activity' )) {
					$request = array(
						'id' => $_GET['id'],
						'value' => "true"
					);
					$message = ActivityController::updatePublishAction($request);
					$SMM = SessionMessageManager::getInstance();
					$SMM->setSessionMessage($message);
					URLUtils::redirection(URLUtils::getPreviousURL());
				}else{
					throw new AccessRefusedException("Vous ne pouvez pas publier d'activity.");
				}
				break;
			
		case "unpublish" :
			if (RoleManager::getInstance ()->hasCapabilitySession ( 'activity-publish-activity' )) {
				$request = array(
					'id' => $_GET['id'],
					'value' => "false"
				);
				$message = ActivityController::updatePublishAction($request);
				$SMM = SessionMessageManager::getInstance();
				$SMM->setSessionMessage($message);
				URLUtils::redirection(URLUtils::getPreviousURL());	
			}else{
				throw new AccessRefusedException("Vous ne pouvez pas d&eacute;publier d'activity.");
			}
			break;
			
		case "deletecensure" :
			if (RoleManager::getInstance ()->hasCapabilitySession ( 'activity-can-censure' )) {
				$message = new Message(1);
				if(isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id'])){	
					try{
						$cmanager = CensureManager::getInstance();
						$cmanager->delete($_GET['id']);
						$message = new Message(1);
						$message->addMessage("La demande de censure a �t� supprim�e avec succes.");
					}catch(Exception $e){
						$message = new Message(3);
						$message->addMessage($e);
					}
				}else{
					$message = new Message(3);
					$message->addMessage($e);
				}
				$SMM = SessionMessageManager::getInstance();
				$SMM->setSessionMessage($message);
				URLUtils::redirection(URLUtils::generateURL($module->getName(), array("list"=>"censures")));
			}else{
				throw new AccessRefusedException("Vous ne pouvez pas publier d'activity.");
			}
			break;
			
		default:
			echo "switch default : action inconnue.";
	}
}else{
	$managerActi = ActivityManager::getInstance();
	
	if(isset($_GET['list']) && !empty($_GET['list'])){
		
		if($_GET['list'] == "unpublished"){
			//list unpublished
			$level = system_session_privilege();
			$list = $managerActi->getListUnpublishedActivity($level);
			
			$smanager = SessionMessageManager::getInstance();
			$message = $smanager->getSessionMessage();
			
			$view->pageListActivity($message, $list, 0, 0, 0);
		}
		
		if($_GET['list'] == "censures"){
			$cmanager = CensureManager::getInstance();
			$censures = $cmanager->getUnapprovedCensure();
			
			$SMM = SessionMessageManager::getInstance();
			$message = $SMM->getSessionMessage();
			
			$view->pageListCensures($censures, $message);
		}
		
		
		if($_GET['list'] == "directory"){
			$repdb = $managerActi->getListDirectories();
			$subDir = system_get_sub_directories(DIR_HD_PICTURES);
			
			$list = array_diff($subDir, $repdb);
			$view->pageUnusedDirectories(array_values($list));	
		}
		
		if($_GET['list'] == "stats"){	
			$smanager = StatManager::getInstance();
			
			//Stat Activity
			$statsA = $smanager->getAllStatActivity();
				
			//Stat User
			$year = system_get_begin_year();
			$statsU = array();
			for($i=0 ; $i < 4 ; $i++){
				$y = $year-$i;
				$yearName = $y . '-' . ($y+1);
				$statsU[$yearName] = $smanager->getStatTeam($y);
			}
		
			//Stat last year at the same time
			$statC = array();
			// this year
			$statC[system_get_begin_year()] = $smanager->getStatPeriod(system_get_begin_year().'-'.BEGINYEAR_MONTH.'-'.BEGINYEAR_DAY, date('Y-m-d'));
			//last year
			$statC[(system_get_begin_year()-1)] = $smanager->getStatPeriod((system_get_begin_year()-1).'-'.BEGINYEAR_MONTH.'-'.BEGINYEAR_DAY, (date('Y')-1).'-'.date('m').'-'.date('d'));
				
			$view->pageStatistics($statsU,$statsA,$statC);
		}
	}else{	
		// list
		if (RoleManager::getInstance ()->hasCapabilitySession ( 'activity-read-activity')) {
			$smanager = SessionMessageManager::getInstance();
			$message = $smanager->getSessionMessage();
			
			$desc = system_get_desc_pagination();
			$page = (system_get_page_pagination()-1);
			$limit = ($page*$desc);
			
			$level = system_session_privilege();

			$list = $managerActi->getSelectionActivity($limit,$desc, $level);
			$count = $managerActi->getCountActivity($level);
			
			$view->pageListActivity($message, $list, $count, $desc, ($page+1));
		}else{
			throw new AccessRefusedException("Vous n'avez pas les permissions pour lire les activities.");
		}
	}
}