<?php
/**
 * Maes Jerome
 * website.class.php, created at Sep 28, 2015
 *
 */
namespace module\website\model;

use \system\core\BlackModel as BlackModel;
use \system\core\IrConfigParameter as IrConfig;
use \system\http\Session as Session;
use \module\website\model\Menu as Menu;

use \system\exceptions\MissingException as MissingException;

class Website {

	protected static $_instance;
	
	public static function getInstance(){
		if (!isset(self::$_instance)) {
			$c = __CLASS__;
			self::$_instance = new $c;
		}
		return self::$_instance;
	}
	
	private function __construct(){
		
	}
	
	public function get($key, $default){
		return IrConfig::get_param('website.'.$key, $default);
	}
	
	public function __get($key){
		$value = IrConfig::get_param('website.'.$key);
		if($value){
			return $value;
		}
		throw new MissingException(sprintf("Website doesn't have attribute %s", $key));
	}

}