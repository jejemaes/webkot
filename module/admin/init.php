<?php
/**
 * Maes Jerome
 * init.php, created at Nov 13, 2015
 *
 */

global $Router;

$controller = 'module\admin\controller\AdminController';

$Router->addAdminRoute('/', $controller . ':indexAction', 'GET', 'user');
$Router->addAdminRoute('/:model/create/', $controller . ':createAction', 'GET|POST', 'user', array('model' => '[a-zA-Z1-9_]*'));
$Router->addAdminRoute('/:model/edit/:id', $controller . ':editAction', 'GET|POST', 'user', array('model' => '[a-zA-Z1-9_]*', 'id' => '[1-9]*'), 'admin_view_form_edit');
$Router->addAdminRoute('/:model/delete/:id', $controller . ':deleteAction', 'GET|POST', 'user', array('model' => '[a-zA-Z1-9_]*', 'id' => '[1-9]*'));
$Router->addAdminRoute('/:model/list(/page/:page)', $controller . ':listAction', 'GET', 'user', array('model' => '[a-zA-Z1-9_]*', 'page' => '[1-9]*'), 'admin_view_tree');


// routes : module
$controller = 'module\admin\controller\ModuleController';
$Router->addAdminRoute('/module/:module/', $controller . ':updateAction', 'GET', 'user', array('module' => '[a-zA-Z1-9\-._]*'));
$Router->addAdminRoute('/system/update/', $controller . ':updateSystemAction', 'GET', 'user', array());
