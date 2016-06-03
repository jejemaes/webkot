<?php
/**
 * Maes Jerome
 * init.php, created at May 27, 2016
 *
 */

define('DIR_SYS_CORE', __SITE_PATH . 'system/core/');
define('DIR_SYS_INCLUDE', __SITE_PATH . 'system/include/');
define('DIR_ADDONS', __SITE_PATH . 'module/');


date_default_timezone_set(TIMEZONE);
error_reporting(E_ALL);
ini_set('display_errors', 'on');


// import the include file to load the system
include DIR_SYS_INCLUDE . 'autoload.inc.php';
include DIR_SYS_INCLUDE . 'loading.inc.php';


use system\core\Environment as Env;
use system\http\Router as Router;

global $env;
$env = Env::get();
$env->register('system\core\BlackView');
$env->register('system\core\IrExternalIdentifier');
$env->register('system\core\IrConfigParameter');
$env->register('system\core\IrModule');

$modules = array('base', 'website');
load_modules($modules);


global $router;
$router = Router::get();
$response = $router->dispatch();

exit();