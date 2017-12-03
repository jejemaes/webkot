<?php
/**
 * Maes Jerome
 * IrModel.class.php, created at Sep 26, 2015
 *
 */
namespace system\core;
use system\core\BlackModel;

class IrModel extends BlackModel{

	static $name = "Model";
	static $table_name = 'ir_model';

	static $attr_accessible = array(
			'id' => array('label'=> 'Id', 'type' => 'integer', 'required' => true),
			'name' => array('label'=> 'Name', 'type' => 'string', 'length' => 128),
			'table_name' => array('label'=> 'Table name', 'type' => 'string', 'length' => 128, 'required' => true),
			'class_name' => array('label'=> 'PHP Class name', 'type' => 'string', 'length' => 128, 'required' => true),
	);

	public static function get_model($model_slug){
		$model = static::find('first', array('conditions' => array('table_name = ?', $model_slug)));
		if(!$model){
			throw new \Exception(sprintf('No model %s found', $model_slug));
		}
		return $model->class_name;
	}
	
	public static function get_ir_model($model_slug, $raise_if_not_found=False){
		$model = static::find('first', array('conditions' => array('table_name = ?', $model_slug)));
		if(!$model){
			if($raise_if_not_found){
				throw new \Exception(sprintf('No model %s found', $model_slug));
			}
			return NULL;
		}
		return $model;
	}
	
	/**
	 * Register the model in ir_model table (mapping class and table name)
	 * @param string $classname: namespaced php class name
	 * @return IrModel instance (matching the given classname)
	 */
	public static function register_model($classname){
		$model = NULL;
		if(property_exists($classname, 'table_name')){
			$table = $classname::$table_name;
			if($table){ // BlackModel does not have $table_name filled 
				$model = self::get_ir_model($table);
				if($model){ // update it
					$model->write(array(
							'name' => $classname::$name,
							'class_name' => $classname,
					));
				}else{ // create it
					$model = self::create(array(
							'name' => $classname::$name,
							'class_name' => $classname,
							'table_name' => $table,
					));
				}
			}
		}
		return $model;
	}
	
	/**
	 * Register the model of given directory
	 * @param string $directory: absolute path of directory to register
	 */
	public static function register_model_directory($directory, $namespace){
		if(!is_dir($directory)){
			return False;
		}
		if ($handle = opendir($directory)) {
			while (false !== ($entry = readdir($handle))) {
				if (!in_array($entry, array('.', '..'))) {
					$file = $directory . $entry;
					if(substr($file, -10) === '.class.php'){
						$file_parts = explode(DIRECTORY_SEPARATOR, $file);
						$filename = end($file_parts);
						$classname = str_replace('.class.php', "", $filename);
						$complete_classname = $namespace . $classname;
						IrModel::register_model($complete_classname);
					}
				}
			}
			closedir($handle);
		}
	}

}
