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
				"div-id-content" => "bootpag-content",
				"div-id-pager" => "bootpag-page-selection",
				"callback" => "alert(num)"
		);
		$this->options = array_merge($default, $options);
	}

	public function load(){
		$jsCode = '<script>';
		// pour gerer le rafraichissement et avoir la bonne page selon le #fragment_id
		//$jsCode .= 'var url = SystemUtils.parse_url(document.URL);';
		//$jsCode .= 'var p = 1;';
		//$jsCode .= 'if(url.fragment){p = url.fragment.replace ( /[^\d.]/g, "");}';
		$jsCode .= '$("#'.$this->options["div-id-pager"].'").bootpag({
           total: '.$this->options["total"].',
		   page: '.$this->options["page"].',
		   maxVisible: '.$this->options["maxVisible"].',
		   href: "'.$this->options["href"].'",
		   leaps: '.$this->options["leaps"].',
		   next: "'.$this->options["next"].'",
		   prev: "'.$this->options["prev"].'" 
        }).on("page", function(event, num){
		   	 var res = '.$this->options["callback"].';
             $("#'.$this->options["div-id-content"].'").html(res);
        });';
		$jsCode .= '</script>';
		
		if($this->options["template"]){
			$this->options["template"]->addStyle('<link href="'.DIR_PLUGIN.'bootpag/css/bootstrap2.3.2-pagination.css" rel="stylesheet">');
			$this->options["template"]->addJSFooter($jsCode);
			$this->options["template"]->addJSHeader('<script src="'.DIR_PLUGIN.'bootpag/js/bootpag-1.0.4.js" type="text/javascript"></script>');
		}
		
		
		$html = ' <div id="'.$this->options["div-id-content"].'"></div>
    				<div id="'.$this->options["div-id-pager"].'"></div>';

		return $html;

	}


}