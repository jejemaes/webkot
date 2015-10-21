<?php
/**
 * Maes Jerome
 * iSession.class.php, created at Oct 22, 2015
 *
 */

namespace system\interfaces;

interface iSession{
	
	public static function getInstance();
	
	public static function id($new = false);
	
	public static function destroy();
	
	public function alive();

	public function user();

}