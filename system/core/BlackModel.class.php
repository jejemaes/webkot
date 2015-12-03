<?php
/**
 * Maes Jerome
 * BlackModel.class.php, created at Sep 24, 2015
 *
 */

namespace system\core;

class BlackModel extends \ActiveRecord\Model{
	
	public $env;
	
	static $name = 'Black Model';
	static $rec_name = False;
	static $_order = 'id DESC';
	
	
	//--------------------------------
	// CRUD and other base methods
	//--------------------------------
	
	public static function create($attributes, $validate=true) {
		//TODO 
		self::_check_access_right();
		$class_name = get_called_class();
		$record = new $class_name($attributes);
		foreach($attributes as $name => $value){
			$record->assign_attribute($name, $value);
		}
		$record->save($validate);
		//TODO : generate the env
		return $record;
	}
	
	public static function read($ids, array $fields){
		array_push($fields, 'id'); // force id to be in the fields
		if(!is_array($ids)){
			$ids = array($ids);
		}
		$table = static::table();
		$attributes = static::$attr_accessible;
		
		$records = self::find('all', array('conditions' => array(sprintf('id IN (%s)', join(',', $ids)))));
		//var_dump(sprintf('id IN (%s)', join(',', $ids)));
		$result = array();
		foreach($records as $r){
			$val = array();
			foreach ($fields as $field) {
				// scalar field
				if(array_key_exists($field, $attributes)){
					$field_val = $r->$field;
					if($field_val instanceof \ActiveRecord\DateTime){
						$field_type = $attributes[$field]['type'];
						if($field_type == 'date'){
							$field_val = $field_val->format('Y-m-d');
						}
						if($field_type == 'datetime'){
							$field_val = $field_val->format('Y-m-d H:i:s');
						}
					}
					$val[$field] = $field_val;
				}else{ // relational field
					$relation = $table->get_relationship($field);
					$class_name = $relation->class_name;
					
					if($relation->is_poly()){
						
					}else{
						$names = $class_name::name_get(array($r->$field->id));
						$val[$field] = array($r->$field->id, $names[$r->$field->id]);
					}
				}
			}
			array_push($result, $val);
		}
		return $result;
	}
	
	
	public static function search($domain, $limit=0, $order=0, $offset=0){
		self::_check_access_right();
		// TODO if not order, set static::$_order
		$param = array(
			'conditions' => $domain,
		);
		return static::find('all', $params);
	}
	
	
	
	
	//--------------------------------
	// Access
	//--------------------------------
	
	public static function _check_access_right(){
		// throw an AccessError if required
		// TODO : how to get session_user_id since static method
	}
	
	//--------------------------------
	// Patch
	//--------------------------------
	
	public function set_attributes(array $attributes){
		foreach($attributes as $name => $value){
			$this->assign_attribute($name, $value);
		}
	}
	
	
	//--------------------------------
	// Methods based on Name
	//--------------------------------
	
	public static function name_get($ids){
		if(!is_array($ids)){
			$ids = array($ids);
		}
		$rec_name = static::$rec_name;
		$result = static::find_by_sql(sprintf("SELECT id, %s FROM %s WHERE id IN (%s)", $rec_name, static::$table_name, join(',', $ids)));
		$names = array();
		foreach ($result as $r){
			$names[$r->id] = $r->$rec_name;
		}
		return $names;
	}
	
	public static function name_search($name, $additional_domain = False){
		$rec_name = static::$rec_name;
		$domain = array($rec_name . " LIKE ?", '%'.$name.'%');
		if($additional_domain){
			$condition = $additional_domain . ' AND ' . $domain[0];
			$dom = $additional_domain;
			$dom[0] = $condition;
			array_push($dom, $domain[1]);
			$domain = $dom;
		}
		$params = array(
			'conditions' => $domain,
			'order' => $rec_name . ' ASC',
		);
		$records = static::find('all', $params);
		$result = array();
		foreach ($records as $r){
			array_push($result, array($r->id, $r->$rec_name));
		}
		return $result;
	}
	
	//--------------------------------
	// Fields and views querying
	//--------------------------------
	
	/**
	 * returns a key array with field properties
	 * @param array $field_names : list of the fields to get properties from
	 * @return key-array : field declaration as value, the key is the field name.
	 */
	public static function fields_view_get($field_names=array()){
		$fields = array();
		$table = static::table();
		$attributes = static::$attr_accessible;
		foreach ($field_names as $name) {
			if(array_key_exists($name, $attributes)){
				$field = $attributes[$name];
				$fields[$name] = $field;
			}else{
				//TODO check the relational field !!!!
				$relation = $table->get_relationship($name);
				$class_name = $relation->class_name;
				
				if($relation->is_poly()){ // hasmany/hasmanythrough/...
					// TODO : make it for x2many relationship !!
				}else{ // belongs to
					$fk = $relation->foreign_key[0];
					$label = $class_name::$name;
					// get the label from the column (as integer fields of the FK)
					if(array_key_exists($fk, $attributes)){
						$pk_field = $attributes[$fk];
						if(array_key_exists('label', $pk_field)){
							$label = $pk_field['label'];
						}
					}
					$fields[$name] = array(
						'type' => 'many2one',
						'label' => $label,
						'class_name' => $class_name,
						'foreign_key' => $relation->foreign_key,
						'model' => $class_name::$table_name
					);
				}
			}
		}
		return $fields;
	}
	
}