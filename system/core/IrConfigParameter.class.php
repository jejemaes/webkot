<?php
/**
 * Maes Jerome
 * IrConfigParameter.class.php, created at May 28, 2016
 *
 */
namespace system\core;
use system\core\BlackModel;

class IrConfigParameter extends BlackModel{

	static $table_name = 'ir_config_parameter';
	
	public $id;
	public $name;
	public $value;
	public $description;
	public $category;

	public static function get_param($key, $default=NULL){
		$param = static::search(array('_where' => 'name = :name', 'name' => $key));
		if($param){
			return $param[0]->value;
		}
		return $default;
	}
}