<?php



interface iModuleLoader{

	// FRONTEND
	public static function loadModel($relativePath);

	public static function loadView($relativePath);

	public static function loadFunctions($relativePath);
	
	public static function getController($relativePath);

	public static function loadModule($relativePath);
	
	public static function loadJsCode(iTemplate $template);


	// BACKEND
	public static function loadAdminModel($relativePath);

	public static function loadAdminView($relativePath);

	public static function loadAdminFunctions($relativePath);

	public static function getAdminController($relativePath);
	
	public static function loadAdminModule($relativePath);
}