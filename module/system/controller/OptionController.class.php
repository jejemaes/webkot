<?php


class OptionController{
	
	/**
	 * add an Option
	 * @param array $request : the REQUEST variables
	 * @return array(Message Option) : array containing the return Message and Option that was added
	 */
	public static function addAction(array $request){
		$omanager = OptionManager::getInstance();
		$option = new Option();
		$message = new Message();
		if(isset($request['input-option-key']) && isset($request['input-option-value']) && isset($request['input-option-description']) && isset($request['input-option-type'])){	
			$option->setKey($request['input-option-key']);
			$option->setValue($request['input-option-value']);
			$option->setDescription($request['input-option-description']);
			$option->setType($request['input-option-type']);
			if(!empty($request['input-option-key']) && !empty($request['input-option-value']) && !empty($request['input-option-description']) && !empty($request['input-option-type'])){	
				try{
					$omanager->add($request['input-option-key'], $request['input-option-value'], $request['input-option-description'], $request['input-option-type']);
					$message = new Message(1);
					$message->addMessage("L'option <i>".$request['input-option-key']."</i> a bien &eacute;t&eacute; ajout&eacute;e.");
				}catch(SQLException $sqle){
					$message = new Message(3);
					$message->addMessage("Une erreur s'est produite, la mise a jour de vos options a echoue.");
					$message->addMessage($sqle->getMessage());
				}catch(DatabaseExcetion $dbe){
					$message = new Message(3);
					$message->addMessage("Une erreur s'est produite, la mise a jour de vos options a echoue.");
					$message->addMessage($dbe->getMessage());
				}
			}else{
				$message->setType(3);
				$message->addMessage("Au moins un des champs requis est vide !");
			}
		}
		return array($message, $option);
	}
	
}