<?php
/**
 * Maes Jerome
 * init.php, created at Nov 12, 2017
 *
 */

// define system directory constants
define('_DIR_SYS', __SITE_PATH . 'system/core/');
define('_DIR_EXCEPTION', __SITE_PATH . 'system/exception/');
define('_DIR_INCLUDE', __SITE_PATH . 'system/include/');
define('_DIR_TOOLS', __SITE_PATH . 'system/tools/');
define('_DIR_MODULE', __SITE_PATH . 'module/');
define('_DIR_MEDIA', __SITE_PATH . 'media/');

// autoload for classes
include _DIR_TOOLS . 'autoload.inc.php';
