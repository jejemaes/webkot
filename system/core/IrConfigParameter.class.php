<?php
/**
 * Maes Jerome
 * IrConfigParameter.class.php, created at Nov 12, 2017
 *
 */
namespace system\core;
use system\core\BlackModel;


class IrConfigParameter extends BlackModel{

	static $table_name = 'ir_config_parameter';

	public $id;
	public $name;
	public $value;
	public $category;

	public static function get_param($key, $default=NULL){
		$row = \DB::queryFirstRow("SELECT id, value FROM " . self::$table_name . " WHERE name = %s LIMIT 1", $key);
		if($row){
			return $row['value'];
		}
		return $default;
	}
}