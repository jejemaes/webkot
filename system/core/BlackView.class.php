<?php
/**
 * Maes Jerome
 * BlackView.class.php, created at Nov 12, 2017
 *
 */
namespace system\core;
use system\core\BlackModel as BlackModel;
use system\core\IrExternalIdentifier as XMLID;
use \DOMDocument as DOMDocument;

use system\lib\qweb\QWebEngine;
use system\lib\ViewBuilder\FormView as FormView;
use system\lib\ViewBuilder\TreeView as TreeView;

class BlackView extends BlackModel{

	static $table_name = 'ir_view';

	public $id;
	public $name;
	public $type;
	public $arch;
	public $sequence;
	public $active;
	
	/**
	 * get the inherited views of the given view_id
	 * @param integer $view_id : identifier of the view (master view)
	*/
	public static function get_inherited_view($view_id){
		$results = array();
		$rows = \DB::query("SELECT * FROM " . self::$table_name . " WHERE inherit_id = %i AND active = %s ORDER BY sequence DESC", $view_id, '1');
		foreach ($rows as $row) {
			array_push($results, new BlackView($row));
		}
		return $results;
	}

	public static function get_view($xml_id){
		$base_view = XMLID::xml_id_to_object($xml_id);
		return self::apply_inheritance_arch($base_view);
	}

	/**
	 * get the arch field of the given view xmlid, after the inheritances were applied.
	 * It return a string (code) of thebase view extended by its children
	 * @param BlackView $base_view : the base view object
	 * @return string : the inherited view arch
	 */
	public static function apply_inheritance_arch($base_view, $saveXML=True){
		$inherited_views = $base_view->get_inherited_view($base_view->id);

		$base_arch_dom = new DOMDocument();
		$base_arch_dom->loadXML($base_view->arch, LIBXML_NOWARNING);

		foreach ($inherited_views as $view){
			$view_arch_dom = new DOMDocument;
			$view_arch_dom->loadXML($view->arch, LIBXML_NOWARNING);

			$elements = $view_arch_dom->getElementsByTagName("xpath");
			foreach ($elements as $xpath_element) {
				$query = $xpath_element->getAttribute('expr');
				$position = $xpath_element->getAttribute('position') ? $xpath_element->getAttribute('position') : 'inside';
					
				if($query){
					$xpath = new DOMXPath($base_arch_dom);
					$result = $xpath->query($query);

					if($result){
						// import nodes (child of xpath tags) from inherited view to base DOM
						$result = $result->item(0); //TODO only take the first ?
						$nodes = array();
						foreach($xpath_element->childNodes as $child){
							$nodes[] = $base_arch_dom->importNode($child, true);
						}
						// place correctly the new nodes into the base architecture DOM
						if($position == 'inside'){
							foreach ($nodes as $n) {
								$result->appendChild($n);
							}
						}
						if($position == 'replace'){
							foreach ($nodes as $n) {
								$result->parentNode->insertBefore($n, $result);
							}
							$result->parentNode->removeChild($result);
						}
						if($position == 'before'){
							foreach ($nodes as $n) {
								$result->parentNode->insertBefore($n, $result);
							}
						}
						if($position == 'after'){
							// TODO
						}
					}else{
						// no elem found, wrong xpath expr
					}
				}else{
					// TODO : not tag expr
				}
			}
		}
		//echo htmlspecialchars($base_arch_dom->saveXML());
		//var_dump($base_arch_dom);
		if($saveXML){
			return $base_arch_dom->saveXML();
		}
		return $base_arch_dom;
	}


	/**
	 * get list of field contained in the view
	 * @param DOMDocument $view_doc : document representing the complete view to sextract field list
	 * @return array : list of field name
	 */
	public static function view_get_fields($view_doc){
		$fields = array();
		$elements = $view_doc->getElementsByTagName("field");
		foreach ($elements as $field_tag) {
			$field_name = $field_tag->getAttribute('name');
			array_push($fields, $field_name);
		}
		return $fields;
	}


}
