<?php

include DIR_MODULE . $module->getLocation() . 'functions.inc.php';

include DIR_MODULE . $module->getLocation() . 'model/Media.class.php';
include DIR_MODULE . $module->getLocation() . 'model/MediaCategory.class.php';
include DIR_MODULE . $module->getLocation() . 'model/MediaManager.class.php';



if(isset($_REQUEST['part']) && !empty($_REQUEST['part'])){
	
	if($_GET['part'] == 'role'){
		// media action
		if(isset($_GET['action']) && !empty($_GET['action'])){
			switch ($_GET['action']) {
				// get a media
				case "getroles":
					$rmanager = RoleManager::getInstance();
					$roles = $rmanager->getRoleList();
					
					$message = new Message(1);
					$message->addMessage("La r&eacute;cup&eacute;ration des Privil&egrave;ges a r&eacute;ussie");
					
					$arr = array();
					$arr["message"] = $message->toArray();
					$arr["roles"] = system_array_obj_to_data_array($roles);
					echo json_encode($arr);
					break;
				default:
					break;
			}
		}
	}
	
	if($_GET['part'] == 'media'){
		// media action
		if(isset($_GET['action']) && !empty($_GET['action'])){
			switch ($_GET['action']) {
				// get a media
				case "getmedia":
					if (RoleManager::getInstance ()->hasCapabilitySession ( 'system-download-media' )) {
						if(isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])){
							try{
								$mmanager = MediaManager::getInstance();
								$media = $mmanager->getMedia($_REQUEST['id']);
								
								$ext = strtolower($media->getExtension());
								switch ($ext) {
									// Images
									case "jpg":
									case "jpeg":
									case "gif":
										header('Content-Description: File Transfer');
										header('Content-Type: image/jpeg');
										header('Content-Disposition: attachment; filename='.$media->getFilename());
										header('Content-Transfer-Encoding: binary');
										header('Expires: 0');
										header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
										header('Pragma: public');
										header('Content-Length: ' . filesize(DIR_MEDIA . $media->getCategory() . $media->getFilename()));
										ob_clean();
										flush();
										readfile(DIR_MEDIA . $media->getCategory() . $media->getFilename());
										break;
									case "png":
										header('Content-Description: File Transfer');
										header('Content-Type: image/png');
										header('Content-Disposition: attachment; filename='.$media->getFilename());
										header('Content-Transfer-Encoding: binary');
										header('Expires: 0');
										header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
										header('Pragma: public');
										header('Content-Length: ' . filesize(DIR_MEDIA . $media->getCategory() . $media->getFilename()));
										ob_clean();
										flush();
										readfile(DIR_MEDIA . $media->getCategory() . $media->getFilename());
										break;
									// Others
									case "doc":
									case "xls":
									case "pdf":
										header("Content-Type: application/octet-stream");
										header ( "Content-Disposition: attachment; filename=" . urlencode ( $media->getFilename() ) );
										header ( "Content-Type: application/octet-stream" );
										header ( "Content-Type: application/download" );
										header ( "Content-Description: File Transfer" );
										header ( "Content-Length: " . filesize ( DIR_MEDIA . $media->getCategory() . $media->getFilename()  ) );
										flush (); // this doesn't really matter.
										$fp = fopen ( DIR_MEDIA . $media->getCategory() . $media->getFilename(), "r" );
										while ( ! feof ( $fp ) ) {
											echo fread ( $fp, 65536 );
											flush (); // this is essential for large downloads
										}
										fclose ( $fp ); 
										break;
								}
								
							} catch ( DatabaseException $dbe ) {
								echo '{"message" : {"type" : "error", "content" : "'.$dbe->getMessage().'"}}';
							} catch ( SQLException $sqle ) {
								echo '{"message" : {"type" : "error", "content" : "'.$dbe->getMessage().'"}}';
							}
						}else{
							echo '{"message" : {"type" : "error", "content" : "Identifiant manquant ! Publication impossible."}}';
						}
					}else{
						echo '{"message" : {"type" : "error", "content" : "Vous n avez pas les autorisations requises pour cette operation!"}}';
					}
					break;
				case "mediapicker":
					$mmanager = MediaManager::getInstance();
					$list = $mmanager->getCategoriesAndContent();
						
					echo media_admin_html_mediapicker($list, $_REQUEST['id']);
					break;
				default:
					break;
			}
		}else{
			
		}
	}
	
	
	
	if($_GET['part'] == 'log'){
		// media action
		if(isset($_GET['action']) && !empty($_GET['action'])){
			$message = new Message(3);
			switch ($_GET['action']) {
				// get a log
				case "getlog":
					if (RoleManager::getInstance ()->hasCapabilitySession ( 'system-read-log' )) {
						if(isset($_REQUEST['id']) && !empty($_REQUEST['id'])){
							if(file_exists(DIR_LOG . $_REQUEST['id'] . ".txt")){
								echo nl2br(file_get_contents(DIR_LOG . $_REQUEST['id'] . ".txt"));
							}else{
								$message->addMessage("Le fichier n'existe pas.");
							}
						}else{
							$message->addMessage("L'identifiant du log est manquant.");
						}
					}else{
						throw new AccessRefusedException("Vous ne pouvez pas lire les logs du syst&egrave;me.");
					}
					break;
				case "delete":
					if (RoleManager::getInstance ()->hasCapabilitySession ( 'system-delete-log' )) {
						if(isset($_REQUEST['id']) && !empty($_REQUEST['id'])){
							if(file_exists(DIR_LOG . $_REQUEST['id'].".txt")){
								if(unlink(DIR_LOG . $_REQUEST['id'].".txt")){
									$message->setType(1);
									$message->addMessage("Le fichier ".$_REQUEST['id'].".txt a &eacute;t&eacute; supprim&eacute; avec succ&egrave;s.");
								}else{
									$message->addMessage("Echec de la suppression du fichier " . $_REQUEST['id'] . ".txt.");
								}
							}else{
								$message->addMessage("Le fichier n'existe pas.");
							}
						}else{
							$message->addMessage("L'identifiant du log est manquant.");
						}
					}else{
						throw new AccessRefusedException("Vous ne pouvez pas lire les logs du syst&egrave;me.");
					}
					break;
				default:
					$message->addMessage("Action inconnue pour le log.");
					break;
			}
			if(!$message->isEmpty()){				
				echo $message->toJSON();
			}
		}
	}	
}