<?php
/**
 * Maes Jerome
 * init.php, created at Sep 28, 2015
 *
 */

global $Router;

$website_controller = 'module\website\controller\WebsiteController';

$Router->addRoute('/', $website_controller . ':indexAction', 'GET', 'public');
$Router->addRoute('/page/:page_slug/', $website_controller . ':pageAction', 'GET', 'public', array('page_slug' => '[a-zA-Z1-9\-._]*'), 'website_page');

// route : module
$Router->addRoute('/module/:module/', $website_controller . ':updateAction', 'GET', 'public', array('module' => '[a-zA-Z1-9\-._]*'));

// route : login
$login_controller = 'module\website\controller\LoginController';
$Router->addRoute('/login', $login_controller . ':loginAction', 'GET|POST', 'public');
$Router->addRoute('/logout', $login_controller . ':logoutAction', 'GET|POST', 'public');

// route : user
$user_controller = 'module\website\controller\UserController';
$Router->addRoute('/user/list(/:page)', $user_controller . ':indexAction', 'GET', 'public', array('page' => '[1-9]*'), 'website_user_list');
$Router->addRoute('/user/:user_id/', $user_controller . ':profileAction', 'GET', 'public', array('user_id' => '[1-9]*'), 'website_user_profile');
$Router->addRoute('/user/register/', $user_controller . ':userSubscriptionAction', 'GET|POST', 'public', array(), 'website_user_register');
