<?php


class EchogitoModuleLoader implements iModuleLoader{

	// FRONTEND
	public static function loadModel($relativePath){
		system_include_file($relativePath . 'model/EventCategory.class.php');
		system_include_file($relativePath . 'model/EventCategoryManager.class.php');
		system_include_file($relativePath . 'model/Event.class.php');
		system_include_file($relativePath . 'model/EventManager.class.php');
	}

	public static function loadView($relativePath){
		system_include_file($relativePath . 'view/EventView.class.php');
	}

	public static function loadFunctions($relativePath){
		system_include_file($relativePath . 'functions.inc.php');
	}

	public static function getController($relativePath){
		system_include_file($relativePath . 'controller/EventController.class.php');
		system_include_file($relativePath . 'controller/EventCategoryController.class.php');
		return $relativePath . 'controller/frontend.inc.php';
	}

	public static function loadModule($relativePath){
		EchogitoModuleLoader::loadFunctions($relativePath);
		EchogitoModuleLoader::loadModel($relativePath);
		EchogitoModuleLoader::loadView($relativePath);
		return EchogitoModuleLoader::getController($relativePath);
	}

	public static function loadJsCode(iTemplate $template, $relativePath){
	
	}
	

	// BACKEND
	public static function loadAdminModel($relativePath){
		system_include_file($relativePath . 'model/EventCategory.class.php');
		system_include_file($relativePath . 'model/EventCategoryManager.class.php');
		system_include_file($relativePath . 'model/Event.class.php');
		system_include_file($relativePath . 'model/EventManager.class.php');
	}

	public static function loadAdminView($relativePath){
		system_include_file($relativePath . 'view/EventAdminView.class.php');
	}

	public static function loadAdminFunctions($relativePath){
		system_include_file($relativePath . "functions.inc.php");
	}

	public static function getAdminController($relativePath){
		system_include_file($relativePath . 'controller/EventController.class.php');
		system_include_file($relativePath . 'controller/EventCategoryController.class.php');
		return $relativePath . 'controller/backend.inc.php';
	}

	public static function loadAdminModule($relativePath){
		EchogitoModuleLoader::loadAdminFunctions($relativePath);
		EchogitoModuleLoader::loadAdminModel($relativePath);
		EchogitoModuleLoader::loadAdminView($relativePath);
		return EchogitoModuleLoader::getAdminController($relativePath);
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