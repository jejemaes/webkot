<?php
/**
 * Maes Jerome
 * BlackView.class.php, created at Sep 22, 2015
 *
 */
namespace system\core;
use system\core\BlackModel as BlackModel;
use system\core\IrExternalIdentifier as XMLID;
use \DOMDocument as DOMDocument;

class BlackView extends BlackModel{
	
	static $table_name = 'ir_view';
	
	static $attr_accessible = array(
		'id' => array('label'=> 'Id', 'type' => 'integer', 'required' => true),
		'name' => array('label'=> 'Name', 'type' => 'string', 'length' => 128, 'required' => true),
		'type' => array('label'=> 'Type', 'selection' => array('form' => 'Form', 'tree' => 'List', 'template' => 'Template Qweb', 'bundle' => 'Assets Bundle'), 'default' => 'template', 'required' => true),
		'arch' => array('label'=> 'Architecture', 'type' => 'text'),
		'sequence' => array('label'=> 'Sequence', 'type' => 'integer', 'default' => 10),
		'active' => array('label'=> 'Active', 'type' => 'boolean', 'default' => true),
	);
	
	static $belongs_to = array(
		array('inherit_id', 'class_name' => '\system\core\BlackView', 'foreign_key' => 'id')
	);
	
	static $has_many = array(
		array('inherit_children_ids', 'class_name' => '\system\core\BlackView', 'foreign_key' => 'inherit_id')
	);
	
	
	
	/**
	 * get the inherited views of the given view_id
	 * @param integer $view_id : identifier of the view (master view)
	 * @return Ambigous <\ActiveRecord\mixed, NULL, unknown, \ActiveRecord\Model, multitype:>
	 */
	public function get_inherited_view($view_id){
		return static::find('all', array('conditions' => array('inherit_id = ? AND active = ?', $view_id, '1'), 'order' => 'sequence DESC'));
	}
	
	public static function get_view($xml_id){
		return self::apply_inheritance_arch($xml_id);
	}

	/**
	 * get the arch field of the given view xmlid, after the inheritances were applied.
	 * It return a string (code) of thebase view extended by its children
	 * @param string $xml_id : the base xmlid
	 * @return string : the inherited view arch
	 */
	public static function apply_inheritance_arch($xml_id){
		$base_view = XMLID::xml_id_to_object($xml_id);
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
		return $base_arch_dom->saveXML();
	}

}
