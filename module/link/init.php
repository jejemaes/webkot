<?php
/**
 * Maes Jerome
 * init.php, created at Dec 1, 2017
 *
 */

global $Router;

// route : link frontend
$page_controller = 'module\link\controller\LinkController';
$Router->addRoute('/links', $page_controller . ':pageLinksAction', 'GET', 'public', array(), 'page_link');
