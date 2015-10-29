<?php
/**
 * Maes Jerome
 * init.php, created at Sep 28, 2015
 *
 */

global $Router;

$website_controller = 'module\website\controller\WebsiteController';

$Router->addRoute('/', $website_controller . ':indexAction', 'GET', 'public');
$Router->addRoute('/page/:page_slug/', $website_controller . ':pageAction', 'GET', 'public', array('page_slug' => '[a-zA-Z1-9\-._]*'));

// route : module
$Router->addRoute('/module/:module/', $website_controller . ':updateAction', 'GET', 'public', array('module' => '[a-zA-Z1-9\-._]*'));

// route : login
$login_controller = 'module\website\controller\LoginController';
$Router->addRoute('/login', $login_controller . ':loginAction', 'GET|POST', 'public');


// Hooks
$Router->addHook('\module\website\model\Website:hook_prerender');
