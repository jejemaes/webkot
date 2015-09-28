<?php
/**
 * Maes Jerome
 * Logger.class.php, created at Sep 22, 2015
 *
 */
namespace system\core;

class Logger{

	const LOG_DEBUG = 0;
	const LOG_INFO = 1;
	const LOG_WARNING = 2;
	const LOG_ERROR = 3;
	const LOG_NONE = 4;

	protected static $_instance;

	private $mode;

	public static function getInstance($mode){
		if (!isset(self::$_instance)) {
			$c = __CLASS__;
			self::$_instance = new $c;
			self::$_instance->__construct($mode);
		}
		return self::$_instance;
	}


	public function __construct($mode = self::LOG_NONE){
		$this->$mode = $mode;
	}

	/**
	 * log the infos
	 * @param string $m : the message to log
	 */
	public function info($m){
		if($this->mode == self::LOG_INFO || $this->mode == self::LOG_DEBUG){
			$this->_print('info', $m);
		}
	}

	/**
	 * log the warning
	 * @param string $m : the message to log
	 */
	public function warn($m){
		if($this->mode == self::LOG_WARNING || $this->mode == self::LOG_DEBUG){
			$this->_print('warning', $m);
		}
	}


	/**
	 * log the error
	 * @param string $m : the message to log
	 */
	public function error($m){
		if($this->mode == self::LOG_ERROR || $this->mode == self::LOG_DEBUG){
			$this->_print('error', $m);
		}
	}

	/**
	 * log the debug
	 * @param string $m : the message to log
	 */
	public function debug($m){
		if($this->mode == self::LOG_DEBUG){
			$this->_print('debug', $m);
		}
	}

	/**
	 * print the logged notification
	 * @param string $mode
	 * @param string $m : the message to log
	 */
	public function _print($mode, $m){
		//echo '<br>'.date('Y-m-d h:i:s').' -- [' . ucfirst($mode) . '] : ' . $m . '<br>';
	}
}
