<?php
/**
 * Maes Jerome
 * iModuleInit.php, created at May 27, 2016
 *
 */
namespace system\interfaces;


interface iModuleLoader{
	
	public static function load_models();
	
	public static function load_routes();
	
	public static function install();
	
	public static function uninstall();
	
}
