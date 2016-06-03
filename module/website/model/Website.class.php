<?php
/**
 * Maes Jerome
 * Website.class.php, created at May 28, 2016
 *
 */
namespace module\website\model;
use system\core\IrConfigParameter as IrConfig;
use system\exception\MissingException as MissingException;


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