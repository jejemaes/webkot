<?php
/**
 * Maes Jerome
 * init.php, created at Sep 28, 2015
 *
 */

global $Router;

$website_controller = 'module\website\controller\WebsiteController';
$Router->addRoute('/', $website_controller . ':indexAction', 'GET', 'public');

// route : login
$login_controller = 'module\website\controller\LoginController';
$Router->addRoute('/login', $login_controller . ':loginAction', 'GET|POST', 'public');
$Router->addRoute('/logout', $login_controller . ':logoutAction', 'GET|POST', 'public');
