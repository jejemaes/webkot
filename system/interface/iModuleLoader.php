<?php



interface iModuleLoader{

	// FRONTEND
	public static function loadModel($relativePath);

	public static function loadView($relativePath);

	public static function loadFunctions($relativePath);
	
	public static function getController($relativePath);

	public static function loadModule($relativePath);
	
	public static function loadJsCode(iTemplate $template, $relativePath);


	// BACKEND
	public static function loadAdminModel($relativePath);

	public static function loadAdminView($relativePath);

	public static function loadAdminFunctions($relativePath);

	public static function getAdminController($relativePath);
	
	public static function loadAdminModule($relativePath);
	
	public static function loadAdminJsCode(iAdminTemplate $template, $relativePath);
	
	
	// SERVER
	public static function loadServerModel($relativePath);
	
	public static function loadServerFunctions($relativePath);
	
	public static function getServerController($relativePath);
	
	public static function loadServerModule($relativePath);
}