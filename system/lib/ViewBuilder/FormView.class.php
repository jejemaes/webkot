<?php
/**
 * Maes Jerome
 * FormView.class.php, created at Nov 13, 2015
 *
 */
namespace system\lib\ViewBuilder;

use \system\lib\ViewBuilder\FormBuild\Button as Button;
use \system\lib\ViewBuilder\FormBuild\Checkbox as Checkbox;
use \system\lib\ViewBuilder\FormBuild\Custom as Custom;
use \system\lib\ViewBuilder\FormBuild\Email as Email;
use \system\lib\ViewBuilder\FormBuild\File as File;
use \system\lib\ViewBuilder\FormBuild\Form as Form;
use \system\lib\ViewBuilder\FormBuild\FormInput as FormInput;
use \system\lib\ViewBuilder\FormBuild\FormUtils as FormUtils;
use \system\lib\ViewBuilder\FormBuild\GeneralInput as GeneralInput;
use \system\lib\ViewBuilder\FormBuild\Help as Help;
use \system\lib\ViewBuilder\FormBuild\Hidden as Hidden;
use \system\lib\ViewBuilder\FormBuild\InputButton as InputButton;
use \system\lib\ViewBuilder\FormBuild\Password as Password;
use \system\lib\ViewBuilder\FormBuild\Radio as Radio;
use \system\lib\ViewBuilder\FormBuild\Reset as Reset;
use \system\lib\ViewBuilder\FormBuild\Select as Select;
use \system\lib\ViewBuilder\FormBuild\Submit as Submit;
use \system\lib\ViewBuilder\FormBuild\Text as Text;
use \system\lib\ViewBuilder\FormBuild\Textarea as Textarea;

class FormView extends View{
	
	
	public function build($values, array $options){
		$default = array(
			'method' => 'POST',
			'title' => '',
			'action' => '#',
			'css_class' => '',	
		);
		$options = array_merge($default, $options);
		
		$form = new Form;
		$form = $form->init($options['action'], $options['method'], array(
			'class'=> $options['css_class'] . ' form-horizontal'
		));
		
		$elements = $this->get_dom()->getElementsByTagName("field");
		foreach ($elements as $field_tag) {
			$field_name = $field_tag->getAttribute('name');
			
			$field_label = $this->get_field_label($field_name, $field_tag->getAttribute('label'));
			$field_type = $this->get_field_type($field_name, $field_tag->getAttribute('widget'));
			$field_default_value = $this->get_default_value($values, $field_name);
			
			switch (strtolower($field_type)){
				// normal type
				case 'string':
				case 'integer':
					$form->group($field_label,
				        new Text(array(
				            'placeholder'   => $field_label,
				            'value'			=> $field_default_value,
				            'name'			=> $field_name,
				            'id'            => $field_name,
				            'data-type'		=> strtolower($field_type),
				        ))
				    );
					break;
				case 'text':
					$form->group($field_label, new Textarea($field_default_value, array(
						'id'        => $field_name,
						'name'		=> $field_name,
						'data-type' => strtolower($field_type),
					)));
					break;
				case 'boolean':
					$attr = array('name' => $field_name);
					if($field_default_value){
						$attr['checked'] = 'checked';
					}
					$form->group($field_label,
				        new Checkbox('', $attr)
				    );
					break;
				case 'date':
				case 'datetime':
					$form->group($field_label,
						new Text(array(
								'placeholder'   => $field_label,
								'value'			=> $field_default_value,
								'name'			=> $field_name,
								'id'            => $field_name,
								'data-type'		=> strtolower($field_type),
						))
					);
					break;
				case 'select':
							
					break;
				// widget specific
				case 'html':
					$form->group($field_label, new Textarea($field_default_value, array(
						'id'        => $field_name,
						'name'		=> $field_name,
						'data-type' => strtolower($field_type),
					)));
					break;
				case 'hidden':
					$form->hidden(array(
						'name'	=> $field_name,
						'value'	=> $field_default_value,
						'id'	=> $field_name
					));
					break;
				// relational
				case 'many2one':
					$form->group($field_label,
					new Select(
						array($field_default_value[0] => $field_default_value[1]),
						array($field_default_value[0]),
						array(
								'placeholder'   => $field_label,
								'value'			=> $field_default_value[1],
								'name'			=> $this->get_properties()[$field_name]['foreign_key'][0], //$field_name,
								'id'            => $field_name,
								'data-res-model'=> $this->get_properties()[$field_name]['model'],
								'data-res-id'	=> $field_default_value[0],
								'data-type'		=> 'many2one',
						)
					)
					/*
							new Text(array(
									'placeholder'   => $field_label,
									'value'			=> $field_default_value[1],
									'name'			=> $field_name,
									'id'            => $field_name,
									'data-res-model'=> $this->get_properties()[$field_name]['model'],
									'data-res-id'	=> $field_default_value[0],
									'data-type'		=> 'many2one',
							))
							*/
					);
					break;
				default:
					echo "i n'est ni gal ˆ 2, ni ˆ 1, ni ˆ 0." . $field_type;
			}
			
		}
		$form->group('', new Submit('Submit'));
		return $form->render();
	}

}
