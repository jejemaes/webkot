<?php
/**
 * Maes Jerome
 * iTemplateEngine.class.php, created at Jan 2, 2015
 *
 */

namespace system\interfaces;

interface iTemplateEngine{
	
	/**
	 * get the instance of the engine (singleton)
	 * @param \Closure $loader : the function to load the given template
	 */
	public static function getEngine($loader);
	
	
	/**
	 * 
	 * @param unknown $id_or_xml_id
	 * @param array $qwebcontext
	 * @param string $loader
	 */
	public function render($id_or_xml_id, $qwebcontext, $loader=NULL);
	
}