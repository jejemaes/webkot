<?php
/**
* Maes Jerome
* BlackView.class.php, created at Sep 22, 2015
*
*/
namespace system\core;

use system\core\IrExternalIdentifier as XMLID;
use \system\qweb\QWebEngine as QWebEngine;

class BlackView extends BlackModel{
	
	static $table_name = 'ir_view';
	
	public $id;
	public $name;
	public $type;
	public $arch;
	public $sequence;
	public $active;

	public static function render($xml_id, $data=array(), $engine=null, $loader=false){
		if(! $engine){
			$engine = \system\qweb\QWebEngine::getEngine();
		}
		if(! $loader){
			$loader = 'system\qweb\QWebLoader';
		}
		return $engine->render($xml_id, $data, $loader);
	}
	
	/**
	 * Get the view arch
	 * @param string $xml_id
	 * @return string : the xml code of the template
	 */
	public static function get_template_arch($xml_id){
		$id = XMLID::xml_id_to_id($xml_id);
		return self::browse($id)->arch;
	}
}
