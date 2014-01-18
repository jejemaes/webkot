<?php
/*
 * Created on 12 avr. 2012
 *
 * MAES Jerome, Webkot 2011-2012
 * Class description : control the page of the different webkot-team (old and actual) 
 *
 */
 
$view = new WebkotView($template, $module);

// old team
if ((isset($_GET['p'])) && ($_GET['p']=='vieux')){
	
	if (RoleManager::getInstance ()->hasCapabilitySession ( 'webkot-read-webkot' )) {	
		try{
			$managerW = WebkotteurManager::getInstance();
		   	$oldteams = $managerW->getAllOldWebkotTeam();
		   
			$view->pageOldWebkotTeam($oldteams);
		} catch ( SQLException $sqle ) {
			$logger->logwarn ( "Erreur SQL : " . $sqle->getDescription () );
			$view->error ( new Error ( "Erreur SQL", $sqle->getDescription () ) );
		} catch ( DatabaseExcetion $dbe ) {
			$logger->logwarn ( "Connection impossible ˆ la Base de donnees." );
			$view->error ( new Error ( "Erreur de BD", "Connection impossible ˆ la Base de donnees" ) );
		}
	}else {
		throw new AccessRefusedException ( "Vous ne pouvez pas consulter cette page." );
	}
	
} else {
	
	if (RoleManager::getInstance ()->hasCapabilitySession ( 'webkot-read-webkot' )) {
		// actual team page
		$text = '<h3>C\'est quoi ?</h3><div>
	<p><strong>Un serveur...</strong>Nous h&eacute;bergeons gratuitement tout site estudiantin namurois qui en fait la demande. Ce serveur nous permet de mettre &agrave; disposition et g&eacute;rer toutes les adresses @age-namur.be et @webkot.be. Nous proposons &eacute;galement aux &eacute;tudiants, principalement en informatique, qui auraient besoin de travailler sur des projets informatiques de groupe, un service SVN.</p>
	
	<p><strong>Un site web...</strong>Notre site c\'est l\'album photo des &eacute;tudiants namurois. Il constitue la deuxi&egrave;me partie du projet avec des paparazzis en herbe qui circulent dans toutes les activit&eacute;s du campus! </p></div>
	<h3>Nous contacter ?</h3>
	<div id="describe">
	<p><strong>Partie serveur : admin@webkot.be</strong><br />espace ftp, bases de donn&eacute;es, boites mail, mailinglist, comptes subversion...</p>
	<p><strong>Partie site : webkot@age-namur.be</strong><br />faire censurer des photos, faire rapporter un bug du site, ou demander la couverture d\'une soir&eacute;e en particulier</p>
	<p><strong>Ou encore, individuellement...</strong></p>'
		. '<h3>Qui sommes-nous ?</h3>';
		
		$texts = array(
			'C\'est quoi ?' =>	'<p><strong>Un serveur...</strong>Nous h&eacute;bergeons gratuitement tout site estudiantin namurois qui en fait la demande. Ce serveur nous permet de mettre &agrave; disposition et g&eacute;rer toutes les adresses @age-namur.be et @webkot.be. Nous proposons &eacute;galement aux &eacute;tudiants, principalement en informatique, qui auraient besoin de travailler sur des projets informatiques de groupe, un service SVN.</p><p><strong>Un site web...</strong>Notre site c\'est l\'album photo des &eacute;tudiants namurois. Il constitue la deuxi&egrave;me partie du projet avec des paparazzis en herbe qui circulent dans toutes les activit&eacute;s du campus! </p>',
			'Nous contacter ?' => '<p><strong>Partie serveur : admin@webkot.be</strong><br />espace ftp, bases de donn&eacute;es, boites mail, mailinglist, comptes subversion...</p>
	<p><strong>Partie site : webkot@age-namur.be</strong><br />faire censurer des photos, faire rapporter un bug du site, ou demander la couverture d\'une soir&eacute;e en particulier</p>
	<p><strong>Ou encore, individuellement...</strong></p>'	
		);
		
	 	$team = array();
		try{
		  	$managerW = WebkotteurManager::getInstance();
		   	$team = $managerW->getYoungestTeam();
		} catch ( SQLException $sqle ) {
			$logger->logwarn ( "Erreur SQL : " . $sqle->getDescription () );
			$view->error ( new Error ( "Erreur SQL", $sqle->getDescription () ) );
		} catch ( DatabaseExcetion $dbe ) {
			$logger->logwarn ( "Connection impossible ˆ la Base de donnees." );
			$view->error ( new Error ( "Erreur de BD", "Connection impossible ˆ la Base de donnees" ) );
		}
		
		$view->pageActualWebkotTeam($team,$texts);
		
	}else {
		throw new AccessRefusedException ( "Vous ne pouvez pas consulter cette page." );
	}
		
}