<?php



// include config & autres class utiles
require_once("../config/configuration.php");
require_once("../config/backend.inc.php");
date_default_timezone_set(TIMEZONE);
error_reporting(DEBUG_MODE);


//######## SYSTEM FUNCTINOS ##########
include("../system/functions.inc.php");


//########## SYSTEM INTERFACE ########
include(DIR_SYST_INTERFACE."iAdminView.php");
include(DIR_SYST_INTERFACE."iPlugin.php");
include(DIR_SYST_INTERFACE ."iGeneralTemplate.php");
include(DIR_SYST_INTERFACE ."iAdminTemplate.php");
include(DIR_SYST_INTERFACE."iModuleLoader.php");

//########### SYSTEM LIB #############
include(DIR_SYST_LIB."ConversionUtils.class.php");
include(DIR_SYST_LIB."URLUtils.class.php");
include(DIR_SYST_LIB."FormatUtils.class.php");
include(DIR_SYST_LIB."Database.class.php");
include(DIR_SYST_LIB."Widget.class.php");
include(DIR_SYST_LIB."WidgetManager.class.php");
include(DIR_SYST_LIB."Module.class.php");
include(DIR_SYST_LIB."ModuleManager.class.php");
include(DIR_SYST_LIB."Role.class.php");
include(DIR_SYST_LIB."RoleManager.class.php");
include(DIR_SYST_LIB."Session.class.php");
include(DIR_SYST_LIB."SessionManager.class.php");
include(DIR_SYST_LIB."Logger.class.php");
include(DIR_SYST_LIB."Error.class.php");
include(DIR_SYST_LIB."Message.class.php");
include(DIR_SYST_LIB."SessionMessageManager.class.php");
include(DIR_SYST_LIB."jformer.php");
include(DIR_SYST_LIB."recaptchalib.php");
include(DIR_SYST_LIB."User.class.php");
include(DIR_SYST_LIB."UserManager.class.php");
include(DIR_SYST_LIB."Plugin.class.php");
include(DIR_SYST_LIB."Option.class.php");
include(DIR_SYST_LIB."OptionManager.class.php");

include(DIR_SYST_LIB . "AdminView.class.php");
include(DIR_SYST_LIB . "AbstractAdminTemplate.class.php");

//######## FACEBOOK LIB #############
require '../system/facebook/facebook.php';

//########### EXCEPTIONS #############
require_once(DIR_SYST_EXCEPTION . "SQLException.class.php");
require_once(DIR_SYST_EXCEPTION . "DatabaseException.class.php");
require_once(DIR_SYST_EXCEPTION . "NullObjectException.class.php");
require_once(DIR_SYST_EXCEPTION . "InvalidURLException.class.php");
require_once(DIR_SYST_EXCEPTION . "AccessRefusedException.class.php");
require_once(DIR_SYST_EXCEPTION . "ExceptionHandler.class.php");
set_exception_handler(array("ExceptionHandler", "handleUncaughtException"));


// control the User Session
system_session_login();
system_session_logout();



// Initialize the logger
$logger = new Logger(LOGGING_FILE, false);


//####### OPTIONS
$omanager = OptionManager::getInstance();
$options = array();
$options["site-title"] = $omanager->getOption('site-title');
$options["site-metatags"] = $omanager->getOption('site-metatags');

// Initialize the template
include DIR_TEMPLATE . 'sb-admin/load.inc.php';
$template = new Template($options);
system_load_js_file(DIR_SYST_JS, $template);


if(!system_session_can_access_admin()){
	$template->renderClosed();
	exit();
}




// Set the menu content
$logger->loginfo("Before loading Template");
$managerMod = ModuleManager::getInstance();
$modules = $managerMod->getBackendMenuModule();
$template->setMenuContent(($modules));


$logger->loginfo("Before the module logic");
try{
	// Identify the module
	if(isset($_GET['mod'])){
		$manager = ModuleManager::getInstance();
		try{
			$module = $manager->getModule($_GET['mod']);
			if($module == null){
				throw new InvalidURLException("Le module que vous demandez n'existe pas.");
			}else{
				// import module
				if ($module->getIsActive()) {
					//load module;
					$logger->loginfo("Module ".$module->getName()." is gonna be loaded");
					
					//load module;
					$loader = $module->getLoader();
					include DIR_MODULE . $module->getLocation() . $loader . '.class.php';
					include $loader::loadAdminModule(DIR_MODULE . $module->getLocation());
				}else{
					throw new InvalidURLException("Le module est actuellement inactif.");
				}
			}
		}catch(NullObjectException $e){
			throw new InvalidURLException("Le module que vous demandez n'existe pas.");
		}
	}else{
		$manager = ModuleManager::getInstance();
		$module = $manager->getModule('accueil');
		//init the capbilities for the session
		$logger->loginfo("Module ".$module->getName()." is gonna be loaded");
		
		//load module;
		$loader = $module->getLoader();
		include DIR_MODULE . $module->getLocation() . $loader . '.class.php';
		include $loader::loadAdminModule(DIR_MODULE . $module->getLocation());
	}	
}catch(Exception $e){
	//$template->initLayout();
	$message = new Message(3);
	$message->addMessage($e);
	$message->addMessage('<a href="javascript:history.back()" class="btn btn-danger">Precedent</a>');
	$template->setContent($message);
}
$logger->loginfo("After the module logic");



$template->render();
