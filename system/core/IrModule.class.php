<?php
/**
 * Maes Jerome
 * IrModule.class.php, created at May 31, 2016
 *
 */
namespace system\core;
use system\core\IrExternalIdentifier as XMLID;
use system\core\BlackView as BlackView;


class IrModule extends BlackModel{

	static $table_name = 'ir_module';

	public $id;
	public $name;
	//public $dependencies;
	public $technical_name;
	public $description;
	
	
	// UPDATE MODULE
	
	public static function install_module($module){
		static::load_script($module);
		static::load_view($module);
	}
	
	
	public static function update_module(array $modules=[]){
		foreach ($modules as $module){
			static::load_view($module);
		}
	}
	
	
	public static function update_module_list(){
		$modules = scandir(DIR_ADDONS);
		foreach($modules as $item){
			if($item != '.' && $item != '..'){
				if(is_dir(DIR_ADDONS . $item)){
					$manifest_path = DIR_ADDONS . $item . DIRECTORY_SEPARATOR . 'manifest.php';
					if(is_file($manifest_path)){
						$manifest_values = include $manifest_path;
						self::_insert_or_update($item, $manifest_values);
					}
				}
			}
		}
	}
	
	
	private static function _insert_or_update($technical_name, $value){
		$module = self::search(['_where' => 'technical_name = :technical_name', 'technical_name' => $technical_name], 'id DESC', 1);
		if($module){
			$module->write($value);
		}else{
			$data = array_merge($value, ['technical_name' => $technical_name]);
			self::create($data);
		}
	}
	
	
	// LOADING SCRIPTS
	
	public static function load_script($module){
		// TODO : load sql script to create tables
	}
	
	// LOADING VIEW
	
	public static function load_view($module){
		$view_directory = DIR_ADDONS . $module . '/view/';
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
								static::_update_view_template($xmlid, $child);
							}
							// view
							if($tag_name == 'view'){
								static::_update_view_view($xmlid, $child);
							}
						}
					}
				}
			}
			closedir($handle);
		}
	}
	

	/**
	 * Parse XML 'template' tag, and update or create it in the database as ir_view (template).
	 * @param unknown $xmlid
	 * @param unknown $node
	 * @return Ambigous <NULL, unknown>
	 */
	private static function _update_view_template($xmlid, $node){
		$template = XMLID::ref($xmlid);
		$values = array(
			'name' => static::_get_attribute($node, 'name', $xmlid),
			'type' => 'template',
			'active' => static::_get_attribute($node, 'active', true),
			'sequence' => static::_get_attribute($node, 'sequence', 10),
			'arch' => static::_get_arch_template($xmlid, $node)
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
			$template->write($values);
		}
		return $template;
	}
	
	
	/**
	 * Parse XML 'view' tag, and update or create it in the database as ir_view (view).
	 * @param unknown $xmlid
	 * @param unknown $node
	 * @return Ambigous <NULL, unknown>
	 */
	public function _update_view_view($xmlid, $node){
		$template = XMLID::ref($xmlid);
		$values = array(
			'name' => static::_get_attribute($node, 'name', $xmlid),
			'type' => static::_get_attribute($node, 'type', 'form'),
			'model' => static::_get_attribute($node, 'model', NULL),
			'active' => static::_get_attribute($node, 'active', true),
			'sequence' => static::_get_attribute($node, 'sequence', 10),
			'arch' => static::_get_arch_view($xmlid, $node)
		);
		if(is_null($template)){
			$template = BlackView::create($values);
			$values = array(
				'xml_id' => $xmlid,
				'res_model' => 'view',
				'res_id' => $template->id
			);
			XMLID::create($values);
		}else{
			// update the existing one
			$template->write($values);
		}
		return $template;
	}
	
	
	private static function _get_arch_view($xmlid, $node){
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
	
	
	private static function _get_arch_template($xmlid, $node){
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
	
	
	private static function _get_attribute($node, $attribute_name, $default){
		$attributes = $node->attributes();
		if(isset($attributes[$attribute_name])){
			return (string)$attributes->$attribute_name;
		}
		return $default;
	}
}