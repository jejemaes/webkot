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
				if (RoleManager::getInstance ()->hasCapabilitySession ( 'echogito-read-event' )) {			
					// pagination
					$desc = 10;// system_get_desc_pagination();
					$page = (system_get_page_pagination()-1);
					$limit = ($page*$desc);
					if(ECHOGITO_JS_ACTIVE){	
						$emanager = EventManager::getInstance();
						$events = $emanager->getEventsAfter(date('Y-m-d'), false, $limit,$desc);
						$events = echogito_sort_by_month($events);
					}else{
						$events = array();
					}
					$count = $emanager->getCountEventsAfter(date('Y-m-d'), false);
				
					$view->pageLaterEvents($events, $count, $desc, ($page+1));
				}else{
					throw new AccessRefusedException("Vous ne pouvez pas lire la liste des &eacute;v&egrave;nements.");
				}
				break;
			case 'event':
				if (RoleManager::getInstance ()->hasCapabilitySession ( 'echogito-read-event' )) {	
					if(isset($_GET['id']) && is_numeric($_GET['id'])){
						$emanager = EventManager::getInstance();
						$event = $emanager->getEvent($_GET['id']);
						$view->pageEvent($event);
					}else{
						throw new InvalidURLException("L'identifiant n'est pas sp&eacute;cifi&eacute;. Il n'y a donc rien &agrave; afficher. Salut Johnny !");
					}
				}else{
					throw new AccessRefusedException("Vous ne pouvez pas lire l'&eacute;v&egrave;nement.");
				}
				break;
			default:
				URLUtils::redirect(URLUtils::generateURL($module->getName(),array()));
				break;
		}	
	}else{
		if(ECHOGITO_ACTIVE){
			if (RoleManager::getInstance ()->hasCapabilitySession ( 'echogito-read-event' )) {			
				$events = array();
				$SMM = SessionMessageManager::getInstance();
				$message = $SMM->getSessionMessage();
				if(ECHOGITO_JS_ACTIVE){			
					try{
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
				}
				$view->pageWeekEvents($events, $message);
			}else{
				throw new AccessRefusedException("Vous ne pouvez pas lire la liste des &eacute;v&egrave;nements.");
			}
		}else{
			$view->pageCalendarAge();
		}
	}
}