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
use \DOMXPath as DOMXPath;
use system\exception\NullObjectException as NullObjectException;

use system\lib\qweb\QWebEngine;
use system\lib\ViewBuilder\FormView as FormView;
use system\lib\ViewBuilder\TreeView as TreeView;

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
	public static function get_inherited_view($view_id){
		return static::find('all', array('conditions' => array('inherit_id = ? AND active = ?', $view_id, '1'), 'order' => 'sequence DESC'));
	}
	
	public static function get_view($xml_id){
		$base_view = XMLID::xml_id_to_object($xml_id);
		if(!$base_view){
			throw new \Exception('View not found: ' . $xml_id);
		}
		return self::apply_inheritance_arch($base_view);
	}

	/**
	 * get the arch field of the given view xmlid, after the inheritances were applied.
	 * It return a string (code) of thebase view extended by its children
	 * @param BlackView $base_view : the base view object
	 * @return string : the inherited view arch
	 */
	public static function apply_inheritance_arch($base_view, $saveXML=True){
		$inherited_views = self::get_inherited_view($base_view->id);
	
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

	// --------------------------------
	// Template View
	// --------------------------------

	/**
	 * Render the given template with the given data
	 * @param string $template_name : the name of the template to render
	 * @return the HTML code of the rendered template
	 */
	public static function template_render($template, $data=array()){
		$loader = function($name){
			return BlackView::get_view($name);
		};
		$engine = QWebEngine::getEngine($loader);
		return $engine->render($template, $data);
	}



	// --------------------------------
	// Form View
	// --------------------------------

	public static function form_view_render($model_name, $object=NULL, $options=array()){
		// find the view for $model
		$view = static::find('first', array('conditions' => array('inherit_id IS NULL AND active = ? AND type = ? AND model = ?', '1', 'form', $model_name), 'order' => 'id DESC'));
		if(!$view){
			return False;
		}
		
		//get view as XML document
		$view_doc = self::apply_inheritance_arch($view, False);
		
		// get the list of field in the view
		$field_name_list = self::view_get_fields($view_doc);
		
		// get fields properties (type, label, ...) with fields_view_get()
		$model = IrModel::get_model($model_name);
		$fields = $model::fields_view_get($field_name_list);
		
		// get object value
		$values = $model::read([$object->id], $field_name_list);
		
		// built the form
		$builder = new FormView($view_doc, $fields, $field_name_list, $model_name);
		$html_form = $builder->build($values[0], $options);
		return $html_form;
	}
	
	public static function tree_view_render($model_name, $objects=array(), $options=array()){
		// find the view for $model
		$view = static::find('first', array('conditions' => array('inherit_id IS NULL AND active = ? AND type = ? AND model = ?', '1', 'tree', $model_name), 'order' => 'id DESC'));
		if(!$view){
			return False;
		}
		
		//get view as XML document
		$view_doc = self::apply_inheritance_arch($view, False);
		
		// get the list of field in the view
		$field_name_list = self::view_get_fields($view_doc);
		
		// get fields properties (type, label, ...) with fields_view_get()
		$model = IrModel::get_model($model_name);
		$fields = $model::fields_view_get($field_name_list);
		
		// get object value
		$ids = array();
		foreach ($objects as $o){
			array_push($ids, $o->id);
		}
		$values = $model::read($ids, $field_name_list);
		
		// built the form
		$builder = new TreeView($view_doc, $fields, $field_name_list, $model_name);
		$html_form = $builder->build($values, $options);
		return $html_form;
	}

}
