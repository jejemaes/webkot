<?php


$view = new EventAdminView($template, $module);


if(isset($_GET['part']) && !empty($_GET['part']) && ($_GET['part'] == 'category')){
	// CATEGORY part
	if(isset($_GET['action']) && !empty($_GET['action'])){
		switch ($_GET['action']) {
			case "add":
				if (RoleManager::getInstance ()->hasCapabilitySession ( 'echogito-add-category' )) {
					
					list($message, $category) = EventCategoryController::addEventCategoryAction($_REQUEST);
					if($message->isSuccess()){
						$SMM = SessionMessageManager::getInstance();
						$SMM->setSessionMessage($message);
						URLUtils::redirection(URLUtils::generateURL($module->getName(), array("part"=>"category")));
					}else{
						$view->pageFormEventCategory('add', $message, $category);
					}
				}else{
					throw new AccessRefusedException("Vous ne pouvez pas d'ajouter de cat&eacute;gorie.");
				}
				break;
			case "edit":
				if (RoleManager::getInstance ()->hasCapabilitySession ( 'echogito-edit-category' )) {
					list($message, $category) = EventCategoryController::editEventCategoryAction($_REQUEST);
					if($message->isSuccess()){
						$SMM = SessionMessageManager::getInstance();
						$SMM->setSessionMessage($message);
						URLUtils::redirection(URLUtils::generateURL($module->getName(), array("part"=>"category")));
					}else{
						$view->pageFormEventCategory('edit', $message, $category);
					}
				}else{
					throw new AccessRefusedException("Vous ne pouvez pas &eacute;diter de cat&eacute;gorie.");
				}
				break;
			case "delete":
				if(RoleManager::getInstance ()->hasCapabilitySession ( 'echogito-delete-category' )){
					$message = EventCategoryController::deleteEventCategoryAction($_GET);
					$SMM = SessionMessageManager::getInstance();
					$SMM->setSessionMessage($message);
					URLUtils::redirection(URLUtils::generateURL($module->getName(), array("part"=>"category")));
				}else{
					throw new AccessRefusedException("Vous ne pouvez pas supprimer d'&eacute;v&eacute;nement.");
				}
				break;
			default:
				break;
		}
	}else{
		$SMM = SessionMessageManager::getInstance();
		$message = $SMM->getSessionMessage();
		
		$ecmanager = EventCategoryManager::getInstance();
		$categories = $ecmanager->getAllEventCategory();
		$view->pageListCategory($message, $categories);
	}
	
}else{
	// EVENT part
	if(isset($_GET['action']) && !empty($_GET['action'])){
		switch ($_GET['action']) {
			case "add":
				if (RoleManager::getInstance ()->hasCapabilitySession ( 'echogito-add-event' )) {
					list($message, $event) = EventController::addEventAction($_REQUEST);
					if($message->isSuccess()){
						$SMM = SessionMessageManager::getInstance();
						$SMM->setSessionMessage($message);
						URLUtils::redirection(URLUtils::generateURL($module->getName(), array("part"=>"event")));
					}else{
						$ecmanager = EventCategoryManager::getInstance();
						$categories = $ecmanager->getAllEventCategory();
						$view->pageFormEvent('add', $message, $event, $categories);
					}
				}else{
					throw new AccessRefusedException("Vous ne pouvez pas editer d'&eacute;v&eacute;nement.");
				}
				break;
			case "edit":
				if (RoleManager::getInstance ()->hasCapabilitySession ( 'echogito-edit-event' )) {
					list($message, $event) = EventController::editEventAction($_REQUEST);
					if($message->isSuccess()){
						$SMM = SessionMessageManager::getInstance();
						$SMM->setSessionMessage($message);
						URLUtils::redirection(URLUtils::generateURL($module->getName(), array("part"=>"event")));
					}else{
						$ecmanager = EventCategoryManager::getInstance();
						$categories = $ecmanager->getAllEventCategory();
						$view->pageFormEvent('edit', $message, $event, $categories);
					}
				}else{
					throw new AccessRefusedException("Vous ne pouvez pas editer d'&eacute;v&eacute;nement.");
				}
				break;
			case "delete":
				if(RoleManager::getInstance ()->hasCapabilitySession ( 'echogito-delete-event' )){
					$message = EventController::deleteEventAction($_GET);
					$SMM = SessionMessageManager::getInstance();
					$SMM->setSessionMessage($message);
					URLUtils::redirection(URLUtils::generateURL($module->getName(), array("part"=>"event")));
				}else{
					throw new AccessRefusedException("Vous ne pouvez pas supprimer d'&eacute;v&eacute;nement.");
				}
				break;
			case "approve":
				if(RoleManager::getInstance ()->hasCapabilitySession ( 'echogito-approve-event' )){
					$message = new Message();
					if(isset($_POST['echogito-input-id']) && !empty($_POST['echogito-input-id']) && is_numeric($_POST['echogito-input-id']) && isset($_POST['echogito-input-categoryid']) && !empty($_POST['echogito-input-categoryid']) && is_numeric($_POST['echogito-input-categoryid'])){
						
						$request = array();
						$request['id'] = $_POST['echogito-input-id'];
						$request['categoryid'] = $_POST['echogito-input-categoryid'];
						
						$message = EventController::updateCategoryEventAction($request);
						if($message->isSuccess()){
							$tmp = EventController::approveEventAction($request);
							$message->addMessage($tmp->getMessage());
						}
					}else{
						$message->setType(3);
						$message->addMessage("Au moins un des champs requis est manquant. Opration annule.");
					}
					$SMM = SessionMessageManager::getInstance();
					$SMM->setSessionMessage($message);
					URLUtils::redirection(URLUtils::generateURL($module->getName(), array("part"=>"event")));
				}else{
					throw new AccessRefusedException("Vous ne pouvez pas approuver d'&eacute;v&eacute;nement.");
				}
				break;
			default:
				break;
		}
	}else{
		if(isset($_GET['p']) && !empty($_GET['p'])){
			switch ($_GET['p']) {
				case 'approve':
					if(isset($_GET['id']) && is_numeric($_GET['id'])){
						$SMM = SessionMessageManager::getInstance();
						$message = $SMM->getSessionMessage();
						
						$ecmanager = EventCategoryManager::getInstance();
						$categories = $ecmanager->getAllEventCategory();
							
						$view->pageFormApprove($message, $_GET['id'], $categories);
					}else{
						throw new InvalidURLException("Sans identifiant, il sera difficile d'approuver l'event.");
					}
					break;
				case 'past':
					break;
				case 'unapproved':
					$SMM = SessionMessageManager::getInstance();
					$message = $SMM->getSessionMessage();
					
					$ecmanager = EventCategoryManager::getInstance();
					$categories = $ecmanager->getAllEventCategory();
					
					$emanager = EventManager::getInstance();
					$events = $emanager->getUnapprovedEvent(date('Y-m-d'));
					$view->pageList($message, $events, $categories);
					break;
				default:
					URLUtils::redirection(URLUtils::generateURL($module->getName(),array("part"=>"event")));
					break;	
			}
		}else{		
			$desc = system_get_desc_pagination();
			$page = (system_get_page_pagination()-1);
			$limit = ($page*$desc);
			
			$SMM = SessionMessageManager::getInstance();
			$message = $SMM->getSessionMessage();
			
			$emanager = EventManager::getInstance();
			$events = $emanager->getEventsAfter(date('Y-m-d'), true, $desc, $limit);
			$count = $emanager->getCountEventsAfter(date('Y-m-d'), true);
			
			$view->pageListPaging($message, $events, $count, $desc, ($page+1));
		}
	}
	
	
}
