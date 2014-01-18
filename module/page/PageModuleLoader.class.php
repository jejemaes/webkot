<?php


class PageModuleLoader implements iModuleLoader{

	// FRONTEND
	public static function loadModel($relativePath){
		system_include_file($relativePath . 'model/Page.class.php');
		system_include_file($relativePath . 'model/PageManager.class.php');
	}

	public static function loadView($relativePath){
		system_include_file($relativePath . 'view/PageView.class.php');
	}

	public static function loadFunctions($relativePath){
		//system_include_file($relativePath . 'functions.inc.php');
	}

	public static function getController($relativePath){
		return $relativePath . 'controller/page.mod.php';
	}

	public static function loadModule($relativePath){
		PageModuleLoader::loadFunctions($relativePath);
		PageModuleLoader::loadModel($relativePath);
		PageModuleLoader::loadView($relativePath);
		return PageModuleLoader::getController($relativePath);
	}
	
	public static function loadJsCode(iTemplate $template){
	
	}


	// BACKEND
	public static function loadAdminModel($relativePath){
		system_include_file($relativePath . 'model/Page.class.php');
		system_include_file($relativePath . 'model/PageManager.class.php');
	}

	public static function loadAdminView($relativePath){
		system_include_file($relativePath . 'view/PageAdminView.class.php');
	}

	public static function loadAdminFunctions($relativePath){
		//system_include_file($relativePath . 'functions.inc.php');
	}

	public static function getAdminController($relativePath){
		return $relativePath . 'controller/admin.mod.php';
	}

	public static function loadAdminModule($relativePath){
		PageModuleLoader::loadAdminFunctions($relativePath);
		PageModuleLoader::loadAdminModel($relativePath);
		PageModuleLoader::loadAdminView($relativePath);
		return PageModuleLoader::getAdminController($relativePath);
	}
	
		
}