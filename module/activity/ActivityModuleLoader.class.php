<?php

define('ACTIVITY_PUBLISHING_LOG', DIR_TMP . "activity-publishing-log.log");
define('ACTIVITY_PUBLISHING_FILE', DIR_TMP . "activity-publishing.json");
define('ACTIVITY_PUBLISHING_FILE_BACKUP', DIR_TMP . "activity-publishing-backup.json");

define('ACTIVITY_JS_CLASS_CALL_ANCHOR','activity-js-anchor');
define('ACTIVITY_DIR_ARCHIVE2002','archive2002/');


define('ACTIVITY_FONT_PATH', (dirname(__FILE__)."/fonts/Harabara.ttf"));


class ActivityModuleLoader implements iModuleLoader{
	
	// FRONTEND
	public static function loadModel($relativePath){
		system_include_file($relativePath . 'model/ImgUtils.class.php');
		system_include_file($relativePath . 'model/Activity.class.php');
		system_include_file($relativePath . 'model/ActivityManager.class.php');	
		system_include_file($relativePath . 'model/AbstractPicture.class.php');
		system_include_file($relativePath . 'model/Picture.class.php');
		system_include_file($relativePath . 'model/PictureManager.class.php');
		system_include_file($relativePath . 'model/MyPicture.class.php');
		system_include_file($relativePath . 'model/MyPictureManager.class.php');
		system_include_file($relativePath . 'model/Comment.class.php');
		system_include_file($relativePath . 'model/CommentManager.class.php');
		system_include_file($relativePath . 'model/Censure.class.php');
		system_include_file($relativePath . 'model/CensureManager.class.php');
	}
	
	public static function loadView($relativePath){
		system_include_file($relativePath . 'view/ActivityView.class.php');
	}
	
	public static function loadFunctions($relativePath){
		system_include_file($relativePath . 'functions.inc.php');
	}
	
	public static function getController($relativePath){
		system_include_file($relativePath . 'controller/ActivityController.class.php');
		system_include_file($relativePath . 'controller/PictureController.class.php');
		return $relativePath . 'controller/frontend.inc.php';
	}
	
	public static function loadModule($relativePath){
		ActivityModuleLoader::loadFunctions($relativePath);
		ActivityModuleLoader::loadModel($relativePath);
		ActivityModuleLoader::loadView($relativePath);
		return ActivityModuleLoader::getController($relativePath);
	}
	
	public static function loadJsCode(iTemplate $template, $relativePath){
		$template->addJSFooter('<script type="text/javascript" src="'.$relativePath.'view/js/script.js"></script>');
	}
	
	
	// BACKEND
	public static function loadAdminModel($relativePath){
		system_include_file($relativePath . 'model/ImgUtils.class.php');
		system_include_file($relativePath . 'model/Activity.class.php');
		system_include_file($relativePath . 'model/ActivityManager.class.php');
		system_include_file($relativePath . 'model/AbstractPicture.class.php');
		system_include_file($relativePath . 'model/Picture.class.php');
		system_include_file($relativePath . 'model/PictureManager.class.php');
		system_include_file($relativePath . 'model/MyPicture.class.php');
		system_include_file($relativePath . 'model/MyPictureManager.class.php');
		system_include_file($relativePath . 'model/Comment.class.php');
		system_include_file($relativePath . 'model/CommentManager.class.php');

		system_include_file($relativePath . 'model/Censure.class.php');
		system_include_file($relativePath . 'model/CensureManager.class.php');
		
		system_include_file($relativePath . 'model/Publisher.class.php');
		
		system_include_file($relativePath . 'model/StatActivity.class.php');
		system_include_file($relativePath . 'model/StatUser.class.php');
		system_include_file($relativePath . 'model/StatManager.class.php');
	}
	
	public static function loadAdminView($relativePath){
		system_include_file($relativePath . 'view/ActivityAdminView.class.php');
	}
	
	public static function loadAdminFunctions($relativePath){
		system_include_file($relativePath . 'functions.inc.php');
	}
	
	public static function getAdminController($relativePath){
		system_include_file($relativePath . 'controller/ActivityAdminController.class.php');
		system_include_file($relativePath . 'controller/PictureAdminController.class.php');
		return $relativePath . 'controller/backend.inc.php';
	}
	
	public static function loadAdminModule($relativePath){
		ActivityModuleLoader::loadAdminFunctions($relativePath);
		ActivityModuleLoader::loadAdminModel($relativePath);
		ActivityModuleLoader::loadAdminView($relativePath);
		return ActivityModuleLoader::getAdminController($relativePath);
	}
	
	
	public static function loadAdminJsCode(iAdminTemplate $template, $relativePath){
	
	}
	
	// SERVER
	public static function loadServerModel($relativePath){
		system_include_file($relativePath . 'model/ImgUtils.class.php');
		system_include_file($relativePath . 'model/Activity.class.php');
		system_include_file($relativePath . 'model/ActivityManager.class.php');
		system_include_file($relativePath . 'model/AbstractPicture.class.php');
		system_include_file($relativePath . 'model/Picture.class.php');
		system_include_file($relativePath . 'model/PictureManager.class.php');
		system_include_file($relativePath . 'model/MyPicture.class.php');
		system_include_file($relativePath . 'model/MyPictureManager.class.php');
		system_include_file($relativePath . 'model/Comment.class.php');
		system_include_file($relativePath . 'model/CommentManager.class.php');
		
		system_include_file($relativePath . 'model/Censure.class.php');
		system_include_file($relativePath . 'model/CensureManager.class.php');
		
		// don't load this here because it exstends a class loaded after --> fatal error php
		//system_include_file($relativePath . 'model/PictureUploadHandler.class.php');
		
		system_include_file($relativePath . 'model/StatActivity.class.php');
		system_include_file($relativePath . 'model/StatUser.class.php');
		system_include_file($relativePath . 'model/StatManager.class.php');
	}
	
	public static function loadServerFunctions($relativePath){
		system_include_file($relativePath . 'functions.inc.php');
	}
	
	public static function getServerController($relativePath){
		system_include_file($relativePath . 'controller/ActivityController.class.php');
		system_include_file($relativePath . 'controller/PictureController.class.php');
		return $relativePath . 'controller/server.inc.php';
	}
	
	public static function loadServerModule($relativePath){
		ActivityModuleLoader::loadServerFunctions($relativePath);
		ActivityModuleLoader::loadServerModel($relativePath);
		return ActivityModuleLoader::getServerController($relativePath);
	}
	
	
}