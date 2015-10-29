<?php
/**
 * Maes Jerome
 * init.php, created at Sep 22, 2015
 *
 */

global $Router;

$blog_controller = 'module\blog\controller\BlogController';

$Router->addRoute('/blog/', $blog_controller . ':indexAction', 'GET', 'public');
$Router->addRoute('/blog/post/:post_id/', $blog_controller . ':blogPostAction', 'GET', 'public', array('post_id' => '[1-9]*'), 'blog_post_page');
$Router->addRoute('/blog/comment/:post_id/', $blog_controller . ':CommentAction', 'POST', 'user', array('post_id' => '[1-9]*'), 'blog_post_comment');

