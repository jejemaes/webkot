<?php


//######################
//# Action for picture #
//######################
if(isset($_REQUEST['action']) && !empty($_REQUEST['action'])){	
	
	
	switch ($_REQUEST['action']) {
		
		// ASK CENSURE
		case "askcensure" :
			$message = PictureController::addCensure($_REQUEST);
			echo $message->toJSON();
			break;
	
		// PUBLICATION
		/* case "ispublishing":
			if(file_exists(ACTIVITY_PUBLISHING_FILE_BACKUP)){
				echo "1";
			}else{
				echo "0";
			}
			break;
		case "publish":
			if(RoleManager::getInstance()->hasCapabilitySession('activity-publish-activity')){
				if(isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])){
					if(!activity_utils_is_publishing()){		
						$manager = ActivityManager::getInstance();
						$activity = $manager->getActivity($_REQUEST['id']);
						if(!$activity->getIspublished()){
							if(is_dir(DIR_HD_PICTURES . $activity->getDirectory())){
								$options = array (
										"file" => ACTIVITY_PUBLISHING_FILE,
										"file_backup" => ACTIVITY_PUBLISHING_FILE_BACKUP,
										"font_path" => dirname(__FILE__) . "/fonts/Harabara.ttf",
										"log_path" => ACTIVITY_PUBLISHING_LOG,
										"mail_notification" => false
								);
								
								if(isset($_REQUEST['sendmail']) && !empty($_REQUEST['sendmail'])){
									if(($_REQUEST['sendmail'] == 'true')){
										$options["mail_notification"] = true;
									}
								}
								
								$publisher = new Publisher($activity->getId(), DIR_HD_PICTURES . $activity->getdirectory(), DIR_PICTURES . $activity->getdirectory(), $options);
								$publisher->start();
								
								rebuild_rss();
								
								echo '{"message" : {"type" : "success", "content" : "La publication s\'est termin�e avec success."}}';
							}else{
								echo '{"message" : {"type" : "error", "content" : "Le repertoire de de l activite ('.DIR_HD_PICTURES . $activity->getDirectory().') n existe pas!"}}';
							}
						}else{
							echo '{"message" : {"type" : "error", "content" : "L\'activit�e est d�j� publiee."}}';
						}
					}else{
						echo '{"message" : {"type" : "error", "content" : "Une autre publication est en cours. Veuillez reessayer plus tard. Xoxo."}}';
					}
				}else{
					echo '{"message" : {"type" : "error", "content" : "Identifiant manquant ! Publication impossible."}}';
				}
			}else{
				echo '{"message" : {"type" : "error", "content" : "Vous n avez pas les autorisations requises pour cette operation!"}}';
			}
			break; */
		
		case "publish" :
			if (RoleManager::getInstance ()->hasCapabilitySession ( 'activity-publish-activity' )) {
				$request = array(
						'id' => $_REQUEST['id'],
						'value' => "true"
				);
				$message = ActivityController::updatePublishAction($request);
				echo $message->toJSON();
			}else{
				throw new AccessRefusedException("Vous ne pouvez pas publier d'activit&eacute;.");
			}
			break;
					
				
		case "unpublish" :
			if (RoleManager::getInstance ()->hasCapabilitySession ( 'activity-publish-activity' )) {
				$request = array(
						'id' => $_REQUEST['id'],
						'value' => "false"
					);
				$message = ActivityController::updatePublishAction($request);
				echo $message->toJSON();
			}else{
				throw new AccessRefusedException("Vous ne pouvez pas d&eacute;publier d'activit&eacute;.");
			}
			break;
			
		case "getstat":
			if(RoleManager::getInstance()->hasCapabilitySession('activity-publish-activity')){			
				if(isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])){	
					$filename = ACTIVITY_PUBLISHING_FILE_BACKUP;
					if(file_exists($filename)){
						echo file_get_contents($filename);
					}else{
						echo '{"message" : {"type" : "error", "content" : "Le fichier des stats est manquant."}}';
					}
				}else{
					echo '{"message" : {"type" : "error", "content" : "L\'identifiant est manquant, on ne peut voir les stats !"}}';
				}
			}
			break;
			
		case "clear":
			if(RoleManager::getInstance()->hasCapabilitySession('activity-publish-activity')){
				if(isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])){	
					$filename = ACTIVITY_PUBLISHING_FILE;
					if(file_exists($filename)){
						$content = file_get_contents($filename);
						$publi = json_decode($content);
						if(intval($publi->id) == intval($_REQUEST['id'])){
							unlink($filename);
							$filename = ACTIVITY_PUBLISHING_FILE_BACKUP;
							unlink($filename);
							echo '{"message" : {"type" : "success", "content" : "Objet de la publication detruit."}}';
						}else{
							echo '{"message" : {"type" : "error", "content" : "L identifiant n est pas celui de la publication en cours !"}}';
						}
					}else{
						echo '{"message" : {"type" : "warn", "content" : "Les fichiers sont deja supprimes !"}}';
					}
				}else{
					echo '{"message" : {"type" : "error", "content" : "L\'identifiant est manquant, on ne peut pas supprimer les fichiers !"}}';
				}
			}else{
				echo '{"message" : {"type" : "error", "content" : "Vous n avez pas les autorisations requises pour cette operation!"}}';
			}			
			break;
			
			
		// UPLOAD PICTURES
		case "picturehandler":
			if(RoleManager::getInstance()->hasCapabilitySession('activity-manage-picture')){
				if(isset($_REQUEST['activityid']) && !empty($_REQUEST['activityid'])){
					$activity = ActivityManager::getInstance()->getActivity($_REQUEST['activityid'],  system_session_privilege());
					
					system_load_plugin(array('bootstrap-fileuploadhandler' => array()));
					system_include_file(DIR_MODULE . $module->getLocation() . 'model/PictureUploadHandler.class.php');
					
					$parameters = array(
						'activity' => system_to_data_obj($activity),
						'url' => URLUtils::builtServerUrl($module->getName(), array("action" => "picturehandler", "activityid" => $activity->getId())),
						'directory_original' => DIR_HD_PICTURES,
						'directory_medium' => DIR_PICTURES
					);
					
					$upload_handler = new PictureUploadHandler($parameters);
				}else{
					$message = new Message(3);
					$message->addMessage("Le directory est manquant !");
					echo $message->toJSON();
				}
			}else{
				throw new AccessRefusedException("Vous n'avez pas les autorisations requises pour cette ajouter des photos !");
			}
			break;
			
		// MYPICTURE (add and remove)
		case "addfav":
			if(RoleManager::getInstance()->hasCapabilitySession('activity-manage-mypicture')){
				$message = new Message(3);
				$smanager = SessionManager::getInstance();
				if(isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id']) && $smanager->existsUserSession()){
					try{
						$pid = $_REQUEST['id'];
						$uid = $smanager->getUserprofile()->getId();
						
						$mpmanager = MyPictureManager::getInstance();
						if($mpmanager->exists($uid,$pid)){
							$message->setType(2);
							$message->addMessage("La photo '.$pid.' est deja pr&eacute;sente dans vos favoris.");
						}else{
							$mpmanager->addFavorite($uid,$pid);
							$message->setType(1);
							$message->addMessage("La photo '.$pid.' a &eacute;t&eacute; ajout&eacute;e avec succes a vos favoris.");
						}
					} catch ( DatabaseException $dbe ) {
						$message->addMessage($dbe->getMessage());
					} catch ( SQLException $sqle ) {
						$message->addMessage($sqlbe->getMessage());
					}
				}else{
					$message->addMessage("Au moins un des champs requis est vide.");
				}
				echo $message->toJSON();
			}else{
				throw new AccessRefusedException("Vous n'avez pas les autorisations requises d'ajouter une photos a vos favoris.");
			}
			break;
		case "delfav":
			if(RoleManager::getInstance()->hasCapabilitySession('activity-manage-mypicture')){
				$message = new Message(3);
				$smanager = SessionManager::getInstance();
				if(isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id']) && $smanager->existsUserSession()){
					try{
						$pid = $_REQUEST['id'];
						$uid = $smanager->getUserprofile()->getId();
		
						$mpmanager = MyPictureManager::getInstance();
						if(!$mpmanager->exists($uid,$pid)){
							$message->setType(2);
							$message->addMessage("La photo '.$pid.' n'&eacute;tait pas pr&eacute;sente dans vos favoris.");
						}else{
							$mpmanager->removeFavorite($uid,$pid);
							$message->setType(1);
							$message->addMessage("La photo '.$pid.' a &eacute;t&eacute; supprim&eacute;e avec succes a vos favoris.");
						}
					} catch ( DatabaseException $dbe ) {
						$message->addMessage($dbe->getMessage());
					} catch ( SQLException $sqle ) {
						$message->addMessage($sqlbe->getMessage());
					}
				}else{
					$message->addMessage("Au moins un des champs requis est vide.");
				}
			}else{
				throw new AccessRefusedException("Vous n'avez pas les autorisations requises de supprimer une photos de vos favoris.");
			}
			break;
			
		// COMMENT
		case "sendcomment":
			if(RoleManager::getInstance()->hasCapabilitySession('activity-add-comment')){
				if(isset($_REQUEST['pid']) && is_numeric($_REQUEST['pid']) && isset($_REQUEST['uid']) && !empty($_REQUEST['uid']) && isset($_REQUEST['comment']) && !empty($_REQUEST['comment'])){
					try{
						//check the user
						$userid = $_REQUEST['uid'];
						$umanager = UserManager::getInstance();
						if(!is_numeric($_REQUEST['uid'])){
							$user = $umanager->getUserByLogin($_REQUEST['uid']);
						}else{
							$user = $umanager->getUserById($_REQUEST['uid']);
						}
						
						$data = array();
						$data['userid'] = $userid;
						$data['pictureid'] = $_REQUEST['pid'];
						$data['comment'] = nl2br($_REQUEST['comment']);
						$data['ip'] = system_ip_client();
							
						$managerC = CommentManager::getInstance();
						$managerC->add($data);
									
						
						$coms = $managerC->getCommentsPicture($_REQUEST['pid']);
						foreach ($coms as $c){
							$c->setComment(ConversionUtils::smiley(ConversionUtils::decoding($c->getComment())));
						}
						$commentsJSONList = system_array_obj_to_data_array($coms);
						
						$action = '""';
						if(RoleManager::getInstance()->hasCapabilitySession('activity-delete-comment')){
							$action = '[{"title" : "Supprimer", "href" : "javascript:activityDeleteComment(\'server.php?module='.$module->getName().'&action=delcomment&id=comid\', comid );", "param" : {"comid" : "id"}}]';
						}
						
						echo '{"message" : {"type" : "success", "content" : "Votre commentaire a ete ajoute avec succes sur la photo '.$_REQUEST['pid'].'."},
								"comments" : '.json_encode($commentsJSONList).',
								"actions" : '.$action.' }';
						
					} catch ( DatabaseException $dbe ) {
						echo '{"message" : {"type" : "error", "content" : "'.$dbe->getMessage().'"}}';
					} catch ( SQLException $sqle ) {
						echo '{"message" : {"type" : "error", "content" : "'.$dbe->getMessage().'"}}';
					}
					
				}else{
					echo '{"message" : {"type" : "error", "content" : "Au moins un des champs requis est vide."}}';
				}
			}else{
				echo '{"message" : {"type" : "error", "content" : "Vous n\'avez pas les autorisations requises commenter une photos."}}';
			}
			break;
			
		case "delcomment":
			if(RoleManager::getInstance()->hasCapabilitySession('activity-delete-comment')){
				if(isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])){		
					try{
						$managerC = CommentManager::getInstance();
						$managerC->delete($_REQUEST['id']);
						echo '{"message" : {"type" : "success", "content" : "Le commentaire '.$_REQUEST['id'].' a ete efface avec succes."}}';
					} catch ( DatabaseException $dbe ) {
						echo '{"message" : {"type" : "error", "content" : "'.$dbe->getMessage().'"}}';
					} catch ( SQLException $sqle ) {
						echo '{"message" : {"type" : "error", "content" : "'.$sqle->getMessage().'"}}';
					}			
				}else{
					echo '{"message" : {"type" : "error", "content" : "Au moins un des champs requis est vide."}}';
				}
			}else{
				echo '{"message" : {"type" : "error", "content" : "Vous n\'avez pas les autorisations requises supprimer un commentaire."}}';
			}
			break;
			
		// GET CSV
		case "getcsv":
			if(RoleManager::getInstance()->hasCapabilitySession('activity-read-activity')){
				$message = new Message(3);
				if(isset($_REQUEST['nbr']) && !empty($_REQUEST['nbr']) && is_numeric($_REQUEST['nbr'])){
					try{
						$managerActi = ActivityManager::getInstance();
						$list = $managerActi->getSelectionActivity(0, $_REQUEST['nbr'], system_session_privilege());
						
						$data = array();
						foreach ($list as $activity){
							/*echo '"'.utf8_decode($activity->getTitle()) . '";';
							echo '"'.$activity->getDate() . '";';
							echo '"'.$activity->getAuthors() . '";';
							echo '<br>';*/
							$tmp = array();
							$tmp[] = utf8_decode($activity->getTitle());
							$tmp[] = $activity->getDate();
							$tmp[] = $activity->getAuthors();
							$data[] = $tmp;
						}
						
						header('Content-Type: application/excel; charset=utf-8');
						header('Content-Disposition: attachment; filename="webkot_activites_auteurs.csv"');
						
						$fp = fopen('php://output', 'w');
						foreach ( $data as $line ) {
							//$val = explode(",", $line);
							fputcsv($fp, $line,";");
						}
						fclose($fp);
					} catch ( DatabaseException $dbe ) {
						$message->addMessage($dbe->getMessage());
						echo $message->toJSON();
					} catch ( SQLException $sqle ) {
						$message->addMessage($sqle->getMessage());
						echo $message->toJSON();
					}
				}else{
					$message->addMessage("Au moins un des champs requis est vide (ici, il s'agit du nombre d'activit&eacute;s a mettre dans le CSV).");
					echo $message->toJSON();
				}
			}else{
				throw new AccessRefusedException("vous n'avez pas les autorisations pour pouvoir t&eacute;l&eacute;charger le CSV.");
			}
			break;
			
		// CENSURE
		case "censure":
			if(RoleManager::getInstance()->hasCapabilitySession('activity-can-censure')){
				$message = PictureController::censureAction($_REQUEST);
				echo $message->toJSON();
			}else{
				throw new AccessRefusedException("Vous ne pouvez pas censurer la photo.");
			}
			break;
			
		// ROTATION
		case "rotation":
			if(RoleManager::getInstance()->hasCapabilitySession('activity-rotate-picture')){
				$message = new Message(3);
				if(isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id']) && isset($_REQUEST['degree']) && is_numeric($_REQUEST['degree'])){
					$degreeAllowed = array("90","180","270");
	 				if(in_array($_REQUEST['degree'],$degreeAllowed)){
		 				try{
			 				$managerPict = PictureManager::getInstance();
			 				$picture = $managerPict->getPicture($_REQUEST['id']);
			 				
			 				$managerActi = ActivityManager::getInstance();
			 				$activity = $managerActi->getActivity($picture->getIdactivity());
			 				
			 				$paths = array();
			 				$paths[0] = DIR_PICTURES . $activity->getDirectory() . '/' . $picture->getFilename();
			 				$paths[1] = DIR_PICTURES . $activity->getDirectory() . '/small/' . $picture->getFilename();
			 				$paths[2] = DIR_HD_PICTURES . $activity->getDirectory() . '/'. $picture->getFilename();
			 				
			 				for($i=0 ; $i < count($paths) ; $i++){
			 	 				ImgUtils::rotation($paths[$i],$paths[$i],$_REQUEST['degree']);
			 				}
			 			
			 				$message->setType(1);
			 				$message->addMessage('Les 3 fichiers (thumbnail,normale et HD) ont &eacute;t&eacute; retourn&eacute;s <strong>avec succ&egrave;s</strong> de '.$_REQUEST['degree'].' degrees. Si vous ne voyez aucun changement, rafraichissez la page ;)');		
		 				} catch ( DatabaseException $dbe ) {
							$message->addMessage($dbe->getMessage());
						} catch ( SQLException $sqle ) {
							$message->addMessage($sqlbe->getMessage());
						}		
	 				}else{
	 					$message->addMessage("Les degres introduits ne sont pas reglementaires.");
	 				}
				}else{
					$message->addMessage("Les degres introduits ne sont pas reglementaires.");
				}
				echo $message->toJSON();
			}else{
				throw new AccessRefusedException("Vous n'avez pas les autorisations requises pour retourner une photo.");
			}
			break;
		case "getimage":
			if(RoleManager::getInstance()->hasCapabilitySession('activity-read-picture')){
				if(isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])){
					
					$types = array("hd","medium","small");
					$type = (isset($_REQUEST['type']) && in_array($_REQUEST['type'], $types) ? $_REQUEST['type'] : "small");
		
					$pmanager = PictureManager::getInstance();
					$picture = $pmanager->getPicture($_REQUEST['id']);
					
					$amanager = ActivityManager::getInstance();
					$activity = $amanager->getActivity($picture->getIdactivity(), system_session_privilege());
					
					$path = activity_path_picture($module->getLocation(), $activity->getDirectory(), $picture, $type);
						
					$im = file_get_contents($path); 
					header('content-type: image/gif'); 
					echo $im;
				}else{
					echo '{"message" : {"type" : "error", "content" : "Au moins un des champs requis est vide."}}';
				}
			}else{
				echo '{"message" : {"type" : "error", "content" : "Vous n\'avez pas les autorisations requises pour retourner une photo."}}';
			}
			break;
		case "download":
			if(RoleManager::getInstance()->hasCapabilitySession('activity-read-picture')){
				if(isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])){	
					$pmanager = PictureManager::getInstance();
					$picture = $pmanager->getPicture($_REQUEST['id']);
					
					$amanager = ActivityManager::getInstance();
					$activity = $amanager->getActivity($picture->getIdactivity(), system_session_privilege());
						
					$path = activity_path_picture($module->getLocation(), $activity->getDirectory(), $picture, 'hd');
					
					header('Content-Description: File Transfer');
					header('Content-Type: image/jpeg');
					header('Content-Disposition: attachment; filename='.$_REQUEST['id'].'.jpg');
					header('Content-Transfer-Encoding: binary');
					header('Expires: 0');
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header('Pragma: public');
					header('Content-Length: ' . filesize($path));
					ob_clean();
					flush();
					readfile($path);
				
				}else{
					echo '{"message" : {"type" : "error", "content" : "Au moins un des champs requis est vide."}}';
				}
			}else{
				echo '{"message" : {"type" : "error", "content" : "Vous n\'avez pas les autorisations requises pour retourner une photo."}}';
			}
				
			break;
			
		// PICTURE MODAL
		case "picture":
			if(RoleManager::getInstance()->hasCapabilitySession('activity-read-picture')){
				
				list($message, $picture, $activity, $actions) = PictureController::getPicture($_REQUEST);	
				
				if($message->isSuccess()){
					// orders : get the next and previous picture id
					$orders = activity_get_neighbor_pictures($activity->getPictures(), $picture);
					$orders['previous'] = ($orders['previous'] ? URLUtils::generateURL($module->getName(), array("p" => "picture", "id" => $orders['previous'])) : false);
					$orders['next'] = ($orders['next'] ? URLUtils::generateURL($module->getName(), array("p" => "picture", "id" => $orders['next'])) : false);
					
					// get the current module
					$module = ModuleManager::getInstance()->getModule($_REQUEST['module']);
					
					// preprare the user profile (if there is)
					$smanager = SessionManager::getInstance();
					$profile = $smanager->getUserprofile();	
					
					// the album is a collection of picture (not necessarily an Activity). Ex: Top10, MyPicture, ...
					$album = array();
					$album['title'] = $activity->getTitle();
					$album['href'] = URLUtils::generateURL($module->getName(), array("p" => "activity", "id" => $activity->getId()));
					$album['count'] = count($activity->getPictures());
					
					echo activity_html_page_picture($module, $activity, $picture, $profile, $album, $orders, $actions, true, true);
				}else{
					echo $message->toJSON();
				}
			}else{
				throw new AccessRefusedException("Vous ne pouvez pas regarder cette photo. Bisou Jacky !");
			}
			break;
			
		case "mypicture":
			if(RoleManager::getInstance()->hasCapabilitySession('activity-read-picture')){
				// preprare the user profile (if there is)
				$smanager = SessionManager::getInstance();
				$profile = $smanager->getUserprofile();
				
				if($profile){		
					list($message, $picture, $activity, $actions) = PictureController::getPicture($_REQUEST);
			
					if($message->isSuccess()){
						$uid = $smanager->getUserprofile()->getId();		
						
						$mpmanager = MyPictureManager::getInstance();
						$list = $mpmanager->getListPicture($uid);
						
						// orders : get the next and previous picture id
						$orders = activity_get_neighbor_pictures($list,$picture);
						$orders['previous'] = ($orders['previous'] ? URLUtils::generateURL($module->getName(), array("p" => "mypicture", "id" => $orders['previous'])) : false);
						$orders['next'] = ($orders['next'] ? URLUtils::generateURL($module->getName(), array("p" => "mypicture", "id" => $orders['next'])) : false);
						
						
						// get the current module
						$module = ModuleManager::getInstance()->getModule($_REQUEST['module']);
								
						// the album is a collection of picture (not necessarily an Activity). Ex: Top10, MyPicture, ...
						$album = array();
						$album['title'] = "Mes Photos";
						$album['href'] = URLUtils::generateURL($module->getName(), array("p" => "mypicture"));
						$album['count'] = count($list);
							
						echo activity_html_page_picture($module, $activity, $picture, $profile, $album, $orders, $actions, false, true);
					}else{
						echo $message->toJSON();
					}
				}else{
					throw new AccessRefusedException("Vous ne pouvez pas regarder cette photo qui est dans 'Mes Photos'. Il faut etre logg&eacute; Jacky !");
				}
			}else{
				throw new AccessRefusedException("Vous ne pouvez pas regarder cette photo. Bisou Jacky !");
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
					$request['module'] = $_REQUEST['module'];
					
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
							//$tmp = $list[($index-1)];
							$orders["previous"] = ($index);
						}
						$orders['previous'] = ($orders['previous'] ? URLUtils::generateURL($module->getName(), array("p" => "lastcomm", "index" => $orders['previous'])) : false);
						$orders['next'] = ($orders['next'] ? URLUtils::generateURL($module->getName(), array("p" => "lastcomm", "index" => $orders['next'])) : false);
						$orders['order'] = $index;
						
						// get the current module
						$module = ModuleManager::getInstance()->getModule($_REQUEST['module']);
						
						// the album is a collection of picture (not necessarily an Activity). Ex: Top10, MyPicture, ...
						$album = array();
						$album['title'] = "Derni&egrave;res photos comment&eacute;es";
						$album['href'] = URLUtils::generateURL($module->getName(), array("p" => "lastcomm"));
						$album['count'] = count($list);
							
						echo activity_html_page_picture($module, $activity, $picture, $profile, $album, $orders, $actions, false, true);
					}else{
						$message = new Message(3);
						$message->addMessage("L'index est manquant. Impossible de savoir quelle image vous voulez voir !");
						echo $message->toJSON();
					}
				}else{
					$message = new Message(3);
					$message->addMessage("L'index est manquant. Impossible de savoir quelle image vous voulez voir !");
					echo $message->toJSON();
				}
			}else{
				throw new AccessRefusedException("Vous ne pouvez pas voir cette image de la liste des dernieres commentees. Sorry ma biche !");
			}
			break;	
			
		case "top10":
			if(RoleManager::getInstance()->hasCapabilitySession('activity-read-top10')){
				if(isset($_REQUEST['index']) && !empty($_REQUEST['index']) && is_numeric($_REQUEST['index']) && isset($_REQUEST['type']) && !empty($_REQUEST['type']) && isset($_REQUEST['year']) && !empty($_REQUEST['year'])){
					$index = ($_REQUEST['index']-1);
					
					// get the current module
					$module = ModuleManager::getInstance()->getModule($_REQUEST['module']);
					
					// building the album array and list of Picture
					$pmanager = PictureManager::getInstance();
					
					$album = array();
					$album['title'] = "Un Top10 ...";
					if($_REQUEST['type'] == 'view'){
						// most Viewed
						if(is_numeric($_REQUEST['year'])){
							$list = $pmanager->getTop10ViewedYear($_REQUEST['year']);
							$album['title'] = "Top10 des plus vues de " . $_REQUEST['year'];
						}else{
							$list = $pmanager->getTop10ViewedEver();
							$album['title'] = "Top10 des plus vues depuis toujours";
						}
					}else{
						// most Commented
						if(is_numeric($_REQUEST['year'])){
							$list = $pmanager->getTop10CommentYear($_REQUEST['year']);
							$album['title'] = "Top10 des plus comment&eacute;es de " . $_REQUEST['year'];
						}else{
							$list = $pmanager->getTop10CommentEver();
							$album['title'] = "Top10 des plus comment&eacute;es depuis toujours";
						}
					}
					$album['href'] = URLUtils::generateURL($module->getName(), array("p" => "top10", "type" => $_REQUEST['type'], "year" => $_REQUEST['year']));
					$album['count'] = count($list);
					
					
					$pmanager = PictureManager::getInstance();
					$p = $list[$index];
					$picture = $pmanager->getPicture($p->getId());
				
					// request for the html code of the picture page
					$request = array();
					$request['id'] = $picture->getId();
					$request['module'] = $_REQUEST['module'];
					
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
							//$tmp = $list[($index-1)];
							$orders["previous"] = ($index);
						}
						$orders['previous'] = ($orders['previous'] ? URLUtils::generateURL($module->getName(), array("p"=>"top10","type" => $_REQUEST['type'], "year" => $_REQUEST['year'], "index" => $orders['previous'])) : false);
						$orders['next'] = ($orders['next'] ? URLUtils::generateURL($module->getName(), array("p"=>"top10","type" => $_REQUEST['type'], "year" => $_REQUEST['year'], "index" => $orders['next'])) : false);
						$orders['order'] = $index;
						
						// get the current module
						$module = ModuleManager::getInstance()->getModule($_REQUEST['module']);
							
						echo activity_html_page_picture($module, $activity, $picture, $profile, $album, $orders, $actions, false, true);
						
					}else{
						$message = new Message(3);
						$message->addMessage("L'index est manquant. Impossible de savoir quelle image vous voulez voir !");
						echo $message->toJSON();
					}
				}else{
					$message = new Message(3);
					$message->addMessage("L'index est manquant. Impossible de savoir quelle image vous voulez voir !");
					echo $message->toJSON();
				}
			}else{
				throw new AccessRefusedException("Vous ne pouvez pas voir cette image de la liste des Top10. Sorry ma biche !");
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
					$request['module'] = $_REQUEST['module'];
						
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
							//$tmp = $list[($index-1)];
							$orders["previous"] = ($index);
						}
						$orders['previous'] = ($orders['previous'] ? URLUtils::generateURL($module->getName(), array("p" => "censures", "index" => $orders['previous'])) : false);
						$orders['next'] = ($orders['next'] ? URLUtils::generateURL($module->getName(), array("p" => "censures", "index" => $orders['next'])) : false);
						$orders['order'] = $index;
		
						// get the current module
						$module = ModuleManager::getInstance()->getModule($_REQUEST['module']);
		
						// the album is a collection of picture (not necessarily an Activity). Ex: Top10, MyPicture, ...
						$album = array();
						$album['title'] = "Les censur&eacute;es";
						$album['href'] = URLUtils::generateURL($module->getName(), array("p" => "censures"));
						$album['count'] = count($list);
							
						echo activity_html_page_picture($module, $activity, $picture, $profile, $album, $orders, $actions, false, true);
					}else{
						$message = new Message(3);
						$message->addMessage("L'index est manquant. Impossible de savoir quelle image vous voulez voir !");
						echo $message->toJSON();
					}
				}else{
					$message = new Message(3);
					$message->addMessage("L'index est manquant. Impossible de savoir quelle image vous voulez voir !");
					echo $message->toJSON();
				}
			}else{
				throw new AccessRefusedException("Vous ne pouvez pas voir cette image de la liste de des images. Pars tr&egrave;s loin et ne reviens jamais !");
			}
			break;
		default :
			echo '{"message" : {"type" : "error", "content" : "ACTION INCONNUE."}}';
			break;
	}
}

