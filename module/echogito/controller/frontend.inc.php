<?php


$view = new EventView($template, $module);

if(isset($_GET['action']) && !empty($_GET['action'])){
	switch ($_GET['action']) {
		case "submit":
			if (RoleManager::getInstance ()->hasCapabilitySession ( 'echogito-submit-event' )) {
				
				list($message, $event) = EventController::submitEventAction($_POST);
				
				if($message->isSuccess()){
					$SMM = SessionMessageManager::getInstance();
					$SMM->setSessionMessage($message);
					URLUtils::redirection(URLUtils::generateURL($module->getName(), array()));
				}else{
					$view->pageForm($message);
				}
			}else{
				throw new AccessRefusedException("Vous ne pouvez pas ajouter d'&eacute;v&eacute;nement.");
			}
			break;
			
		default:
			echo "switch default : action inconnue.";
	}
}else{
	if(isset($_GET['p']) && !empty($_GET['p'])){

		switch ($_GET['p']) {
			case 'calendar':
				$view->pageCalendarAge();
				break;
			case 'later':
				// pagination
				$desc = system_get_desc_pagination();
				$page = (system_get_page_pagination()-1);
				$limit = ($page*$desc);
				
				$emanager = EventManager::getInstance();
				$events = $emanager->getEventsAfter(date('Y-m-d'), false, $desc, $limit);
				$events = echogito_sort_by_month($events);
				$count = $emanager->getCountEventsAfter(date('Y-m-d'));
				
				$view->pageLaterEvents($events, $count, $desc, ($page+1));
				break;
			case 'event':
				if(isset($_GET['id']) && is_numeric($_GET['id'])){
					$emanager = EventManager::getInstance();
					$event = $emanager->getEvent($_GET['id']);
					$view->pageEvent($event);
				}else{
					throw new InvalidURLException("L'identifiant n'est pas spécifié. Il n'y a donc rien &agrave; afficher. Salut Johnny !");
				}
				break;
			default:
				URLUtils::redirect(URLUtils::generateURL($module->getName(),array()));
				break;
		}	
	}else{
		$events = array();
		try{
			$SMM = SessionMessageManager::getInstance();
			$message = $SMM->getSessionMessage();
			
			$emanager = EventManager::getInstance();
			$events = $emanager->getWeekEvent(date('Y'), date('W'));
			$events = echogito_sort_by_day($events); 
		}catch(SQLException $sqle){
			$message = new Message(3);
			$message->addMessage("Une erreur s'est produite, la recuperation des &eacute;v&eacute;nements a echou&eacute;.");
			$message->addMessage($sqle->getMessage());
		}catch(DatabaseExcetion $dbe){
			$message = new Message(3);
			$message->addMessage("Une erreur s'est produite, la recuperation des &eacute;v&eacute;nements a echou&eacute;.");
			$message->addMessage($dbe->getMessage());
		}
		$view->pageWeekEvents($events, $message);
	}
}