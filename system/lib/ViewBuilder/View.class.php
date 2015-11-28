<?php
/**
 * Maes Jerome
 * View.class.php, created at Nov 16, 2015
 *
 */
namespace system\lib\ViewBuilder;

use \system\interfaces\iView as iView;

abstract class View implements iView{
	
	private $_dom;
	private $_properties;
	private $_field_list;
	private $_model;
	
	public function __construct(\DOMDocument $domdoc, array $fields_properties, array $field_name_list, $model){
		$this->_dom = $domdoc;
		$this->_properties = $fields_properties;
		$this->_field_list = $field_name_list;
		$this->_model = $model;
	}
	
	public function get_dom(){
		return $this->_dom;
	}
	
	public function get_properties(){
		return $this->_properties;
	}
	
	public function get_field_list(){
		return $this->_field_list;
	}
	
	public function get_model(){
		return $this->_model;
	}

	protected function get_field_type($field_name, $field_widget = false){
		if($field_widget){
			return $field_widget;
		}
		if(array_key_exists($field_name, $this->get_properties())){
			return $this->get_properties()[$field_name]['type'];
		}
		return 'text';
	}
	
	protected function get_field_label($field_name, $label_attribute = ''){
		if($label_attribute == '0'){
			return '';
		}
		if(array_key_exists($field_name, $this->get_properties())){
			return $this->get_properties()[$field_name]['label'];
		}
		return '';
	}
	
	protected function get_default_value($values, $name, $default=''){
		if(array_key_exists($name, $values)){
			return $values[$name];
		}
		if(array_key_exists($name, $this->get_properties())){
			if(array_key_exists('default', $this->get_properties()[$name])){
				return $this->get_properties()[$name]['default'];
			}
		}
		return $default;
	}
	
}