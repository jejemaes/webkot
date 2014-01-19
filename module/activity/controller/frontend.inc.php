<?php

$view = new ActivityView($template,$module);


if(isset($_GET['p']) && !empty($_GET['p'])){
	switch($_GET['p']){
		case "activity":
			if(isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id'])){
				if(RoleManager::getInstance()->hasCapabilitySession('activity-read-activity')){
					$logger->loginfo("display the  activity " . $_GET['id']);
					try{
						$manager = ActivityManager::getInstance();
						$level = system_session_privilege();
						$activity = $manager->getActivity($_GET['id'], $level);
						$manager->updateView($activity->getId());
						
						$smanager = SessionManager::getInstance();
						$profile = $smanager->getUserprofile();
						
						// display the given picture in the modal, directly
						if($activity->getIspublished()){						
							$view->pageActivity($activity);
						}else{
							throw new AccessRefusedException("L'activit&eacute; <i>".$activity->getTitle() . "</i> n'est actuellement pas disponible. Elle sera publi&eacute;e plus tard.");
						}
					} catch ( SQLException $sqle ) {
						$logger->logwarn ( "Erreur SQL : " . $sqle->getDescription () );
						$view->error ( new Error ( "Erreur SQL", $sqle->getDescription () ) );
					} catch ( DatabaseExcetion $dbe ) {
						$logger->logwarn ( "Connection impossible à la Base de donnees." );
						$view->error ( new Error ( "Erreur de BD", "Connection impossible à la Base de donnees" ) );
					}catch(NullObjectException $ne){
						$logger->logwarn ( "Activity innexistante. Quelqu'un chipote dans les URL !" );
						$view->error ( new Error ( "Erreur d'Activite", "Aucune activites n'est disponible a cette page. Circulez !" ) );
					}
				}else{
					throw new AccessRefusedException("Vous ne pouvez pas lire cette activite.");
				}
			}else{
				throw new InvalidURLException("Il y a une erreur dans ton URL Nicolas ! Arrete de chipoter a cela.");
			}
			break;
			
		case "top10":
			if(RoleManager::getInstance()->hasCapabilitySession('activity-read-top10')){
				$pmanager = PictureManager::getInstance();
				
				if(isset($_GET['index']) && !empty($_GET['index']) && is_numeric($_GET['index'])){
						
					$index = ($_GET['index']-1);
					
					$album = array();
					$album['title'] = "Un Top10 ...";
					if($_GET['type'] == 'view'){
						// most Viewed
						if(is_numeric($_GET['year'])){
							$list = $pmanager->getTop10ViewedYear($_REQUEST['year']);
							$album['title'] = "Top10 des plus vues de " . $_REQUEST['year'];
						}else{
							$list = $pmanager->getTop10ViewedEver();
							$album['title'] = "Top10 des plus vues depuis toujours";
						}
					}else{
						// most Commented
						if(is_numeric($_GET['year'])){
							$list = $pmanager->getTop10CommentYear($_REQUEST['year']);
							$album['title'] = "Top10 des plus comment&eacute;es de " . $_REQUEST['year'];
						}else{
							$list = $pmanager->getTop10CommentEver();
							$album['title'] = "Top10 des plus comment&eacute;es depuis toujours";
						}
					}
					$album['href'] = URLUtils::generateURL($module->getName(), array("p"=>"top10", "type" => $_REQUEST['type'], "year" => $_REQUEST['year']));
					$album['count'] = count($list);
					
					$p = $list[$index];
					$picture = $pmanager->getPicture($p->getId());
				
					// request for the html code of the picture page
					$request = array();
					$request['id'] = $picture->getId();
					$request['module'] = $_GET['mod'];
					
					list($message, $picture, $activity, $actions) = PictureController::getPicture($request);
					
					if($message->isSuccess()){
						// orders : get the next and previous picture id
						$orders = array();
						if(($index+1) < count($list)){
							$orders["next"] = ($index+2);
						}
						if(($index-1) >= 0){
							//$tmp = $list[($index-1)];
							$orders["previous"] = ($index);
						}
						$orders['previous'] = ($orders['previous'] ? URLUtils::generateURL($module->getName(), array("p" => "top10","type" => $_GET['type'], "year" => $_GET['year'], "index" => $orders['previous'])) : false);
						$orders['next'] = ($orders['next'] ? URLUtils::generateURL($module->getName(), array("p" => "top10","type" => $_GET['type'], "year" => $_GET['year'], "index" => $orders['next'])) : false);
						$orders['order'] = $index;	
							
						// get the current module
						$module = ModuleManager::getInstance()->getModule($module->getName());
					
						// preprare the user profile (if there is)
						$smanager = SessionManager::getInstance();
						$profile = $smanager->getUserprofile();
							
						$view->pagePicture($module, $activity, $picture, $profile, $album, $orders, $actions);
					}else{
						throw new NullObjectException("La photo demand&eacute;e n'existe pas : " . $message->getMessage());
					}				
				}else{
					if(is_numeric($_GET['year'])){						
						$title = $_GET['year'];
						$mostView = $pmanager->getTop10ViewedYear($_GET['year']);
						$mostCommented = $pmanager->getTop10CommentYear($_GET['year']);
					}else{
						$title = ' toujours';
						$mostView = $pmanager->getTop10ViewedEver();
						$mostCommented = $pmanager->getTop10CommentEver();
					}
					$view->pageTop10($mostView, $mostCommented, $title);
				}
			}else{
				throw new AccessRefusedException("Vous ne pouvez pas lire les top10.");
			}		
			break;
			
		case "picture" :
			if(RoleManager::getInstance()->hasCapabilitySession('activity-read-picture')){
				$request = $_GET;
				$request['module'] = $module->getName();
				
				list($message, $picture, $activity, $actions) = PictureController::getPicture($request);
				
				if($message->isSuccess()){
					// orders : get the next and previous picture id
					$orders = activity_get_neighbor_pictures($activity->getPictures(),$picture);
					$orders['previous'] = ($orders['previous'] ? URLUtils::generateURL($module->getName(), array("p" => "picture", "id" => $orders['previous'])) : false);
					$orders['next'] = ($orders['next'] ? URLUtils::generateURL($module->getName(), array("p" => "picture", "id" => $orders['next'])) : false);
							
					
					// get the current module
					$module = ModuleManager::getInstance()->getModule($module->getName());
						
					// preprare the user profile (if there is)
					$smanager = SessionManager::getInstance();
					$profile = $smanager->getUserprofile();
						
					// the album is a collection of picture (not necessarily an Activity). Ex: Top10, MyPicture, ...
					$album = array();
					$album['title'] = $activity->getTitle();
					$album['href'] = URLUtils::generateURL($module->getName(), array("p" => "activity", "id" => $activity->getId()));
					$album['count'] = count($activity->getPictures());

					
					$view->pagePicture($module, $activity, $picture, $profile, $album, $orders, $actions);
					//echo activity_html_picture_page($module, $activity, $picture, $profile, $album, $orders, $actions, true);
				}else{
					throw new NullObjectException("La photo demand&eacute;e n'existe pas : " . $message->getMessage());
				}
			}else{
				throw new AccessRefusedException("Vous ne pouvez pas regarder cette photo. Bisou Jacky !");
			}
			
			break;
		case "archive":
			if(isset($_GET['year']) && !empty($_GET['year']) && is_numeric($_GET['year'])){
				if(RoleManager::getInstance()->hasCapabilitySession('activity-read-activity')){
					$logger->loginfo("display the archive of activities");
					if ($_GET ['year'] == 2002) {
						
						$view->pageArchiveOld();
					} else {
						try {
							// prepare the year
							$an = $_GET ['year'];
							$tab = preg_split ( "/[ ]+/", ( int ) $an );
							
							// only the first part of the $_GET['year'] is important
							$years = $tab [0] . '-' . ($tab [0] + 1);
							
							// pagination
							$desc = system_get_desc_pagination();
							$page = (system_get_page_pagination()-1);
							$limit = ($page*$desc);
					
							// request the list of Activity Objects
							$manager = ActivityManager::getInstance ();
							$level = system_session_privilege();
							$list = $manager->getListActivityYear ( $tab [0], $limit, $desc, $level);
							$count = $manager->getCountActivityPeriod ( ($tab [0]) . "-" . BEGINYEAR_MONTH . "-" . BEGINYEAR_DAY, ($tab [0] + 1) . "-" . BEGINYEAR_MONTH . "-" . BEGINYEAR_DAY, $level);
						
							$view->pageArchive($list, $count, ($page+1), $desc, $an);
						} catch ( DatabaseException $dbe ) {
							$error = ExceptionHandler::handleException ( $dbe );
							$template->PageError ( $error );
						} catch ( SQLException $sqle ) {
							$error = ExceptionHandler::handleException ( $sqle );
							$template->PageError ( $error );
						}
					}
				}else{
					throw new AccessRefusedException("Vous ne pouvez pas lire lces archives.");
				}
			}else{
				throw new InvalidURLException("Il y a une erreur dans ton URL Nicolas ! Arrete de chipoter a cela.");
			}
			break;
		
		case "mypicture":
		case "mypictures":
			if(RoleManager::getInstance()->hasCapabilitySession('activity-manage-mypicture')){		
				$smanager = SessionManager::getInstance();
				if($smanager->existsUserSession()){
					$uid = $smanager->getUserprofile()->getId();
					
					if(isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id'])){
						
						$mpmanager = MyPictureManager::getInstance();
						$list = $mpmanager->getListPicture($uid);
						
						$count = count($list);
						
						// prepare request params
						$request = $_GET;
						$request['module'] = $module->getName();
						
						list($message, $picture, $activity, $actions) = PictureController::getPicture($request);
						
						if($message->isSuccess()){
							// orders : get the next and previous picture id
							$orders = activity_get_neighbor_pictures($list,$picture);
							$orders['previous'] = ($orders['previous'] ? URLUtils::generateURL($module->getName(), array("p" => "mypicture", "id" => $orders['previous'])) : false);
							$orders['next'] = ($orders['next'] ? URLUtils::generateURL($module->getName(), array("p" => "mypicture", "id" => $orders['next'])) : false);

							// get the current module
							$module = ModuleManager::getInstance()->getModule($module->getName());
						
							// preprare the user profile (if there is)
							$smanager = SessionManager::getInstance();
							$profile = $smanager->getUserprofile();
						
							// the album is a collection of picture (not necessarily an Activity). Ex: Top10, MyPicture, ...
							$album = array();
							$album['title'] = "Mes photos";
							$album['href'] = URLUtils::generateURL($module->getName(), array("p" => "mypicture"));
							$album['count'] = $count;
						
								
							$view->pagePicture($module, $activity, $picture, $profile, $album, $orders, $actions);
							//echo activity_html_picture_page($module, $activity, $picture, $profile, $album, $orders, $actions, true);
						}else{
							throw new NullObjectException("La photo demand&eacute;e n'existe pas.");
						}
					}else{
						$desc = system_get_desc_pagination();
						$page = (system_get_page_pagination()-1);
						$limit = ($page*$desc);
							
						$mpmanager = MyPictureManager::getInstance();
						$list = $mpmanager->getListPicture($uid, $limit, $desc);
						
						$count = $mpmanager->getCountMyPict($uid);
						
						$view->pageListMyPicture($list, $count, $desc, ($page+1));
					}
					
				}else{
					throw new InvalidURLException("Il y a une erreur dans ton URL Nicolas ! Arrete de chipoter a cela.");
				}
			}else{
				throw new AccessRefusedException("Vous ne pouvez pas manager Vos photos.");
			}
			break;
			
		case "censures":
			if(RoleManager::getInstance()->hasCapabilitySession('activity-read-censured')){
				if(isset($_REQUEST['index']) && !empty($_REQUEST['index']) && is_numeric($_REQUEST['index'])){
					$index = ($_REQUEST['index']-1);
				
					$pmanger = PictureManager::getInstance();
					$list = $pmanger->getCensuredPicture();
				
					$picture = $list[$index];
						
					$request = array();
					$request['id'] = $picture->getId();
					$request['module'] = $_GET['mod'];
						
					list($message, $picture, $activity, $actions) = PictureController::getPicture($request);
				
					if($message->isSuccess()){
						$smanager = SessionManager::getInstance();
						$profile = $smanager->getUserprofile();
				
						//get the next and previous picture id
						$orders = array();
						if(($index+1) < count($list)){
							$orders["next"] = ($index+2);
						}
						if(($index-1) >= 0){
							$orders["previous"] = ($index);
						}
						$orders['previous'] = ($orders['previous'] ? URLUtils::generateURL($module->getName(), array("p" => "censures", "index" => $orders['previous'])) : false);
						$orders['next'] = ($orders['next'] ? URLUtils::generateURL($module->getName(), array("p" => "censures", "index" => $orders['next'])) : false);
						$orders['order'] = $index;
				
						// the album is a collection of picture (not necessarily an Activity). Ex: Top10, MyPicture, ...
						$album = array();
						$album['title'] = "Les censur&eacute;es";
						$album['href'] = URLUtils::generateURL($module->getName(), array("p" => "censures"));
						$album['count'] = count($list);
							
						$view->pagePicture($module, $activity, $picture, $profile, $album, $orders, $actions, false);
					}else{
						throw new NullObjectException("La photo demand&eacute;e n'existe pas : " . $message->getMessage());
					}
				}else{
					$pmanger = PictureManager::getInstance();
					$censures = $pmanger->getCensuredPicture();
					$view->pageListCensured($censures);
				}
			}else{
				throw new AccessRefusedException("Vous ne pouvez pas lire lces archives.");
			}
			break;
			
		case "lastcomm":
			if(RoleManager::getInstance()->hasCapabilitySession('activity-read-picture')){
				if(isset($_REQUEST['index']) && !empty($_REQUEST['index']) && is_numeric($_REQUEST['index'])){
					$index = ($_REQUEST['index']-1);
						
					$nbr = OptionManager::getInstance()->getOption("accueil-last-commented");
					$pmanager = PictureManager::getInstance();
					$list = $pmanager->getLastCommentedPicture($nbr, system_session_privilege());
						
					$picture = $list[$index];
					
					$request = array();
					$request['id'] = $picture->getId();
					$request['module'] = $_GET['mod'];
					
					list($message, $picture, $activity, $actions) = PictureController::getPicture($request);
						
					if($message->isSuccess()){
						$smanager = SessionManager::getInstance();
						$profile = $smanager->getUserprofile();
		
						//get the next and previous picture id
						$orders = array();
						if(($index+1) < count($list)){
							$orders["next"] = ($index+2);
						}
						if(($index-1) >= 0){
							$orders["previous"] = ($index);
						}
						$orders['previous'] = ($orders['previous'] ? URLUtils::generateURL($module->getName(), array("p" => "lastcomm", "index" => $orders['previous'])) : false);
						$orders['next'] = ($orders['next'] ? URLUtils::generateURL($module->getName(), array("p" => "lastcomm", "index" => $orders['next'])) : false);
						$orders['order'] = $index;
						
						// the album is a collection of picture (not necessarily an Activity). Ex: Top10, MyPicture, ...
						$album = array();
						$album['title'] = "Derni&egrave;res photos comment&eacute;es";
						$album['href'] = URLUtils::generateURL($module->getName(), array("p" => "lastcomm"));
						$album['count'] = count($list);
							
						$view->pagePicture($module, $activity, $picture, $profile, $album, $orders, $actions, false);
					}else{
						throw new NullObjectException("La photo demand&eacute;e n'existe pas : " . $message->getMessage());
					}
				}else{
					$nbr = OptionManager::getInstance()->getOption("accueil-last-commented");
					$pmanager = PictureManager::getInstance();
					$list = $pmanager->getLastCommentedPicture($nbr, system_session_privilege());
						
					$view->pageLastComm($list);
				}
			}else{
				throw new AccessRefusedException("Vous ne pouvez pas voir cette image de la liste des dernieres commentees. Sorry ma biche !");
			}
			break;
		default:
			if(RoleManager::getInstance()->hasCapabilitySession('activity-read-activity')){		
				$logger->loginfo("display the list of all activities");
				try{
					$level = system_session_privilege();
					
					$manager = ActivityManager::getInstance();
					$list = $manager->getListActivity($level);
					
					$view->pageList($list);
				} catch ( DatabaseException $dbe ) {
					$logger->logwarn ( "Connection impossible à la Base de donnees." );
					$view->error ( new Error ( "Erreur de BD", "Connection impossible à la Base de donnees" ) );
				} catch ( SQLException $sqle ) {
					$logger->logwarn ( "Erreur SQL : " . $sqle->getDescription () );
					$view->error ( new Error ( "Erreur SQL", $sqle->getDescription () ) );
				}
			}else{
				throw new AccessRefusedException("Vous ne pouvez pas lire lces archives.");
			}
	}
}else{
	if(RoleManager::getInstance()->hasCapabilitySession('activity-read-activity')){
		$logger->loginfo("display the list of all activities");
		try{
			$level = system_session_privilege();
			
			$manager = ActivityManager::getInstance();
			$list = $manager->getListActivity($level);
				
			$view->pageList($list);
		} catch ( DatabaseException $dbe ) {
			$logger->logwarn ( "Connection impossible à la Base de donnees." );
			$view->error ( new Error ( "Erreur de BD", "Connection impossible à la Base de donnees" ) );
		} catch ( SQLException $sqle ) {
			$logger->logwarn ( "Erreur SQL : " . $sqle->getDescription () );
			$view->error ( new Error ( "Erreur SQL", $sqle->getDescription () ) );
		}
	}else{
		throw new AccessRefusedException("Vous ne pouvez pas lire lces archives.");
	}
}

