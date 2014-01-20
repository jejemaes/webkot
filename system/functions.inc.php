<?php

//################################
//############# LOAD #############
//################################

/**
 * include a a given file
 * @param string $file : the path (absolute or relative) to a php file
 * @return boolean : true if the file was included, false otherwise
 */
function system_include_file($file){
	$included_files = get_included_files();
	if(!in_array(realpath($file), $included_files)){
		include $file;
		return true;
	}
	return false;
}

/**
 * load a module : the Model, View and include the controller. It includes the Controller and the js file too.
 * @param string $name : the name of the module to load
 * @param iTemplate $template : the template to add the js code tags
 * @return string : the path to the controller
 */
function system_load_module_frontend($name, iTemplate $template = null){
	$mmanager = ModuleManager::getInstance();
	$module = $mmanager->getModule($name);
	$relativePath = DIR_MODULE . $module->getLocation();
	$loader = $module->getLoader();
	if(!class_exists($loader)){
		include_once $relativePath . $loader . '.class.php';
	}
	$controller = $loader::loadModule($relativePath);
	if($template){
		$loader::loadJsCode($template, $relativePath);
	}
	return $controller;
}

/**
 * load a partial module (modal, functions, and js code)
 * @param string $name : the name of the module to load
 * @param iTemplate $template : the template to add the js code tags
 */
function system_load_partial_module_frontend($name, iTemplate $template = null){
	$mmanager = ModuleManager::getInstance();
	$module = $mmanager->getModule($name);
	$relativePath = DIR_MODULE . $module->getLocation();
	$loader = $module->getLoader();
	if(!class_exists($loader)){
		include_once $relativePath . $loader . '.class.php';
	}
	$loader::loadFunctions($relativePath);
	$loader::loadModel($relativePath);
	if($template){
		$loader::loadJsCode($template, $relativePath);
	}
}


/**
 * load a module : the Model, View and include the controller. It includes the Controller and the js file too.
 * @param string $name : the name of the module to load
 * @param iTemplate $template : the template to add the js code tags
 * @return string : the path to controller file to include
 */
function system_load_module_backend($name, iAdminTemplate $template = null){
	$mmanager = ModuleManager::getInstance();
	$module = $mmanager->getModule($name);
	$relativePath = DIR_MODULE . $module->getLocation();
	$loader = $module->getLoader();
	if(!class_exists($loader)){
		include_once $relativePath . $loader . '.class.php';
	}
	$controller = $loader::loadAdminModule($relativePath);
	if($template){
		$loader::loadAdminJsCode($template, $relativePath);
	}
	return $controller;
}

/**
 * load a partial module (modal, functions, and js code)
 * @param string $name : the name of the module to load
 * @param iTemplate $template : the template to add the js code tags
 */
function system_load_partial_module_backend($name, iTemplate $template = null){
	$mmanager = ModuleManager::getInstance();
	$module = $mmanager->getModule($name);
	$relativePath = DIR_MODULE . $module->getLocation();
	$loader = $module->getLoader();
	if(!class_exists($loader)){
		include_once $relativePath . $loader . '.class.php';
		$loader::loadAdminFunctions($relativePath);
		$loader::loadAdminModel($relativePath);
	}
	if($template){
		$loader::loadAdminJsCode($template, $relativePath);
	}
}

/**
 * load a module : the Model, View and include the controller. It includes the Controller and the js file too.
 * @param string $name : the name of the module to load
 * @param iTemplate $template : the template to add the js code tags
 * @return string : the path to the controller
 */
function system_load_module_server($name){
	$mmanager = ModuleManager::getInstance();
	$module = $mmanager->getModule($name);
	$relativePath = DIR_MODULE . $module->getLocation();
	$loader = $module->getLoader();
	if(!class_exists($loader)){
		include_once $relativePath . $loader . '.class.php';
	}
	$controller = $loader::loadServerModule($relativePath);
	return $controller;
}








/**
 * Load the class that the path is given
 * @param array $paths : the paths to the files to load
 */
function system_load_classes(array $paths){
	$included_files = get_included_files();
	foreach ($paths as $file){
		if(!in_array(realpath($file), $included_files)){
			include realpath($file);
		}
		
	}
}


function system_load_class($path, $classname){
	if(!class_exists($classname)){
		include $path . $classname . ".class.php";
	}
}

/**
 * load only the model of a given Module
 * @uses to load the widget
 * @param String $name : the name of the module to load the model
 */
function system_load_module_model($name){
	$mmanager = ModuleManager::getInstance();
	$module = $mmanager->getModule($name);
	$loader = $module->getLoader();
	if(!class_exists($loader)){
		include_once DIR_MODULE . $module->getLocation() . $loader . '.class.php';
		$loader::loadModel(DIR_MODULE . $module->getLocation());
	}
}


/**
 * Load the plugin into the given template
 * @param array $plugins : the list of plugin to load
 * @param iGeneralTemplate $template : the template
 * @return $html : html code generate by the plugin to include and that need to be place in the page content (for example)
 */
function system_load_plugin(array $plugins/*, iGeneralTemplate $template = null*/){
	$html = '';
	foreach ($plugins as $dir => $param){
		if(is_dir(DIR_PLUGIN . $dir .'/')){
			$config = json_decode(file_get_contents(DIR_PLUGIN . $dir . '/config.json'), true);
			$toload = array();
			foreach ($config["files"] as $file){
				$toload[] = DIR_PLUGIN . $dir . '/' . $file;
			}	
		
			system_load_classes($toload);
			
			$class = $config["class"];
			$plugin = new $class($param);
			$html .= $plugin->load();
			
		}else{
			//exception!
			echo "EXCEPTION LOAD PLUGIN";
		}		
	}
	return $html;	
}


function system_load_php_files($directory){
	if ($handle = opendir($directory)) {
		while (false !== ($entry = readdir($handle))) {
			$ext = pathinfo($entry, PATHINFO_EXTENSION);
			if($ext == 'php'){
				include $directory . $entry;
			}
		}
		closedir($handle);
	}
}

/**
 * include the js file in the given directory in the header of the template
 * @param String $directory
 * @param iTemplate $template
 */
function system_load_js_file($directory, $template){
	if ($handle = opendir($directory)) {
		while (false !== ($entry = readdir($handle))) {
			$ext = pathinfo($entry, PATHINFO_EXTENSION);
			if($ext == 'js'){
				$template->addJsHeader('<script src="'.$directory.$entry.'" type="text/javascript"></script>');
			}
		}
		closedir($handle);
	}
}
//################################
//######## LOGIN SESSION #########
//################################
/**
 * check the User Profile Session to determine the access to the admin panel
 * @return boolean
 */
function system_session_can_access_admin(){
	$smanager = SessionManager::getInstance();
	if($smanager->getUserprofile()){
		$role = $smanager->getUserprofile()->getRole();	
		return ($role = 'Webkot' || $role = 'Administrator');
	}
	return false;
}
/**
 * Control if the login form was submitted. If so, a User Session is created.
 */
function system_session_login(){
	
	$rmanager = RoleManager::getInstance();
	$role = $rmanager->getMinRole()->getRole();
	$user = null;
	
	if(isset($_POST['form-login-input-username']) && isset($_POST['form-login-input-password']) && (isset($_POST['form-login-sended']))){
		if(!empty($_POST['form-login-input-username']) && !empty($_POST['form-login-input-password']) && !empty($_POST['form-login-sended']) && ($_POST['form-login-sended'] == "fromform")){
			$login = ConversionUtils::encoding($_POST['form-login-input-username']);
			$pass = $_POST['form-login-input-password'];
			if((strlen($login) >= 2) && (strlen($pass) > 2)){
				// password encrypted with MD5
				$md5pass = md5($pass);
				
				// check the existence of the user
				$manager = UserManager::getInstance();
				if($manager->exists($login, $md5pass)){
					// login exists 
					$user = $manager->getUserByLogin($login);
					$manager->updateLastLogin($user->getId());
					
					$role = $user->getRole();
					 
				}else{
					//echo "user don't exist";
				}
			}else{
				//echo "the lenght of the login or the password are not respected : they are too short !";
				$message = new Message(3);
				$message->addMessage("Password et/ou login trop court.");
				$manager = SessionMessageManager::getInstance ();
				$manager->setSessionMessage($message);
			}
		}else{
			//echo "some fields are empty";
			$message = new Message(3);
			$message->addMessage("Au moins un des champs est vide.");
			$manager = SessionMessageManager::getInstance ();
			$manager->setSessionMessage($message);
		}
	}else{
		// check Facebook connection
		$facebook = new Facebook(array(
				'appId'  => FACEBOOK_APPID,
				'secret' => FACEBOOK_SECRET,
		));
		
		// Get User ID
		$fb_id = $facebook->getUser();
		if ($fb_id) {
			$logoutUrl = $facebook->getLogoutUrl();
		} else {
			$loginUrl = $facebook->getLoginUrl();
		}
		
		if ($fb_id) {
			$manager = UserManager::getInstance();
			try{
				$profile = $manager->getUserByFacebookid($fb_id);
				$user = $profile;
				$manager->updateLastLogin($user->getId());		
				$role = $user->getRole();
			}catch(NullObjectException $nue){
				//redirect : in case a first fb-connect, a webkot account maybe already exists for the user. We redirect here to give the opportunity 
				//to the user to match its accounts
				if(isset($_GET['action']) && ($_GET['action'] == 'fb-connect')){
					//let go
				}else{
					URLUtils::redirection(URLUtils::generateURL('user',array('action'=>'fb-connect')));
				}
			}
			
		}else{
			//do nothing : like nobody logged in
			//echo "<br>FAILED!";
		}
	}
	$mmanager = ModuleManager::getInstance();
	$capabilities = $mmanager->getUserRoleCapabilities($role);

	$managerSession = SessionManager::getInstance ();
	$managerSession->initializeSession ( $user, $capabilities, $role );
}

function system_session_logout(){
	if(isset($_GET['logout'])){
		$managerSession = SessionManager::getInstance();
		$managerSession->destroySession();
		if(!empty($_GET['logout'])){
			URLUtils::redirection($_GET['logout']);
		}else{
			URLUtils::redirection(URLUtils::getPreviousURL());
		}
	}
}

/**
 * return the level of the Role of the current Session
 * @return int $level : the level of the Privilege, assciated to the current Session Role
 */
function system_session_privilege(){
	$manager = SessionManager::getInstance();
	$role = $manager->getSessionRole();
	return $rmanager = RoleManager::getInstance()->getLevel($role);
}

//################################
//######## GENERAL UTILS #########
//################################

/**
 * get list of the sub directories of the given directory
 * @param unknown $directory
 */
function system_get_sub_directories($directory){
	$res = array();
	if ($handle = opendir($directory)) {
		while (false !== ($entry = readdir($handle))) {
			if(is_dir($directory . $entry . '/')){
				if($entry != "." && $entry != ".."){
					$res[] = $entry;
				}
			}
		}
		closedir($handle);
	}
	return $res;
}

/**
 * get the directory content
 * @return multitype:string
 */
function system_get_directory_content($directory){
	$res = array();
	if ($handle = opendir($directory)) {
		while (false !== ($entry = readdir($handle))) {
			if($entry != "." && $entry != ".."){
				$res[] = $entry;
			}
		}
		closedir($handle);
	}
	return $res;
}

/**
 * Convert a PHP Object into an Array
 * @param unknown $myObj
 * @return multitype:unknown
 */
function system_to_data_obj($myObj) {
	$ref = new ReflectionClass($myObj);
	$data = array();
	foreach (array_values($ref->getMethods()) as $method) {
		if ((0 === strpos($method->name, "get")) && $method->isPublic()) {
			$name = substr($method->name, 3);
			$name[0] = strtolower($name[0]);
			$value = $method->invoke($myObj);
			if ("object" === gettype($value)) {
				$value = system_to_data_obj($value);
			}
			$data[$name] = $value;//htmlentities($value);
		}
	}
	return $data;
}

/**
 * Convert an array of PHP Object into an array of key-array
 * @param array $array
 * @return multitype:Ambigous <multitype:unknown, multitype:unknown >
 */
function system_array_obj_to_data_array(array $array){
	$data = array();
	for($i=0 ; $i<count($array) ; $i++){
		$tmp = $array[$i];
		$data[] = system_to_data_obj($tmp);
	}
	return $data;
}

/**
 * remove a given directory and its content (recursively)
 * @param unknown $dir
 * @return boolean
 */
function system_remove_directory($dir) { 
   if (is_dir($dir)) { 
     $objects = scandir($dir); 
     foreach ($objects as $object) { 
       if ($object != "." && $object != "..") { 
         if (filetype($dir."/".$object) == "dir") 
         	system_remove_directory($dir."/".$object); 
         else 
         	unlink($dir."/".$object); 
       } 
     } 
     reset($objects); 
     return rmdir($dir); 
   }
   return false;
 }
 
/**
 * return the number of element in a given directory
 * @param string $directory : the directory to analyse
 * @param array $extension : the extension to count
 * @return number : the number of files having the given extensions
 */
function system_count_files_in_directory($directory, $extensions = array()){
	$res = array();
	if ($handle = opendir($directory)) {
		while (false !== ($entry = readdir($handle))) {
			if(is_file($directory ."/". $entry)){
				if(empty($extensions)){
					$res[] = $entry;	
				}else{
					$ext = pathinfo($entry, PATHINFO_EXTENSION);
					if(in_array($ext, $extensions)){
						$res[] = $entry;
					}
				}
			}
		}
		closedir($handle);
	}
	return count($res);
}

function system_menu_content($mod){
	$menu = array();
	foreach($mod as $module){
		$menu[$module->getDisplayedName()] = URLUtils::getModuleURL($module->getName());
	}
	return $menu;
}

/**
 * Set a key-array containing the url of the admin menu
 * @param array $modules
 */
function system_admin_menu_content(array $modules){
	$tab = array();
	foreach($modules as $module){	
		$tab[$module->getDisplayedName()] = $module->getAdminUrl();
	}
	return $tab;
}


/**
 * check if an item of the given array is empty
 * @param array $t
 * @return boolean
 */
function system_is_item_array_empty(array $t){
	foreach($t as $item){
		if($item == "" || empty($item)){
			return true;
		}
	}
	return false;
}


/**
 * check if the given array contain another array in its value
 * @param array $array : the given array to check
 * @return boolean
 */
function system_array_sub_array(array $array){
	for($i = 0 ; $i<count($array) ; $i++){
		if(!empty($array[$i])){
			$item = $array[$i];
			if(is_array($item)){
				return true;
			}
		}
	}
	return false;
}

/**
 * check if the given array contain a null
 * @param array $array
 * @return boolean true if the at least a cell is null. False otherwise.
 */
function system_array_empty_contain(array $array){
	for($i = 0 ; $i<count($array) ; $i++){
		$item = $array[$i];
		if($item === null){
			return true;
		}
	}
	return false;
}

function system_render_tag(array $tags){
	$code = "";
	foreach($tags as $tag){
		$code .=  $tag . "\n";
	}
	return $code;
}

/**
 * return the IP of the client
 * (from webkot v3.2)
 */
function system_ip_client() {
	return $_SERVER['REMOTE_ADDR'];
}


/**
 * Return the year corresponding of the beginning of this schoolar year (for the academic year "2011-2012" --> 2011)
 */
function system_get_begin_year(){
	$today = getdate();
	if($today['mon']>BEGINYEAR_MONTH){
		$year = $today['year'];
	}else{
		if($today['mon']<BEGINYEAR_MONTH){
			$year = $today['year']-1;
		}else{
			if($today['mday']>BEGINYEAR_DAY){
				$year = $today['year'];
			}else{
				$year = $today['year']-1;
			}
		}
	}
	return $year;
}


/**
 * return a html array with the define fields for the given object list
 * @param array $List : the list of object to display
 * @param array $fields : the field of the object to display
 */
function system_html_list_object(array $List, array $fields){
	$html = '<table class="table"><tr><thead>';
	foreach ($fields as $key => $value){
		$html .= '<td><b>' . $key . '</b></td>';
	}
	$html .= '</thead></tr>';
	for($i=0 ; $i<count($List) ; $i++){
		$html .= '<tr>';
		$obj = $List[$i];
		foreach ($fields as $key => $value){
			if(is_array($value)){
				$html .= '<td>';
				$fct = $value[0];
				$link = $value[1];
				$url = $link[0];
				$param = $link[1];
				$html .= '<a href="'.$url.$obj->$param().'">' . $obj->$fct() . '</a>';
				$html .= '</td>';
			}else{
				$html .= '<td>' . $obj->$value() . '</td>';
			}
		}
		$html .= '</tr>';
	}
	$html .= '</table>';
	return $html;	
}


function system_html_action_list(array $list, $class = null){
	$classTag = ($class ? 'class="'.$class.'"' : "");
	$html = "<ul ".$classTag.">";
	foreach ($list as $key => $link){
		$html .= "<li><a href=\"".$link."\">".$key."</a></li>";
	}
	$html .= "</ul>";
	return $html;
}

/**
 * generate a random password with a given length (8 by default)
 * @param number $length : the lenght of the desired random password
 * @return string $pass : return the generated password
 */
function system_generate_password($length = 8) {
	$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	$count = mb_strlen($chars);
	for ($i = 0, $result = ''; $i < $length; $i++) {
		$index = rand(0, $count - 1);
		$result .= mb_substr($chars, $index, 1);
	}
	return $result;
}


//################################
//############ MAILER ############
//################################
/**
 * send a email to the given address with the given message and subject
 * @param unknown $subject
 * @param unknown $message
 * @param unknown $to
 * @param string $from
 * @return boolean
 */
function system_send_mail($subject, $message, $to, $from, $headers = 'Reply-To: admin@webkot.be'){
	$omanager = OptionManager::getInstance();
	$title = $omanager->getOption("site-title");
	
	
	if(SENDMAIL_ACTIVE){
		
		$headers='From: ' .$from. "\r\n" .
				'X-Mailer: PHP/' . phpversion();
		$headers .= 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		
		//$headers .= 'From: '.$from . "\r\n";
		return mail($to, "[".$title."] " . $subject, $message, $headers);	
	}
	return false;
}

//################################
//########## PAGINATION ##########
//################################

/**
 * get the desc value for the pagination, in the GET params
 */
function system_get_desc_pagination(){
	if(isset($_GET['desc']) && !empty($_GET['desc']) && (is_numeric($_GET['desc']))){
		$desc = (int) $_GET['desc'];
	}else{
		$desc = NBR_DEFAULT;
	}
	return $desc;
}

/**
 * get the current page for the pagination, in the GET params
 */
function system_get_page_pagination(){

	if(isset($_GET['page']) && !empty($_GET['page']) && (is_numeric($_GET['page']))){
		$page = (int) $_GET['page'];
	}else{
		$page = 1;
	}
	return $page;

}

/**
 * built the Pagination : list of the page, and the next and prev button (pager)
 * @param $moduleName : the name of the current module
 * @param $params : the list of GET variables
 * @param $occ : total number of occurence
 * @param $desc : the number of occurence to display
 * @param $page : current page number
 */
function system_html_pagination($moduleName, array $params,$occ,$desc,$page, $context){
	//presente les options de pagination en fonction de la page courante $page, du nombre d'�l�ments par pages $desc, et du nombre d'�lements � afficher $occ

	$nbrpage=ceil($occ/$desc);
	$i=1;
	$str =  '';


	$prevURL = "";
	if($page>1){
		$newpage = $page-1;
		//$HTML .=' <li><a href="'.$space.'.php?'.$supp.'&desc=' . $desc . '&page=' . $newpage . '">Precedent</a></li> ';
		//$nextURL = $space.'.php?'.$supp.'&amp;desc=' . $desc . '&amp;page=' . $newpage;
		$params["desc"] = $desc;
		$params["page"] = $newpage; 
		$prevURL = URLUtils::generateURL($moduleName,$params);
	}

	$nextURL = "";
	if($page<$nbrpage){
		$newpage = $page+1;
		//$prevURL = $space.'.php?'.$supp.'&amp;desc=' . $desc . '&amp;page=' . $newpage;
		$params["desc"] = $desc;
		$params["page"] = $newpage;
		$nextURL = URLUtils::generateURL($moduleName,$params);
		//$HTML .='<li> <a href="'.$space.'.php?'.$supp.'&desc=' . $desc . '&page=' . $newpage . '">Suivant</a></li> ';
	}

	$str = $str . system_html_pager($prevURL,$nextURL);

	while($i<=$nbrpage){
		$params["desc"] = $desc;
		$params["page"] = $i;
		if($i == $page){
			$str = $str . '<a href="'.URLUtils::generateURL($moduleName,$params). '">[' . $i . ']</a> ';
		}else{
			$str = $str . '<a href="'.URLUtils::generateURL($moduleName,$params) . '">' . $i . '</a> ';
		}
		++$i;
	}
	$params["page"] = 1;
	$params["desc"] = 10;
	$str .= '<br>Nombre de '.$context.' par page : <a href="'.URLUtils::generateURL($moduleName,$params).'">10</a> '; 
	$params["desc"] = 20;
	$str .= '<a href="'.URLUtils::generateURL($moduleName,$params).'">20</a> ';
	$params["desc"] = 50;
	$str .= '<a href="'.URLUtils::generateURL($moduleName,$params).'">50</a> ';
	$params["desc"] = 100;
	$str .= '<a href="'.URLUtils::generateURL($moduleName,$params).'">100</a> ';
	return $str;
}

/**
 * 
 * @param unknown $url_prev
 * @param unknown $url_next
 * @return string
 */
function system_html_pager($url_prev,$url_next){

	$str = '<ul class="pager">';
	if($url_prev != null || !empty($url_prev)){
		$str = $str . ' <li><a href="'.$url_prev. '"> &larr; Pr&eacute;c&eacute;dent</a></li> ';
	}
	if($url_next != null || !empty($url_next)){
		$str = $str . '<li> <a href="'.$url_next . '">Suivant &rarr;</a></li> ';
	}
	$str = $str . '</ul>';
	
	return $str;
}





/**
 * transform the given array in to html code containing its content, in a list, or in enumeration (with coma)
 * @param array $array : an array containing only string
 * @param boolean $listed
 */
function system_html_array_to_string(array $array, $listed = false){
	if($listed){
		$html = '<ul>';
		foreach ($array as $str){
			$html .= '<li>'.$str.'</li>';
		}
		$html .= '</ul>';
	}else{
		$html = '';
		foreach ($array as $str){
			$html .= $str.',';
		}
	}
	return $html; 
}

