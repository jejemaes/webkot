<?php

include DIR_MODULE . $module->getLocation() . 'functions.inc.php';

include DIR_MODULE . $module->getLocation() . 'model/ImgUtils.class.php';
include DIR_MODULE . $module->getLocation() . 'model/Activity.class.php';
include DIR_MODULE . $module->getLocation() . 'model/ActivityManager.class.php';
include DIR_MODULE . $module->getLocation() . 'model/AbstractPicture.class.php';
include DIR_MODULE . $module->getLocation() . 'model/Picture.class.php';
include DIR_MODULE . $module->getLocation() . 'model/PictureManager.class.php';
include DIR_MODULE . $module->getLocation() . 'model/MyPicture.class.php';
include DIR_MODULE . $module->getLocation() . 'model/MyPictureManager.class.php';
include DIR_MODULE . $module->getLocation() . 'model/ActivityPicture.class.php';
include DIR_MODULE . $module->getLocation() . 'model/ActivityPictureManager.class.php';
include DIR_MODULE . $module->getLocation() . 'model/Comment.class.php';
include DIR_MODULE . $module->getLocation() . 'model/CommentManager.class.php';
include DIR_MODULE . $module->getLocation() . 'model/StatUser.class.php';
include DIR_MODULE . $module->getLocation() . 'model/StatManager.class.php';
include DIR_MODULE . $module->getLocation() . 'model/Censure.class.php';
include DIR_MODULE . $module->getLocation() . 'model/CensureManager.class.php';

include DIR_MODULE . $module->getLocation() . 'model/Publisher.class.php';

include DIR_MODULE . $module->getLocation() . 'controller/ActivityController.class.php';
include DIR_MODULE . $module->getLocation() . 'controller/PictureController.class.php';

//######################
//## Upload picture ####
//######################
if(isset($_REQUEST['directory']) && !empty($_REQUEST['directory'])){
	system_load_php_files(DIR_PLUGIN . 'bootstrap-uploadhandler/');
	if(isset($_REQUEST['directory'])){	
		$directory = DIR_HD_PICTURES . $_REQUEST['directory'];		
		$options = array();
		$options['inline_file_types'] = '/\.(jpe?g)$/i';
		$options['upload_dir'] = dirname($_SERVER['SCRIPT_FILENAME']) . "/". $directory . "/";
		$options['upload_url'] = URLUtils::getFullUrl(). "/" . $directory . "/";
		$options['script_url'] = URLUtils::getFullUrl().'/server.php?module='.$_REQUEST['module'].'&directory='.$_REQUEST['directory'] ;
		$upload_handler = new UploadHandler($options);
	}else{
		$upload_handler = new UploadHandler();
	}
}



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
		case "ispublishing":
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
								
								echo '{"message" : {"type" : "success", "content" : "La publication s\'est terminŽe avec success."}}';
							}else{
								echo '{"message" : {"type" : "error", "content" : "Le repertoire de de l activite ('.DIR_HD_PICTURES . $activity->getDirectory().') n existe pas!"}}';
							}
						}else{
							echo '{"message" : {"type" : "error", "content" : "L\'activitŽe est dŽjˆ publiee."}}';
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
			break;
			
		case "unpublish" :
			if (RoleManager::getInstance ()->hasCapabilitySession ( 'activity-publish-activity' )) {
				$message = ActivityController::unpublishAction($_REQUEST);
				echo $message->toJSON();
			}else{
				throw new AccessRefusedException("Vous ne pouvez pas dŽpublier d'activity.");
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
			
		// MYPICTURE (add and remove)
		case "addfav":
			if(RoleManager::getInstance()->hasCapabilitySession('activity-manage-mypicture')){
				$smanager = SessionManager::getInstance();
					if(isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id']) && $smanager->existsUserSession()){
					try{
						$pid = $_REQUEST['id'];
						$uid = $smanager->getUserprofile()->getId();
						
						$mpmanager = MyPictureManager::getInstance();
						if($mpmanager->exists($uid,$pid)){
							echo '{"message" : {"type" : "warn", "content" : "La photo '.$pid.' est deja presente dans vos favoris."}}';
						}else{
							$mpmanager->addFavorite($uid,$pid);
							echo '{"message" : {"type" : "success", "content" : "La photo '.$pid.' a ete ajoutee avec succes a vos favoris."}}';
						}
		
					} catch ( DatabaseException $dbe ) {
						echo '{"message" : {"type" : "error", "content" : "'.$dbe->getMessage().'"}}';
					} catch ( SQLException $sqle ) {
						echo '{"message" : {"type" : "error", "content" : "'.$dbe->getMessage().'"}}';
					}
						
				}else{
					echo '{"message" : {"type" : "error", "content" : "Au moins un des champs requis est vide."}}';
				}
			}else{
				echo '{"message" : {"type" : "error", "content" : "Vous n\'avez pas les autorisations requises d\'ajouter une photos a vos favoris."}}';
			}
			break;
		case "delfav":
			if(RoleManager::getInstance()->hasCapabilitySession('activity-manage-mypicture')){
				$smanager = SessionManager::getInstance();
				if(isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id']) && $smanager->existsUserSession()){
					try{
						$pid = $_REQUEST['id'];
						$uid = $smanager->getUserprofile()->getId();
		
						$mpmanager = MyPictureManager::getInstance();
						if(!$mpmanager->exists($uid,$pid)){
							echo '{"message" : {"type" : "warn", "content" : "La photo est deja presente dans vos favoris."}}';
						}else{
							$mpmanager->removeFavorite($uid,$pid);
							echo '{"message" : {"type" : "success", "content" : "La photo '.$pid.' a ete supprimee avec succes de vos favoris."}}';
						}
		
					} catch ( DatabaseException $dbe ) {
						echo '{"message" : {"type" : "error", "content" : "'.$dbe->getMessage().'"}}';
					} catch ( SQLException $sqle ) {
						echo '{"message" : {"type" : "error", "content" : "'.$dbe->getMessage().'"}}';
					}
		
				}else{
					echo '{"message" : {"type" : "error", "content" : "Au moins un des champs requis est vide."}}';
				}
			}else{
				echo '{"message" : {"type" : "error", "content" : "Vous n\'avez pas les autorisations requises d\'ajouter une photos a vos favoris."}}';
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
						echo '{"message" : {"type" : "error", "content" : "'.$dbe->getMessage().'"}}';
					} catch ( SQLException $sqle ) {
						echo '{"message" : {"type" : "error", "content" : "'.$sqle->getMessage().'"}}';
					}
				}else{
					echo '{"message" : {"type" : "error", "content" : "Au moins un des champs requis est vide."}}';
				}
			}else{
				echo '{"message" : {"type" : "error", "content" : "Vous n\'avez pas les autorisations requises pour tŽlŽcharger le CSV."}}';
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
			 		
			 				echo '{"message" : {"type" : "success", "content" : "Les 3 fichiers (thumbnail,normale et HD) ont &eacute;t&eacute; retourn&eacute;s <strong>avec succ&egrave;s</strong> de '.$_REQUEST['degree'].' degrees. Si vous ne voyez aucun changement, rafraichissez la page ;)"}}';		
		 				} catch ( DatabaseException $dbe ) {
							echo '{"message" : {"type" : "error", "content" : "' . $dbe->getMessage () . '"}}';
						} catch ( SQLException $sqle ) {
							echo '{"message" : {"type" : "error", "content" : "' . $sqle->getMessage () . '"}}';
						}		
	 				}else{
	 					echo '{"message" : {"type" : "error", "content" : "Les degres introduits ne sont pas reglementaires."}}';
	 				}
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
					
					if(!$picture->getIscensured()){		
						$amanager = ActivityManager::getInstance();
						$activity = $amanager->getActivity($picture->getIdactivity());

						if(file_exists(DIR_HD_PICTURES . $activity->getDirectory() . "/". $picture->getFilename())){
							header('Content-Description: File Transfer');
							header('Content-Type: image/jpeg');
							header('Content-Disposition: attachment; filename='.$picture->getFilename());
							header('Content-Transfer-Encoding: binary');
							header('Expires: 0');
							header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
							header('Pragma: public');
							header('Content-Length: ' . filesize(DIR_HD_PICTURES . $activity->getDirectory() . "/". $picture->getFilename()));
							ob_clean();
							flush();
							readfile(DIR_HD_PICTURES . $activity->getDirectory() . "/". $picture->getFilename());	
						}else{
							echo "ERREUR : Le fichier est introuvable !";	
						}
					}else{
						echo "La photo est censuree. Vous ne pouvez pas la voir, surtout en HD !";
					}
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
				if(isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])){
					
					$pmanager = PictureManager::getInstance();
					$picture = $pmanager->getPicture($_REQUEST['id']);
					$pmanager->updateView($_REQUEST['id']);

					$amanager = ActivityManager::getInstance();
					$activity = $amanager->getActivity($picture->getIdactivity());
			
					//get the next and previous picture id
					$orders = activity_get_neighbor_pictures($activity->getPictures(),$picture);
							
					// Infos panel
					$infos = '<div class="activity-content-box ">
					<h4>Infos</h4>
					<b>Id : </b>'.$picture->getId() . '<br>
					<p><b>Numero : </b>'.($orders["order"]+1).' sur '.count($activity->getPictures()).'<br>
					<b>Nom : </b>'.$picture->getFilename() . '<br>
					<b>Prise : </b> le '. ConversionUtils::dateToDateFr($activity->getDate()) . ' &agrave; '.ConversionUtils::timeToTimefr($picture->getTime()).'<br>
					<b>Commentaires : </b>'.count($picture->getComments()).'<br>
					</p></div>';
					
					// Comments panel
					$smanager = SessionManager::getInstance();
					$comm = '<div class="activity-content-box  activity-modal-infos">';
					$comm .= '<div id="activity-modal-comments">';
					//if(count($picture->getComments()) > 0 || ($smanager->existsUserSession())){				
						if(count($picture->getComments()) > 0){	
							$comm .= '<h4>Commentaires</h4>';
							
							$actions = array();
							if(RoleManager::getInstance()->hasCapabilitySession('activity-delete-comment')){
								$actions[] = array("title" => "Supprimer", "href"=>"javascript:activityDeleteComment('server.php?module=".$module->getName()."&action=delcomment&id=%comid',%comid);", "param" => array("%comid"=>"getId"));
							}
							
							$listCom = $picture->getComments();
							for($i=0 ; $i<count($picture->getComments()) ; $i++){
								$currentComment = $listCom[$i];
								//class="activity-modal-infos"
								$comm .= activity_html_modal_comment($currentComment,$actions);	
							}
						}else{
							$comm .= "Il n'y a pas de commentaire sur cette photos.";
						}
					//}
					$comm .= '</div>';
					if(RoleManager::getInstance()->hasCapabilitySession('activity-add-comment')){
						$comm .= '<form method="post" id="activity-comment-form" class="activity-comment-form"><span id="activity-modal-loading-comment"></span>Ajouter votre commentaire
		 								<textarea id="activity-comment-textarea" name="activity-input-comment" class="activity-comment-textarea"></textarea>
		 						</form>';
					}
					$comm .= '</div>';
					
					// admin action button
					if(system_session_privilege() >= 5){	
						$adminActions = '<div class="btn-group">
										  <a class="btn btn-danger dropdown-toggle" data-toggle="dropdown" href="#">
										    Admin
										    <span class="caret"></span>
										  </a>
										  <ul class="dropdown-menu">';
						if(RoleManager::getInstance()->hasCapabilitySession("activity-can-censure")){
							if($picture->getIscensured()){
								$adminActions .= '<li><a id="activity-action-censure" tabindex="-1" href="javascript:activityChangeCensure(\'server.php?module='.$module->getName().'&action=censure\','.$picture->getId().',0);">D&eacute;censurer</a></li>';
							}else{
								$adminActions .= '<li><a id="activity-action-censure" tabindex="-1" href="javascript:activityChangeCensure(\'server.php?module='.$module->getName().'&action=censure\','.$picture->getId().',1);">Censurer</a></li>';
							}
						}
						if(RoleManager::getInstance()->hasCapabilitySession("activity-rotate-picture")){
							$adminActions .= '<li><a tabindex="-1" href="javascript:activityRotationPicture(\'server.php?module='.$module->getName().'&action=rotation\','.$picture->getId().',90);">Rotation 90&ordm;</a></li>';
							$adminActions .= '<li><a tabindex="-1" href="javascript:activityRotationPicture(\'server.php?module='.$module->getName().'&action=rotation\','.$picture->getId().',180);">Rotation 180&ordm;</a></li>';
							$adminActions .= '<li><a tabindex="-1" href="javascript:activityRotationPicture(\'server.php?module='.$module->getName().'&action=rotation\','.$picture->getId().',270);">Rotation 270&ordm;</a></li>';
						}
						$adminActions .= '</ul>
										</div>  ';
						
					}
					// common action
					$actionCommon = '<div class="btn-group">
										  <a class="btn btn-inverse dropdown-toggle" data-toggle="dropdown" href="#">
										    Actions
										    <span class="caret"></span>
										  </a>
										  <ul class="dropdown-menu">';
					// admin action button
					$actionCommon .= '<li><a tabindex="-1" target="_blank" href="'.URL.'server.php?module='.$module->getName().'&action=download&id='.$picture->getId().'"><i class="icon-download"></i> Download</a></li>' ;
					if($smanager->existsUserSession()){
						$actionCommon .= '<li><a tabindex="-1" href="javascript:activityAddFavorite(\'server.php?module='.$module->getName().'&action=addfav\','.$picture->getId().');"><i class="icon-star"></i> Favoris</a></li>' ;
					}
					$actionCommon .= '<li><a tabindex="-1" href="'.URLUtils::generateURL('page', array("id"=>"contact")).'"><i class="icon-ban-circle"></i> Demander censure </a></li>' ;
					$actionCommon .= '</ul>
										</div>  ';

					//###########
					// built the html code of the modal body
											
					$HTML = '<div class="row-fluid">
					      <div class="span9">
					        <div class="activity-content-picture">
								<div class="activity-img-center activity-modal-div-picture">';
					// the picture
					$path = DIR_PICTURES . $activity->getDirectory() ."/". $picture->getFilename();
					$class = "img-polaroid";
					if(count($picture->getComments()) > 0){
						$class = "activity-img-commented";
					}		
					if($picture->getIscensured()){
						$class = "activity-img-censured";
						if(RoleManager::getInstance()->hasCapabilitySession('activity-read-censured')){
							$path = DIR_PICTURES . $activity->getDirectory() ."/". $picture->getFilename();	
						}else{
							$path = DIR_MODULE . $module->getLocation() ."view/img/censure.jpg";
						}
					}
					if(!file_exists($path)){
						$path = $path = DIR_MODULE . $module->getLocation() ."view/img/missing.jpg";
					}
		  			$HTML .= '<img src="'.$path.'" alt="Photo" id="activity-the-picture" class="'.$class.'"/>';
					
					//the neigbor of the picture
		  			if($orders["next"]){
			  			$HTML .= '<a class="carousel-control right" href="javascript:activityMakeModal('.$orders["next"].')"><!--<img src="'.DIR_MODULE . $module->getLocation().'view/img/left.png" alt="right"/>-->&rsaquo;</a>';
		  			}
		  			if($orders["previous"]){
			  			$HTML .= '<a class="carousel-control left" href="javascript:activityMakeModal('.$orders["previous"].')"><!--<img src="'.DIR_MODULE . $module->getLocation().'view/img/right.png" alt="left"/>-->&lsaquo;</a>';
		  			}
		  			// the informations and comments
					$HTML .= '  </div>
							</div>';
					//SocialRing plugin
					//$HTML .= system_load_plugin(array('social-ring' => array("level" => $activity->getLevel())));
					$HTML .= system_load_plugin(array('social-ring' => array("level" => $activity->getLevel(), "appId" => OptionManager::getInstance()->getOption("facebook-appid"), "url" => URL . URLUtils::generateURL($module->getName(), array("p"=>"activity", "id"=>$activity->getId(), "picture"=>$picture->getId())))));
					//$HTML .= $pl->load();
							
					$HTML .= '</div>
					      <div class="span3">
							<div>
								'.$infos . '<div style="margin:auto;margin-bottom:10px;">' .$actionCommon .$adminActions . '</div>'
								.'<div id="activity-modal-message"></div>'
								.$comm.'
					        </div>
					      </div>
					    </div>
					  </div>';
				
					$smanager = SessionManager::getInstance();
					$profile = $smanager->getUserprofile();
				
					if($profile){		
						$HTML .= '<script>
						 var isMAJ = false;
						 $("#activity-comment-textarea").keyup(function(event) {
						 	if(event.keyCode == 16){ isMAJ = false}
						 }).keydown(function(event){
						    if(event.keyCode == 16){ isMAJ = true}
						    if(event.keyCode == 13 && isMAJ == false){
						   		activitySendComment("'.URL.'server.php?module='.$module->getName().'&action=sendcomment",'.$picture->getId().', '.$profile->getId().');
						   	}
						 });
					</script>';
					}
					echo $HTML;
					
				}else{
					echo '{"message" : {"type" : "error", "content" : "L\'identifiant est manquant, on ne peut pas supprimer les fichiers !"}}';
				}
			}else{
				echo '{"message" : {"type" : "error", "content" : "Vous n avez pas les autorisations requises voir une photos."}}';
			}
			break;

		case "mypicture":
			if(RoleManager::getInstance()->hasCapabilitySession('activity-read-picture')){
				$smanager = SessionManager::getInstance();
				if(isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id']) && $smanager->existsUserSession()){
					$uid = $smanager->getUserprofile()->getId();
					
					$mpmanager = MyPictureManager::getInstance();
					$list = $mpmanager->getListPicture($uid);
					
					
					$pmanager = PictureManager::getInstance();
					$picture = $pmanager->getPicture($_REQUEST['id']);
					$pmanager->updateView($_REQUEST['id']);
					
					$amanager = ActivityManager::getInstance();
					$level = system_session_privilege();
					$activity = $amanager->getActivity($picture->getIdactivity(), $level);
						
					//get the next and previous picture id
					$orders = activity_get_neighbor_pictures($list,$picture);
					// Infos panel
					$infos = '<div class="activity-content-box ">
				<h4>Infos</h4>
				<b>Id : </b>'.$picture->getId() . '<br>
				<p><b>Numero : </b>'.($orders["order"]+1).' sur '.count($list).' "Mes Photos"<br>
				<b>Nom : </b>'.$picture->getFilename() . '<br>
				<b>Activit&eacute; : </b>'.$activity->getTitle() . '<br>
				<b>Prise : </b> le '. ConversionUtils::dateToDateFr($activity->getDate()) . ' &agrave; '.ConversionUtils::timeToTimefr($picture->getTime()).'<br>
				<b>Commentaires : </b>'.count($picture->getComments()).'<br>
				</p></div>';
						
					// Comments panel
					// COMMENTS
				$commHTML = '<div id="activity-modal-comments" class="activity-content-box">';
				if(count($picture->getComments()) > 0){
					$commHTML .= '<h3>Commentaires</h3>';
					$actions = array();
					if(RoleManager::getInstance()->hasCapabilitySession('activity-delete-comment')){
						$actions[] = array("title" => "Supprimer", "href"=>"javascript:activityDeleteComment('server.php?module=".$module->getName()."&action=delcomment&id=%comid',%comid);", "param" => array("%comid"=>"getId"));
					}
					$commHTML .= '<div class="activity-modal-infos">';	
					$listCom = $picture->getComments();
					for($i=0 ; $i<count($picture->getComments()) ; $i++){
						$currentComment = $listCom[$i];
						//class="activity-modal-infos"
						$commHTML .= activity_html_modal_comment($currentComment,$actions);
		
					}
					$commHTML .= '</div>';
				}
				$commHTML .= '</div>';
				if(RoleManager::getInstance()->hasCapabilitySession('activity-add-comment')){
					$commHTML .= '<div class="activity-content-box">';
					$commHTML .= '<form method="post" id="activity-comment-form" class="activity-comment-form">Ajouter votre commentaire<span id="activity-modal-loading-comment" class="activity-invisible"> <img src="'. DIR_MODULE . $module->getLocation() .'view/img/loader.gif"></span>
		 								<textarea id="activity-comment-textarea" name="activity-input-comment" class="activity-comment-textarea form-control"></textarea>
		 						</form>';
					$commHTML .= '</div>';
						
					$smanager = SessionManager::getInstance();
					$profile = $smanager->getUserprofile();
					if($profile){
						$code .= '<script>
						 var isMAJ = false;
						 $("#activity-comment-textarea").keyup(function(event) {
						 	if(event.keyCode == 16){ isMAJ = false}
						 }).keydown(function(event){
						    if(event.keyCode == 16){ isMAJ = true}
						    if(event.keyCode == 13 && isMAJ == false){
						   		activitySendComment("'.URL.'server.php?module='.$module->getName().'&action=sendcomment",'.$picture->getId().', '.$profile->getId().');
						   	}
						 });
					</script>';
						$commHTML .= $code;
					}
				}
					
					// admin action button
					if(system_session_privilege() >= 5){	
						$adminActions = '<div class="btn-group">
										  <a class="btn btn-danger dropdown-toggle" data-toggle="dropdown" href="#">
										    Admin
										    <span class="caret"></span>
										  </a>
										  <ul class="dropdown-menu">';
						if(RoleManager::getInstance()->hasCapabilitySession("activity-can-censure")){
							if($picture->getIscensured()){
								$adminActions .= '<li><a id="activity-action-censure" tabindex="-1" href="javascript:activityChangeCensure(\'server.php?module='.$module->getName().'&action=censure\','.$picture->getId().',0);">D&eacute;censurer</a></li>';
							}else{
								$adminActions .= '<li><a id="activity-action-censure" tabindex="-1" href="javascript:activityChangeCensure(\'server.php?module='.$module->getName().'&action=censure\','.$picture->getId().',1);">Censurer</a></li>';
							}
						}
						if(RoleManager::getInstance()->hasCapabilitySession("activity-rotate-picture")){
							$adminActions .= '<li><a tabindex="-1" href="javascript:activityRotationPicture(\'server.php?module='.$module->getName().'&action=rotation\','.$picture->getId().',90);">Rotation 90&ordm;</a></li>';
							$adminActions .= '<li><a tabindex="-1" href="javascript:activityRotationPicture(\'server.php?module='.$module->getName().'&action=rotation\','.$picture->getId().',180);">Rotation 180&ordm;</a></li>';
							$adminActions .= '<li><a tabindex="-1" href="javascript:activityRotationPicture(\'server.php?module='.$module->getName().'&action=rotation\','.$picture->getId().',270);">Rotation 270&ordm;</a></li>';
						}
						$adminActions .= '</ul>
										</div>  ';
						
					}
					// common action
					$actionCommon = '<div class="btn-group">
										  <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="#">
										    Actions
										    <span class="caret"></span>
										  </a>
										  <ul class="dropdown-menu">';
					// admin action button
					$actionCommon .= '<li><a tabindex="-1" target="_blank" href="'.URL.'server.php?module='.$module->getName().'&action=download&id='.$picture->getId().'"><i class="icon-download"></i> Download</a></li>' ;
					if($smanager->existsUserSession()){
						$actionCommon .= '<li><a tabindex="-1" href="javascript:activityAddFavorite(\'server.php?module='.$module->getName().'&action=addfav\','.$picture->getId().');"><i class="icon-star"></i> Favoris</a></li>' ;
					}
					$email = "";
					if($profile){
						$email = $profile->getMail();
					}
					$actionCommon .= '<li>'.activity_html_censure_modal($picture->getId(), "", $email).'</li>' ;
					$actionCommon .= '</ul>
										</div>  ';
						
						
		
					//###########
					// built the html code of the modal body
						
					$HTML = '<div class="row">
				      <div class="span9 col-lg-9">
				        <div class="activity-content-picture">
							<div class="activity-img-center activity-modal-div-picture">';
					// the picture
					$path = DIR_PICTURES . $activity->getDirectory() ."/". $picture->getFilename();
					$class = "img-responsive";
					if(count($picture->getComments()) > 0){
						$class .= " activity-img-commented";
					}
					if($picture->getIscensured()){
						$class .= " activity-img-censured";
						if(RoleManager::getInstance()->hasCapabilitySession('activity-read-censured')){
							$path = DIR_PICTURES . $activity->getDirectory() ."/". $picture->getFilename();
						}else{
							$path = DIR_MODULE . $module->getLocation() ."view/img/censure.jpg";
						}
					}
					if(!file_exists($path)){
						$path = $path = DIR_MODULE . $module->getLocation() ."view/img/missing.jpg";
					}
					$HTML .= '<img src="'.$path.'" alt="Photo" id="activity-the-picture" class="'.$class.'" style="margin:auto"/>';
						
					//the neigbor of the picture
					if($orders["next"]){
						$HTML .= '<a class="carousel-control right" href="javascript:activityMakeModal('.$orders["next"].')"><span class="icon-next"></span></a>';
					}
					if($orders["previous"]){
						$HTML .= '<a class="carousel-control left" href="javascript:activityMakeModal('.$orders["previous"].')"><span class="icon-prev"></span></a>';
					}
					// the informations and comments
					$HTML .= '  </div>
						</div>
				      </div>
				      <div class="span3 col-lg-3">
						<div>
							'.$infos . '<div style="margin:auto;margin-bottom:10px;">' .$actionCommon .$adminActions . '</div>'
												.'<div id="activity-modal-message"></div>'
														.$commHTML.'
				        </div>
				      </div>
				    </div>
				  </div>';
		
					$smanager = SessionManager::getInstance();
					$profile = $smanager->getUserprofile();
		
					if($profile){
						$HTML .= '<script>
					 var isMAJ = false;
					 $("#activity-comment-textarea").keyup(function(event) {
					 	if(event.keyCode == 16){ isMAJ = false}
					 }).keydown(function(event){
					    if(event.keyCode == 16){ isMAJ = true}
					    if(event.keyCode == 13 && isMAJ == false){
					   		activitySendComment("'.URL.'server.php?module='.$module->getName().'&action=sendcomment",'.$picture->getId().', '.$profile->getId().');
					   	}
					 });
				</script>';
					}
					echo $HTML;
						
				}else{
					echo '{"message" : {"type" : "error", "content" : "L\'identifiant est manquant, on ne peut pas supprimer les fichiers !"}}';
				}
			}else{
				echo '{"message" : {"type" : "error", "content" : "Vous n avez pas les autorisations requises voir une photos."}}';
			}
			break;
	
		case "lastcomm":
			if(RoleManager::getInstance()->hasCapabilitySession('activity-read-picture')){
				if(isset($_REQUEST['index']) && !empty($_REQUEST['index']) && is_numeric($_REQUEST['index'])){
					$index = ($_REQUEST['index']-1);	
					
					$nbr = OptionManager::getInstance()->getOption("accueil-last-commented");
					
					$pmanager = PictureManager::getInstance();
					$list = $pmanager->getLastCommentedPicture($nbr, system_session_privilege());
					
					$p = $list[$index];	
					$picture = $pmanager->getPicture($p->getId());
					$pmanager->updateView($p->getId());
		
					$amanager = ActivityManager::getInstance();
					$level = system_session_privilege();
					$activity = $amanager->getActivity($picture->getIdactivity(), $level);
		
					$smanager = SessionManager::getInstance();
					
					//get the next and previous picture id
					$orders = array();
					if(($index+1) < count($list)){	
						$orders["next"] = ($index+2);
					}
					if(($index-1) >= 0){
						$tmp = $list[($index-1)];
						$orders["previous"] = ($index);
					}
					
					
					// Infos panel
					$infos = '<div class="activity-content-box ">
			<h4>Infos</h4>
			<b>Id : </b>'.$picture->getId() . '<br>
			<p><b>Numero : </b>'.($index+1).' sur '.count($list).' "Derni&egrave;res photos comment&eacute;es"<br>
			<b>Nom : </b>'.$picture->getFilename() . '<br>
			<b>Activit&eacute; : </b>'.$activity->getTitle() . '<br>
			<b>Prise : </b> le '. ConversionUtils::dateToDateFr($activity->getDate()) . ' &agrave; '.ConversionUtils::timeToTimefr($picture->getTime()).'<br>
			<b>Commentaires : </b>'.count($picture->getComments()).'<br>
			</p></div>';
					// Comments panel
					// COMMENTS
					$commHTML = '<div id="activity-modal-comments" class="activity-content-box">';
					if(count($picture->getComments()) > 0){
						$commHTML .= '<h3>Commentaires</h3>';
						$actions = array();
						if(RoleManager::getInstance()->hasCapabilitySession('activity-delete-comment')){
							$actions[] = array("title" => "Supprimer", "href"=>"javascript:activityDeleteComment('server.php?module=".$module->getName()."&action=delcomment&id=%comid',%comid);", "param" => array("%comid"=>"getId"));
						}
						$commHTML .= '<div class="activity-modal-infos">';
						$listCom = $picture->getComments();
						for($i=0 ; $i<count($picture->getComments()) ; $i++){
							$currentComment = $listCom[$i];
							//class="activity-modal-infos"
							$commHTML .= activity_html_modal_comment($currentComment,$actions);
		
						}
						$commHTML .= '</div>';
					}
					$commHTML .= '</div>';
					if(RoleManager::getInstance()->hasCapabilitySession('activity-add-comment')){
						$commHTML .= '<div class="activity-content-box">';
						$commHTML .= '<form method="post" id="activity-comment-form" class="activity-comment-form">Ajouter votre commentaire<span id="activity-modal-loading-comment" class="activity-invisible"> <img src="'. DIR_MODULE . $module->getLocation() .'view/img/loader.gif"></span>
	 								<textarea id="activity-comment-textarea" name="activity-input-comment" class="activity-comment-textarea form-control"></textarea>
	 						</form>';
						$commHTML .= '</div>';
		
						$profile = $smanager->getUserprofile();
						if($profile){
							$code .= '<script>
					 var isMAJ = false;
					 $("#activity-comment-textarea").keyup(function(event) {
					 	if(event.keyCode == 16){ isMAJ = false}
					 }).keydown(function(event){
					    if(event.keyCode == 16){ isMAJ = true}
					    if(event.keyCode == 13 && isMAJ == false){
					   		activitySendComment("'.URL.'server.php?module='.$module->getName().'&action=sendcomment",'.$picture->getId().', '.$profile->getId().');
					   	}
					 });
				</script>';
							$commHTML .= $code;
						}
					}
					
					// admin action button
					if(system_session_privilege() >= 5){
						$adminActions = '<div class="btn-group">
									  <a class="btn btn-danger dropdown-toggle" data-toggle="dropdown" href="#">
									    Admin
									    <span class="caret"></span>
									  </a>
									  <ul class="dropdown-menu">';
						if(RoleManager::getInstance()->hasCapabilitySession("activity-can-censure")){
							if($picture->getIscensured()){
								$adminActions .= '<li><a id="activity-action-censure" tabindex="-1" href="javascript:activityChangeCensure(\'server.php?module='.$module->getName().'&action=censure\','.$picture->getId().',0);">D&eacute;censurer</a></li>';
							}else{
								$adminActions .= '<li><a id="activity-action-censure" tabindex="-1" href="javascript:activityChangeCensure(\'server.php?module='.$module->getName().'&action=censure\','.$picture->getId().',1);">Censurer</a></li>';
							}
						}
						if(RoleManager::getInstance()->hasCapabilitySession("activity-rotate-picture")){
							$adminActions .= '<li><a tabindex="-1" href="javascript:activityRotationPicture(\'server.php?module='.$module->getName().'&action=rotation\','.$picture->getId().',90);">Rotation 90&ordm;</a></li>';
							$adminActions .= '<li><a tabindex="-1" href="javascript:activityRotationPicture(\'server.php?module='.$module->getName().'&action=rotation\','.$picture->getId().',180);">Rotation 180&ordm;</a></li>';
							$adminActions .= '<li><a tabindex="-1" href="javascript:activityRotationPicture(\'server.php?module='.$module->getName().'&action=rotation\','.$picture->getId().',270);">Rotation 270&ordm;</a></li>';
						}
						$adminActions .= '</ul>
									</div>  ';
		
					}
				
					// common action
					$actionCommon = '<div class="btn-group">
									  <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="#">
									    Actions
									    <span class="caret"></span>
									  </a>
									  <ul class="dropdown-menu">';
					// admin action button
					$actionCommon .= '<li><a tabindex="-1" target="_blank" href="'.URL.'server.php?module='.$module->getName().'&action=download&id='.$picture->getId().'"><i class="icon-download"></i> Download</a></li>' ;
					if($smanager->existsUserSession()){
						$actionCommon .= '<li><a tabindex="-1" href="javascript:activityAddFavorite(\'server.php?module='.$module->getName().'&action=addfav\','.$picture->getId().');"><i class="icon-star"></i> Favoris</a></li>' ;
					}
					//$actionCommon .= '<li><a tabindex="-1" href="'.URLUtils::generateURL('page', array("id"=>"contact")).'"><i class="icon-ban-circle"></i> Demander censure</a></li>' ;
					$email = "";
					if($profile){
						$email = $profile->getMail();
					}
					$actionCommon .= '<li>'.activity_html_censure_modal($picture->getId(), "", $email).'</li>' ;
					$actionCommon .= '</ul>
									</div>  ';
		
		
					
					//###########
					// built the html code of the modal body
		
					$HTML = '<div class="row">
			      <div class="span9 col-lg-9">
			        <div class="activity-content-picture">
						<div class="activity-img-center activity-modal-div-picture">';
					// the picture
					$path = DIR_PICTURES . $activity->getDirectory() ."/". $picture->getFilename();
					$class = "img-responsive";
					if(count($picture->getComments()) > 0){
						$class .= " activity-img-commented";
					}
					if($picture->getIscensured()){
						$class .= " activity-img-censured";
						if(RoleManager::getInstance()->hasCapabilitySession('activity-read-censured')){
							$path = DIR_PICTURES . $activity->getDirectory() ."/". $picture->getFilename();
						}else{
							$path = DIR_MODULE . $module->getLocation() ."view/img/censure.jpg";
						}
					}
					if(!file_exists($path)){
						$path = $path = DIR_MODULE . $module->getLocation() ."view/img/missing.jpg";
					}
					$HTML .= '<img src="'.$path.'" alt="Photo" id="activity-the-picture" class="'.$class.'" style="margin:auto"/>';
		
					//the neigbor of the picture
					if($orders["next"]){
						$HTML .= '<a class="carousel-control right" href="javascript:activityMakeModal('.$orders["next"].')"><span class="icon-next"></span></a>';
					}
					if($orders["previous"]){
						$HTML .= '<a class="carousel-control left" href="javascript:activityMakeModal('.$orders["previous"].')"><span class="icon-prev"></span></a>';
					}
					// the informations and comments
					$HTML .= '  </div>
					</div>
			      </div>
			      <div class="span3 col-lg-3">
					<div>
						'.$infos . '<div style="margin:auto;margin-bottom:10px;">' .$actionCommon .$adminActions . '</div>'
											.'<div id="activity-modal-message"></div>'
													.$commHTML.'
			        </div>
			      </div>
			    </div>
			  </div>';
					echo $HTML;
		
				}else{
					echo '{"message" : {"type" : "error", "content" : "L\'identifiant est manquant, on ne peut pas supprimer les fichiers !"}}';
				}
			}else{
				echo '{"message" : {"type" : "error", "content" : "Vous n avez pas les autorisations requises voir une photos."}}';
			}
			break;	
			
		case "top10":
			if(RoleManager::getInstance()->hasCapabilitySession('activity-read-top10')){
				if(isset($_REQUEST['index']) && !empty($_REQUEST['index']) && is_numeric($_REQUEST['index']) && isset($_REQUEST['type']) && !empty($_REQUEST['type']) && isset($_REQUEST['year']) && !empty($_REQUEST['year'])){
					$index = ($_REQUEST['index']-1);
					
					$pmanager = PictureManager::getInstance();
					$list = array();
					$album = "Un Top10 ...";
					if($_REQUEST['type'] == 'view'){
						// most Viewed
						if(is_numeric($_REQUEST['year'])){
							$list = $pmanager->getTop10ViewedYear($_REQUEST['year']);
							$album = "Top10 des plus vues de " . $_REQUEST['year'];
						}else{
							$list = $pmanager->getTop10ViewedEver();
							$album = "Top10 des plus vues depuis toujours";
						}
					}else{
						// most Commented
						if(is_numeric($_REQUEST['year'])){
							$list = $pmanager->getTop10CommentYear($_REQUEST['year']);
							$album = "Top10 des plus comment&eacute;es de " . $_REQUEST['year'];
						}else{
							$list = $pmanager->getTop10CommentEver();
							$album = "Top10 des plus c comment&eacute;es depuis toujours";
						}
					}
						
					$p = $list[$index];
					$picture = $pmanager->getPicture($p->getId());
		
					
					$amanager = ActivityManager::getInstance();
					$level = system_session_privilege();
					$activity = $amanager->getActivity($picture->getIdactivity(),$level);
		
					$smanager = SessionManager::getInstance();
					//get the next and previous picture id
					$orders = array();
					if(($index+1) < count($list)){
						$orders["next"] = ($index+2);
					}
					if(($index-1) >= 0){
						$tmp = $list[($index-1)];
						$orders["previous"] = ($index);
					}
						
						
					// Infos panel
					$infos = '<div class="activity-content-box ">
		<h4>Infos</h4>
		<b>Id : </b>'.$picture->getId() . '<br>
		<p><b>Numero : </b>'.($index+1).' sur '.count($list).' "'.$album.'"<br>
		<b>Nom : </b>'.$picture->getFilename() . '<br>
		<b>Activit&eacute; : </b>'.$activity->getTitle() . '<br>
		<b>Prise : </b> le '. ConversionUtils::dateToDateFr($activity->getDate()) . ' &agrave; '.ConversionUtils::timeToTimefr($picture->getTime()).'<br>
		<b>Commentaires : </b>'.count($picture->getComments()).'<br>
		</p></div>';
					// Comments panel
					// COMMENTS
					$commHTML = '<div id="activity-modal-comments" class="activity-content-box">';
					if(count($picture->getComments()) > 0){
						$commHTML .= '<h3>Commentaires</h3>';
						$actions = array();
						if(RoleManager::getInstance()->hasCapabilitySession('activity-delete-comment')){
							$actions[] = array("title" => "Supprimer", "href"=>"javascript:activityDeleteComment('server.php?module=".$module->getName()."&action=delcomment&id=%comid',%comid);", "param" => array("%comid"=>"getId"));
						}
						$commHTML .= '<div class="activity-modal-infos">';
						$listCom = $picture->getComments();
						for($i=0 ; $i<count($picture->getComments()) ; $i++){
							$currentComment = $listCom[$i];
							//class="activity-modal-infos"
							$commHTML .= activity_html_modal_comment($currentComment,$actions);
		
						}
						$commHTML .= '</div>';
					}
					$commHTML .= '</div>';

					if(RoleManager::getInstance()->hasCapabilitySession('activity-add-comment')){
						$commHTML .= '<div class="activity-content-box">';
						$commHTML .= '<form method="post" id="activity-comment-form" class="activity-comment-form">Ajouter votre commentaire<span id="activity-modal-loading-comment" class="activity-invisible"> <img src="'. DIR_MODULE . $module->getLocation() .'view/img/loader.gif"></span>
 								<textarea id="activity-comment-textarea" name="activity-input-comment" class="activity-comment-textarea form-control"></textarea>
 						</form>';
						$commHTML .= '</div>';
		
						$profile = $smanager->getUserprofile();
						if($profile){
							$code .= '<script>
				 var isMAJ = false;
				 $("#activity-comment-textarea").keyup(function(event) {
				 	if(event.keyCode == 16){ isMAJ = false}
				 }).keydown(function(event){
				    if(event.keyCode == 16){ isMAJ = true}
				    if(event.keyCode == 13 && isMAJ == false){
				   		activitySendComment("'.URL.'server.php?module='.$module->getName().'&action=sendcomment",'.$picture->getId().', '.$profile->getId().');
				   	}
				 });
			</script>';
							$commHTML .= $code;
						}
					}
						
					// admin action button
					if(system_session_privilege() >= 5){
						$adminActions = '<div class="btn-group">
								  <a class="btn btn-danger dropdown-toggle" data-toggle="dropdown" href="#">
								    Admin
								    <span class="caret"></span>
								  </a>
								  <ul class="dropdown-menu">';
						if(RoleManager::getInstance()->hasCapabilitySession("activity-can-censure")){
							if($picture->getIscensured()){
								$adminActions .= '<li><a id="activity-action-censure" tabindex="-1" href="javascript:activityChangeCensure(\'server.php?module='.$module->getName().'&action=censure\','.$picture->getId().',0);">D&eacute;censurer</a></li>';
							}else{
								$adminActions .= '<li><a id="activity-action-censure" tabindex="-1" href="javascript:activityChangeCensure(\'server.php?module='.$module->getName().'&action=censure\','.$picture->getId().',1);">Censurer</a></li>';
							}
						}
						if(RoleManager::getInstance()->hasCapabilitySession("activity-rotate-picture")){
							$adminActions .= '<li><a tabindex="-1" href="javascript:activityRotationPicture(\'server.php?module='.$module->getName().'&action=rotation\','.$picture->getId().',90);">Rotation 90&ordm;</a></li>';
							$adminActions .= '<li><a tabindex="-1" href="javascript:activityRotationPicture(\'server.php?module='.$module->getName().'&action=rotation\','.$picture->getId().',180);">Rotation 180&ordm;</a></li>';
							$adminActions .= '<li><a tabindex="-1" href="javascript:activityRotationPicture(\'server.php?module='.$module->getName().'&action=rotation\','.$picture->getId().',270);">Rotation 270&ordm;</a></li>';
						}
						$adminActions .= '</ul>
								</div>  ';
		
					}
		
					// common action
					$actionCommon = '<div class="btn-group">
								  <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="#">
								    Actions
								    <span class="caret"></span>
								  </a>
								  <ul class="dropdown-menu">';
					// admin action button
					$actionCommon .= '<li><a tabindex="-1" target="_blank" href="'.URL.'server.php?module='.$module->getName().'&action=download&id='.$picture->getId().'"><i class="icon-download"></i> Download</a></li>' ;
					if($smanager->existsUserSession()){
						$actionCommon .= '<li><a tabindex="-1" href="javascript:activityAddFavorite(\'server.php?module='.$module->getName().'&action=addfav\','.$picture->getId().');"><i class="icon-star"></i> Favoris</a></li>' ;
					}
					$email = "";
					if($profile){
						$email = $profile->getMail();
					}
					$actionCommon .= '<li>'.activity_html_censure_modal($picture->getId(), "", $email).'</li>' ;
					$actionCommon .= '</ul>
								</div>  ';
		
		
						
					//###########
					// built the html code of the modal body
		
					$HTML = '<div class="row">
		      <div class="span9 col-lg-9">
		        <div class="activity-content-picture">
					<div class="activity-img-center activity-modal-div-picture">';
					// the picture
					$path = DIR_PICTURES . $activity->getDirectory() ."/". $picture->getFilename();
					$class = "img-responsive";
					if(count($picture->getComments()) > 0){
						$class .= " activity-img-commented";
					}
					if($picture->getIscensured()){
						$class .= " activity-img-censured";
						if(RoleManager::getInstance()->hasCapabilitySession('activity-read-censured')){
							$path = DIR_PICTURES . $activity->getDirectory() ."/". $picture->getFilename();
						}else{
							$path = DIR_MODULE . $module->getLocation() ."view/img/censure.jpg";
						}
					}
					if(!file_exists($path)){
						$path = $path = DIR_MODULE . $module->getLocation() ."view/img/missing.jpg";
					}
					$HTML .= '<img src="'.$path.'" alt="Photo" id="activity-the-picture" class="'.$class.'" style="margin:auto"/>';
		
					//the neigbor of the picture
					if($orders["next"]){
						$HTML .= '<a class="carousel-control right" href="javascript:activityMakeModal('.$orders["next"].',\''.$_REQUEST['type'].'\',\''.$_REQUEST['year'].'\')"><span class="icon-next"></span></a>';
					}
					if($orders["previous"]){
						$HTML .= '<a class="carousel-control left" href="javascript:activityMakeModal('.$orders["previous"].',\''.$_REQUEST['type'].'\',\''.$_REQUEST['year'].'\')"><span class="icon-prev"></span></a>';
					}
					// the informations and comments
					$HTML .= '  </div>
				</div>
		      </div>
		      <div class="span3 col-lg-3">
				<div>
					'.$infos . '<div style="margin:auto;margin-bottom:10px;">' .$actionCommon .$adminActions . '</div>'
										.'<div id="activity-modal-message"></div>'
												.$commHTML.'
		        </div>
		      </div>
		    </div>
		  </div>';
		
					echo $HTML;
		
				}else{
					echo '{"message" : {"type" : "error", "content" : "L\'index, ou l\'ann&eacute;e ou le type de Top10 est manquant, la photo ne peut etre affich&eacute;e !"}}';
				}
			}else{
				echo '{"message" : {"type" : "error", "content" : "Vous n avez pas les autorisations requises voir une photos."}}';
			}
			break;
				
		default :
			echo '{"message" : {"type" : "error", "content" : "ACTION INCONNUE."}}';
			break;
	}
}

