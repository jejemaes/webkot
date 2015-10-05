<?php
/**
 * Maes Jerome
 * init.php, created at Sep 22, 2015
 *
 */
// define system directory constants
define('_DIR_SYS', __SITE_PATH . 'system/core/');
define('_DIR_EXCEPTION', __SITE_PATH . 'system/exception/');
define('_DIR_INCLUDE', __SITE_PATH . 'system/include/');
define('_DIR_MODULE', __SITE_PATH . 'module/');
define('_DIR_MEDIA', __SITE_PATH . 'media/');

// autoload for classes
include _DIR_INCLUDE . 'autoload.inc.php';

// imports
use system\core\BlackRouter as BlackRouter;
use system\core\BlackApp as BlackApp;
use system\core\Logger as Logger;

use system\core\IrExternalIdentifier as XMLID;

// instanciate the global variable (router, logger, ...)
global $Logger;
$Logger = Logger::getInstance(Logger::LOG_DEBUG);

global $Router;
$Router = BlackRouter::getInstance();

// instanciate the app (helpers)
function App() {
	static $app;
	if($app === null) {
		$db_cfg = array(
			'db_name' => DB_NAME,
			'db_login' => DB_LOGIN,
			'db_pass' => DB_PASS,
			'db_host' => DB_HOST,
			'db_driver' => 'pdo_mysql',
		);
		$app = new BlackApp($db_cfg);
	}
	return $app;
}
$app = App();

include 'module/blog/init.php';
include 'module/website/init.php';

$matched_routes = $Router->hasRoute();
if(count($matched_routes)){
	$Router->dispatch();
	exit;
}

