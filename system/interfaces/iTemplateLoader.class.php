<?php
/**
 * Maes Jerome
 * iTemplateLoader.class.php, created at Jan 2, 2015
 *
 */

namespace system\interfaces;


interface iTemplateLoader{
	
	/**
	 * Load the template named 'name', and return its code
	 * @param string $name : the name of the searched template
	 * @return code : the code (content) of the template
	 */
	public function load_template($name);
	
	/**
	 * Store the given template in the database
	 * @param string $name : the name of the template
	 * @param code $content : the content of the template
	 */
	public function add_template($name, $content);
	
	/**
	 * remove (definitively) the given template
	 * @param string $name : the name of the template to remove
	 */
	public function remove_template($name);
	
}