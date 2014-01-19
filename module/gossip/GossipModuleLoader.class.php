<?php


class GossipModuleLoader implements iModuleLoader{

	// FRONTEND
	public static function loadModel($relativePath){
		system_include_file($relativePath . 'model/Gossip.class.php');
		system_include_file($relativePath . 'model/GossipManager.class.php');
	}

	public static function loadView($relativePath){
		system_include_file($relativePath . 'view/GossipView.class.php');
	}

	public static function loadFunctions($relativePath){
		system_include_file($relativePath . 'functions.inc.php');
	}

	public static function getController($relativePath){
		system_include_file($relativePath . 'controller/GossipController.class.php');
		return $relativePath . 'controller/frontend.inc.php';
	}

	public static function loadModule($relativePath){
		GossipModuleLoader::loadFunctions($relativePath);
		GossipModuleLoader::loadModel($relativePath);
		GossipModuleLoader::loadView($relativePath);
		return GossipModuleLoader::getController($relativePath);
	}

	public static function loadJsCode(iTemplate $template, $relativePath){

	}


	// BACKEND
	public static function loadAdminModel($relativePath){
		system_include_file($relativePath . 'model/Gossip.class.php');
		system_include_file($relativePath . 'model/GossipManager.class.php');
	}

	public static function loadAdminView($relativePath){
		system_include_file($relativePath . 'view/GossipAdminView.class.php');
	}

	public static function loadAdminFunctions($relativePath){
		system_include_file($relativePath . 'functions.inc.php');
	}

	public static function getAdminController($relativePath){
		system_include_file($relativePath . 'controller/GossipController.class.php');
		return $relativePath . 'controller/backend.inc.php';
	}

	public static function loadAdminModule($relativePath){
		GossipModuleLoader::loadAdminFunctions($relativePath);
		GossipModuleLoader::loadAdminModel($relativePath);
		GossipModuleLoader::loadAdminView($relativePath);
		return GossipModuleLoader::getAdminController($relativePath);
	}

	public static function loadAdminJsCode(iAdminTemplate $template, $relativePath){
	
	}
	
	// SERVER
	public static function loadServerModel($relativePath){
		system_include_file($relativePath . 'model/Gossip.class.php');
		system_include_file($relativePath . 'model/GossipManager.class.php');
	}
	
	public static function loadServerFunctions($relativePath){
		system_include_file($relativePath . 'functions.inc.php');
	}
	
	public static function getServerController($relativePath){
		system_include_file($relativePath . 'controller/GossipController.class.php');
		return $relativePath . 'controller/server.inc.php';
	}
	
	public static function loadServerModule($relativePath){
		GossipModuleLoader::loadServerFunctions($relativePath);
		GossipModuleLoader::loadServerModel($relativePath);
		return GossipModuleLoader::getServerController($relativePath);
	}
	

}