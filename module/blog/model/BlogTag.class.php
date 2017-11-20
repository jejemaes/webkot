<?php
/**
 * Maes Jerome
 * BlogTags.class.php, created at Dec 6, 2015
 *
 */
namespace module\blog\model;

use system\core\BlackModel as BlackModel;


class BlogTag extends BlackModel {
	
	static $name = 'Blog Tag';
	static $rec_name = 'name';
	static $table_name = 'blog_tag';
	
	static $attr_accessible = array(
			'id' => array('label'=> 'Id', 'type' => 'integer', 'required' => true),
			'name' => array('label'=> 'Name', 'type' => 'string', 'length' => 128, 'required' => true),
	);
	
	static $has_many = array(
			array('tags_rel', 'class_name' => '\module\blog\model\BlogTagRel', 'foreign_key' => 'blog_tag_id'),
			array('blog_posts', 'through' => 'tags_rel', 'class_name' => '\module\blog\model\BlogPost')
	);
}
