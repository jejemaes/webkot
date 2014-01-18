<?php

class PluginTableSorter extends Plugin implements iPlugin{

	public function __construct(array $options = array()){
		$this->setOptions($options);
	}


	public function load(){
		$js = '<script src="'.DIR_PLUGIN.'tablesorter/js/jquery.tablesorter.js" type="text/javascript"></script>';
		$this->getOptions()["template"]->addJSHeader($js);

		$jsF = '<script type="text/javascript" charset="utf-8">
		$(document).ready(function() {
			$(".tablesorter").tablesorter({debug: true});
		});
</script>';
		$this->getOptions()["template"]->addJSFooter($jsF);
	}


}