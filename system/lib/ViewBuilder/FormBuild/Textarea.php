<?php
namespace system\lib\ViewBuilder\FormBuild;

class Textarea extends FormInput	{
	public function __construct($Value='', $Attribs=array()){
		var_dump($Attribs);
		$this->Code='<textarea class="form-control"'.parent::parseAttribs($Attribs).' style="height:400px">'.$Value.'</textarea>';
	}
}
?>
