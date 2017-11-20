<?php
/**
 * Maes Jerome
 * init.php, created at Nov 20, 2017
 *
 */

global $Router;

// route : page frontend
$page_controller = 'module\page\controller\PageController';
$Router->addRoute('/page/list', $page_controller . ':pagelistAction', 'GET', 'public', array(), 'page_list');
$Router->addRoute('/page/:slug', $page_controller . ':pageAction', 'GET', 'public', array('slug' => '[a-z]*'), 'page_page');
