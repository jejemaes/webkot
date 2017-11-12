<?php
/**
 * Maes Jerome
 * Model.class.php, created at Nov 12, 2017
 *
 */

namespace system\core;


// TODO : we will need to use sql builder
// https://github.com/nilportugues/php-sql-query-builder/commits/master
// https://github.com/envms/fluentpdo

class BlackModel {
	
	static $table_name = 'uknown';
	
	/**
	 * Constructor
	 * @param array $data : the data from the file, in a key-array
	 */
	public function __construct(array $donnees = array()){
		$this->pictures = array();
		$this->authors = array();
		$this->hydrate($donnees);
	}
	
	/**
	 * Hydrate : fill the field with the array
	 * @param array $data : the data from the file, in a key-array
	 */
	private function hydrate(array $donnees){
		foreach ($donnees as $key => $value){
			$method = 'set'.ucfirst($key);
			if (method_exists($this, $method)){
				$this->$method($value);
			}
		}
	}
	
	public static function browse($id){
		$row = \DB::queryFirstRow("SELECT * FROM " . self::$table_name . " WHERE id = %s LIMIT 1", $id);
		$klass = get_called_class();
		return new $klass($row);
	}
	
	public static function create(array $values){
		\DB::insert(self::$table_name, $values);
	}
	
	public static function read($ids, array $fields){
		if(!is_array($ids)){
			$ids = array($ids);
		}
		// TODO: check field list
		return \DB::query("SELECT * FROM ' . self::$table_name . ' WHERE id IN %li", $ids);
	}
	
}
