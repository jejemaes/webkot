<?php


class SQLException extends Exception{

    /* The SQL associated with this exception */ 
    private $_sql; 
    
    private $_decription;
     
    /* Create a new SQLException */ 
    function __construct($message, $code = "", $query = null, $description = null){ 
        parent::__construct($message, $code); 
        if ($query != null) 
            $this->_sql = $query; 
            
        if($description != null){
           $this->_decription = $description;	
        }
    } 
    
  
     
    
    function getQuery(){ 
        return $this->_sql; 
    } 
    
    function getDescription(){
    	return $this->_decription;
    }
     
    /* Convert the Exception to String */ 
    function __toString() { 
    	$html .= '<strong>SQL Exception</strong>';
    	$html .= '<br>DATE : ' . date('Y-m-d H:i');
    	$html .= '<br>DESCRIPTION : ' . $this->getDescription();
    	if(system_session_privilege() >= 5){
    		$html .= '<br>MESSAGE : ' . $this->getMessage();
    		$html .= '<br>CODE : ' . $this->getCode();
    		$html .= '<br>SQL : ' . $this->getQuery();
    		$html .= '<br>FILE : ' . $this->getFile();
    		$html .= '<br>LINE : ' . $this->getLine();
    		$html .= '<br>TRACE : ' . $this->getTraceAsString();
    	}
    	return $html;
    } 
}
?>