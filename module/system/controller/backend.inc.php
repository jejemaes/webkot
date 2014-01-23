<?php


$view = new MediaAdminView($template, $module);


if(isset($_GET['part']) && !empty($_GET['part'])){
	
	// MEDIA
	if($_GET['part'] == 'media'){
		// media action
		if(isset($_GET['action']) && !empty($_GET['action'])){
			switch ($_GET['action']) {
				// Add a media
				case "addmedia":
					if (RoleManager::getInstance ()->hasCapabilitySession ( 'system-add-media' )) {
						$message = new Message(1);
						if(isset($_POST['media-input-name']) && isset($_POST['media-input-category'])){
							if(!empty($_POST['media-input-name']) && ($_FILES['media-input-file']["error"] <= 0) && !empty($_POST['media-input-category']) && is_numeric($_POST['media-input-category'])){
								$mmanager = MediaManager::getInstance();
								$category = $mmanager->getCategory($_POST['media-input-category']);
								
								// File name check
								if (!preg_match ( "#^[a-z0-9A-Z._-\s]+$#", $_FILES["media-input-file"]["name"] )) {
									$message->setType ( 3 );
									$message->addMessage ( "Votre fichier contient d'autres caractères que lettres, chiffres, espaces, points, tirets et underscores." );
								}else{
									// move the file
									$file =  preg_replace('/\s+/', '_', $_FILES["media-input-file"]["name"]);
									$path = DIR_MEDIA . $category->getDirectory() . $file;
									if (file_exists($path)){
										//TODO : creer un nom plus cool si un fichier de meme nom existe deja
										$splits = preg_split("/[.]+/", $_FILES["media-input-file"]["name"]);
										$ext = $splits[count($splits)-1];
										$file = time() . ".".$ext;
										$path = DIR_MEDIA . $category->getDirectory() . $file;
									}
									$res = move_uploaded_file($_FILES["media-input-file"]["tmp_name"],$path);
									if($res){
										$message->addMessage ( "Votre fichier a bien ete deplace." );
										try{
											$mmanager->addMedia($_POST['media-input-name'], $file, $_POST['media-input-category']);
											$message->addMessage ( "Votre fichier a bien ete ajoute a la base de donnees." );
										}catch(SQLException $sqle){
											$message = new Message(3);
											$message->addMessage("Une erreur s'est produite, l'ajout de votre activity a echoue.");
											$message->addMessage($sqle->getMessage());
										}catch(DatabaseExcetion $dbe){
											$message = new Message(3);
											$message->addMessage("Une erreur s'est produite, l'ajout de votre activity a echoue.");
											$message->addMessage($dbe->getMessage());
										}
									}else{
										$message->setType ( 3 );
										$message->addMessage ( "Votre fichier n'a pas ete correctement deplacer et risque d'etre inacessible." );
									}
									$SMM = SessionMessageManager::getInstance();
									$SMM->setSessionMessage($message);
									URLUtils::redirection(URLUtils::generateURL($module->getName(), array('part'=>'media')));
								}	
							}else{
								$message->setType(3);
								$message->addMessage("Au moins un des champs requis est vide !");
							}
						}
						$categories = MediaManager::getInstance()->getCategories();
						$view->pageMediaForm($categories, $message);	
					}else{
						throw new AccessRefusedException("Vous ne pouvez pas ajouter de media.");
					}
					break;
				// Add a cat
				case "addcat":
					if (RoleManager::getInstance ()->hasCapabilitySession ( 'system-add-mediacat' )) {
						$message = new Message(1);
						if(isset($_POST['media-input-name']) && isset($_POST['media-input-description']) && isset($_POST['media-input-directory'])){
							if(!empty($_POST['media-input-name']) && !empty($_POST['media-input-description']) && !empty($_POST['media-input-directory'])){
								// File name check
								if (!preg_match ( "#^[a-z0-9A-Z._-\s]+$#", $_POST['media-input-directory'])) {
									$message->setType ( 3 );
									$message->addMessage ( "Votre dossier contient d'autres caractères que lettres, chiffres, espaces, points, tirets et underscores." );
								}else{
									$directory = preg_replace('/\s+/', '_', $_POST['media-input-directory']);
									$directory = strtolower($directory . "/");
									
									if(is_dir(DIR_MEDIA . $directory)){
										$message->setType ( 3 );
										$message->addMessage("Une erreur s'est produite : un dossier portant le meme nom existe deja. Veuillez le changer !");
									}else{			
										if(mkdir(DIR_MEDIA . $directory, CHMOD, true)){		
											try{
												$mmanager = MediaManager::getInstance();
												$mmanager->addMediaCategory($_POST['media-input-name'], $_POST['media-input-description'], $directory);	
												$message->addMessage ( "Votre categorie a bien ete ajoute a la base de donnees." );
											}catch(SQLException $sqle){
												$message = new Message(3);
												$message->addMessage("Une erreur s'est produite, l'ajout de votre activity a echoue.");
												$message->addMessage($sqle->getMessage());
											}catch(DatabaseExcetion $dbe){
												$message = new Message(3);
												$message->addMessage("Une erreur s'est produite, l'ajout de votre activity a echoue.");
												$message->addMessage($dbe->getMessage());
											}
											
											$SMM = SessionMessageManager::getInstance();
											$SMM->setSessionMessage($message);
											URLUtils::redirection(URLUtils::generateURL($module->getName(), array('part'=>'media')));
										}else{
											$message->setType(3);
											$message->addMessage("Le dossier n'a pu etre cree.");
										}
									}
									
								}
							}else{
								$message->setType(3);
								$message->addMessage("Au moins un des champs requis est vide !");
							}
						}
						$view->pageMediaCatForm('add',$message);
					}else{
						throw new AccessRefusedException("Vous ne pouvez pas ajouter de catégorie de média.");
					}
					break;
				default:
					break;
			}
		}else{
			// display list
			$mmanager = MediaManager::getInstance();
			$list = $mmanager->getCategoriesAndContent();
			
			$SMM = SessionMessageManager::getInstance();
			$message = $SMM->getSessionMessage();
				
			$view->pageMediaList($list,$message);
		}
	}
	
	
	// MODULE
	if($_GET['part'] == 'module'){
		
		if(isset($_GET["action"]) && !empty($_GET['action'])){
			switch ($_GET['action']) {
				case 'edit' :
					if(RoleManager::getInstance()->hasCapabilitySession('system-edit-module')){	
						if(isset($_GET['mname']) && !empty($_GET['mname'])){
							try{
								$mmanager = ModuleManager::getInstance();
								$mod = $mmanager->getModule($_GET['mname']);
						
								$message = new Message(1);
								if(isset($_POST['module-input-name']) && isset($_POST['module-input-active']) && isset($_POST['module-input-inmenu'])){
									if(!empty($_POST['module-input-name']) && is_numeric($_POST['module-input-active']) && is_numeric($_POST['module-input-inmenu'])){
										$mmanager->updateModule($_GET['mname'], $_POST['module-input-name'], $_POST['module-input-inmenu'], $_POST['module-input-active']);
											
										$message->addMessage("Modification réussie.");
						
										$SMM = SessionMessageManager::getInstance();
										$SMM->setSessionMessage($message);
										URLUtils::redirection(URLUtils::generateURL($module->getName(), array('part'=>'module')));
									}else{
										$message->setType(3);
										$message->addMessage("Au moins un des champs requis est vide !");
									}
								}
								$view->pageModuleUpdate($mod, $message);
							} catch ( SQLException $sqle ) {
								$message = new Message ( 3 );
								$message->addMessage ( "Une erreur s'est produite, la mise a jour de vos options a echoue." );
								$message->addMessage ( $sqle->getMessage () );
							} catch ( DatabaseExcetion $dbe ) {
								$message = new Message ( 3 );
								$message->addMessage ( "Une erreur s'est produite, la mise a jour de vos options a echoue." );
								$message->addMessage ( $dbe->getMessage () );
							}
						}else{
							$message = new Message(3);
							$message->addMessage("Le nom du module est manquant !");
							$SMM = SessionMessageManager::getInstance();
							$SMM->setSessionMessage($message);
							URLUtils::redirection(URLUtils::generateURL($module->getName(), array('part'=>'module')));
						}
					}else{
						throw new AccessRefusedException("Vous ne pouvez pas éditer de module.");
					}
					break;
				case 'role' :
					if(RoleManager::getInstance()->hasCapabilitySession('system-edit-role')){
						if(isset($_GET['mname']) && !empty($_GET['mname'])){
							$message = new Message(1);
							
							$rmanager = RoleManager::getInstance();
							$roles = $rmanager->getRoleList();
							
							$mmanager = ModuleManager::getInstance();
							$mod = $mmanager->getModule($_GET['mname']);
							
							if(isset($_POST['system-role-checkboxes'])){
								/*$mmanager = ModuleManager::getInstance();
								$mod = $mmanager->getModule($_GET['mname']);*/
								$available = $mod->getAvailableCapabilities();
								
								//check if every submitted capabilities exists
								$checkedCapabilities = array();
								$generalChecked = true;
								foreach ($roles as $role){
									$rolename = $role->getRole();
									$checkedCapabilities[$rolename] = array();
									$submittedCapabilities = $_POST['system-role-checkboxes'][$rolename];
									
									if($submittedCapabilities){	
										$correct = true;
										foreach ($submittedCapabilities as $capa){
											if(!in_array($capa,$available)){
												$correct = false;
												$generalChecked = false;
												$message->addMessage("La capabilities " . $capa . " n'existe pas pour " . $rolename . " pour le module donné.");
												$message->setType(3);
											}
										}
										if($correct){
											$checkedCapabilities[$rolename] = $submittedCapabilities;
										}
									}
								}
								if($generalChecked){
									$mod->setCapabilities($checkedCapabilities);
									$config = $mod->getConfig();
									$rep = $mmanager->updateModuleConfig($_GET['mname'], $config);
									if($rep){
										$message->addMessage("La mise à jour des rôles ".$_GET['mname']." de a été éffectuée avec succès. La configuration est maintenant : " . json_encode($checkedCapabilities));
										$message->setType(1);
									}else{
										$message->addMessage("La mise à jour des rôles ".$_GET['mname']." de n'a pas été éffectuée. Un problème lors de la mise a jour a eu lieu.");
										$message->setType(3);
									}
									
									$SMM = SessionMessageManager::getInstance();
									$SMM->setSessionMessage($message);
									URLUtils::redirection(URLUtils::generateURL($module->getName(), array('part'=>'module')));
								}else{
									$message->addMessage("La mise à jour n'a pas été éffectuée.");
									$message->setType(3);
								}
							}
	
							$smanager = SessionManager::getInstance();
							$rolename = $smanager->getSessionRole();
							$activeCapabilities = $mod->getCapabilities();
							
							$view->pageModuleRole($_GET['mname'], $mod->getAvailableCapabilities(), $activeCapabilities, $roles, $message);
						}else{
							$message = new Message ( 3 );
							$message->addMessage ( "Le nom du module est manquant !" );
							$SMM = SessionMessageManager::getInstance();
							$SMM->setSessionMessage($message);
							URLUtils::redirection(URLUtils::generateURL($module->getName(), array('part'=>'module')));
						}		
					}else{
						throw new AccessRefusedException("Vous ne pouvez pas éditer de roles.");
					}
					break;
				default:
					break;
			}
		}else{
			$mmanager = ModuleManager::getInstance();
			$modules = $mmanager->getAllModule();
				
			$SMM = SessionMessageManager::getInstance();
			$message = $SMM->getSessionMessage();
				
			$view->pageModuleList($modules, $message);
		}

	}
	
	
	
	

	// OPTIONS
	if($_GET['part'] == 'options'){
		$omanager = OptionManager::getInstance();
		$options = $omanager->getOptions();
		
		if (RoleManager::getInstance ()->hasCapabilitySession ( 'system-edit-option' )) {
			$message = new Message(1);
			if(isset($_POST["option-input"]) && !empty($_POST['option-input'])){
				foreach($_POST['option-input'] as $key => $value){
					switch ($omanager->getOptionObject($key)->getType ()) {
						case 'boolean' :
							if(!($_POST['option-input'][$key] == "true") && !($_POST['option-input'][$key] == "false")){
								$message->setType(3);
								$message->addMessage($key . " n 'est pas un boolean." . $_POST['option-input'][$key]);
							}
							break;
						case 'integer' :
							if(empty($_POST['option-input'][$key]) || !is_numeric($_POST['option-input'][$key])){
								$message->setType(3);
								$message->addMessage($key . " n 'est pas un entier.");
							}
							break;
						case 'string' :
						case 'text':
							if(empty($_POST['option-input'][$key])){
								$message->setType(3);
								$message->addMessage($key . " est vide.");
							}
							break;
						default :
							if(empty($_POST['option-input'][$key])){
								$message->setType(3);
								$message->addMessage($key . " est vide.");
							}
							break;
					}
				}
				if($message->getType() == 1){
					try{
						$omanager->update($_POST['option-input']);
						$message->addMessage("La mise a jour des Options a ete effectuee avec succes. La nouvelle configuration prend effet des maintenant.");
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
					$message->addMessage("Les options n'ont pas ete mise a jour. L'ancienne configuration est toujours active.");
				}
				$options = $omanager->getOptions();
			}		
			$view->pageOptionsForm($options, $message);
		}else{
			throw new AccessRefusedException("Vous ne pouvez pas editer les options.");
		}
		
	}
	
	
	// LOG
	if($_GET['part'] == 'log'){
		if (RoleManager::getInstance ()->hasCapabilitySession ( 'system-read-log' )) {
			$d = system_get_directory_content(DIR_LOG);
			$logs= array();
			foreach ($d as $file){
				if(is_file(DIR_LOG . $file)){
					$logs[] = $file;
				}
			}
			$view->pageLog($logs);
		}else{
			throw new AccessRefusedException("Vous ne pouvez pas lire les logs du systeme.");
		}
	}
	
	
	
	
	// WIDGETS
	if($_GET['part'] == 'widgets'){
		$view = new WidgetAdminView($template, $module);
		$message = new Message(1);
		
		if(isset($_GET['action']) && !empty($_GET['action'])){
			switch ($_GET['action']) {
				case 'add':
					if (RoleManager::getInstance ()->hasCapabilitySession ( 'system-add-widget' )) {
						$message = new Message(1);
						$wmanager = WidgetManager::getInstance();
						if(isset($_POST['widget-input-name']) && isset($_POST['widget-input-active']) && isset($_POST['widget-input-infooter'])){
							if(!empty($_POST['widget-input-name']) && is_numeric($_POST['widget-input-active']) && is_numeric($_POST['widget-input-infooter'])){
								$wmanager->addWidget($_POST['widget-input-name'], $_POST['widget-input-infooter'], $_POST['widget-input-active'], $_POST['widget-input-class'], $_POST['widget-input-module']);
								$message->addMessage("Widget ajout� avec succes.");
									
								$SMM = SessionMessageManager::getInstance();
								$SMM->setSessionMessage($message);
								URLUtils::redirection(URLUtils::generateURL($module->getName(), array('part'=>'widgets')));
							}else{
								$message->setType(3);
								$message->addMessage("Au moins un des champs requis est vide !");
							}
						}
						
						$existing = system_get_directory_content(DIR_WIDGET);
						$added = $wmanager->getAllGenericWidgets();
						$classes = array();
						foreach ($added as $w){
							$classes[] = $w->getClassname();//str_replace(".class.php", "", $w->getClassname());
						}
						$potentials = array();
						foreach ($existing as $w){
							if(!in_array(str_replace(".class.php", "", $w), $classes)){
								$potentials[] = str_replace(".class.php", "", $w);
							}
						}
						
						$modules = ModuleManager::getInstance()->getAllModule();
						$view->pageWidgetAddForm($message, $modules, $potentials);
					}else{
						throw new AccessRefusedException("Vous ne pouvez pas editer les Widgets.");
					}
					break;
				case "edit":
					if (RoleManager::getInstance ()->hasCapabilitySession ( 'system-edit-widget' )) {
						if(isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id'])){
							$wmanager = WidgetManager::getInstance();
	
							$message = new Message(1);
							if(isset($_POST['widget-input-name']) && isset($_POST['widget-input-active']) && isset($_POST['widget-input-infooter'])){
								if(!empty($_POST['widget-input-name']) && is_numeric($_POST['widget-input-active']) && is_numeric($_POST['widget-input-infooter'])){
									$wmanager->updateWidget($_GET['id'], $_POST['widget-input-name'], $_POST['widget-input-infooter'], $_POST['widget-input-active']);		
									$message->addMessage("Modification reussie.");
							
									$SMM = SessionMessageManager::getInstance();
									$SMM->setSessionMessage($message);
									URLUtils::redirection(URLUtils::generateURL($module->getName(), array('part'=>'widgets')));
								}else{
									$message->setType(3);
									$message->addMessage("Au moins un des champs requis est vide !");
								}
							}	
							$widget = $wmanager->getGenericWidget($_GET['id']);
							
							$SMM = SessionMessageManager::getInstance();
							$message = $SMM->getSessionMessage();
							
							$view->pageWidgetForm($widget, $message);	
						}else{
							$message->setType(3);
							$message->addMessage("L'identifiant est manquant.");
							$SMM = SessionMessageManager::getInstance();
							$SMM->setSessionMessage($message);
							URLUtils::redirection(URLUtils::generateURL($module->getName(), array('part'=>'widgets')));
						}		
					}else{
						throw new AccessRefusedException("Vous ne pouvez pas editer les Widgets.");
					}	
					break;
				
				case "place":
					if (RoleManager::getInstance ()->hasCapabilitySession ( 'system-place-widget' )) {	
						$message = new Message(1);
						if(isset($_GET['mid']) && !empty($_GET['mid']) && is_numeric($_GET['mid'])){
	
							$wmanager = WidgetManager::getInstance();
							if(isset($_POST['widget-input-place'])){
								$wmanager->deleteAllWidgets($_GET['mid']);	
								foreach ($_POST['widget-input-place'] as $key => $value){
									if($value != 0){
										$wmanager->addWidgetPlace($_GET['mid'], $key, $value);
									}
								}
								$message->addMessage("Mise a jour enregistree avec succes.");
								$SMM = SessionMessageManager::getInstance();
								$SMM->setSessionMessage($message);
								URLUtils::redirection(URLUtils::generateURL($module->getName(), array('part'=>'widgets')));
							}
							
							$allwidgets = $wmanager->getAllGenericWidgets();
							$modwidgets = $wmanager->getGenericWidgets($_GET['mid']);
							
							$mmanager = ModuleManager::getInstance();
							$mod = $mmanager->getModuleById($_GET['mid']);
							
							$view->pageWidgetPlacement($allwidgets, $mod, $modwidgets, $message);
						}else{
							$message->setType(3);
							$message->addMessage("L'identifiant du module est manquant.");
							$SMM = SessionMessageManager::getInstance();
							$SMM->setSessionMessage($message);
							URLUtils::redirection(URLUtils::generateURL($module->getName(), array('part'=>'widgets')));
						}
					}else{
						throw new AccessRefusedException("Vous ne pouvez pas editer les Widgets.");
					}
					break;
			}
		}else{
			$mmanager = ModuleManager::getInstance();
			$mods = $mmanager->getAllModule();
			
			$wmanager = WidgetManager::getInstance();
			$widgets = $wmanager->getAllGenericWidgets();
			
			$SMM = SessionMessageManager::getInstance();
			$message = $SMM->getSessionMessage();
			$view->pageWidgetList($widgets,$mods,$message);
		}
	}
	
	
	
}else{
	echo "ERROR";
}
