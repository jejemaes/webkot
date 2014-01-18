<?php


class PluginBootstrapEditor extends Plugin implements iPlugin{
	
	public function __construct(array $options = array()){
		$this->setOptions($options);
	}
	
	
	public function load(){
		$jsCodeF = '<script>
			$(\'.bootstrap-editor\').wysihtml5({
	"font-styles": true, //Font styling, e.g. h1, h2, etc. Default true
	"emphasis": true, //Italics, bold, etc. Default true
	"lists": true, //(Un)ordered lists, e.g. Bullets, Numbers. Default true
	"html": true, //Button which allows you to edit the generated HTML. Default false
	"link": true, //Button to insert a link. Default true
	"image": true, //Button to insert an image. Default true,
	"color": false //Button to change color of font  
});
		</script>';
		$this->getOptions()["template"]->addJSFooter($jsCodeF);
		
		$jsCodeH = '<script src="'. DIR_PLUGIN . 'bootstrap-editor/js/wysihtml5-0.3.0.js"></script>
<script src="'. DIR_PLUGIN . 'bootstrap-editor/js/prettify.js"></script>
<!--<script src="'. DIR_PLUGIN . 'bootstrap-editor/js/bootstrap.min333333.js"></script>-->
<script src="'. DIR_PLUGIN . 'bootstrap-editor/src/bootstrap-wysihtml5.js"></script>';
		$this->getOptions()["template"]->addJSHeader($jsCodeH);
		
		$style = '<!-- Bootstrap Editor (required bootstrap)-->
<link rel="stylesheet" type="text/css" href="'. DIR_PLUGIN . 'bootstrap-editor/css/prettify.css"></link>
<link rel="stylesheet" type="text/css" href="'. DIR_PLUGIN . 'bootstrap-editor/src/bootstrap-wysihtml5.css"></link>';
		
		$this->getOptions()["template"]->addStyle($style);
	}
	
	
}