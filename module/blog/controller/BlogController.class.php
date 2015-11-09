<?php
/**
 * Maes Jerome
 * BlogController.class.php, created at Sep 22, 2015
 *
 */
namespace module\blog\controller;

use system\core\BlackController as BlackController;
use module\website\controller\WebsiteController as WebsiteController;
use module\blog\model\BlogPost as BlogPost;
use module\blog\model\BlogComment as Comment;

class BlogController extends WebsiteController{

	public function blogListAction(){
		$blogs = BlogPost::get_published_post();
		return $this->render('blog.index', array(
				'blog_posts' => $blogs,
				'website_title' => 'Blog',
		));
	}

	public function blogPostAction($post_id){
		$post = BlogPost::get_post($post_id);
		return $this->render('blog.post_page', array(
				'post' => $post,
				'website_title' => 'Blog',
		));
	}
	
	public function blogCommentAction($post_id){
		$comment = Comment::create(array(
			'user_id' => $this->session()->uid,
			'post_id' => $post_id,
			'comment' => $this->request()->params('comment'),
		));
		$comment->save();
		
		$url = sprintf('/blog/post/%d', $post_id);
		return $this->redirect($url);
	}

}