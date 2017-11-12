<?php

$view = new UserAdminView($template,$module);

if(isset($_GET['action']) && !empty($_GET['action'])){
	
	switch ($_GET['action']) {
		case "search":
			// Edit a user
			if(isset($_GET['field']) || isset($_POST['field'])){
				if(isset($_GET['field'])){
					$field = $_GET['field'];
				}else{
					$field = $_POST['field'];
				}
				$managerUser = UserManager::getInstance();
				$list = $managerUser->research(ConversionUtils::encoding($field));
				
				$desc = system_get_desc_pagination();
				$page = (system_get_page_pagination()-1);
				$limit = ($page*$desc);
				//compute the size of the complete result list
				$count = count($list);
				// take a slice of the list
				$list = array_slice($list,$limit,$desc);
				$SMM = SessionMessageManager::getInstance();
				$message = $SMM->getSessionMessage();
				$view->pageListUser($message, $list, $count, $desc, ($page+1), array("action"=>"search", "field"=>$field));
			}	
			break;
			
		case "edit":
			if (RoleManager::getInstance ()->hasCapabilitySession ( 'user-edit-other' )) {
					list($message, $user) = UserController::editAdminAction($_REQUEST);	
					if($message){
						if(($message->isSuccess())){
							$SMM = SessionMessageManager::getInstance();
							$SMM->setSessionMessage($message);
							URLUtils::redirection(URLUtils::generateURL($module->getName(), array()));
						}else{
							$view->pageFormUser('edit', $message, $user);
						}
					}else{
						$SMM = SessionMessageManager::getInstance();
						$SMM->setSessionMessage($message);
						URLUtils::redirection(URLUtils::generateURL($module->getName(), array()));
					}
			}else{
				throw new AccessRefusedException("Vous ne pouvez pas editer d'utilisateur. Permissions refusees.");
			}
			break;
		
		case "delete":
			if (RoleManager::getInstance ()->hasCapabilitySession ('user-delete-other')) {
				$message = UserController::deleteAdminAction($_GET);
				$SMM = SessionMessageManager::getInstance();
				$SMM->setSessionMessage($message);
				URLUtils::redirection(URLUtils::generateURL($module->getName(), array()));
			}else{
				throw new AccessRefusedException("Vous ne pouvez pas editer d'utilisateur. Permissions refusees.");
			}
			break;
		default:
			echo "Action inconnue, ou non implementee encore.";
	}
}else{
	
	$param = array();
	if(isset($_GET['list']) && !empty($_GET['list'])){
		$rmanager = RoleManager::getInstance();
		
		switch ($_GET['list']) {
			case "webkot":
				$role = $rmanager->getRole("Webkot");
				$condition = 'P.level >= ' . $role->getLevel();
				$param = array("list" => $_GET['list']);
				break;
			case "admin":
				$role = $rmanager->getRole("Administrator");
				$condition = 'P.level >= ' . $role->getLevel();
				$param = array("list" => $_GET['list']);
				break;
			default:
				$condition = '';
				$param = array();
		}
	}
		
	$desc = system_get_desc_pagination();
	$page = (system_get_page_pagination()-1);
	$limit = ($page*$desc);
	
	$SMM = SessionMessageManager::getInstance();
	$message = $SMM->getSessionMessage();

	// complete list
	$managerUser = UserManager::getInstance();
	$count = $managerUser->getCountUsers($condition);
	$list = $managerUser->getListUser($condition,$limit,$desc);
	$view->pageListUser($message, $list, $count, $desc, ($page+1), $param);
	
}