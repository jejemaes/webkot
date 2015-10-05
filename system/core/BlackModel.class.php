<?php
/**
 * Maes Jerome
 * BlackModel.class.php, created at Sep 24, 2015
 *
 */

namespace system\core;

class BlackModel extends \ActiveRecord\Model{
	
	public $env;
	
	static $_order = 'id DESC';
	
	
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
	
	
	public static function search($domain, $limit=0, $order=0, $offset=0){
		self::_check_access_right();
		// if not order, set static::$_order
		$param = array(
			'conditions' => $domain,
		);
		return self::find('all', $params);
	}
	
	
	
	
	
	public static function _check_access_right(){
		// throw an AccessError if required
		// TODO : how to get session_user_id since static method
	}
	
	// Patch
	public function set_attributes(array $attributes){
		foreach($attributes as $name => $value){
			$this->assign_attribute($name, $value);
		}
	}
	
}