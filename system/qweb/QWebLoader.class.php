<?php
/**
 * Maes Jerome
 * IrQwebLoader.class.php, created at May 11, 2015
 *
 */
namespace system\qweb;
use system\interfaces\iTemplateLoader;
use system\core\BlackView as BlackView;


class QWebLoader implements iTemplateLoader{
	
	/**
	 * Load the template named 'name', and return its code
	 * @param string $name : the name of the searched template
	 * @return code : the code (content) of the template
	 */
	public static function load_template($name){
		return BlackView::get_template_arch($name);
	}
	
	/**
	 * Store the given template in the database
	 * @param string $name : the name of the template
	 * @param code $content : the content of the template
	*/
	public static function add_template($name, $content){
		
	}
	
	/**
	 * remove (definitively) the given template
	 * @param string $name : the name of the template to remove
	*/
	public static function remove_template($name){
		
	}
	
}