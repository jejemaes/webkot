<?php


class UserModuleLoader implements iModuleLoader{

	// FRONTEND
	public static function loadModel($relativePath){
		
	}

	public static function loadView($relativePath){
		system_include_file($relativePath . 'view/UserView.class.php');
	}

	public static function loadFunctions($relativePath){
		system_include_file($relativePath . 'functions.inc.php');
	}

	public static function getController($relativePath){
		system_include_file($relativePath . 'controller/UserController.class.php');
		return $relativePath . 'controller/frontend.inc.php';
	}

	public static function loadModule($relativePath){
		UserModuleLoader::loadFunctions($relativePath);
		UserModuleLoader::loadModel($relativePath);
		UserModuleLoader::loadView($relativePath);
		return UserModuleLoader::getController($relativePath);
	}

	public static function loadJsCode(iTemplate $template, $relativePath){
	
	}

	// BACKEND
	public static function loadAdminModel($relativePath){
		
	}

	public static function loadAdminView($relativePath){
		system_include_file($relativePath . 'view/UserAdminView.class.php');
	}

	public static function loadAdminFunctions($relativePath){
		system_include_file($relativePath . 'functions.inc.php');
	}

	public static function getAdminController($relativePath){
		system_include_file($relativePath . 'controller/UserController.class.php');
		return $relativePath . 'controller/backend.inc.php';
	}

	public static function loadAdminModule($relativePath){
		UserModuleLoader::loadAdminFunctions($relativePath);
		UserModuleLoader::loadAdminModel($relativePath);
		UserModuleLoader::loadAdminView($relativePath);
		return UserModuleLoader::getAdminController($relativePath);
	}
	
	public static function loadAdminJsCode(iAdminTemplate $template, $relativePath){
	
	}
	
	
	// SERVER
	public static function loadServerModel($relativePath){
	
	}
	
	public static function loadServerFunctions($relativePath){
		system_include_file($relativePath . 'functions.inc.php');
	}
	
	public static function getServerController($relativePath){
		system_include_file($relativePath . 'controller/UserController.class.php');
		return $relativePath . 'controller/server.inc.php';
	}
	
	public static function loadServerModule($relativePath){
		UserModuleLoader::loadServerFunctions($relativePath);
		UserModuleLoader::loadServerModel($relativePath);
		return UserModuleLoader::getServerController($relativePath);
	}
	
		
}