<?php
/**
 * Maes Jerome
 * IrModule.class.php, created at Nov 12, 2017
 *
 */
namespace system\base;
use \system\DB;
use system\core\BlackModel;
use system\base\IrModel as IrModel;
use system\base\IrExternalIdentifier as XMLID;
use system\base\BlackView as View;

class IrModule extends BlackModel{

	static $table_name = 'ir_module';
	
	public $id;
	public $name;
	public $description;
	public $directory; // also technical name
	public $active;
	
	public static function get_module($tech_name){
		$row = \DB::queryFirstRow("SELECT * FROM " . self::$table_name . " WHERE directory = %s LIMIT 1", $tech_name);
		return new IrModule($row);
	}
	
	public function do_install(){
		$this->register_model();
		$this->register_view();
	}

	public function do_update(){
		$this->register_view();
	}
	
	public function register_model(){
		$model_directory = _DIR_MODULE . $this->directory . '/model/';
		if ($handle = opendir($view_directory)) {
			while (false !== ($entry = readdir($handle))) {
				if (!in_array($entry, array('.', '..'))) {
					$model_name = substr($entry, 0, -9);
					$klass = 'module\\' . $this->directory . '\model\\' . $model_name;
					IrModel::create(array(
						'name' => $model_name,
						'class_name' => $klass,
						'table_name' => $klass::$table_name,
					));
				}
			}
		}
	}

	public function register_view(){
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
			$template = View::create($values);
			$values = array(
				'xml_id' => $xmlid,
				'res_model' => View::$table_name,
				'res_id' => $template->id,
				'module' => $this->directory
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
		$values = array(
				'name' => $this->_get_attribute($node, 'name', $xmlid),
				'type' => 'template',
				'active' => $this->_get_attribute($node, 'active', true),
				'sequence' => $this->_get_attribute($node, 'sequence', 10),
				'arch' => $this->_get_arch_template($xmlid, $node)
		);
		if(is_null($template)){
			$template = View::create($values);
			$values = array(
					'xml_id' => $xmlid,
					'res_model' => View::$table_name,
					'res_id' => $template->id,
					'module' => $this->directory
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
