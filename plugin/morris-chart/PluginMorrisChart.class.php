<?php


class PluginMorrisChart extends Plugin implements iPlugin{

	private $options;
	
	public function __construct(array $options = array()){
		
		$default = array (
				"template" => null,
				"type" => "line",
				"element" => 'morris-div-id',
				"data" => array(),
				"xkey" => 'y',
				"ykeys" => array('a','b'),
				"labels" => array("Serie A", "Serie B")
		);
		$this->options = array_merge($default, $options);
	}

	public function load(){
		if($this->options["template"] && $this->options["element"]){	
			$this->options["template"]->addStyle('<link rel="stylesheet" href="http://cdn.oesmith.co.uk/morris-0.4.3.min.css">');
			$this->options["template"]->addJSHeader('<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>');
			$this->options["template"]->addJSHeader('<script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>');
			$this->options["template"]->addJSHeader('<script src="http://cdn.oesmith.co.uk/morris-0.4.3.min.js"></script>');
			
			$type = strtolower($this->options["type"]);
			
			$js = '<script type="text/javascript">';
			switch($type){
				case 'line':
					$js .= $this->graphLine();
					break;
				case 'area':
					$js .= $this->graphArea();
					break;
				case 'bar':
					$js .= $this->graphBar();
					break;
				case 'donut':
					$js .= $this->graphDonut();
					break;
				default:
					break;
			}
			$js .= '</script>';
		}
		return $js;
		
	}
	
	
	
	private function graphLine(){
		$js = "Morris.Line({
					element: '".$this->options['element']."',
					data: ".json_encode($this->options['data']).",
					xkey: '".$this->options['xkey']."',
					ykeys: ".json_encode($this->options['ykeys']).",
					labels: ".json_encode($this->options['labels'])."
				});";
		return $js;
	}
	
	
	private function graphArea(){
		$js = "Morris.Area({
					element: '".$this->options['element']."',
					data: ".json_encode($this->options['data']).",
					xkey: '".$this->options['xkey']."',
					ykeys: ".json_encode($this->options['ykeys']).",
					labels: ".json_encode($this->options['labels'])."
				});";
		return $js;
	}
	
	
	private function graphBar(){
		$js = "Morris.Bar({
					element: '".$this->options['element']."',
					data: ".json_encode($this->options['data']).",
					xkey: '".$this->options['xkey']."',
					ykeys: ".json_encode($this->options['ykeys']).",
					labels: ".json_encode($this->options['labels'])."
				});";
		return $js;
	}
	
	/**
	 * TODO : faire les graph
	 * @return string
	 */
	private function graphDonut(){
		$js = "Morris.Donut({
					element: '".$this->options['element']."',
					data: ".json_encode($this->options['data']).",
					xkey: '".$this->options['xkey']."',
					ykeys: ".json_encode($this->options['ykeys']).",
					labels: ".json_encode($this->options['labels'])."
				});";
		return '';
	}

	

}