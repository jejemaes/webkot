<?php


class ChallengeModuleLoader implements iModuleLoader{

	// FRONTEND
	public static function loadModel($relativePath){
		system_include_file($relativePath . 'model/Challenge.class.php');
		system_include_file($relativePath . 'model/ChallengeAnswer.class.php');
		system_include_file($relativePath . 'model/ChallengeManager.class.php');
	}

	public static function loadView($relativePath){
		system_include_file($relativePath . 'view/ChallengeView.class.php');
	}

	public static function loadFunctions($relativePath){
		system_include_file($relativePath . 'functions.inc.php');
	}

	public static function getController($relativePath){
		return $relativePath . 'controller/challenge.mod.php';
	}

	public static function loadModule($relativePath){
		ChallengeModuleLoader::loadFunctions($relativePath);
		ChallengeModuleLoader::loadModel($relativePath);
		ChallengeModuleLoader::loadView($relativePath);
		return ChallengeModuleLoader::getController($relativePath);
	}
	
	public static function loadJsCode(iTemplate $template){
	
	}


	// BACKEND
	public static function loadAdminModel($relativePath){
		system_include_file($relativePath . 'model/Challenge.class.php');
		system_include_file($relativePath . 'model/ChallengeAnswer.class.php');
		system_include_file($relativePath . 'model/ChallengeManager.class.php');
	}

	public static function loadAdminView($relativePath){
		system_include_file($relativePath . 'view/ChallengeAdminView.class.php');
	}

	public static function loadAdminFunctions($relativePath){
		system_include_file($relativePath . 'functions.inc.php');
	}

	public static function getAdminController($relativePath){
		return $relativePath . 'controller/admin.mod.php';
	}

	public static function loadAdminModule($relativePath){
		ChallengeModuleLoader::loadAdminFunctions($relativePath);
		ChallengeModuleLoader::loadAdminModel($relativePath);
		ChallengeModuleLoader::loadAdminView($relativePath);
		return ChallengeModuleLoader::getAdminController($relativePath);
	}
	
		
}