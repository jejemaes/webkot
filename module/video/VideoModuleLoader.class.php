<?php


class VideoModuleLoader implements iModuleLoader{

	// FRONTEND
	public static function loadModel($relativePath){
		system_include_file($relativePath . 'model/Video.class.php');
		system_include_file($relativePath . 'model/VideoManager.class.php');
	}

	public static function loadView($relativePath){
		system_include_file($relativePath . 'view/VideoView.class.php');
	}

	public static function loadFunctions($relativePath){
		system_include_file($relativePath . "functions.inc.php");
	}

	public static function getController($relativePath){
		return $relativePath . 'controller/video.mod.php';
	}

	public static function loadModule($relativePath){
		VideoModuleLoader::loadFunctions($relativePath);
		VideoModuleLoader::loadModel($relativePath);
		VideoModuleLoader::loadView($relativePath);
		return VideoModuleLoader::getController($relativePath);
	}

	public static function loadJsCode(iTemplate $template){
	
	}
	

	// BACKEND
	public static function loadAdminModel($relativePath){
		system_include_file($relativePath . 'model/Video.class.php');
		system_include_file($relativePath . 'model/VideoManager.class.php');
	}

	public static function loadAdminView($relativePath){
		system_include_file($relativePath . 'view/VideoAdminView.class.php');
	}

	public static function loadAdminFunctions($relativePath){
		system_include_file($relativePath . "functions.inc.php");
	}

	public static function getAdminController($relativePath){
		return $relativePath . 'controller/admin.mod.php';
	}

	public static function loadAdminModule($relativePath){
		VideoModuleLoader::loadAdminFunctions($relativePath);
		VideoModuleLoader::loadAdminModel($relativePath);
		VideoModuleLoader::loadAdminView($relativePath);
		return VideoModuleLoader::getAdminController($relativePath);
	}
	
		
}