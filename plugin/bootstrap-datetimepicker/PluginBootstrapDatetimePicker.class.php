<?php




class PluginBootstrapDatetimePicker extends Plugin implements iPlugin{

	
	public function __construct(array $options = array()){
		$default = array (
				"format" => 'yyyy-mm-dd hh:ii:ss',
				"withDate" => true,
				"withTime" => true,
				"id" => "datetime-picker-input",
				"name" => "datetime-picker-input",
				"template" => null,
				"value" => ""
		);
		
		$this->setOptions(array_merge($default, $options));
	}
	
	
	public function load(){

		if($this->getOptions()["template"]){
			$jsCodeH = '<script src="'. DIR_PLUGIN . 'bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>';
			$this->getOptions()["template"]->addJSFooter($jsCodeH);
				
			$jsCodeH = '<script src="'. DIR_PLUGIN . 'bootstrap-datetimepicker/js/locales/bootstrap-datetimepicker.fr.js"></script>';
			$this->getOptions()["template"]->addJSFooter($jsCodeH);
			
			$jsCodeH = '<script src="'. DIR_PLUGIN . 'bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>';
			$this->getOptions()["template"]->addJSFooter($jsCodeH);
			
			$withDate = (!$this->getOptions()["withDate"] ? "pickDate: false," : "pickDate: true,");
			$withTime = (!$this->getOptions()["withTime"] ? "pickTime: false," : "pickTime: true,");
			
			
			$js = '<script type="text/javascript">
					    $("#'.$this->getOptions()["id"].'").datetimepicker({
					        format: "'.$this->getOptions()["format"].'",
					        weekStart: 1,
					        autoclose: true,
					        todayBtn: true,
					        pickerPosition: "bottom-left"
					    });
					</script> ';
			
			$this->getOptions()["template"]->addJSFooter($js);
			
			$style = '<link rel="stylesheet" type="text/css" href="'. DIR_PLUGIN . 'bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css"></link>';	
			$this->getOptions()["template"]->addStyle($style);
			
		}
		
		$html = '
			<div class="input-group date">
				<input id="'.$this->getOptions()["id"].'" type="text" class="form-control form_datetime" name="'.$this->getOptions()["name"].'" data-format="'.$this->getOptions()["format"].'" value="'.$this->getOptions()["value"].'" readonly/>
				<span class="input-group-addon">
					<span data-icon-element="" class="fa fa-calendar"></span>
				</span>
			</div>';
	
		
		$html2 = '<div class="input-append date">
    <input size="16" type="text" value="" readonly class="form_datetime">
    <span class="add-on"><i class="fa fa-calendar"></i></span>
</div>';
		
		return $html;
	}
	
	
}