<?php




class PluginBootstrapDatetimePicker extends Plugin implements iPlugin{

	
	public function __construct(array $options = array()){
		$default = array (
				"format" => 'YYYY-MM-DD hh:mm:ss',
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
			$jsCodeH = '<script src="'. DIR_PLUGIN . 'bootstrap-datetimepicker/js/moment.js"></script>';
			$this->getOptions()["template"]->addJSFooter($jsCodeH);
			
			$jsCodeH = '<script src="'. DIR_PLUGIN . 'bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>';
			$this->getOptions()["template"]->addJSFooter($jsCodeH);
			
			$withDate = (!$this->getOptions()["withDate"] ? "pickDate: false," : "pickDate: true,");
			$withTime = (!$this->getOptions()["withTime"] ? "pickTime: false," : "pickTime: true,");
			
			$js = '<script type="text/javascript">
					$(\'#datetimepicker\').datetimepicker();
					  $(function() {
					    $("#'.$this->getOptions()["id"].'").datetimepicker({
					      '.$withDate.'
					      '.$withTime.'
					      pick12HourFormat: false,
					      language: "fr",
					      icons: {
							time: "fa fa-clock-o",
							date: "fa fa-calendar",
							up: "fa fa-arrow-up",
							down: "fa fa-arrow-down"
						}
					    });
					  });
				</script>';
			$this->getOptions()["template"]->addJSFooter($js);
			
			$style = '<link rel="stylesheet" type="text/css" href="'. DIR_PLUGIN . 'bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css"></link>';	
			$this->getOptions()["template"]->addStyle($style);
			
		}
		
		$html = '
			<div class="input-group date" id="'.$this->getOptions()["id"].'">
				<input type="text" class="form-control" name="'.$this->getOptions()["name"].'" data-format="'.$this->getOptions()["format"].'" value="'.$this->getOptions()["value"].'"/>
				<span class="input-group-addon">
					<span data-icon-element="" class="fa fa-calendar"></span>
				</span>
			</div>';
		
		return $html;
	}
	
	
}