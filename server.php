<?php

error_reporting(0);


// Include config & autres class utiles
require_once("config/configuration.php");
require_once("config/frontend.inc.php");

//######## SYSTEM FUNCTINOS ##########
include("system/functions.inc.php");


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

include(DIR_SYST_INTERFACE."iGeneralTemplate.php");
include(DIR_SYST_INTERFACE."iView.php");
include(DIR_SYST_INTERFACE."iWidget.php");
include(DIR_SYST_INTERFACE."iPlugin.php");


//########### EXCEPTIONS #############
require_once(DIR_SYST_EXCEPTION . "SQLException.class.php");
require_once(DIR_SYST_EXCEPTION . "DatabaseException.class.php");
require_once(DIR_SYST_EXCEPTION . "NullObjectException.class.php");
require_once(DIR_SYST_EXCEPTION . "InvalidURLException.class.php");
require_once(DIR_SYST_EXCEPTION . "AccessRefusedException.class.php");
require_once(DIR_SYST_EXCEPTION . "ExceptionHandler.class.php");
set_exception_handler(array("ExceptionHandler", "handleUncaughtException"));


//system_session_login();
try{
	if(isset($_REQUEST['module']) && !empty($_REQUEST['module'])){
		$module = $_REQUEST['module'];
		$manager = ModuleManager::getInstance();
		$module = $manager->getModule($module);
		if($module != null){
			//load the file
			include(DIR_MODULE . $module->getLocation() . 'server.inc.php');
		}		
	}else{
		echo "{message : {type : 'error' , content : 'Le module est non specifie.'}}";
	}	
}catch(Exception $e){
	if(method_exists(get_class($e),'toJSON')){
		echo $e->toJSON();
	}else{
		echo "{message : {type : 'error' , content : '".$e->getMessage()."'}}";
	}
}

