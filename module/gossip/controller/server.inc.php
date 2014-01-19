<?php


if(isset($_REQUEST['action']) && !empty($_REQUEST['action'])){

	switch ($_REQUEST['action']) {
		// ADD A COMMENT
		case "getpage":
			if(isset($_REQUEST['num']) && !empty($_REQUEST['num']) && is_numeric($_REQUEST['num'])){
				if(RoleManager::getInstance()->hasCapabilitySession('gossip-read-gossip')){
					$page = ($_REQUEST['num']-1);
					$nbr = NBR_DEFAULT;
					$limit = ($page*$nbr);
				
					$gmanager = GossipManager::getInstance();
					$list = $gmanager->getListGossip($limit, $nbr);
					
					$html = gossip_html_list($list, $_REQUEST['num']);
					
					echo $html;
				}else{
					echo '{"message" : {"type" : "error", "content" : "Vous n\'avez pas les autorisations requises pour voir la page '.$_REQUEST['num'].'."}}';
				}
			}else{
				echo '{"message" : {"type" : "error", "content" : "Le num&eacute;ro de la page est manquant !"}}';
			}
			break;
		// like action, return json
		case "like":
			if(isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])){
				if(RoleManager::getInstance()->hasCapabilitySession('gossip-add-comment')){
					$profile = $smanager = SessionManager::getInstance()->getUserprofile();
					if($profile){					
						try{
							$manager = GossipManager::getInstance();
							$rep = $manager->like($_REQUEST['id'],$profile->getId());
							if($rep){
								echo '{"message" : {"type" : "success", "content" : "Vous aimez le potin '.$_REQUEST['id'].'."}}';
							}else{
								echo '{"message" : {"type" : "warning", "content" : "Un probl&egrave;me est survenu. Votre op&eacute;ration n\'a donc pas eu lieu. Veuillez recommencer."}}';
							}
						}catch(SQLException $sqle){
							echo '{"message" : {"type" : "error", "content" : "L\'op&eacute;ration a &eacute;t&eacute; refus&eacute;e. Vous ne pouvez pas aimer et ne pas aimer un meme potin."}}';
						}catch(DatabaseExcetion $dbe){
							echo '{"message" : {"type" : "error", "content" : "Connection impossible a la base de donn&eacute;es."}}';
						}
					}else{
						echo '{"message" : {"type" : "error", "content" : "Aucun profile session n\'existe."}}';
					}
				}else{
					echo '{"message" : {"type" : "error", "content" : "Vous n\'avez pas les autorisations requises pour voir la page '.$_REQUEST['num'].'."}}';
				}
			}else{
				echo '{"message" : {"type" : "error", "content" : "L\'identifiant du potin est manquant !"}}';
			}
			break;
		// like action, return json
		case "unlike":
			if(isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])){
				if(RoleManager::getInstance()->hasCapabilitySession('gossip-add-comment')){
					$profile = $smanager = SessionManager::getInstance()->getUserprofile();
					if($profile){
						try{
							$manager = GossipManager::getInstance();
							$rep = $manager->unlike($_REQUEST['id'],$profile->getId());
							if($rep){
								echo '{"message" : {"type" : "success", "content" : "Vous n\'aimez plus le potin '.$_REQUEST['id'].'."}}';
							}else{
								echo '{"message" : {"type" : "warning", "content" : "Un probl&egrave;me est survenu. Votre op&eacute;ration n\'a donc pas eu lieu. Veuillez recommencer."}}';
							}
						}catch(SQLException $sqle){
							echo '{"message" : {"type" : "error", "content" : "L\'op&eacute;ration a &eacute;t&eacute; refus&eacute;e. Vous ne pouvez pas aimer et ne pas aimer un meme potin."}}';
						}catch(DatabaseExcetion $dbe){
							echo '{"message" : {"type" : "error", "content" : "Connection impossible a la base de donn&eacute;es."}}';
						}
					}else{
						echo '{"message" : {"type" : "error", "content" : "Aucun profile session n\'existe."}}';
					}
				}else{
					echo '{"message" : {"type" : "error", "content" : "Vous n\'avez pas les autorisations requises pour voir la page '.$_REQUEST['num'].'."}}';
				}
			}else{
				echo '{"message" : {"type" : "error", "content" : "L\'identifiant du potin est manquant !"}}';
			}
			break;
		// like action, return json
		case "dislike":
			if(isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])){
				if(RoleManager::getInstance()->hasCapabilitySession('gossip-add-comment')){
					$profile = $smanager = SessionManager::getInstance()->getUserprofile();
					if($profile){
						try{
							$manager = GossipManager::getInstance();
							$rep = $manager->dislike($_REQUEST['id'],$profile->getId());
							if($rep){
								echo '{"message" : {"type" : "success", "content" : "Vous n\'aimez pas le potin '.$_REQUEST['id'].'."}}';
							}else{
								echo '{"message" : {"type" : "warning", "content" : "Un probl&egrave;me est survenu. Votre op&eacute;ration n\'a donc pas eu lieu. Veuillez recommencer."}}';
							}
						}catch(SQLException $sqle){
							echo '{"message" : {"type" : "error", "content" : "L\'op&eacute;ration a &eacute;t&eacute; refus&eacute;e. Vous ne pouvez pas aimer et ne pas aimer un meme potin."}}';
						}catch(DatabaseExcetion $dbe){
							echo '{"message" : {"type" : "error", "content" : "Connection impossible a la base de donn&eacute;es."}}';
						}
					}else{
						echo '{"message" : {"type" : "error", "content" : "Aucun profile session n\'existe."}}';
					}
				}else{
					echo '{"message" : {"type" : "error", "content" : "Vous n\'avez pas les autorisations requises pour voir la page '.$_REQUEST['num'].'."}}';
				}
			}else{
				echo '{"message" : {"type" : "error", "content" : "L\'identifiant du potin est manquant !"}}';
			}
			break;
		case "undislike":
			if(isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])){
				if(RoleManager::getInstance()->hasCapabilitySession('gossip-add-comment')){
					$profile = $smanager = SessionManager::getInstance()->getUserprofile();
					if($profile){
						try{
							$manager = GossipManager::getInstance();
							$rep = $manager->undislike($_REQUEST['id'],$profile->getId());
							if($rep){
								echo '{"message" : {"type" : "success", "content" : "Vous n\'aimez pas plus le potin '.$_REQUEST['id'].'."}}';
							}else{
								echo '{"message" : {"type" : "warning", "content" : "Un probl&egrave;me est survenu. Votre op&eacute;ration n\'a donc pas eu lieu. Veuillez recommencer."}}';
							}
						}catch(SQLException $sqle){
							echo '{"message" : {"type" : "error", "content" : "L\'op&eacute;ration a &eacute;t&eacute; refus&eacute;e. Vous ne pouvez pas aimer et ne pas aimer un meme potin."}}';
						}catch(DatabaseExcetion $dbe){
							echo '{"message" : {"type" : "error", "content" : "Connection impossible a la base de donn&eacute;es."}}';
						}
					}else{
						echo '{"message" : {"type" : "error", "content" : "Aucun profile session n\'existe."}}';
					}
				}else{
					echo '{"message" : {"type" : "error", "content" : "Vous n\'avez pas les autorisations requises pour voir la page '.$_REQUEST['num'].'."}}';
				}
			}else{
				echo '{"message" : {"type" : "error", "content" : "L\'identifiant du potin est manquant !"}}';
			}
			break;
		// censure action, return json
		case "censure":
			if(isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])){
				if(RoleManager::getInstance()->hasCapabilitySession('gossip-censure-gossip')){		
					try{
						$manager = GossipManager::getInstance();
						$rep = $manager->censure($_REQUEST['id']);
						if($rep){
							echo '{"message" : {"type" : "success", "content" : "Le potin '.$_REQUEST['id'].' a &eacute;t&eacute; censur&eacute; avec succ&egrave;s."}}';
						}else{
							echo '{"message" : {"type" : "warning", "content" : "Un probl&egrave;me est survenu. Votre op&eacute;ration n\'a donc pas eu lieu. Veuillez recommencer."}}';
						}
					}catch(SQLException $sqle){
						echo '{"message" : {"type" : "error", "content" : "L\'op&eacute;ration a &eacute;t&eacute; refus&eacute;e. Vous ne pouvez pas aimer et ne pas aimer un meme potin."}}';
					}catch(DatabaseExcetion $dbe){
						echo '{"message" : {"type" : "error", "content" : "Connection impossible a la base de donn&eacute;es."}}';
					}		
				}else{
					echo '{"message" : {"type" : "error", "content" : "Vous n\'avez pas les autorisations requises pour voir la page '.$_REQUEST['num'].'."}}';
				}
			}else{
				echo '{"message" : {"type" : "error", "content" : "L\'identifiant du potin est manquant !"}}';
			}
			break;
		// uncensure action, return json
		case "uncensure":
			if(isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])){
				if(RoleManager::getInstance()->hasCapabilitySession('gossip-censure-gossip')){
					try{
						$manager = GossipManager::getInstance();
						$rep = $manager->uncensure($_REQUEST['id']);
						if($rep){
							echo '{"message" : {"type" : "success", "content" : "Le potin '.$_REQUEST['id'].' a &eacute;t&eacute; d&eacute;censur&eacute; avec succ&egrave;s."}}';
						}else{
							echo '{"message" : {"type" : "warning", "content" : "Un probl&egrave;me est survenu. Votre op&eacute;ration n\'a donc pas eu lieu. Veuillez recommencer."}}';
						}
					}catch(SQLException $sqle){
						echo '{"message" : {"type" : "error", "content" : "L\'op&eacute;ration a &eacute;t&eacute; refus&eacute;e. Vous ne pouvez pas aimer et ne pas aimer un meme potin."}}';
					}catch(DatabaseExcetion $dbe){
						echo '{"message" : {"type" : "error", "content" : "Connection impossible a la base de donn&eacute;es."}}';
					}
				}else{
					echo '{"message" : {"type" : "error", "content" : "Vous n\'avez pas les autorisations requises pour voir la page '.$_REQUEST['num'].'."}}';
				}
			}else{
				echo '{"message" : {"type" : "error", "content" : "L\'identifiant du potin est manquant !"}}';
			}
			break;
		// getlikers action, return json
		case "getliker":
			if(isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])){
				if(RoleManager::getInstance()->hasCapabilitySession('gossip-read-gossip')){
					$manager = GossipManager::getInstance();
					$list = $manager->getLikerList($_REQUEST['id']);
					echo json_encode($list);
				}else{
					echo '{"message" : {"type" : "error", "content" : "Vous n\'avez pas les autorisations requises pour voir la page '.$_REQUEST['num'].'."}}';
				}
			}else{
				echo '{"message" : {"type" : "error", "content" : "L\'identifiant du potin est manquant !"}}';
			}
			break;
		// getdislikers action, return json
		case "getdisliker":
			if(isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])){
				if(RoleManager::getInstance()->hasCapabilitySession('gossip-read-gossip')){
					$manager = GossipManager::getInstance();
					$list = $manager->getDislikerList($_REQUEST['id']);
					echo json_encode($list);
				}else{
					echo '{"message" : {"type" : "error", "content" : "Vous n\'avez pas les autorisations requises pour voir la page '.$_REQUEST['num'].'."}}';
				}
			}else{
				echo '{"message" : {"type" : "error", "content" : "L\'identifiant du potin est manquant !"}}';
			}
			break;
		// delete action, return json
		case "delete":
			if(isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])){
				if(RoleManager::getInstance()->hasCapabilitySession('gossip-read-gossip')){
					try{
						$manager = GossipManager::getInstance();
						$manager->delete($_REQUEST['id']);
						echo '{"message" : {"type" : "success", "content" : "Le potin a &eacute;t&eacute; supprim&eacute; avec succes."}}';
					}catch(SQLException $sqle){
						echo '{"message" : {"type" : "error", "content" : "L\'op&eacute;ration a &eacute;t&eacute; refus&eacute;e. Vous ne pouvez pas aimer et ne pas aimer un meme potin."}}';
					}catch(DatabaseExcetion $dbe){
						echo '{"message" : {"type" : "error", "content" : "Connection impossible a la base de donn&eacute;es."}}';
					}
				}else{
					echo '{"message" : {"type" : "error", "content" : "Vous n\'avez pas les autorisations requises pour voir la page '.$_REQUEST['num'].'."}}';
				}
			}else{
				echo '{"message" : {"type" : "error", "content" : "L\'identifiant du potin est manquant !"}}';
			}
			break;
		default:
			echo '{"message" : {"type" : "error", "content" : "Action inconnue !"}}';
			break;
	}
}else{
	echo '{"message" : {"type" : "error", "content" : "Action inconnue !"}}';
}
