<?php
/**
 * Maes Jerome
 * BlagTagRel.class.php, created at Dec 6, 2015
 *
 */
namespace module\blog\model;

use system\core\BlackModel as BlackModel;


class BlogTagRel extends BlackModel {

	static $name = 'Blog Tag Relation';
	static $rec_name = 'id';
	static $table_name = 'blog_tag_rel';

	static $attr_accessible = array(
			'id' => array('label'=> 'Id', 'type' => 'integer', 'required' => true),
			'blog_post_id' => array('label'=> 'Blog Post', 'type' => 'integer', 'required' => true),
			'blog_tag_id' => array('label'=> 'Blog Tag', 'type' => 'integer', 'required' => true),
	);

	static $belongs_to = array(
			array('post', 'class_name' => '\module\blog\model\BlogPost', 'foreign_key' => 'blog_post_id'),
			array('tag', 'class_name' => '\module\blog\model\BlogTag', 'foreign_key' => 'blog_tag_id')
	);

}
