<?php
/**
 * Maes Jerome
 * Menu.class.php, created at Nov 23, 2015
 *
 */
namespace module\admin\model;

use system\core\BlackModel as BlackModel;


class Menu extends BlackModel {

	static $table_name = 'admin_menu';

	static $attr_accessible = array(
			'id' => array('label'=> 'Id', 'type' => 'integer', 'required' => true),
			'name' => array('label'=> 'Name', 'type' => 'string', 'length' => 128, 'required' => true),
			'url' => array('label'=> 'URL', 'type' => 'string', 'length' => 128, 'required' => true),
			'sequence' => array('label'=> 'Sequence', 'type' => 'integer', 'default' => 10),
			'icon' => array('label' => 'Icon', 'type' => 'string', 'length' => 64, 'default' => 'fa fa-cogs'),
			'parent_id' => array('label'=> 'Parent', 'type' => 'integer'),
	);

	static $belongs_to = array(
			array('parent', 'class_name' => '\module\admin\model\Menu', 'foreign_key' => 'parent_id')
	);

	static $has_many = array(
			array('children_ids', 'class_name' => '\module\admin\model\Menu', 'foreign_key' => 'parent_id')
	);

	public static function get_root_menus(){
		return self::find('all', array('conditions' => "parent_id IS NULL", 'order' => 'sequence ASC'));
	}
	
	public function get_url() {
		$url = $this->read_attribute('url');
		if(!$url){
			return '#';
		}
		return ADMIN_PATH . $this->read_attribute('url');
	}

}