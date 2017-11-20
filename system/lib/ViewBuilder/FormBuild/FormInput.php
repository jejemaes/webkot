<?php
namespace system\lib\ViewBuilder\FormBuild;

/**
 * Wrapper class to handle code generation
 */
class FormInput extends FormUtils	{
	protected $Code='';
	
	protected function render(){
		return $this->Code;
	}
}
?>
