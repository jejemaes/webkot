<?php


if(isset($_REQUEST['action']) && !empty($_REQUEST['action'])){	
	switch ($_REQUEST['action']) {
		case 'getechogito':
			if (RoleManager::getInstance ()->hasCapabilitySession ( 'echogito-read-event' )) {		
				$emanager = EventManager::getInstance();
				$echogito = $emanager->getWeekEvent(date('Y'), date('W'));
				$echogito = echogito_sort_by_day($echogito);
				
				$array = array();
				foreach ($echogito as $day => $events){
					$array[$day] = system_array_obj_to_data_array($events);
				}
				echo json_encode($array);
			}else{
				throw new AccessRefusedException("Vous ne pouvez pas lire les &eacute;v&egrave;nements.");
			}
			break;
		case 'later':
			if (RoleManager::getInstance ()->hasCapabilitySession ( 'echogito-read-event' )) {
				$desc = 10;// system_get_desc_pagination();
				$page = ((isset($_REQUEST['page']) && is_numeric($_REQUEST['page']) && ($_REQUEST['page']>=1)) ? ($_REQUEST['page']-1) : 0);
				//(system_get_page_pagination()-1);
				$limit = ($page*$desc);
					
				$emanager = EventManager::getInstance();
				$events = $emanager->getEventsAfter(date('Y-m-d'), false, $limit,$desc);
				//$events = echogito_sort_by_month($events);
				$eventsArray = echogito_event_array_object_to_array($events, $module->getName());
				echo json_encode($eventsArray);
			}else{
				throw new AccessRefusedException("Vous ne pouvez pas lire les &eacute;v&egrave;nements.");
			}
			break;
		default:
			throw new InvalidURLException("Aucune action n'a &eacute;t&eacute; indiqu&eacute;e.");
			break;
	}

}
		