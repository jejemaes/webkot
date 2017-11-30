<?php
/**
 * Maes Jerome
 * BlogPost.class.php, created at Oct 7, 2015
 *
 */
namespace module\blog\model;

use system\core\BlackModel as BlackModel;


class BlogPost extends BlackModel {
	
	static $name = 'Blog Post';
	static $rec_name = 'title';
	static $table_name = 'blog_post';
	
	static $attr_accessible = array(
			'id' => array('label'=> 'Id', 'type' => 'integer', 'required' => true),
			'title' => array('label'=> 'Title', 'type' => 'string', 'length' => 128, 'required' => true),
			'content' => array('label'=> 'Content', 'type' => 'text'),
			'date' => array('label'=> 'Date', 'type' => 'datetime'),
			'published' => array('label' => 'Published', 'type' => 'boolean'),
			'userid' => array('label'=> 'Author', 'type' => 'integer', 'required' => true),
	);
	
	static $belongs_to = array(
			array('user', 'class_name' => '\module\user\model\User', 'foreign_key' => 'userid')
	);
	
	static $has_many = array(
			array('comments', 'class_name' => '\module\blog\model\BlogComment', 'foreign_key' => 'postid'),
			array('tags_rel', 'class_name' => '\module\blog\model\BlogTagRel', 'foreign_key' => 'blog_post_id'),
			array('tags', 'through' => 'tags_rel', 'class_name' => '\module\blog\model\BlogTag')
	);
	
	
	public static function get_published_post($limit = 15, $offset = 0){
		return self::find('all', array('conditions' => array('published = ?', '1'), 'order' => 'date ASC', 'limit' => $limit, 'offset' => $offset));
	}
	
	public static function get_post($post_id){
		return self::find('first',  array('conditions' => array('id = ?', $post_id)));
	}
	
	public function get_short_content(){
		$content = $this->read_attribute('content');
		if(strlen($content) >= 200){
			return substr($content, 0, 200) . '...';
		}
		return $content;
	}
}
