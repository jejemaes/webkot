<?php
/**
 * Maes Jerome
 * IrExternalIdentifier.class.php, created at May 27, 2016
 *
 */
namespace system\core;


class IrExternalIdentifier extends BlackModel{
	
	static $table_name = 'ir_external_identifier';
	
	public $id;
	public $xml_id;
	public $res_model;
	public $res_id;
	
	
	public static function ref($xml_id){
		$domain = array('_where' => 'xml_id = :xml_id', 'xml_id' => $xml_id);
		$result = static::search($domain);
		if($result){
			$result = $result[0];
			$model = self::env($result->res_model);
			return $model::browse($result->id);
		}
		return null;
	}
	
	public static function xml_id_to_id($xml_id){
		$domain = array('_where' => 'xml_id = :xml_id', 'xml_id' => $xml_id);
		$result = static::search($domain);
		if($result){
			return $result[0]->id;
		}
		return false;
	}
	
}
