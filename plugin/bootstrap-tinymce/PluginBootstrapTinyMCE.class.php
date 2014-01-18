<?php


class PluginBootstrapTinyMCE extends Plugin implements iPlugin{
	
	public function __construct(array $options = array()){
		$default = array (
				"selector" => "textarea",
				"template" => null
		);
		
		$tmp = array_merge($default, $options);
		$this->setOptions($tmp);
		//$this->options = array_merge($default, $options);
		
	}
	
	
	public function load(){
		
		if($this->getOptions()["template"]){
			
			$this->getOptions()["template"]->addJSHeader('<script type="text/javascript" src="'.DIR_PLUGIN.'bootstrap-tinymce/tinymce/tinymce.min.js"></script>');
			
			$jsCodeF = '<script type="text/javascript">
							tinymce.init({
							    selector: "'.$this->getOptions()["selector"].'",
								plugins: "textcolor, link, code, image advlist autolink charmap preview emoticons paste table wordcount searchreplace fullscreen insertdatetime media",
								// Theme options
								toolbar: "insertfile undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist | outdent indent | link image preview media fullpage | forecolor backcolor | code",
								
								//toolbar: "undo redo | styleselect | bold italic underline | forecolor backcolor | link image",
								menubar : false,
								height : 300,
								content_css : "'.DIR_PLUGIN.'bootstrap-tinymce/tinymce/skins/boostrap/content.css"
							 });
							</script>';	
			$this->getOptions()["template"]->addJSFooter($jsCodeF);
		}
	}
	
	
}