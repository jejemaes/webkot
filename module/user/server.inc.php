<?php

include DIR_MODULE . $module->getLocation() . 'functions.inc.php';

include DIR_MODULE . $module->getLocation() . 'controller/UserController.class.php';




//######################
//# Action for picture #
//######################
if(isset($_REQUEST['action']) && !empty($_REQUEST['action'])){
	switch ($_REQUEST['action']) {
		case "grant":
			if(RoleManager::getInstance()->hasCapabilitySession('user-grant-user')){
				list($message,$role) = UserController::grantedAdminAction ($_REQUEST);
				$rp = array();
				$rp['message'] = $message->toArray();
				$rp['role'] = system_to_data_obj($role);
				echo json_encode($rp);
			}else{
				throw new AccessRefusedException("Vous ne pouvez pas modifier le role d'un utilisateur.");
			}
			break;	
		case "mailwatch":
			$profile = SessionManager::getInstance()->getUserprofile();
			if((RoleManager::getInstance()->hasCapabilitySession('user-grant-user')) || ($profile && ($profile->getId() == $_REQUEST['id']))){
				$message = UserController::editMailwatchAction ($_REQUEST);
				$arr = array();
				$arr["message"] = $message->toArray();
				echo json_encode($arr);
			}else{
				throw new AccessRefusedException("Vous ne pouvez pas modifier l'abonnement de l'utilisateur.");
			}
			break;
		default:
			break;
	}
}else{
	throw new InvalidURLException("Aucune action n'est pr&eacute;sente &agrave; cette URL.");
}