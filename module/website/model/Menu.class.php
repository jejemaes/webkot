<?php
/**
 * Maes Jerome
 * Menu.class.php, created at May 28, 2016
 *
 */
namespace module\website\model;

use system\core\BlackModel as BlackModel;


class Menu extends BlackModel {

	static $table_name = 'website_menu';
	
	public $id;
	public $name;
	public $url;
	public $sequence;
	public $parent_id; 

	public static function get_root_menus(){
		return self::search(array('_where' => "parent_id IS NULL"),  'sequence ASC');
	}
	
}