<?php
/**
 * Maes Jerome
 * init.php, created at Nov 20, 2017
 *
 */

global $Router;

// route : user
$user_controller = 'module\user\controller\UserController';
$Router->addRoute('/user/list(/:page)', $user_controller . ':listAction', 'GET', 'public', array('page' => '[1-9]*'), 'website_user_list');
$Router->addRoute('/user/register', $user_controller . ':signupAction', 'GET|POST', 'public', array(), 'website_user_register');
$Router->addRoute('/user/:user_id', $user_controller . ':profileAction', 'GET', 'public', array('user_id' => '[1-9]*'), 'website_user_profile');
