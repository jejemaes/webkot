<?php
/**
 * Maes Jerome
 * BlackController.class.php, created at May 27, 2016
 *
 */
namespace system\core;
use \system\http\AbstractController as AbstractController;


class BlackController extends AbstractController{
	
	
	public function check_mandatory_params(array $names){
		$params = $this->request->getParsedBody();
		foreach($params as $key => $value){
			if(array_key_exists($key, $names)){
				if(!$value){
					return false;
				}
			}
		}
		return true;
	}
	
	public function get_post_params(array $keys=[]){
		$params = array_fill_keys($keys, false);
		$parsed_body = $this->request->getParsedBody();
		if(!$parsed_body){
			$parsed_body = [];
		}
		foreach($parsed_body as $key => $value){
			if(in_array($key, $keys)){
				$params[$key] = $value;
			}
		}
		return $params;
	}
	
}
