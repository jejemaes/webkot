<?php


class PluginMediaPicker extends Plugin implements iPlugin{

	private $options;
	
	public function __construct(array $options = array()){
		
		$default = array (
				"template" => null,
				"class" => " input-xlarge",
				"id" => "input-plg-mediapicker",
				"name" => "input-plg-mediapicker",
				"value" => ""
		);
		$this->options = array_merge($default, $options);
	}

	public function load(){
		
		
		if($this->options['template']){
			$this->options['template']->addJsFooter('<script src="'. DIR_PLUGIN . 'media-picker/mediapicker.js"></script>');
		}
		
		$html .= '<div class="input-append">
      <input id="'.$this->options["id"].'" name="'.$this->options["name"].'" class="'.$this->options["class"].'" placeholder="path/to/img.jpg" type="text" value="'.$this->options["value"].'">
      <div class="btn-group">
      	<button class="btn btn-default" onclick="pluginMediaPickerModal(\''.URLUtils::builtServerUrl('system',array("part" => "media", "action" => "mediapicker")).'\',\''.$this->options["id"].'\');return false;"> <i class="icon-th"></i> MediaPicker</button>
       </div>
    </div>';
		
		
		return $html;
		
	}


}