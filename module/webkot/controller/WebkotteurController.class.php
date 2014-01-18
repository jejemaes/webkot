<?php


class WebkotteurController{
	
	
	/**
	 * edit a membership of a given Webkotteur profile for a given year
	 * @param array $request : the request variables
	 * 			- year : the year of the membership in the format YYYY-YYYY
	 * 			- id : the identifier of the Webkotteur
	 * @return array (Message Webkotteur) : the Message and the Webkotteur Objects
	 */
	public static function editMembershipAction($request){
		$webkotteur = new Webkotteur();
		$message = new Message();
		if(isset($request['year']) && !empty($request['year']) && isset($request['id']) && !empty($request['id']) && is_numeric($request['id'])){
			$manager = WebkotteurManager::getInstance();
			$webkotteur = $manager->getMember($request['year'],$request['id']);
			if(isset($request['webkot-input-id']) && !empty($request['webkot-input-id']) && isset($request['webkot-input-year']) && !empty($request['webkot-input-year']) && isset($request['webkot-input-age']) && !empty($request['webkot-input-age']) && isset($request['webkot-input-order']) && !empty($request['webkot-input-order'])){
		
				$webkotteur->setAge ( $request ['webkot-input-age'] );
				$webkotteur->setFunction ( $request ['webkot-input-function'] );
				$webkotteur->setImg ( $request ['webkot-input-img'] );
				$webkotteur->setStudies ( $request ['webkot-input-study'] );
				$webkotteur->setPlace ( $request ['webkot-input-order'] );
		
				if(!is_numeric($request['webkot-input-age'])){
					$message->setType(3);
					$message->addMessage("L'age n'est pas un chiffre.");
				}
		
				if(!is_numeric($request['webkot-input-order'])){
					$message->setType(3);
					$message->addMessage("L'ordre n'est pas un chiffre.");
				}
		
				if($message->isEmpty()){
					try{
						//update
						$manager = WebkotteurManager::getInstance();
						$manager->updateMembership($request['id'], $request['year'], $request['webkot-input-function'], $request['webkot-input-age'], $request['webkot-input-study'], $request['webkot-input-img'], $request['webkot-input-order']);
						$message = new Message(1);
						$message->addMessage("La modification a ete effectuee avec succes.");
					}catch(SQLException $sqle){
						$message->setType(3);
						$message->addMessage("Une erreur s'est produite, l'ajout de votre activity a echoue.");
						$message->addMessage($sqle->getMessage());
					}catch(DatabaseExcetion $dbe){
						$messagesetType(3);
						$message->addMessage("Une erreur s'est produite, l'ajout de votre activity a echoue.");
						$message->addMessage($dbe->getMessage());
					}
				}
			}
		}
		return array($message, $webkotteur);
	}
	
	
	
	/**
	 * delete a given membership
	 * @param array $request : the REQUEST variables. It contains
	 * 			- id : the identifier of the Webkotteur profile
	 * 			- year : the year of the membership, in the format YYYY-YYYY
	 * @return Message $message : Message Object containing the result of the action
	 */
	public static function deleteMembershipAction($request){
		$message = new Message(1);
		if(isset($request['id']) && !empty($request['id']) && is_numeric($request['id']) && isset($request['year']) && !empty($request['year'])){
			try{
				$wmanager = WebkotteurManager::getInstance();
				$wmanager->deleteMembership($request['id'], $request['year']);
				$message->addMessage("L'appartenance a t supprime avec succes.");
			}catch(SQLException $sqle){
				$message->addMessage("Une erreur s'est produite, l'appartenance ˆ une quipe n'a pas t supprime.");
				$message->addMessage($sqle->getMessage());
				$messag->setType(3);
			}catch(DatabaseExcetion $dbe){
				$message->addMessage("Une erreur s'est produite, l'appartenance ˆ une quipe n'a pas t supprime.");
				$message->addMessage($dbe->getMessage());
				$messag->setType(3);
			}
		}else{
			$message->setType(3);
			$message->addMessage("L'identifiant ou l'anne est manquant.");
		}
		return $message;
	}
	
}