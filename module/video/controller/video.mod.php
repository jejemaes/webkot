<?php


$view = new VideoView($template,$module);

if(isset($_GET['id']) && !empty($_GET['id'])){
	$vmanager = VideoManager::getInstance();
	$video = $vmanager->getVideo($_GET['id']);
	
	$view->pageVideo($video);
}else{
	// options
	$omanager::getInstance();
	$ytUserId = $omanager->getOption("video-youtube-userid");
	
	// pagination
	$desc = system_get_desc_pagination();
	$page = (system_get_page_pagination()-1);
	$limit = ($page*$desc);
	
	// video list
	$vmanager = VideoManager::getInstance();
	$count = $vmanager->getCountVideos($ytUserId);	
	$videos = $vmanager->getSelectionList($ytUserId,$limit, $desc);
	
	$view->pageList($videos, $count, ($page+1), $desc);
}
