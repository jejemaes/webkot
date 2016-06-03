<?php
/**
 * Maes Jerome
 * Environment.class.php, created at May 27, 2016
 *
 */
namespace system\core;



class Environment extends \ArrayObject {
	
	protected static $_instance;
	
	public static function get(){
		if (!isset(self::$_instance)) {
			$c = __CLASS__;
			self::$_instance = new $c;
			self::$_instance->__construct();
		}
		return self::$_instance;
	}
	
	public function __construct($input = array(), $flags = 0, $iterator_class = 'ArrayIterator') {
		parent::__construct($input, $flags, $iterator_class);
	}
	
	/**
	 * Register a model
	 * @param string $class_name
	 */
	public function register($class_name){
		$model_name = $class_name::$table_name;
		$this[$model_name] = $class_name;
	}
	
	public function __toString(){
		return 'ENVIRONMENT=' . json_encode($this);
	}
}
