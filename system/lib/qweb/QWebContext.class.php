<?php
/**
 * Maes Jerome
 * QWebContext.class.php, created at Jan 7, 2015
 *
 */
namespace system\lib\qweb;

class QWebContext extends \ArrayObject{

	public function __construct($data=array(), $templates=array()){
		parent::__construct(array(), \ArrayObject::ARRAY_AS_PROPS);
		
		$this['data'] = $data;
		$this['template'] = $templates;
		
		$this['__caller__'] = false;
		$this['__stack__'] = array();
	}

	/*
	 function __clone(){
	//$this->_loader = clone $this->_loader;
	$this->_templates = clone $this->_templates;
	$this->_data = clone $this->_data;
	$this->_context = clone $this->_context;
	}
	*/

}
