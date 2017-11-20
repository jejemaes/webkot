<?php
/**
 * Maes Jerome
 * init.php, created at Nov 30, 2015
 *
 */

global $Router;

$controller = 'module\web\controller\WebController';

$Router->addRoute('/web/name_search/:model', $controller . ':nameSearchAction', 'GET', 'user', array());
