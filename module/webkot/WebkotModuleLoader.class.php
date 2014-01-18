<?php


class WebkotModuleLoader implements iModuleLoader{

	// FRONTEND
	public static function loadModel($relativePath){
		system_include_file($relativePath . 'model/Webkotteur.class.php');
		system_include_file($relativePath . 'model/WebkotteurManager.class.php');
	}

	public static function loadView($relativePath){
		system_include_file($relativePath . 'view/WebkotView.class.php');
	}

	public static function loadFunctions($relativePath){
		system_include_file($relativePath . 'functions.inc.php');
	}

	public static function getController($relativePath){
		return $relativePath . 'controller/webkot.mod.php';
	}

	public static function loadModule($relativePath){
		WebkotModuleLoader::loadFunctions($relativePath);
		WebkotModuleLoader::loadModel($relativePath);
		WebkotModuleLoader::loadView($relativePath);
		return WebkotModuleLoader::getController($relativePath);
	}

	public static function loadJsCode(iTemplate $template){
	
	}

	// BACKEND
	public static function loadAdminModel($relativePath){
		system_include_file($relativePath . 'model/Webkotteur.class.php');
		system_include_file($relativePath . 'model/WebkotteurManager.class.php');
	}

	public static function loadAdminView($relativePath){
		system_include_file($relativePath . 'view/WebkotAdminView.class.php');
	}

	public static function loadAdminFunctions($relativePath){
		system_include_file($relativePath . 'functions.inc.php');
	}

	public static function getAdminController($relativePath){
		system_include_file($relativePath."controller/WebkotteurController.class.php");
		return $relativePath . 'controller/admin.mod.php';
	}

	public static function loadAdminModule($relativePath){
		WebkotModuleLoader::loadAdminFunctions($relativePath);
		WebkotModuleLoader::loadAdminModel($relativePath);
		WebkotModuleLoader::loadAdminView($relativePath);
		return WebkotModuleLoader::getAdminController($relativePath);
	}
	
		
}