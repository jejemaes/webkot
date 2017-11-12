<?php
/**
 * Maes Jerome
 * IrModel.class.php, created at Nov 12, 2017
 *
 */
namespace system\base;
use system\core\BlackModel;


class IrModel extends BlackModel{

	static $table_name = 'ir_model';

	public $id;
	public $name;
	public $table_name;
	public $class_name;

	public static function get_model($model_slug){
		$row = \DB::queryFirstRow("SELECT * FROM " . self::$table_name . " WHERE table_name = %s LIMIT 1", $model_slug);
		$model = new IrModel($row);
		if(!$model){
			throw new \Exception(sprintf('No model %s found', $model_slug));
		}
		return $model->class_name;
	}

}