<?php


class SystemModuleLoader implements iModuleLoader{

	// FRONTEND
	public static function loadModel($relativePath){
		
	}

	public static function loadView($relativePath){
		
	}

	public static function loadFunctions($relativePath){
		
	}

	public static function getController($relativePath){
		
	}

	public static function loadModule($relativePath){
		SystemModuleLoader::loadFunctions($relativePath);
		SystemModuleLoader::loadModel($relativePath);
		SystemModuleLoader::loadView($relativePath);
		return SystemModuleLoader::getController($relativePath);
	}

	public static function loadJsCode(iTemplate $template){
	
	}

	// BACKEND
	public static function loadAdminModel($relativePath){
		system_include_file($relativePath . 'model/Media.class.php');
		system_include_file($relativePath . 'model/MediaCategory.class.php');
		system_include_file($relativePath . 'model/MediaManager.class.php');
	}

	public static function loadAdminView($relativePath){
		system_include_file($relativePath . 'view/MediaAdminView.class.php');
		system_include_file($relativePath . 'view/WidgetAdminView.class.php');
	}

	public static function loadAdminFunctions($relativePath){
		system_include_file($relativePath . 'functions.inc.php');
	}

	public static function getAdminController($relativePath){
		return $relativePath . 'controller/admin.mod.php';
	}

	public static function loadAdminModule($relativePath){
		SystemModuleLoader::loadAdminFunctions($relativePath);
		SystemModuleLoader::loadAdminModel($relativePath);
		SystemModuleLoader::loadAdminView($relativePath);
		return SystemModuleLoader::getAdminController($relativePath);
	}
	
		
}