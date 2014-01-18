<?php

$view = new AccueilView($template,$module);

$omanager = OptionManager::getInstance();
$nbr = $omanager->getOption('accueil-last-activity');
$lcp = $omanager->getOption('accueil-last-commented');
$ytUserId = $omanager->getOption("video-youtube-userid");
$nbrLP = $omanager->getOption('blog-widget-lastpost');

try{
	$slmanager = SlideManager::getInstance();
	$slides = $slmanager->getActiveSlides();
}catch(Exception $e){
	$slides = array();
}

try{
	$amanager = ActivityManager::getInstance();
	$activities = $amanager->getLastActivity($nbr,system_session_privilege());
}catch(Exception $e){
	$activities = array();
}

try{
	$pmanager = PictureManager::getInstance();
	$pictures = $pmanager->getLastCommentedPicture($lcp, system_session_privilege());
}catch(Exception $e){
	$pictures = array();
}

try{
	$bmanager = BlogManager::getInstance();
	$posts = $bmanager->getLastListPost($nbrLP);
}catch(Exception $e){
	$posts = array();
}

try{
	$vmanager = VideoManager::getInstance();
	$video = $vmanager->getLastVideo($ytUserId);
}catch(Exception $e){
	$video = new Video();
}


$view->pageHome($slides, $activities, $pictures, $posts, $video);