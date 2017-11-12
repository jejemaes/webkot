<?php
/**
 * Maes Jerome
 * IrExternalIdentifier.class.php, created at Nov 12, 2017
 *
 */
namespace system\core;
use system\core\BlackModel;
use system\base\IrModel as IrModel;


class IrExternalIdentifier extends BlackModel{

	static $table_name = 'ir_external_identifier';
	
	public $id;
	public $xmlid;
	public $module;
	public $res_model;
	public $res_id;
	
	public static function xml_id_to_object($xml_id, $raise=false) {
		$row = \DB::queryFirstRow("SELECT id, xmlid, res_model, res_id FROM " . self::$table_name . " WHERE xmlid = %s LIMIT 1", $xml_id);
		if(!$row){
			if($raise){
				throw new \Exception('xml_id_to_object : xml_id not found');
			}else{
				return NULL;
			}
		}
		$model = IrModel::get_model($row['res_model']);
		$class_name = $model->class_name;
		$object = $class_name::browse($external_id->res_id);
		return $object;
	}

}