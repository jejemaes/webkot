<?php



class URLUtils{
	
	public static function generateURL($moduleName, array $params){
		$url = 'index.php?mod=' . $moduleName;
		foreach ($params as $key => $value){
			$url .= "&" . $key . "=" . $value;
		}
		return $url;
	}
	
	
	public static function builtServerUrl($moduleName, array $params){
		$base = str_replace("/admin", "", self::getFullUrl());
		$url = $base . '/server.php?module=' . $moduleName;
		foreach ($params as $key => $value){
			$url .= "&" . $key . "=" . $value;
		}
		return $url;
	}
	
	public static function getModuleURL($name){
		return "index.php?mod=" . $name;
	}
	
	public static function getUserPageURL($u){
		return "index.php?mod=user&profile=".$u;
	}
	
	//############## GLOBAL URL ###############
	
	public static function getCompleteActualURL(){
		$pageURL = 'http';
		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") {$pageURL .= "s";}
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
		 
		return htmlspecialchars($pageURL);
	}
	
	/**
	 * get the Url without params of the actual page. Ex : http://http://localhost/Web Developpement/Workspace/webkot4dev5
	 * @return string
	 */
	public static function getFullUrl(){
			$https = !empty($_SERVER['HTTPS']) && strcasecmp($_SERVER['HTTPS'], 'on') === 0;
			return
			($https ? 'https://' : 'http://').
			(!empty($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'].'@' : '').
			(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ($_SERVER['SERVER_NAME'].
					($https && $_SERVER['SERVER_PORT'] === 443 ||
							$_SERVER['SERVER_PORT'] === 80 ? '' : ':'.$_SERVER['SERVER_PORT']))).
							substr($_SERVER['SCRIPT_NAME'],0, strrpos($_SERVER['SCRIPT_NAME'], '/'));
		
	}
	
	public static function getCompletePageURL(){
		$redir = 'index.php';
		$premier_elmt = true;
	
		foreach($_GET as $key => $value){
			if ($premier_elmt) {
				$redir .= '?'.$key.'='.$value;
				$premier_elmt = false;
			}else{
				$redir .= '&'.$key.'='.$value;
			}
		}
		return $redir;
	}
	
	public static function getPreviousURL(){
		return $_SERVER['HTTP_REFERER'];
	}
	
	
	public static function redirection($url){
		echo '<script type="text/javascript">
				<!--
				window.location = "'.$url.'"
				//-->
				</script>';
		header( "Location: ". $url );
	}
	
}