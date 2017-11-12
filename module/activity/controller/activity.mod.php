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
							throw new AccessRefusedException("L'actività <i>".$activity->getTitle() . "</i> n'est actuellement pas disponible. Elle sera publiàe plus tard.");
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
				if(isset($_GET['year']) && !empty($_GET['year']) && is_numeric($_GET['year'])){
					$title = $_GET['year'];
					$mostView = $pmanager->getTop10ViewedYear($_GET['year']);
					$mostCommented = $pmanager->getTop10CommentYear($_GET['year']);
				}else{
					$title = ' toujours';
					$mostView = $pmanager->getTop10ViewedEver();
					$mostCommented = $pmanager->getTop10CommentEver();
				}
				$view->pageTop10($mostView, $mostCommented, $title);
			}else{
				throw new AccessRefusedException("Vous ne pouvez pas lire les top10.");
			}		
			break;
		case "picture" :
			if (isset ( $_GET ['id'] ) && ! empty ( $_GET ['id'] ) && is_numeric ( $_GET ['id'] )) {
				if (RoleManager::getInstance ()->hasCapabilitySession ( 'activity-read-picture' )) {
					$managerPict = PictureManager::getInstance();
					//$level = system_session_privilege();
					$picture = $managerPict->getPicture($_GET['id']);
					$managerPict->updateView($_GET['id']);
					
					$managerAct = ActivityManager::getInstance();
					$level = system_session_privilege();
					$activity = $managerAct->getActivity($picture->getIdactivity(), $level);
					
					if($activity->getLevel() <= system_session_privilege()){
						$view->pagePicture($activity,$picture);
					}else{
						throw new AccessRefusedException("Vous n'avez pas l'autorisation de lire cette photo.");
					}
					
				} else {
					throw new AccessRefusedException ( "Vous ne pouvez pas lire la photo " . $_GET['id'] );
				}
			} else {
				throw new InvalidURLException ( "Il y a une erreur dans ton URL Nicolas ! Arrete de chipoter a cela." );
			}
			break;
		case "archive":
			if(isset($_GET['year']) && !empty($_GET['year']) && is_numeric($_GET['year'])){
				if(RoleManager::getInstance()->hasCapabilitySession('activity-read-activity')){
					$logger->loginfo("display the archive of activities");
					if ($_GET ['year'] == 2002) {
						include (DIR_PICT2002 . 'index.php');
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
			
		case "mypictures":
			if(RoleManager::getInstance()->hasCapabilitySession('activity-manage-mypicture')){		
				$smanager = SessionManager::getInstance();
				if($smanager->existsUserSession()){
					$uid = $smanager->getUserprofile()->getId();
					
					$desc = system_get_desc_pagination();
					$page = (system_get_page_pagination()-1);
					$limit = ($page*$desc);
					
					$mpmanager = MyPictureManager::getInstance();
					$list = $mpmanager->getListPicture($uid, $limit, $desc);
	
					$count = $mpmanager->getCountMyPict($uid);
					$view->pageListMyPicture($list, $count, $desc, ($page+1));
				}else{
					throw new InvalidURLException("Il y a une erreur dans ton URL Nicolas ! Arrete de chipoter a cela.");
				}
			}else{
				throw new AccessRefusedException("Vous ne pouvez pas manager Vos photos.");
			}
			break;
			
		case "censures":
			if(RoleManager::getInstance()->hasCapabilitySession('activity-read-censured')){
				$pmanger = PictureManager::getInstance();
				$censures = $pmanger->getCensuredPicture();
				$view->pageListCensured($censures);
			}else{
				throw new AccessRefusedException("Vous ne pouvez pas lire lces archives.");
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

