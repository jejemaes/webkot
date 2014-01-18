<?php


$view = new LinkView ( $template, $module );

if (RoleManager::getInstance ()->hasCapabilitySession ( 'link-read-link' )) {
	try {
		$manager = LinkManager::getInstance ();
		$links = $manager->getListLink ();
		$view->pageLink ( $links );
	} catch ( SQLException $sqle ) {
		$logger->logwarn ( "Erreur SQL : " . $sqle->getDescription () );
		$view->error ( new Error ( "Erreur SQL", $sqle->getDescription () ) );
	} catch ( DatabaseExcetion $dbe ) {
		$logger->logwarn ( "Connection impossible ˆ la Base de donnees." );
		$view->error ( new Error ( "Erreur de BD", "Connection impossible ˆ la Base de donnees" ) );
	}
}else{
	throw new AccessRefusedException("Vous ne pouvez pas lire les liens.");
}