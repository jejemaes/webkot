<?php
/**
 * Maes Jerome
 * IrConfigParameter.class.php, created at Oct 31, 2015
 *
 */
namespace system\core;
use system\core\BlackModel;

class IrConfigParameter extends BlackModel{
	
	static $name = "Configuration Parameter";
	static $table_name = 'ir_config_parameter';
	
	static $attr_accessible = array(
			'id' => array('label'=> 'Id', 'type' => 'integer', 'required' => true),
			'name' => array('label'=> 'Name', 'type' => 'string', 'length' => 64, 'required' => true),
			'value' => array('label'=> 'Value', 'type' => 'text', 'required' => true),
			'description' => array('label'=> 'Description', 'type' => 'text'),
			'category' => array('label'=> 'Category', 'type' => 'string', 'length' => 64),
	);
	
	
	public static function get_param($key, $default=NULL){
		$param = static::first(array('conditions' => array('name = ?', $key)));
		if($param){
			return $param->value;
		}
		return $default;
	}
}