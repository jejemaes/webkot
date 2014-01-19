<?php


class AccueilModuleLoader implements iModuleLoader{
	
	public static $modulesToLoadFrontend = array('activity','blog','video');
	public static $modulesToLoadBackend = array('activity','webkot');
	
	// FRONTEND
	public static function loadModel($relativePath){
		//load the others module
		$modulesToLoad = self::$modulesToLoadFrontend;
		for($i=0 ; $i<count($modulesToLoad) ; $i++){
			$name = $modulesToLoad[$i];
			system_load_partial_module_frontend($name);
		}
		system_include_file($relativePath . 'model/Slide.class.php');
		system_include_file($relativePath . 'model/SlideManager.class.php');
	}

	public static function loadView($relativePath){
		system_include_file($relativePath . 'view/AccueilView.class.php');
		
	}

	public static function loadFunctions($relativePath){
		system_include_file($relativePath . 'functions.inc.php');
	}

	public static function getController($relativePath){
		return $relativePath . 'controller/accueil.mod.php';
	}

	public static function loadModule($relativePath){
		AccueilModuleLoader::loadFunctions($relativePath);
		AccueilModuleLoader::loadModel($relativePath);
		AccueilModuleLoader::loadView($relativePath);
		return AccueilModuleLoader::getController($relativePath);
	}
	
	public static function loadJsCode(iTemplate $template, $relativePath){
		$modulesToLoad = self::$modulesToLoadFrontend;
		for($i=0 ; $i<count($modulesToLoad) ; $i++){
			$name = $modulesToLoad[$i];
			system_load_partial_module_frontend($name, $template);
		}
	}


	// BACKEND
	public static function loadAdminModel($relativePath){
		$modulesToLoad = self::$modulesToLoadBackend;
		for($i=0 ; $i<count($modulesToLoad) ; $i++){
			$name = $modulesToLoad[$i];
			system_load_module_model($name);
		}
		system_include_file($relativePath . 'model/Todo.class.php');
		system_include_file($relativePath . 'model/TodoManager.class.php');
		system_include_file($relativePath . 'model/Slide.class.php');
		system_include_file($relativePath . 'model/SlideManager.class.php');
	}

	public static function loadAdminView($relativePath){
		system_include_file($relativePath . 'view/DashboardView.class.php');
	}

	public static function loadAdminFunctions($relativePath){
		system_include_file($relativePath . 'functions.inc.php');
	}

	public static function getAdminController($relativePath){
		return $relativePath . 'controller/admin.mod.php';
	}

	public static function loadAdminModule($relativePath){
		AccueilModuleLoader::loadAdminFunctions($relativePath);
		AccueilModuleLoader::loadAdminModel($relativePath);
		AccueilModuleLoader::loadAdminView($relativePath);
		return AccueilModuleLoader::getAdminController($relativePath);
	}
	
	public static function loadAdminJsCode(iAdminTemplate $template, $relativePath){
	
	}
	
	// SERVER
	public static function loadServerModel($relativePath){
		
	}
	
	public static function loadServerFunctions($relativePath){
		
	}
	
	public static function getServerController($relativePath){
		
	}
	
	public static function loadServerModule($relativePath){
		
	}
	
		
}