<?php

$view = new DashboardView($template, $module);



if(isset($_GET['part']) && !empty($_GET['part'])){
	
	// Todo
	if($_GET['part'] == "todo"){
		if(isset($_GET['action']) && !empty($_GET['action'])){
			switch($_GET['action']){
				case "add":
					if (RoleManager::getInstance ()->hasCapabilitySession ( 'dash-add-todo' )) {
						$message = new Message();
						$todo = new Todo();
						$managerSession = SessionManager::getInstance();
						$todo->setAuthor($managerSession->getUserprofile()->getUsername());
						if(isset($_POST['input-todo-title']) && isset($_POST['input-todo-description']) && isset($_POST['input-todo-author'])){
							$todo->setTitle($_POST['input-todo-title']);
							$todo->setDescription($_POST['input-todo-description']);
							$todo->setAuthor($_POST['input-todo-author']);
							if(!empty($_POST['input-todo-title']) && !empty($_POST['input-todo-description']) && !empty($_POST['input-todo-author'])){
								try{
									$managerTodo = TodoManager::getInstance();
									$managerTodo->add($_POST['input-todo-title'], $_POST['input-todo-description'], $_POST['input-todo-author']);
					
									$message = new Message(1);
									$message->addMessage("Le Todo a ete ajoute avec succes.");
									$SMM = SessionMessageManager::getInstance();
									$SMM->setSessionMessage($message);
									URLUtils::redirection(URLUtils::generateURL($module->getName(), array('part'=>'todo')));	
								}catch(SQLException $sqle){
									$message = new Message(3);
									$message->addMessage("Une erreur s'est produite, l'ajout de votre article a echoue.");
									$message->addMessage($sqle->getMessage());
								}catch(DatabaseExcetion $dbe){
									$message = new Message(3);
									$message->addMessage("Une erreur s'est produite, l'ajout de votre article a echoue.");
									$message->addMessage($dbe->getMessage());
								}									
							}else{
								$message->setType(3);
								$message->addMessage("AU moins un des champs requis est manquant.");
							}
						}
						$view->pageTodoForm('add', $message, $todo);	
					}else{
						throw new AccessRefusedException("Vous ne pouvez pas ajouter de todo.");
					}	
					break;
					
				case "edit":
					if (RoleManager::getInstance ()->hasCapabilitySession ( 'dash-edit-todo' )) {
						if(isset($_GET['id']) && is_numeric($_GET['id'])){
							$message = new Message();
							$managerTodo = TodoManager::getInstance();
							$todo = $managerTodo->getTodo($_GET['id']);
							if(isset($_POST['input-todo-title']) && isset($_POST['input-todo-description']) && isset($_POST['input-todo-author'])){
								$todo->setTitle($_POST['input-todo-title']);
								$todo->setDescription($_POST['input-todo-description']);
								$todo->setAuthor($_POST['input-todo-author']);
								if(!empty($_POST['input-todo-title']) && !empty($_POST['input-todo-description']) && !empty($_POST['input-todo-author'])){
									try{			
										$managerTodo->update($_GET['id'], $_POST['input-todo-title'], $_POST['input-todo-description']);
										$message = new Message(1);
										$message->addMessage("Le Todo a ete ajoute avec succes.");
										$SMM = SessionMessageManager::getInstance();
										$SMM->setSessionMessage($message);
										URLUtils::redirection(URLUtils::generateURL($module->getName(), array("part"=>"todo")));
									}catch(SQLException $sqle){
										$message = new Message(3);
										$message->addMessage("Une erreur s'est produite, l'ajout de votre article a echoue.");
										$message->addMessage($sqle->getMessage());
									}catch(DatabaseExcetion $dbe){
										$message = new Message(3);
										$message->addMessage("Une erreur s'est produite, l'ajout de votre article a echoue.");
										$message->addMessage($dbe->getMessage());
									}
								}else{
									$message->setType(3);
									$message->addMessage("AU moins un des champs requis est manquant.");
								}
							}
							$view->pageTodoForm('edit', $message, $todo);
						}else{
							$message->setType(3);
							$message->addMessage("L'identifiant du todo est manquant !");
							$SMM = SessionMessageManager::getInstance();
							$SMM->setSessionMessage($message);
							URLUtils::redirection(URLUtils::generateURL($module->getName(), array('part'=>'todo')));
						}	
					}else{
						throw new AccessRefusedException("Vous ne pouvez pas ajouter de todo.");
					}
					break;
					
				case "delete":
					if (RoleManager::getInstance ()->hasCapabilitySession ( 'dash-delete-todo' )) {
						$message = new Message();
						if(isset($_GET['id']) && is_numeric($_GET['id'])){
							try{
								$managerTodo = TodoManager::getInstance();
								$managerTodo->delete($_GET['id']);
								$message->setType(1);
								$message->addMessage("La suppression du Todo s'est effectuee avec succes.");
								$SMM = SessionMessageManager::getInstance();
								$SMM->setSessionMessage($message);
								URLUtils::redirection(URLUtils::getPreviousURL());
							}catch(SQLException $sqle){
								$message = new Message(3);
								$message->addMessage("Une erreur s'est produite, l'ajout de votre article a echoue.");
								$message->addMessage($sqle->getMessage());
							}catch(DatabaseExcetion $dbe){
								$message = new Message(3);
								$message->addMessage("Une erreur s'est produite, l'ajout de votre article a echoue.");
								$message->addMessage($dbe->getMessage());
							}
						}else{
							$message->setType(3);
							$message->addMessage("L'identifiant du todo est manquant !");
						}
						$SMM = SessionMessageManager::getInstance();
						$SMM->setSessionMessage($message);
						URLUtils::redirection(URLUtils::generateURL($module->getName(), array('part'=>'todo')));
					}else{
						throw new AccessRefusedException("Vous ne pouvez pas ajouter de todo.");
					}
					break;	
				case "check":
					if (RoleManager::getInstance ()->hasCapabilitySession ( 'dash-edit-todo' )) {
						$message = new Message();
						if(isset($_GET['id']) && is_numeric($_GET['id'])){
							try{
								$smanager = SessionManager::getInstance();
								$username = $smanager->getUserprofile()->getUsername();
								
								$managerTodo = TodoManager::getInstance();
								$managerTodo->check($_GET['id'], $username);
								
								$message->setType(1);
								$message->addMessage("La terminaison du Todo a ete enregistree avec succes.");
								$SMM = SessionMessageManager::getInstance();
								$SMM->setSessionMessage($message);
								URLUtils::redirection(URLUtils::generateURL($module->getName(), array('part'=>'todo')));
							}catch(SQLException $sqle){
								$message = new Message(3);
								$message->addMessage("Une erreur s'est produite, l'ajout de votre article a echoue.");
								$message->addMessage($sqle->getMessage());
							}catch(DatabaseExcetion $dbe){
								$message = new Message(3);
								$message->addMessage("Une erreur s'est produite, l'ajout de votre article a echoue.");
								$message->addMessage($dbe->getMessage());
							}
						}else{
							$message->setType(3);
							$message->addMessage("L'identifiant du todo est manquant !");
						}
						$SMM = SessionMessageManager::getInstance();
						$SMM->setSessionMessage($message);
						URLUtils::redirection(URLUtils::generateURL($module->getName(), array('part'=>'todo')));
					}else{
						throw new AccessRefusedException("Vous ne pouvez pas terminer un todo.");
					}
					break;
				default:
					break;
			}	
		}else{
			$SMM = SessionMessageManager::getInstance();
			$message = $SMM->getSessionMessage();
			
			$tmanager = TodoManager::getInstance();
			$todos = $tmanager->getAllTodo();
			$view->pageListTodo($message,$todos);
		}
	}
	
	
	// Slider
	if($_GET['part'] == "slider"){	
		
		if(isset($_GET['action']) && !empty($_GET['action'])){
			switch($_GET['action']){
				case "add":
					if (RoleManager::getInstance ()->hasCapabilitySession ( 'dash-add-slide' )) {
						$message = new Message();
						$slide = new Slide();
						if(isset($_POST['input-slide-title']) && isset($_POST['input-slide-description']) && isset($_POST['input-slide-active']) && isset($_POST['input-slide-img'])){
							$slide->setTitle($_POST['input-slide-title']);
							$slide->setDescription($_POST['input-slide-description']);
							$slide->setPathimg($_POST['input-slide-img']);
							if(!empty($_POST['input-slide-title']) && !empty($_POST['input-slide-description']) && !empty($_POST['input-slide-active'])  && !empty($_POST['input-slide-img'])){
								try{
									$statut = (($_POST['input-slide-active'] == 'true' ? '1' : '0'));
									
									$managerSlide = SlideManager::getInstance();
									$managerSlide->add($_POST['input-slide-title'], $_POST['input-slide-description'], $_POST['input-slide-img'], $statut);
									
									$message = new Message(1);
									$message->addMessage("Le Slide a ete ajoute avec succes.");
									$SMM = SessionMessageManager::getInstance();
									$SMM->setSessionMessage($message);
									URLUtils::redirection(URLUtils::generateURL($module->getName(), array('part'=>'slider')));	
								}catch(SQLException $sqle){
									$message = new Message(3);
									$message->addMessage("Une erreur s'est produite, l'ajout de votre slide a echoue.");
									$message->addMessage($sqle->getMessage());
								}catch(DatabaseExcetion $dbe){
									$message = new Message(3);
									$message->addMessage("Une erreur s'est produite, l'ajout de votre slide a echoue.");
									$message->addMessage($dbe->getMessage());
								}									
							}else{
								$message->setType(3);
								$message->addMessage("Au moins un des champs requis est manquant.");
							}
						}
						$view->pageSlideForm('add', $message, $slide);	
					}else{
						throw new AccessRefusedException("Vous ne pouvez pas ajouter de todo.");
					}	
					break;
					
				case "edit":
					if (RoleManager::getInstance ()->hasCapabilitySession ( 'dash-edit-slide' )) {
						if(isset($_GET['id']) && is_numeric($_GET['id'])){
							$message = new Message();
							$managerSlide = SlideManager::getInstance();
							$slide = $managerSlide->getSlide($_GET['id']);
							if(isset($_POST['input-slide-title']) && isset($_POST['input-slide-description']) && isset($_POST['input-slide-active']) && isset($_POST['input-slide-img'])){
								$slide->setTitle($_POST['input-slide-title']);
								$slide->setDescription($_POST['input-slide-description']);
								$slide->setPathimg($_POST['input-slide-img']);
								if(!empty($_POST['input-slide-title']) && !empty($_POST['input-slide-description']) && !empty($_POST['input-slide-active'])  && !empty($_POST['input-slide-img'])){
									try{
										$statut = (($_POST['input-slide-active'] == 'true' ? '1' : '0'));
										$managerSlide->update($_GET['id'], $_POST['input-slide-title'], $_POST['input-slide-description'], $_POST['input-slide-img'], $statut);
											
										$message = new Message(1);
										$message->addMessage("Le Slide a ete ajoute avec succes.");
										$SMM = SessionMessageManager::getInstance();
										$SMM->setSessionMessage($message);
										URLUtils::redirection(URLUtils::generateURL($module->getName(), array("part"=>"slider")));
									}catch(SQLException $sqle){
										$message = new Message(3);
										$message->addMessage("Une erreur s'est produite, l'ajout de votre slide a echoue.");
										$message->addMessage($sqle->getMessage());
									}catch(DatabaseExcetion $dbe){
										$message = new Message(3);
										$message->addMessage("Une erreur s'est produite, l'ajout de votre slide a echoue.");
										$message->addMessage($dbe->getMessage());
									}
								}else{
									$message->setType(3);
									$message->addMessage("AU moins un des champs requis est manquant.");
								}
							}
							$view->pageSlideForm('edit', $message, $slide);
						}else{
							$message->setType(3);
							$message->addMessage("L'identifiant du todo est manquant !");
							$SMM = SessionMessageManager::getInstance();
							$SMM->setSessionMessage($message);
							URLUtils::redirection(URLUtils::generateURL($module->getName(), array('part'=>'todo')));
						}
					}else{
						throw new AccessRefusedException("Vous ne pouvez pas editer de slide.");
					}
					break;
				case "delete":
					if (RoleManager::getInstance ()->hasCapabilitySession ( 'dash-delete-slide' )) {
						$message = new Message();
						if(isset($_GET['id']) && is_numeric($_GET['id'])){
							try{
								$managerSlide = SlideManager::getInstance();
								$managerSlide->delete($_GET['id']);
								$message->setType(1);
								$message->addMessage("La suppression du Slide s'est effectuee avec succes.");
								$SMM = SessionMessageManager::getInstance();
								$SMM->setSessionMessage($message);
								URLUtils::redirection(URLUtils::getPreviousURL());
							}catch(SQLException $sqle){
								$message = new Message(3);
								$message->addMessage("Une erreur s'est produite, la suppression de votre Slide a echou�e.");
								$message->addMessage($sqle->getMessage());
							}catch(DatabaseExcetion $dbe){
								$message = new Message(3);
								$message->addMessage("Une erreur s'est produite, la suppression de votre Slide a echou�e.");
								$message->addMessage($dbe->getMessage());
							}
						}else{
							$message->setType(3);
							$message->addMessage("L'identifiant du Slide est manquant !");
						}
						$SMM = SessionMessageManager::getInstance();
						$SMM->setSessionMessage($message);
						URLUtils::redirection(URLUtils::generateURL($module->getName(), array('part'=>'slide')));
					}else{
						throw new AccessRefusedException("Vous ne pouvez pas supprimer de Slide.");
					}
					break;
					
				default:
					break;
					
			}
		}else{		
			$SMM = SessionMessageManager::getInstance();
			$message = $SMM->getSessionMessage();
				
			$slmanager = SlideManager::getInstance();
			$slides = $slmanager->getAllSlides();
			
			$view->pageListSlide($message, $slides);
		}
		
	}
	
	// Postid
	if($_GET['part'] == "postit"){
	
	}

	
	// APC
	if($_GET['part'] == "apc"){
		if (RoleManager::getInstance ()->hasCapabilitySession ( 'dash-flush-apc' )) {
			$message = new Message(1);
			if((extension_loaded('apc') && ini_get('apc.enabled'))){
				apc_clear_cache();
				$message->addMessage("Le cache APC a ete entierement vide avec succes !");
			}else{
				$message->setType(2);
				$message->addMessage("APC n'est pas active.");
			}
			$SMM = SessionMessageManager::getInstance();
			$SMM->setSessionMessage($message);
			URLUtils::redirection(URLUtils::generateURL($module->getName(), array()));
		}
	}
	
	
	
	if(($_GET['part'] == 'isauthor')){
		$managerActi = ActivityManager::getInstance();
		
		$yb = 2003;
		while($yb <= date('Y')){
		
			$managerWebk = WebkotteurManager::getInstance();
			$team = $managerWebk->getTeam($yb);
			for($i=0 ; $i<count($team) ; $i++){
				$wk = $team[$i];
				if($wk->getValuetolike() != null){
					$valuetolike = array();
					$sp = preg_split('[;]',$wk->getValuetolike());
					for($j=0 ; $j<count($sp) ; $j++){
						$valuetolike[] = $sp[$j];
					}
					$tab = $managerActi->getIdActUser($valuetolike,$yb.'-07-15',($yb+1) . '-07-14');
					//echo '<h4>'.$yb.'--'.$wk->getFirstname(). ' '.$wk->getName().' = '.count($tab).'</h4>';
					//var_dump($tab);
					for($k=0 ; $k<count($tab) ; $k++){
						if($wk->getUserid() !== '3'){
							echo '<br> INSERT INTO isauthor (activityid, userid) values ('.$tab[$k].','.$wk->getUserid().' );';
						}
					}
		//echo '<hr>';
				}
			}
		
			$yb++;
		}
	}
	
	
	
}else{

	$SMM = SessionMessageManager::getInstance();
	$message = $SMM->getSessionMessage();
	
	$tmanager = TodoManager::getInstance();
	$todos = $tmanager->getListTodo();
	
	$smanager = SessionManager::getInstance();
	$username = $smanager->getUSerprofile()->getUsername();
	
	$slmanager = SlideManager::getInstance();
	$slides = $slmanager->getAllSlides();
	
	$cmanager = CensureManager::getInstance();
	$censures = $cmanager->getUnapprovedCensure();
	
	$emanager = EventManager::getInstance();
	$nbrEventNonApproved = $emanager->getCountUnapprovedEvent(date('Y-m-d'));
	
	$view->pageHome($message, $todos, $username, $slides, $censures, $nbrEventNonApproved);
}

