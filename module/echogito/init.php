<?php
/**
 * Maes Jerome
 * init.php, created at Dec 1, 2017
 *
 */
global $Router;

// route : link frontend
$controller = 'module\echogito\controller\EventController';
$Router->addRoute('/echogito', $controller . ':pageEchogitoAction', 'GET', 'public', array(), 'page_echogito');
