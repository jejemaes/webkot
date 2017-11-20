<?php
namespace module\page\model;

use system\core\BlackModel as BlackModel;


class Page extends BlackModel {
	
	static $name = 'Website Page';
	static $rec_name = 'title';
	static $table_name = 'page';
	
	static $attr_accessible = array(
		'id' => array('label'=> 'Id', 'type' => 'integer', 'required' => true),
		'title' => array('label'=> 'title', 'type' => 'string', 'length' => 32, 'required' => true),
		'content' => array('label'=> 'Content', 'type' => 'html'),
		'isactive' => array('label'=> 'Active', 'type' => 'boolean'),
		'file' => array('label'=> 'DEPRECATED', 'type' => 'string', 'length' => 128)
	);
	
	public static function getListPage(){
		return static::find('all', array('conditions' => array(), 'order' => 'id ASC'));
	}
	
	public static function getPage($slug){
		return static::find('first', array('conditions' => array('id = ?', $slug)));
	}
	
}