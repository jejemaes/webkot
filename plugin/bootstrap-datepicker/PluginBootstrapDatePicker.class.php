<?php


class PluginBootstrapDatePicker extends Plugin implements iPlugin{

	
	public function __construct(array $options = array()){
		$this->setOptions($options);
	}
	
	
	public function load(){
		//js Code Footer
		$jsCodeF = "<script>
			$('#".$this->getOptions()['text-input-id']."').datepicker({'weekStart':1});
		</script>";
		$this->getOptions()["template"]->addJSFooter($jsCodeF);
		
		// js Code Header
		$jsCodeH = '<script src="'. DIR_PLUGIN . 'bootstrap-datepicker/js/bootstrap-datepicker.js"></script>';
		$this->getOptions()["template"]->addJSHeader($jsCodeH);
		
		// CSS Style
		$style = '<!-- Bootstrap Editor (required bootstrap)-->
<link rel="stylesheet" type="text/css" href="'. DIR_PLUGIN . 'bootstrap-datepicker/css/datepicker.css"></link>';	
		$this->getOptions()["template"]->addStyle($style);
	}
	
	
}