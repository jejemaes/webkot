<?php
/**
 * Maes Jerome
 * Page.class.php, created at Sep 28, 2015
 *
 */
namespace module\website\model;

use system\core\BlackModel as BlackModel;


class Page extends BlackModel {

	static $table_name = 'website_page';
	
	static $attr_accessible = array(
			'id' => array('label'=> 'Id', 'type' => 'integer', 'required' => true),
			'name' => array('label'=> 'Name', 'type' => 'string', 'length' => 128),
			'slug' => array('label'=> 'Slug', 'type' => 'string', 'length' => 128, 'required' => true),
			'content' => array('label'=> 'Content', 'type' => 'text'),
			'active' => array('label' => 'Active', 'type' => 'boolean', 'default' => False)
	);
	
	
	public static function get_page($slug){
		return self::find('first', array('conditions' => array('slug = ?', $slug)));
	}
	
}