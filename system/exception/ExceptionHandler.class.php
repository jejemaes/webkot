<?php
/*
 * Created on 3 Nov. 2012
 *
 * MAES Jerome, Webkot 2011-2012
 * Class description : 	Manager the Exception (log or not, throw, the type, ...). 
 * 						For the Front-end Site
 *
 */
 
class ExceptionHandler {


	/**
	 * function call when an Exception if catch, and return a Message
	 * @param Exception $e : the caught Exception 
	 */
    public static function handleException(Exception $e){
    	
    }
    
    
    /**
     * function call when an Exception is rised and NOT caught (by set_exception_handler)
     * @param Exception $e : the uncaught Exception
     */
    public static function handleUncaughtException(Exception $e){
    	
    	$title = "Exception Inconnue";
    	$message = " ...";
    	
    	$islogged = false;
    	
    	if($e instanceof DatabaseException){
    		$title = "Database Exception";
    		$message = "Description : " . $e->getDescription();
    		$message .= '<br clear>';
    		$message .= "Commentaire : Une partie du serveur n'est plus active. Veuillez contacter le Webkot afin que cette erreur soit reparee dans les plus brefs delais. Merci.";
    		
    		$islogged = true;
    		ExceptionHandler::logDatabaseException(LOG_OTHER,$e);    		
    	}
    	
    	if($e instanceof SQLException){
    		$title = "SQL Exception";
    		$message = "Description : " . $e->getDescription();
    		$message .= '<br clear>';
    		$message .= "Commentaire : Si cela se reproduit souvent, contactez le Webkot au moyen du formulaire de contact (copier/coller l'error, et envoyer la). Merci.";
    		
    		$islogged = true;
    		ExceptionHandler::logSQLException(LOG_SQL,$e);
    	}
    	
    	if($e instanceof NullObjectException){
    		$title = "Null Object Exception";
    		$message = "Description :  Vous avez demande un objet qui n'existe pas.";
    	}
    	
    	if($e instanceof InvalidURLException){
    		$title = "Invalid URL Exception";
    		$message = "Commentaire :  Vous avez fait une erreur d'URL. Il n'y a rien a voir ici, ou alors vous n'avez pas les privileges requis pour acceder a cette page.";
    		$message .= "<br clear>Description : " . $e->getDescription();
    		
    		$islogged = true;
    		ExceptionHandler::logInvalidURLException(LOG_OTHER,$e);
    	}
    	
    	if(!$islogged){
    		ExceptionHandler::logException(LOG_OTHER,$e);
    		$message = "Description : " . $e->getMessage();
    	}
    	
    	echo "UNCAUGHT EXCEPTION : <br>".$message;
    
    }
    
    
    
    
    //########### LOG FUNCTIONS ###########
    
    /**
     * log the specified Exception in a givent log
     * @param Exception $e : the Exception
     * @param string $logPath : the log file
     */
    public static function logSQLException($logPath, SQLException $e){
    	error_log("\n".'#################### '.get_class($e).' ##################'."\n", 3, $logPath);
    	error_log('DATE : ' . date('Y-m-d H:i')."\n", 3, $logPath);
    	error_log('CODE : ' . $e->getCode()."\n", 3, $logPath);
    	error_log('MESSAGE : ' . $e->getMessage()."\n", 3, $logPath);
    	error_log('FILE : ' . $e->getFile()."\n", 3, $logPath);
    	error_log('LINE : ' . $e->getLine()."\n", 3, $logPath);
    	error_log('QUERY : ' . $e->getQuery()."\n", 3, $logPath);
    	error_log('DESCRIPTION : ' . $e->getDescription()."\n", 3, $logPath);
    	error_log('TRACE : ' . $e->getTraceAsString()."\n", 3, $logPath);
    } 
    
    /**
     * log the specified Exception in a givent log
     * @param Exception $e : the Exception
     * @param string $logPath : the log file
     */
    public static function logDatabaseException($logPath, DatabaseException $e){
    	error_log("\n".'#################### '.get_class($e).' ##################'."\n", 3, $logPath);
    	error_log('DATE : ' . date('Y-m-d H:i')."\n", 3, $logPath);
    	error_log('CODE : ' . $e->getCode()."\n", 3, $logPath);
    	error_log('MESSAGE : ' . $e->getMessage()."\n", 3, $logPath);
    	error_log('FILE : ' . $e->getFile()."\n", 3, $logPath);
    	error_log('LINE : ' . $e->getLine()."\n" , 3, $logPath);
    	error_log('PDO MESSAGE : ' . $e->getPdomessage()."\n", 3, $logPath);
    	error_log('DESCRIPTION : ' . $e->getDescription()."\n", 3, $logPath);
    	error_log('TRACE : ' . $e->getTraceAsString()."\n", 3, $logPath);
    } 
    
    /**
     * log the specified Exception in a givent log
     * @param Exception $e : the Exception
     * @param string $logPath : the log file
     */
    public static function logInvalidURLException($logPath, InvalidURLException $e){
    	error_log("\n".'#################### '.get_class($e).' ##################'."\n", 3, $logPath);
    	error_log('DATE : ' . date('Y-m-d H:i')."\n", 3, $logPath);
    	error_log('CODE : ' . $e->getCode()."\n", 3, $logPath);
    	error_log('MESSAGE : ' . $e->getMessage()."\n", 3, $logPath);
    	error_log('FILE : ' . $e->getFile()."\n", 3, $logPath);
    	error_log('LINE : ' . $e->getLine()."\n", 3, $logPath);
    	error_log('URL : ' . $e->getUrl()."\n", 3, $logPath);
    	error_log('DESCRIPTION : ' . $e->getDescription()."\n", 3, $logPath);
    	error_log('TRACE : ' . $e->getTraceAsString()."\n", 3, $logPath);
    }
    
    
    /**
     * log the specified Exception in a given log
     * @param Exception $e : the Exception
     * @param string $logPath : the log file
     */
    public static function logException($logPath, Exception $e){
    	error_log("\n".'#################### '.get_class($e).' ##################'."\n", 3, $logPath);
    	error_log('DATE : ' . date('Y-m-d H:i')."\n", 3, $logPath);
    	error_log('CODE : ' . $e->getCode()."\n", 3, $logPath);
    	error_log('MESSAGE : ' . $e->getMessage()."\n", 3, $logPath);
    	error_log('FILE : ' . $e->getFile()."\n", 3, $logPath);
    	error_log('LINE : ' . $e->getLine()."\n", 3, $logPath);
    	//error_log('DESCRIPTION : ' . $e->getDescription()."\n", 3, $logPath);
    	error_log('TRACE : ' . $e->getTraceAsString()."\n", 3, $logPath);
    }
    
}
?>