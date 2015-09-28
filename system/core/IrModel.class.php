<?php
/**
 * Maes Jerome
 * IrModel.class.php, created at Sep 26, 2015
 *
 */
namespace system\core;
use system\core\BlackModel;

class IrModel extends BlackModel{

	static $table_name = 'ir_model';

	static $attr_accessible = array(
			'id' => array('label'=> 'Id', 'type' => 'integer', 'required' => true),
			'name' => array('label'=> 'Name', 'type' => 'string', 'length' => 128),
			'table_name' => array('label'=> 'Table name', 'type' => 'string', 'length' => 128, 'required' => true),
			'class_name' => array('label'=> 'PHP Class name', 'type' => 'string', 'length' => 128, 'required' => true),
	);

}
