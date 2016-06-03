<?php
/**
 * Maes Jerome
 * Page.class.php, created at May 28, 2016
 *
 */
namespace module\website\model;

use system\core\BlackModel as BlackModel;


class Page extends BlackModel {

	static $table_name = 'website_page';
	
	public $id;
	public $name;
	public $slug;
	public $content;
	public $active;
	

	public static function get_page($slug){
		return self::search(array('_where' => 'slug = :slug', 'slug' => $slug));
	}

}