<?php
/**
 * Maes Jerome
 * IrExternalIdentifier.class.php, created at Sep 25, 2015
 *
 */
namespace system\core;
use system\core\BlackModel;
use system\core\IrModel as IrModel;

class IrExternalIdentifier extends BlackModel{
	
	static $name = "External Identifier";
	static $table_name = 'ir_external_identifier';
	
	static $attr_accessible = array(
		'id' => array('label'=> 'Id', 'type' => 'integer', 'required' => true),
		'xml_id' => array('label'=> 'XML ID', 'type' => 'string', 'length' => 128, 'required' => true),
		'res_model' => array('label'=> 'Ressource Model (table name)', 'type' => 'string', 'length' => 128),
		'res_id' => array('label'=> 'Ressource ID', 'type' => 'integer'),
	);
	
	
	public static function xml_id_to_object($xml_id, $raise=false) {
		$external_id = static::find('first', array('conditions' => array('xml_id = ?', $xml_id)));
		if(!$external_id){
			if($raise){
				throw new \Exception('xml_id_to_object : xml_id not found');
			}else{
				return NULL;
			}
		}
		$model = IrModel::find('first', array('conditions' => "table_name = '".$external_id->res_model."'"));
		$class_name = $model->class_name;
		$object = $class_name::find($external_id->res_id);
		return $object;
	}
	
}