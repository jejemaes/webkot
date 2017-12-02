<?php
/**
 * Maes Jerome
 * SQLException.class.php, created at Dec 2, 2017
 *
 */
namespace system\exception;


class SQLException extends \Exception{

	private $_sql;
	private $_decription;
	 
	function __construct($message, $code = "", $query = null, $description = null){
		parent::__construct($message, $code);
		if ($query != null)
			$this->_sql = $query;

		if($description != null){
			$this->_decription = $description;
		}
	}

	function getQuery(){
		return $this->_sql;
	}

	function getDescription(){
		return $this->_decription;
	}
	 
	/* Convert the Exception to String */
	public function debug() {
		$html .= '<strong>SQL Exception</strong>';
		$html .= '<br>DATE : ' . date('Y-m-d H:i');
		$html .= '<br>DESCRIPTION : ' . $this->getDescription();
		if(system_session_privilege() >= 5){
			$html .= '<br>MESSAGE : ' . $this->getMessage();
			$html .= '<br>CODE : ' . $this->getCode();
			$html .= '<br>SQL : ' . $this->getQuery();
			$html .= '<br>FILE : ' . $this->getFile();
			$html .= '<br>LINE : ' . $this->getLine();
			$html .= '<br>TRACE : ' . $this->getTraceAsString();
		}
		return $html;
	}
}
?>