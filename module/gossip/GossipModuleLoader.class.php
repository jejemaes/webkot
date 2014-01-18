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
		return $relativePath . 'controller/gossip.mod.php';
	}

	public static function loadModule($relativePath){
		GossipModuleLoader::loadFunctions($relativePath);
		GossipModuleLoader::loadModel($relativePath);
		GossipModuleLoader::loadView($relativePath);
		return GossipModuleLoader::getController($relativePath);
	}

	public static function loadJsCode(iTemplate $template){

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
		return $relativePath . 'controller/admin.mod.php';
	}

	public static function loadAdminModule($relativePath){
		GossipModuleLoader::loadAdminFunctions($relativePath);
		GossipModuleLoader::loadAdminModel($relativePath);
		GossipModuleLoader::loadAdminView($relativePath);
		return GossipModuleLoader::getAdminController($relativePath);
	}


}