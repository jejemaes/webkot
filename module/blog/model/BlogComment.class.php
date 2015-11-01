<?php
/**
 * Maes Jerome
 * BlogComment.class.php, created at Oct 7, 2015
 *
 */
namespace module\blog\model;

use system\core\BlackModel as BlackModel;


class BlogComment extends BlackModel{
	
	static $table_name = 'blog_comment';
	
	static $attr_accessible = array(
			'id' => array('label'=> 'Id', 'type' => 'integer', 'required' => true),
			'comment' => array('label'=> 'Address', 'type' => 'string', 'length' => 128),
			'create_date' => array('label'=> 'Create Date', 'type' => 'datetime')
	);
	
	static $belongs_to = array(
			array('user', 'class_name' => '\system\res\ResUser', 'foreign_key' => 'user_id'),
			array('post', 'class_name' => '\module\blog\model\BlogPost', 'foreign_key' => 'post_id')
	);

}
?>