<?php
/**
 * Maes Jerome
 * TreeView.class.php, created at Nov 13, 2015
 *
 */
namespace system\lib\ViewBuilder;

use system\tools\Url as Url;

class TreeView extends View{
	
	
	public function build($values, array $options){
		$default = array(
			'table_class' => '',
			'table_id' => '',
			'table_role' => 'grid',
		);
		$options = array_merge($default, $options);
		
		$jump_to_form = !$this->get_dom()->getElementsByTagName("tree")->item(0)->getAttribute('jump_to_form');
		
		$html = '<table class="'.$options['table_class'].'" id="'.$options['table_id'].'" role="'.$options['table_role'].'">';
		// table header
		$html .= '<thead><tr>';
		foreach($this->get_field_list() as $field_name){
			$field_label = $this->get_field_label($field_name);
			$html .= '<th>'.$field_label.'</th>';
		}
		if($jump_to_form){
			$html .= '<th></th>';
		}
		$html .= '</tr></thead>';
		// table body
		$html .= '<tbody>';
		foreach ($values as $value){
			$html .= '<tr>';
			$elements = $this->get_dom()->getElementsByTagName("field");
			foreach ($elements as $field_tag) {
				$field_name = $field_tag->getAttribute('name');
				$field_type = $this->get_field_type($field_name, $field_tag->getAttribute('widget'));
				if(in_array($field_type, array('many2one', 'many2many', 'one2many'))){ // TODO improve !!
					$html .= '<td>'.json_encode($value[$field_name]).'</td>';
				}else{
					$html .= '<td>'.$value[$field_name].'</td>';
				}
			}
			if($jump_to_form){
				$html .= '<td><a href="'. Url::url_admin_from_path(sprintf('/%s/edit/%s', $this->get_model(), $value['id'])).'">Edit</a></td>';
			}
			$html .= '</tr>';
		}
		$html .= '</tbody>';
		$html .= '</table>';
		return $html;
	}
}