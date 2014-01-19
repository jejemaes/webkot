<?php


class BlogModuleLoader implements iModuleLoader{

	// FRONTEND
	public static function loadModel($relativePath){
		system_include_file($relativePath . 'model/BlogComment.class.php');
		system_include_file($relativePath . 'model/BlogPost.class.php');
		system_include_file($relativePath . 'model/BlogManager.class.php');
	}

	public static function loadView($relativePath){
		system_include_file($relativePath . 'view/BlogView.class.php');
	}

	public static function loadFunctions($relativePath){
		
	}

	public static function getController($relativePath){
		return $relativePath . 'controller/frontend.inc.php';
	}

	public static function loadModule($relativePath){
		BlogModuleLoader::loadFunctions($relativePath);
		BlogModuleLoader::loadModel($relativePath);
		BlogModuleLoader::loadView($relativePath);
		return BlogModuleLoader::getController($relativePath);
	}

	public static function loadJsCode(iTemplate $template, $relativePath){
	
	}
	

	// BACKEND
	public static function loadAdminModel($relativePath){
		system_include_file($relativePath . 'model/BlogComment.class.php');
		system_include_file($relativePath . 'model/BlogPost.class.php');
		system_include_file($relativePath . 'model/BlogManager.class.php');
	}

	public static function loadAdminView($relativePath){
		system_include_file($relativePath . 'view/BlogAdminView.class.php');
	}

	public static function loadAdminFunctions($relativePath){
		system_include_file($relativePath . "functions.inc.php");
	}

	public static function getAdminController($relativePath){
		return $relativePath . 'controller/backend.inc.php';
	}

	public static function loadAdminModule($relativePath){
		BlogModuleLoader::loadAdminFunctions($relativePath);
		BlogModuleLoader::loadAdminModel($relativePath);
		BlogModuleLoader::loadAdminView($relativePath);
		return BlogModuleLoader::getAdminController($relativePath);
	}
	
	public static function loadAdminJsCode(iAdminTemplate $template, $relativePath){
	
	}
	
	
	// SERVER
	public static function loadServerModel($relativePath){
		system_include_file($relativePath . 'model/BlogComment.class.php');
		system_include_file($relativePath . 'model/BlogPost.class.php');
		system_include_file($relativePath . 'model/BlogManager.class.php');
	}
	
	public static function loadServerFunctions($relativePath){
		system_include_file($relativePath . "functions.inc.php");
	}
	
	public static function getServerController($relativePath){
		return $relativePath . 'controller/server.inc.php';
	}
	
	public static function loadServerModule($relativePath){
		BlogModuleLoader::loadServerFunctions($relativePath);
		BlogModuleLoader::loadServerModel($relativePath);
		return BlogModuleLoader::getServerController($relativePath);
	}
	
		
}