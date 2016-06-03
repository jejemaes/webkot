<?php
/**
 * Maes Jerome
 * QWebContext.class.php, created at Jan 7, 2015
 *
 */
namespace system\qweb;

class QWebContext extends \ArrayObject{

	public function __construct($data=array(), $templates=array()){
		parent::__construct(array(), \ArrayObject::ARRAY_AS_PROPS);
		
		$this['data'] = $data;
		$this['template'] = $templates;
		
		$this['__caller__'] = false;
		$this['__stack__'] = array();
	}
	
}
