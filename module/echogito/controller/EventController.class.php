<?php


class EventController{
	
	
	/**
	 * add an event, via Facebook or via a traditionnal form
	 * @param array $request : the $_POST and/or $_GET variables
	 * 							$request[echogito-input-*] : the field of an event
	 * 							$request[echogito-input-facebook] : the url of a Facebook event
	 * @return array(Message Event) : the Message Object (telling if the operation is a success or not), the Event Object
	 */
	public static function submitEventAction($request){
		$event = new Event();
		if(isset($request['echogito-input-title']) && isset($request['echogito-input-description']) && isset($request['echogito-input-date'])  && isset($request['echogito-input-location'])){
			// traditionnal form
			$event->setName($request['echogito-input-title']);
			$event->setDescription($request['echogito-input-description']);
			$event->setStart_time($request['echogito-input-date']);
			$event->setLocation($request['echogito-input-location']);
				
			if(!empty($request['echogito-input-title']) && !empty($request['echogito-input-description']) && !empty($request['echogito-input-date'])  && !empty($request['echogito-input-location'])){
				try{
					$emanager = EventManager::getInstance();
					$emanager->add($request['echogito-input-title'], nl2br($request['echogito-input-description']), $request['echogito-input-date'], $request['echogito-input-location'], null, '0', null);
		
					$message = new Message(1);
					$message->addMessage("Votre &eacute;v&eacute;nement a bien &eacute;t&eacute; soumis avec succes. Il sera valid&eacute; dans les plus brefs d&eacute;lais.");
				}catch(SQLException $sqle){
					$message = new Message(3);
					$message->addMessage("Une erreur s'est produite, l'ajout de votre &eacute;v&eacute;nement a echou&eacute;.");
					$message->addMessage($sqle->getMessage());
				}catch(DatabaseExcetion $dbe){
					$message = new Message(3);
					$message->addMessage("Une erreur s'est produite, l'ajout de votre &eacute;v&eacute;nement a echou&eacute;.");
					$message->addMessage($dbe->getMessage());
				}
			}else{
				$message = new Message(3);
				$message->addMessage("Au moins un des champs est vide.");
			}
		}else{
			if(isset($request['echogito-input-facebook'])){
				// Facebook form
				if(!empty($request['echogito-input-facebook'])){
					if(FormatUtils::isUrlFormat($request['echogito-input-facebook']) && (FormatUtils::startsWith($request['echogito-input-facebook'], "https://www.facebook.com/events/") || (FormatUtils::startsWith($request['echogito-input-facebook'], "http://www.facebook.com/events/")))){
						$matches = array();
						
						preg_match('/\d+/', $request['echogito-input-facebook'], $matches);
					
						if(count($matches) > 0){
							// check Facebook connection
							$facebook = new Facebook(array(
									'appId'  => FACEBOOK_APPID,
									'secret' => FACEBOOK_SECRET,
							));
							
							try{	
								$fb_event = $facebook->api('/'.$matches[0]);
								if(!empty($fb_event)){
									try{
										$event->setName($fb_event['name']);
										$event->setDescription(nl2br($fb_event['description']));
										$event->setStart_time(ConversionUtils::transformDate($fb_event['start_time'],"Y-m-d H:i:s"));
										$event->setLocation($fb_event['location']);
										$event->setFacebookid($matches[0]);
										
										
										
										$emanager = EventManager::getInstance();
										$emanager->add($fb_event['name'], nl2br($fb_event['description']), ConversionUtils::transformDate($fb_event['start_time'],"Y-m-d H:i:s"), $fb_event['location'], $matches[0], '0', null);
									
										$message = new Message(1);
										$message->addMessage("Votre &eacute;v&eacute;nement a bien &eacute;t&eacute; soumis avec succes. Il sera valid&eacute; dans les plus brefs d&eacute;lais.");
									}catch(SQLException $sqle){
										$message = new Message(3);
										$message->addMessage("Une erreur s'est produite, l'ajout de votre &eacute;v&eacute;nement a echou&eacute;.");
										$message->addMessage($sqle->getMessage());
									}catch(DatabaseExcetion $dbe){
										$message = new Message(3);
										$message->addMessage("Une erreur s'est produite, l'ajout de votre &eacute;v&eacute;nement a echou&eacute;.");
										$message->addMessage($dbe->getMessage());
									}
								}else{
									$messa = new Message(3);
									$message->addMessage("Aucun event ne correspond a ce lien.");
								}
								
							}catch(Exception $fbe){
								$message = new Message(3);
								$message->addMessage("Facebook a retourn&eacute;. Soit un probl&egrave;me est survenu, soit l'event que vous tentez de soumettre n'est pas publique.");
							}
							
						}else{
							$message = new Message(3);
							$message->addMessage("Mauvais format du lien introduit.");
						}
					}else{
						$message = new Message(3);
						$message->addMessage("L'url introduite est incorrecte. Elle doit etre un lien de Facebook.");
					}
				}else{
					$message = new Message(3);
					$message->addMessage("L'url introduite est incorrecte.");
				}
			}else{
				$message = new Message(3);
			}
		}
		return array($message, $event);
	}
	
	
	
	/**
	 * add an Event
	 * @param array $request : the variables
	 * 					$request[echogito-input-*] : the field of an event
	 * 					$request[echogito-input-facebook] : the url of a Facebook event
	 * @return array(Message Event) : array containing the return Message and Event that was added
	 */
	public static function addEventAction(array $request){
		$event = new Event();
		$message = new Message();
		if(isset($request['echogito-input-title']) && isset($request['echogito-input-description']) && isset($request['echogito-input-date'])  && isset($request['echogito-input-location']) && isset($request['echogito-input-facebookid'])){
			if((is_numeric($request['echogito-input-facebookid']) || empty($request['echogito-input-facebookid'])) && FormatUtils::isDatetimeSqlFormat($request['echogito-input-date'])){
				$event->setName($request['echogito-input-title']);
				$event->setDescription($request['echogito-input-description']);
				$event->setStart_time($request['echogito-input-date']);
				$event->setLocation($request['echogito-input-location']);
				$event->setFacebookid($request['echogito-input-facebookid']);
				$event->setCategoryid($request['echogito-input-categoryid']);
				if(!empty($request['echogito-input-title']) && !empty($request['echogito-input-description']) && !empty($request['echogito-input-date']) && !empty($request['echogito-input-location']) && !empty($request['echogito-input-categoryid'])){
					try{
						$emanager = EventManager::getInstance();
						$emanager->add($request['echogito-input-title'], $request['echogito-input-description'], $request['echogito-input-date'], $request['echogito-input-location'], $request['echogito-input-facebookid'], '1', $request['echogito-input-categoryid']);
						
						$message = new Message(1);
						$message->addMessage("Votre &eacute;v&eacute;nement a bien &eacute;t&eacute; mis a jour avec succes.");
					}catch(SQLException $sqle){
						$message = new Message(3);
						$message->addMessage("Une erreur s'est produite, l'ajout de votre &eacute;v&eacute;nement a echou&eacute;.");
						$message->addMessage($sqle->getMessage());
					}catch(DatabaseExcetion $dbe){
						$message = new Message(3);
						$message->addMessage("Une erreur s'est produite, l'ajout de votre &eacute;v&eacute;nement a echou&eacute;.");
						$message->addMessage($dbe->getMessage());
					}
				}else{
					$message->setType(3);
					$message->addMessage("Au moins un des champs requis est vide !");
				}
			}else{
				$message->setType(3);
				$message->addMessage("Le facebookid n'est pas un chiffre (facultatif), ou la date introduite n'est pas au format YYYY-MM-DD hh:mm:ss.");
			}
		}
		return array($message, $event);
	}
	
	/**
	 * edit an event by traditionnal form $_REQUEST variables
	 * 							$request[echogito-input-id] : the identifier of the event to edit
	 * 							$request[echogito-input-*] : the field of an event
	 * 							$request[echogito-input-facebook] : the url of a Facebook event
	 * @return array(Message,Event) : the Message Object (telling if the operation is a success or not), the Event Object
	 */
	public static function editEventAction(array $request){
		$event = new Event();
		$message = new Message(3);
		if(isset($request['id']) && !empty($request['id']) && is_numeric($request['id'])){
			$emanager = EventManager::getInstance();
			$event = $emanager->getEvent($request['id']);
			if(isset($request['echogito-input-title']) && isset($request['echogito-input-description']) && isset($request['echogito-input-date'])  && isset($request['echogito-input-location']) && isset($request['echogito-input-facebookid'])){	
				if((is_numeric($request['echogito-input-facebookid']) || empty($request['echogito-input-facebookid'])) && FormatUtils::isDatetimeSqlFormat($request['echogito-input-date'])){
					$event->setName($request['echogito-input-title']);
					$event->setDescription($request['echogito-input-description']);
					$event->setStart_time($request['echogito-input-date']);
					$event->setLocation($request['echogito-input-location']);
					$event->setFacebookid($request['echogito-input-facebookid']);
					$event->setCategoryid($request['echogito-input-categoryid']);
					if(!empty($request['echogito-input-title']) && !empty($request['echogito-input-description']) && !empty($request['echogito-input-date'])  && !empty($request['echogito-input-location']) && !empty($request['echogito-input-categoryid'])){
						try{
							$emanager->update($request['id'], $request['echogito-input-title'], $request['echogito-input-description'], $request['echogito-input-date'], $request['echogito-input-location'], $request['echogito-input-facebookid'], $request['echogito-input-categoryid']);
							
							$message = new Message(1);
							$message->addMessage("Votre &eacute;v&eacute;nement a bien &eacute;t&eacute; mis a jour avec succes.");
						}catch(SQLException $sqle){
							$message = new Message(3);
							$message->addMessage("Une erreur s'est produite, l'ajout de votre &eacute;v&eacute;nement a echou&eacute;.");
							$message->addMessage($sqle->getMessage());
						}catch(DatabaseExcetion $dbe){
							$message = new Message(3);
							$message->addMessage("Une erreur s'est produite, l'ajout de votre &eacute;v&eacute;nement a echou&eacute;.");
							$message->addMessage($dbe->getMessage());
						}
					}else{
						$message->setType(3);
						$message->addMessage("Au moins un des champs requis est vide !");
					}
				}else{
					$message->setType(3);
					$message->addMessage("Le facebookid n'est pas un chiffre (facultatif), ou la date introduite n'est pas au format YYYY-MM-DD hh:mm:ss.");
				}
			}
		}else{
			$message->setType(3);
			$message->addMessage("L'identifiant est manquant.");
		}
		return array($message, $event);
	}
	
	
	/**
	 * delete a given Event
	 * @param array $request : $request['id'] is the identifier of the Event to remove
	 * @return Message : the Message Object explaining if the removal is a success or not
	 */
	public static function deleteEventAction($request){
		$message = new Message(1);
		if(isset($request['id']) && !empty($request['id']) && is_numeric($request['id'])){
			$emanager = EventManager::getInstance();
			try{
				$emanager->delete($request['id']);
				$message->addMessage("L'Event a &eacute;t&eacute; supprim&eacute; avec succes.");
			}catch(SQLException $sqle){
				$message->addMessage("Une erreur s'est produite, l'Event n'a pas pu etre supprim&eacute; dans la database.");
				$message->addMessage($sqle->getMessage());
				$messag->setType(3);
			}catch(DatabaseExcetion $dbe){
				$message->addMessage("Une erreur s'est produite, l'Event et les photos n'a pu etre supprim&eacute; dans la database.");
				$message->addMessage($dbe->getMessage());
				$messag->setType(3);
			}
		}else{
			$message->setType(3);
			$message->addMessage("L'identifiant est manquant.");
		}
		return $message;
	}
	
	/**
	 * approve a given Event
	 * @param array $request : $request['id'] is the identifier of the Event to remove
	 * @return Message : the Message Object explaining if the approval is a success or not
	 */
	public static function approveEventAction($request){
		$message = new Message(1);
		if(isset($request['id']) && !empty($request['id']) && is_numeric($request['id'])){
			$emanager = EventManager::getInstance();
			try{
				$emanager->updateApproval($request['id'], '1');
				$message->addMessage("L'Event a &eacute;t&eacute; approuv&eacute; avec succes.");
			}catch(SQLException $sqle){
				$message->addMessage("Une erreur s'est produite, l'Event n'a pas pu etre approuv&eacute; dans la database.");
				$message->addMessage($sqle->getMessage());
				$messag->setType(3);
			}catch(DatabaseExcetion $dbe){
				$message->addMessage("Une erreur s'est produite, l'Event et les photos n'a pu etre approuv&eacute; dans la database.");
				$message->addMessage($dbe->getMessage());
				$messag->setType(3);
			}
		}else{
			$message->setType(3);
			$message->addMessage("L'identifiant de l'event est manquant.");
		}
		return $message;
	}
	
}