<?php

$view = new VideoAdminView($template,$module);

$message = new Message();

if(isset($_GET['action']) && $_GET['action'] == "flush"){
	$vmanager = VideoManager::getInstance();
	if($vmanager->flushApc()){
		$message->setType(1);
		$message->addMessage("La partie du cache APC contenant les vid&eacute;os a &eacute;t&eacute; vid&eacute; avec succ&egrave;s.");
	}else{
		$message->setType(3);
		$message->addMessage("Le cache vid&eacute;o n'a pas &eacute;t&eacute; vid&eacute;.");
	}
}



$omanager = OptionManager::getInstance();
$ytuserid = $omanager->getOption("video-youtube-userid");

$vmanager = VideoManager::getInstance();
$videos = $vmanager->getListVideos($ytuserid);

$view->page($videos,$message);
