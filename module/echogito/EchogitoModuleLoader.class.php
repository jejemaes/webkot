<?php

define('ECHOGITO_JS_ACTIVE',true);
define('ECHOGITO_JS_CLASS_CALL_ANCHOR', 'echogito-js-anchor');
define('ECHOGITO_ACTIVE', false);

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
		$template->addJSHeader('<script type="text/javascript" src="'.$relativePath.'view/js/frontend.js"></script>');
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
		$template->addJSFooter('<script type="text/javascript" src="'.$relativePath.'view/js/backend.js"></script>');
	}
	
	// SERVER
	public static function loadServerModel($relativePath){
		system_include_file($relativePath . 'model/EventCategory.class.php');
		system_include_file($relativePath . 'model/EventCategoryManager.class.php');
		system_include_file($relativePath . 'model/Event.class.php');
		system_include_file($relativePath . 'model/EventManager.class.php');
	}
	
	public static function loadServerFunctions($relativePath){
		system_include_file($relativePath . "functions.inc.php");
	}
	
	public static function getServerController($relativePath){
		system_include_file($relativePath . 'controller/EventController.class.php');
		system_include_file($relativePath . 'controller/EventCategoryController.class.php');
		return $relativePath . 'controller/server.inc.php';
	}
	
	public static function loadServerModule($relativePath){
		EchogitoModuleLoader::loadServerFunctions($relativePath);
		EchogitoModuleLoader::loadServerModel($relativePath);
		return EchogitoModuleLoader::getServerController($relativePath);
	}
	
		
}