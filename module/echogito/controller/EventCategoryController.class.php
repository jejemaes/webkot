<?php


class EventCategoryController{
	
	/**
	 * add an Activity
	 * @param array $request : the variables
	 * 					$request[echogito-input-{name, description, color}] : the required field to create a catergory
	 * @return array(Message EventCategory) : array containing the return Message and EventCategory that was added
	 */
	public static function addEventCategoryAction(array $request){
		$category = new EventCategory();
		$message = new Message();
		if(isset($request['echogito-input-name']) && isset($request['echogito-input-description']) && isset($request['echogito-input-color'])){
			if(!empty($request['echogito-input-name']) && !empty($request['echogito-input-description']) && !empty($request['echogito-input-color'])){
				try{
					$ecmanager = EventCategoryManager::getInstance();
					$ecmanager->add($request['echogito-input-name'], $request['echogito-input-description'], $request['echogito-input-color']);
					$message = new Message(1);
					$message->addMessage("La cat&eacute;gorie ".$request['echogito-input-name']." a &eacute;t&eacute; ajout&eacute;e avec succès.");
				}catch(SQLException $sqle){
					$message = new Message(3);
					$message->addMessage("Une erreur s'est produite, l'ajout de votre cat&eacute;gorie a &eacute;choue.");
					$message->addMessage($sqle->getMessage());
				}catch(DatabaseExcetion $dbe){
					$message = new Message(3);
					$message->addMessage("Une erreur s'est produite, l'ajout de votre cat&eacute;gorie a &eacute;choue.");
					$message->addMessage($dbe->getMessage());
				}
			}else{
				$message->setType(3);
				$message->addMessage("Au moins un des champs requis est vide !");
			}
		}
		return array($message, $category);
	}
	
	
	/**
	 * edit a Category by traditionnal form $_REQUEST variables
	 * 							$request[echogito-input-*] : the field of a Category
	 * @return array(Message,EventCategory) : the Message Object (telling if the operation is a success or not), the EventCategory Object
	 */
	public static function editEventCategoryAction(array $request){
		$category = new EventCategory();
		$message = new Message(3);
		if(isset($request['id']) && !empty($request['id']) && is_numeric($request['id'])){
			$ecmanager = EventCategoryManager::getInstance();
			$category = $ecmanager->getEventCategory($request['id']);
			if(isset($request['echogito-input-name']) && isset($request['echogito-input-description']) && isset($request['echogito-input-color'])){
				$category->setName($request['echogito-input-name']);
				$category->setDescription($request['echogito-input-description']);
				$category->setColor($request['echogito-input-color']);
				if(!empty($request['echogito-input-name']) && !empty($request['echogito-input-description']) && !empty($request['echogito-input-color'])){
					try{
						$ecmanager->update($request['id'], $request['echogito-input-name'], $request['echogito-input-description'], $request['echogito-input-color']);

						$message = new Message(1);
						$message->addMessage("Votre cat&eacute;gorie a bien &eacute;t&eacute; mis a jour avec succes.");
					}catch(SQLException $sqle){
						$message = new Message(3);
						$message->addMessage("Une erreur s'est produite, l'ajout de votre cat&eacute;gorie a echou&eacute;.");
						$message->addMessage($sqle->getMessage());
					}catch(DatabaseExcetion $dbe){
						$message = new Message(3);
						$message->addMessage("Une erreur s'est produite, l'ajout de votre cat&eacute;gorie a echou&eacute;.");
						$message->addMessage($dbe->getMessage());
					}
				}else{
					$message->setType(3);
					$message->addMessage("Au moins un des champs requis est vide !");
				}
			}
		}else{
			$message->setType(3);
			$message->addMessage("L'identifiant est manquant.");
		}
		return array($message, $category);
	}
	
	
	/**
	 * delete a given EventCategory
	 * @param array $request : $request['id'] is the identifier of the EventCategory to remove
	 * @return Message : the Message Object explaining if the removal is a success or not
	 */
	public static function deleteEventCategoryAction($request){
		$message = new Message(1);
		if(isset($request['id']) && !empty($request['id']) && is_numeric($request['id'])){
			try{
				$ecmanager = EventCategoryManager::getInstance();
				$ecmanager->delete($request['id']);
				$message->addMessage("La cat&eacute;gorie a &eacute;t&eacute; supprim&eacute;e avec succes.");
			}catch(SQLException $sqle){
				$message->addMessage("Une erreur s'est produite, la cat&eacute;gorie n'a pas pu etre supprim&eacute;e dans la database.");
				$message->addMessage($sqle->getMessage());
				$messag->setType(3);
			}catch(DatabaseExcetion $dbe){
				$message->addMessage("Une erreur s'est produite, la cat&eacute;gorie  n'a pu etre supprim&eacute;e dans la database.");
				$message->addMessage($dbe->getMessage());
				$messag->setType(3);
			}
		}else{
			$message->setType(3);
			$message->addMessage("L'identifiant est manquant.");
		}
		return $message;
	}
	
	
}