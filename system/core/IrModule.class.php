<?php
/**
 * Maes Jerome
 * IrModule.class.php, created at Oct 1, 2015
 *
 */
namespace system\core;
use system\core\BlackModel;
use system\core\IrExternalIdentifier as XMLID;
use system\core\BlackView as BlackView;
use system\exception\NullObjectException as NullObjectException;


class IrModule extends BlackModel{

	static $table_name = 'ir_module';

	static $attr_accessible = array(
			'id' => array('label'=> 'Id', 'type' => 'integer', 'required' => true),
			'name' => array('label'=> 'Name', 'type' => 'string', 'length' => 128),
			'directory' => array('label'=> 'Directory', 'type' => 'string', 'length' => 128, 'required' => true),
			'active' => array('label' => 'Active', 'type' => 'boolean', 'default' => False)
	);
	
	public static function get_module($tech_name){
		return self::find('first', array('conditions' => array('directory = ?', $tech_name)));
	}
	
	public static function get_active_module(){
		return self::find('all', array('conditions' => array('active = ?', 1)));
	}
	
	public function do_update(){
		$this->update_view();
	}

	public function update_view(){
		$view_directory = _DIR_MODULE . $this->directory . '/view/';
		if ($handle = opendir($view_directory)) {
			while (false !== ($entry = readdir($handle))) {
				if (!in_array($entry, array('.', '..'))) {
					$file = $view_directory . $entry;
					if(substr($file, -3) === 'xml'){
						$xml = simplexml_load_file($file);
						foreach($xml->children() as $child) {
							$xmlid = $child['id'];
							$tag_name = $child->getName();
							// template
							if($tag_name == 'template'){
								$this->_update_view_template($xmlid, $child);
							}
							// view
							if($tag_name == 'view'){
								$this->_update_view_view($xmlid, $child);
							}
						}
					}
				}
			}
			closedir($handle);
		}
	}
	
	/**
	 * Parse XML 'view' tag, and update or create it in the database as ir_view (view).
	 * @param unknown $xmlid
	 * @param unknown $node
	 * @return Ambigous <NULL, unknown>
	 */
	public function _update_view_view($xmlid, $node){
		$template = XMLID::xml_id_to_object($xmlid);
		$values = array(
				'name' => $this->_get_attribute($node, 'name', $xmlid),
				'type' => $this->_get_attribute($node, 'type', 'form'),
				'model' => $this->_get_attribute($node, 'model', NULL),
				'active' => $this->_get_attribute($node, 'active', true),
				'sequence' => $this->_get_attribute($node, 'sequence', 10),
				'arch' => $this->_get_arch_view($xmlid, $node)
		);
		if(is_null($template)){
			$template = BlackView::create($values);
			$values = array(
				'xml_id' => $xmlid,
				'res_model' => BlackView::$table_name,
				'res_id' => $template->id
			);
			XMLID::create($values);
		}else{
			// update the existing one
			$template->update_attributes($values);
		}
		return $template;
	}
	
	/**
	 * Parse XML 'template' tag, and update or create it in the database as ir_view (template).
	 * @param unknown $xmlid
	 * @param unknown $node
	 * @return Ambigous <NULL, unknown>
	 */
	public function _update_view_template($xmlid, $node){
		$template = XMLID::xml_id_to_object($xmlid);
		
		$inherit_id = NULL;
		$inherit_xmlid = $this->_get_attribute($node, 'inherit_id', NULL);
		if($inherit_xmlid){
			$inherit_template = XMLID::xml_id_to_object($inherit_xmlid);
			if(!$inherit_template){
				throw new \Exception('No template ' . $inherit_xmlid . ' found to be inherited by ' . $xmlid);
			}
			// TODO JEM: check each xpath to be present in parent view
			$inherit_id = $inherit_template->id;
		}
		
		$values = array(
				'name' => $this->_get_attribute($node, 'name', $xmlid),
				'type' => 'template',
				'active' => $this->_get_attribute($node, 'active', true),
				'sequence' => $this->_get_attribute($node, 'sequence', 10),
				'arch' => $this->_get_arch_template($xmlid, $node),
				'inherit_id' => $inherit_id,
		);
		if(is_null($template)){
			$template = BlackView::create($values);
			$values = array(
				'xml_id' => $xmlid,
				'res_model' => BlackView::$table_name,
				'res_id' => $template->id
			);
			XMLID::create($values);
		}else{
			// update the existing one
			$template->update_attributes($values);
		}
		return $template;
	}
	
	private function _get_arch_view($xmlid, $node){
		$arch = '';
		foreach($node->children() as $child) {
			$arch .= $child->asXML();
		}
		$t = '<?xml version="1.0" encoding="UTF-8"?>
				<view name="'.$xmlid.'">
    				'.$arch.'
				</view>';
		return $t;
	}
	
	private function _get_arch_template($xmlid, $node){
		$arch = '';
		foreach($node->children() as $child) {
			$arch .= $child->asXML();
		}
		$t = '<?xml version="1.0" encoding="UTF-8"?>
				<data>
    				<t t-name="'.$xmlid.'">'.$arch.'</t>
				</data>';
		return $t;
	}
	
	private function _get_attribute($node, $attribute_name, $default){
		$attributes = $node->attributes();
		if(isset($attributes[$attribute_name])){
			return (string)$attributes->$attribute_name;
		}
		return $default;
	}

}
