<?php


class Message {

	private $_type;
	private $_contents;
	
	
	public function __construct($type = 0){
		$this->_type = $type;
		$this->_contents = array();
	}
	
	public function addMessage($text){
		$this->_contents[] = $text;
	}
	
	public function isEmpty(){
		return(count($this->getContent()) == 0);
	}
	
	public function isSuccess(){
		return ($this->getType() == 1);
	}
	
	public function isEchec(){
		return ($this->getType() == 3);
	}
	

	public function toStringContent(){
	 	if(count($this->getContent())<=0){
			return "";
		}else{
			$tmp = $this->getContent();
			if(count($this->getContent())==1){
				return ($tmp[0]);
			}else{
				$str = $tmp[0];
				for($i = 1 ; $i < count($tmp) ; $i++){
					$str = $str . "<br clear>" . $tmp[$i];
				}
				return $str;
			}
		}
		return "";
	}
	
	
	public function toJSON(){
		if(!$this->isEmpty()){
			$i = $this->getType();
			switch ($i) {
				case 0: // info
					$type = "info";
					break;
				case 1: //succes
					$type = "success";
					break;
				case 2: // warning
					$type = "warn";
					break;
				case 3: // error
					$type = "error";
					break;
				default:
					$type = "info";
			}
			$m = array("message" => array());
			$m['message']['type'] = $type;
			$m['message']['content'] = $this->toStringContent();// utf8_encode($this->toStringContent());
			return json_encode($m);
		}else{
			return "{message : {}}";
		}
	}
	
	public function toArray(){
		if(!$this->isEmpty()){
			$i = $this->getType();
			switch ($i) {
				case 0: // info
					$type = "info";
					break;
				case 1: //succes
					$type = "success";
					break;
				case 2: // warning
					$type = "warn";
					break;
				case 3: // error
					$type = "error";
					break;
				default:
					$type = "info";
			}
			$m = array();
			$m['type'] = $type;
			$m['content'] = $this->toStringContent();// utf8_encode($this->toStringContent());
			return $m;
		}else{
			return array('type' =>'info', 'content' =>'');
		}
	}
	 
	/**
	 * return the html code
	 * @return string
	 */
	public function __toString(){
		if(!$this->isEmpty()){
			$i = $this->getType();
			switch ($i) {
				case 0: // info
					return $this->toStringAlertInfos();
					break;
				case 1: //succes
					return $this->toStringAlertSuccess();
					break;
				case 2: // warning
					return $this->toStringAlertWarning();
					break;
				case 3: // error
					return $this->toStringAlertError();
					break;
				default:
					return $this->toStringAlertWarning();
			}
		}else{
			return "";
		}
	}
	
	private function toStringAlertError(){
		return '<div class="alert alert-error alert-danger"><button type="button" class="close" data-dismiss="alert">&#10008;</button>' . $this->toStringContent() . '</div>';
	}
	
	private function toStringAlertSuccess(){
		return '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&#10008;</button>' . $this->toStringContent() . '</div>';
	}
	
	private function toStringAlertInfos(){
		return '<div class="alert alert-info"><button type="button" class="close" data-dismiss="alert">&#10008;</button>' . $this->toStringContent() . '</div>';
	}
	
	private function toStringAlertWarning(){
		return '<div class="alert alert-block alert-warning"><button type="button" class="close" data-dismiss="alert">&#10008;</button>' . $this->toStringContent() . '</div>';
	}
	
	
	public function getMessage(){
		return $this->toStringContent();
	}
	
	
	
	public function setType($type){
		$this->_type = $type;
	}
	
	public function getType(){
		return $this->_type;
	}
	
	public function setContent($type){
		$this->_contents = $type;
	}
	
	public function getContent(){
		return $this->_contents;
	}
    
}
