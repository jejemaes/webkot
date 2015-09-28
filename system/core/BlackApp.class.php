<?php
/**
 * Maes Jerome
 * BlackApp.class.php, created at Sep 26, 2015
 *
 */
namespace system\core;

class BlackApp {
		
	public function __construct(){
		global $Logger;
		\ActiveRecord\Config::initialize(function($cfg){
			//$cfg->set_model_directory(dirname(__FILE__) . '/models');
			$cfg->set_connections(array('production' => 'mysql://'.DB_LOGIN.':'.DB_PASS.'@'.DB_HOST.'/'.DB_NAME . '?charset=utf8'));
			// you can change the default connection with the below
			$cfg->set_default_connection('production');
			//$cfg->set_model_directory(_DIR_LIB);
		});
		$Logger->info("ActiveRecord Loaded and configurated !");
	}
}