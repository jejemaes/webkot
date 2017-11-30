<?php
/**
 * Maes Jerome
 * init.php, created at Nov 30, 2017
 *
 */

global $Router;

$blog_controller = 'module\webkot\controller\WebkotController';

$Router->addRoute('/webkot', $blog_controller . ':webkotAction', 'GET', 'public', array(), 'webkot_index');
$Router->addRoute('/webkot/teams', $blog_controller . ':webkotTeamsAction', 'GET', 'public', array(), 'webkot_teams_page');
