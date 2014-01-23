<?php



class PluginBootpag extends Plugin implements iPlugin{

	private $options;

	public function __construct(array $options = array()){
		$default = array (
				"total" => 10,
				"page" => 1,
				"maxVisible" => 8,
				"href" => "#page-{{number}}",
				"leaps" => "true",
				"next" => ">",
				"prev" => "<",
				//"div-id-content" => "bootpag-content",
				"div-id-pager" => "bootpag-page-selection",
				"call-on-change" => "alert(num);"
		);
		$this->options = array_merge($default, $options);
	}

	public function load(){
		$jsCode = '<script>';
		// pour gerer le rafraichissement et avoir la bonne page selon le #fragment_id
		$jsCode .= '$("#'.$this->options["div-id-pager"].'").bootpag({
           total: '.$this->options["total"].',
		   page: '.$this->options["page"].',
		   maxVisible: '.$this->options["maxVisible"].',
		   href: "'.$this->options["href"].'",
		   leaps: '.$this->options["leaps"].',
		   next: "'.$this->options["next"].'",
		   prev: "'.$this->options["prev"].'" 
        }).on("page", function(event, num){
		   	 '.$this->options["call-on-change"].'
        });';
		$jsCode .= '</script>';
		
		if($this->options["template"]){
			$this->options["template"]->addJSFooter($jsCode);
			$this->options["template"]->addJSHeader('<script src="'.DIR_PLUGIN.'bootpag/js/bootpag-1.0.5.js" type="text/javascript"></script>');
		}
		
		if(!empty($this->options["div-id-content"])){
			$html = '<div id="'.$this->options["div-id-content"].'"></div>';
		}else{
			$html = "";
		}
		$html .= '<div id="'.$this->options["div-id-pager"].'"></div>';

		return $html;

	}


}