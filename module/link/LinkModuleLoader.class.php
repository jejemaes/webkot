<?php


class LinkModuleLoader implements iModuleLoader{

	// FRONTEND
	public static function loadModel($relativePath){
		system_include_file($relativePath . 'model/Link.class.php');
		system_include_file($relativePath . 'model/LinkCategory.class.php');
		system_include_file($relativePath . 'model/LinkManager.class.php');
	}

	public static function loadView($relativePath){
		system_include_file($relativePath . 'view/LinkView.class.php');
	}

	public static function loadFunctions($relativePath){
		
	}

	public static function getController($relativePath){
		return $relativePath . 'controller/link.mod.php';
	}

	public static function loadModule($relativePath){
		LinkModuleLoader::loadFunctions($relativePath);
		LinkModuleLoader::loadModel($relativePath);
		LinkModuleLoader::loadView($relativePath);
		return LinkModuleLoader::getController($relativePath);
	}

	public static function loadJsCode(iTemplate $template){
	
	}

	// BACKEND
	public static function loadAdminModel($relativePath){
		system_include_file($relativePath . 'model/Link.class.php');
		system_include_file($relativePath . 'model/LinkCategory.class.php');
		system_include_file($relativePath . 'model/LinkManager.class.php');
		system_include_file($relativePath . 'model/LinkCategoryManager.class.php');
	}

	public static function loadAdminView($relativePath){
		system_include_file($relativePath . 'view/LinkAdminView.class.php');
	}

	public static function loadAdminFunctions($relativePath){
		system_include_file($relativePath . 'functions.inc.php');
	}

	public static function getAdminController($relativePath){
		return $relativePath . 'controller/admin.mod.php';
	}

	public static function loadAdminModule($relativePath){
		LinkModuleLoader::loadAdminFunctions($relativePath);
		LinkModuleLoader::loadAdminModel($relativePath);
		LinkModuleLoader::loadAdminView($relativePath);
		return LinkModuleLoader::getAdminController($relativePath);
	}
	
		
}