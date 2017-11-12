<?php

include DIR_MODULE . $module->getLocation() . 'model/BlogComment.class.php';
include DIR_MODULE . $module->getLocation() . 'model/BlogPost.class.php';
include DIR_MODULE . $module->getLocation() . 'model/BlogManager.class.php';


if(isset($_REQUEST['action']) && !empty($_REQUEST['action'])){

	switch ($_REQUEST['action']) {
		// ADD A COMMENT
		case "sendcomment":
			if (RoleManager::getInstance()->hasCapabilitySession ( 'blog-add-comment' )) {
				if((isset($_REQUEST['id'])) && !empty($_REQUEST['id']) && (is_numeric($_REQUEST['id']))){
					if(isset($_REQUEST['blogcomment']) && !empty($_REQUEST['blogcomment'])){
						try {
							$Sessmanager = SessionManager::getInstance ();
							$profile = $Sessmanager->getUserprofile ();
							if ($profile != null) {
								$manager = BlogManager::getInstance ();
								$rep = $manager->addComment ($_REQUEST['id'], $profile->getId(), $_REQUEST['blogcomment'], system_ip_client () );
								if ($rep) {
									echo '{"message" : {"type" : "success", "content" : "Votre commentaire a &eacute;t&eacute; ajout&eacute; avec succ&egrave;s."},
										   "id" : "'.$rep.'"}';
								} else {
									echo '{"message" : {"type" : "error", "content" : "Un probl&egrave;me est survenu. L\'ajout n\'a donc pas eu lieu. Veuillez recommencer."}}';
								}
							}
						} catch ( SQLException $sqle ) {
							echo '{"message" : {"type" : "error", "content" : "Un probl&egrave;me est survenu. L\'ajout n\'a donc pas eu lieu. Veuillez recommencer."}}';
						} catch ( DatabaseExcetion $dbe ) {
							echo '{"message" : {"type" : "error", "content" : "Un probl&egrave;me est survenu. L\'ajout n\'a donc pas eu lieu. Connection impossible &agrave; la base de donn&eacute;es."}}';
						}				
					}else{
						echo '{"message" : {"type" : "error", "content" : "Vous ne pouvez soumettre un commentaire vide."}}';
					}
				}else{
					echo '{"message" : {"type" : "error", "content" : "Identifiant manquant ! Publication impossible."}}';
				}
			}else{
				echo '{"message" : {"type" : "error", "content" : "Vous n\'avez pas les autorisations requises pour cette op&eacute;ration!"}}';
			}
			break;
		// DELETE A COMMENT
		case "deletecomment":
			if(RoleManager::getInstance()->hasCapabilitySession('blog-delete-comment')){
				if(isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])){
					$manager = BlogManager::getInstance();
					$manager->deleteComment($_REQUEST['id']);
					echo '{"message" : {"type" : "success", "content" : "Le commentaire '.$_REQUEST['id'].' a &eacute;t&eacute; supprim&eacute; avec succ&egrave;s."}}';
				}else{
					echo '{"message" : {"type" : "error", "content" : "Identifiant manquant ! Publication impossible."}}';
				}
			}else{
				echo '{"message" : {"type" : "error", "content" : "Vous n\'avez pas les autorisations requises pour cette op&eacute;ration!"}}';
			}
			break;
		default:
			break;
	}
	
}
	
	