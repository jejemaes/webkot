<?php
/*
 * Created on 17 nov. 2012
 *
 * MAES Jerome, Webkot 2012-2013
 * Class Description :
 *
 */
 
$view = new BlogView($template,$module);
$logger->loginfo("blog : create the view");

if(isset($_GET['action']) && !empty($_GET['action'])){
	switch ($_GET['action']) {
		case "deletecom":
			if (RoleManager::getInstance ()->hasCapabilitySession ( 'blog-delete-comment' )) {
				if(isset($_GET['comment']) && (!empty($_GET['comment'])) && is_numeric($_GET['comment'])){
					$manager = BlogManager::getInstance();
					$manager->deleteComment($_GET['comment']);
					URLUtils::redirection(URLUtils::getPreviousURL());
				}
			}else{
				throw new AccessRefusedException("Vous ne pouvez pas retirer de commentaire sur un article.");
			}
			break;
		default:
			echo "switch default : action inconnue.";
	}
}


$message = new Message();

if((isset($_GET['post']))){
 	
 	// check if the GET var is correct
 	if((!empty($_GET['post'])) && (is_numeric($_GET['post']))){
 		// add a comment
 		if (RoleManager::getInstance()->hasCapabilitySession ( 'blog-add-comment' )) {
 			if(isset($_POST['blog-input-comment'])){
 				if(!empty($_POST['blog-input-comment'])){
					try {
						$Sessmanager = SessionManager::getInstance ();
						$profile = $Sessmanager->getUserprofile ();
						if ($profile != null) {
							$manager = BlogManager::getInstance ();
							$rep = $manager->addComment ($_GET['post'], $profile->getId (), $_POST ['blog-input-comment'], system_ip_client () );
							if ($rep) {
								 $message->setType(1);
								 $message->addMessage("Votre commentaire a &eacute;t&eacute; ajout&eacute; avec succ&egrave;s.");
							} else {
								 $message->setType(3);
								 $message->addMessage("Un probl&egrave;me est survenu. L'ajout n'a donc pas eu lieu. Veuillez recommencer.");
							}
						}
					} catch ( SQLException $sqle ) {
						$logger->logwarn ( "Erreur SQL : " . $sqle->getDescription () );
						$view->error ( new Error ( "Erreur SQL", $sqle->getDescription () ) );
					} catch ( DatabaseExcetion $dbe ) {
						$logger->logwarn ( "Connection impossible ˆ la Base de donnees." );
						$view->error ( new Error ( "Erreur de BD", "Connection impossible ˆ la Base de donnees" ) );
					}		
 				}else{
 					//comment empty !
 					$message->setType(3);
 					$message->addMessage("Le champs est vide, et ceci n'est pas permis !");
 				}
 			}
 		}
 		
 		//display the given post
 		if(RoleManager::getInstance()->hasCapabilitySession('blog-read-post')){
 			$logger->loginfo("Display the post " . $_GET['post']);
 			try{
 				$manager = BlogManager::getInstance();
 				$post = $manager->getPost($_GET['post']);
 				
 				$view->PostPage($post, $message);
 			
 			}catch(SQLException $sqle){
 				$logger->logwarn("Erreur SQL : " . $sqle->getDescription());
 				$view->error(new Error("Erreur SQL", $sqle->getDescription()));
 			}catch(DatabaseExcetion $dbe){
 				$logger->logwarn("Connection impossible ˆ la Base de donnees.");
 				$view->error(new Error("Erreur de BD", "Connection impossible ˆ la Base de donnees"));
 			}catch(NullObjectException $ne){
 				$view->error(new Error("Erreur de post", "Aucun post n'existe ici. Circulez !"));
 			}
 			
 		}else{
 			throw new AccessRefusedException("Vous ne pouvez pas lire cet article.");
 		}
 		
 	}else{
 		// error
 		$logger->logfatal("Le post n'exists pas !");
 		//throw new InvalidURL("Le post demande n'existe pas.");
 	}
 	
 	
 	
 }else{
 	$logger->loginfo("blog : display the list of posts");
 	// display the list of post
 	if (RoleManager::getInstance ()->hasCapabilitySession ( 'blog-read-post' )) {		
	 	$logger->loginfo("Display the list of posts");
	 	$posts = array();
	 	try{
	 		$manager = BlogManager::getInstance();
	 		$posts = $manager->getListPost();
	 			
	 		$view->PostList($posts);
	 		
	 	}catch(SQLException $sqle){
	 		$logger->logwarn("Erreur SQL : " . $sqle->getDescription());
	 		$view->error(new Error("Erreur SQL", $sqle->getDescription()));
	 	}catch(DatabaseExcetion $dbe){
			$logger->logwarn("Connection impossible ˆ la Base de donnees.");
	 		$view->error(new Error("Erreur de BD", "Connection impossible ˆ la Base de donnees"));
	 		
	 	}
 	}else{
 		throw new AccessRefusedException("Vous ne pouvez pas consulter la page du blog.");
 	}
 	
 	
 	
 }
 
 
 

?>
