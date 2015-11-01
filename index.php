<?php

// Include config & autres class utiles
require_once("config/configuration.php");
require_once("config/frontend.inc.php");
date_default_timezone_set(TIMEZONE);
error_reporting(DEBUG_MODE);


//######## define the site path and the base url constant ######
$site_path = realpath(dirname(__FILE__));
define ('__SITE_PATH', $site_path . '/');

define('__BASE_PATH_URL', isset($_SERVER['SCRIPT_NAME']) ? dirname($_SERVER['SCRIPT_NAME']) : dirname(getenv('SCRIPT_NAME')));

$baseUrl = ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? 'https://' : 'http://'; // checking if the https is enabled
$baseUrl .= isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : getenv('HTTP_HOST'); // checking adding the host name to the website address
define('__HOST_URL', $baseUrl);
$WebBaseUrl = $baseUrl . isset($_SERVER['SCRIPT_NAME']) ? dirname($_SERVER['SCRIPT_NAME']) : dirname(getenv('SCRIPT_NAME')); // adding the directory name to the created url and then returning it.
//define('__BASE_URL', $WebBaseUrl . '/');
define('__BASE_URL', 'http://localhost/Web%20Developpement/Workspace/webkot4/');

//####### include the system ########
include 'vendor/autoload.php';
include 'system/init.php';


//######## SYSTEM FUNCTIONS ##########
include("system/functions.inc.php");

//########### INTERFACE #############
include(DIR_SYST_INTERFACE."iGeneralTemplate.php");
include(DIR_SYST_INTERFACE."iView.php");
include(DIR_SYST_INTERFACE."iWidget.php");
include(DIR_SYST_INTERFACE."iPlugin.php");
include(DIR_SYST_INTERFACE."iModuleLoader.php");
require_once(DIR_SYST_INTERFACE . "iLayout.php");
require_once(DIR_SYST_INTERFACE . "iLayout2col.php");
require_once(DIR_SYST_INTERFACE . "iLayout1col.php");
require_once(DIR_SYST_INTERFACE . "iTemplate.php");


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
include(DIR_SYST_LIB."View.class.php");
include(DIR_SYST_LIB."Plugin.class.php");
include(DIR_SYST_LIB."jformer.php");
include(DIR_SYST_LIB."recaptchalib.php");
include(DIR_SYST_LIB."User.class.php");
include(DIR_SYST_LIB."UserManager.class.php");
include(DIR_SYST_LIB."Option.class.php");
include(DIR_SYST_LIB."OptionManager.class.php");
include(DIR_SYST_LIB."AbstractTemplate.class.php");


//######## FACEBOOK LIB #############
require 'system/facebook/facebook.php';

//########### EXCEPTIONS #############
require_once(DIR_SYST_EXCEPTION . "SQLException.class.php");
require_once(DIR_SYST_EXCEPTION . "DatabaseException.class.php");
require_once(DIR_SYST_EXCEPTION . "NullObjectException.class.php");
require_once(DIR_SYST_EXCEPTION . "InvalidURLException.class.php");
require_once(DIR_SYST_EXCEPTION . "AccessRefusedException.class.php");

require_once(DIR_SYST_EXCEPTION . "ExceptionHandler.class.php");
set_exception_handler(array("ExceptionHandler", "handleUncaughtException"));



// control the User Session
//system_session_logout();
//system_session_login();


// Initialize the logger
$logger = new Logger(LOGGING_FILE, false);
$logger->loginfo("Logger initialized");


//####### OPTIONS
$omanager = OptionManager::getInstance();
$options = array();
$options["site-title"] = $omanager->getOption('site-title');
$options["site-edito"] = $omanager->getOption('site-edito');
$options["site-footer"] = $omanager->getOption('site-footer');
$options["site-closed-message"] = $omanager->getOption('site-closed-message');
$options["site-metatags"] = $omanager->getOption('site-metatags');

//###### TEMPALTE
include DIR_TEMPLATE . 'coffee/load.inc.php';
//include DIR_TEMPLATE . 'modern-business/load.inc.php';
$template = new Template($options);
$template->addStyle('<link rel="alternate" type="application/rss+xml" title="Webkot.be : les dernieres activites" href="'.RSS_FILE.'" />');
system_load_js_file(DIR_SYST_JS, $template);
$analystics = "<script async=\"async\">
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

        ga('create', 'UA-45621288-1', 'webkot.be');
        ga('send', 'pageview');
</script>";
$template->addJSHeader($analystics);
$logger->loginfo("Template loaded");



//####### CLOSED
if($omanager->getOption('site-closed')){
	if(!system_session_can_access_admin()){
		//include("config/closed.html");
		$template->renderClosed();
		exit();
	}
}


//###### MODULE 
try{
	$logger->loginfo("Before the module logic");
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
					include system_load_module_frontend($module->getName(), $template);
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
		
		//load module;
		$logger->loginfo("Module ".$module->getName()." is gonna be loaded (default)");
		include system_load_module_frontend($module->getName(), $template);
		$logger->loginfo("Module ".$module->getName()." is loaded (default)");
	}
}catch(Exception $e){
	//$template->initLayout();
	$message = new Message(3);
	$message->addMessage($e);
	$message->addMessage('<a href="javascript:history.back()" class="btn btn-danger">Precedent</a>');
	$template->setContent($message);
}


//###### WIDGETS 
$logger->loginfo("Load the widgets");
$Wmanager = WidgetManager::getInstance();
$dependancies = $Wmanager->getWidgetDependencies($module->getId());
for($i=0 ; $i<count($dependancies) ; $i++){
	$dep = $dependancies[$i];
	if($dep["ModuleName"]){
		system_load_module_model($dep["ModuleName"]);
	}
	system_load_class(DIR_WIDGET, $dep["classname"]);
}
$widgets = $Wmanager->getWidgets($module->getId());
$template->setWidgetSidebar($widgets);
//footer widgets
$dependanciesFooter = $Wmanager->getWidgetFooterDependencies();
for($i=0 ; $i<count($dependanciesFooter) ; $i++){
	$dep = $dependanciesFooter[$i];
	if($dep["ModuleName"]){
		system_load_module_model($dep["ModuleName"]);
	}
	system_load_class(DIR_WIDGET, $dep["classname"]);
}
$widgets = $Wmanager->getFooterWidgets();
$template->setWidgetFooter($widgets);

//#### MENU
$logger->loginfo("Get the module of the menu");
$manager = ModuleManager::getInstance();
$modmenu = $manager->getFrontendMenuModule();
$template->setMenuContent(system_menu_content($modmenu));


$template->render();
$logger->loginfo("Template rendered");


if(LOGGING_FILE){
	echo $logger->toLogFile();	
}


