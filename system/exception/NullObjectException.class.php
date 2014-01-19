<?php

class NullObjectException extends Exception{

    function __construct($message = "Votre requete a return&eacute; NULL."){ 
        parent::__construct($message); 
    }

    /**
     * toString
     * @return string
     */
    public function __toString(){
    	$html .= '<strong>Not Found</strong><br>Vous recherchez un objet qui n\'existe pas.<br>';
    	$html .= '<br>DATE : ' . date('Y-m-d H:i');
    	$html .= '<br>MESSAGE : ' . $this->getMessage();
    	if(system_session_privilege() >= 5){	
	    	$html .= '<br>CODE : ' . $this->getCode();
	    	$html .= '<br>FILE : ' . $this->getFile();
	    	$html .= '<br>LINE : ' . $this->getLine();
	    	$html .= '<br>TRACE : ' . $this->getTraceAsString();
    	}
    	return $html;
    }
    
    
    public function toJSON(){
    	return "{message : {type : 3 ; content : 'Vous recherchez un objet qui n\'existe pas. ".$this->getMessage()."'}}";
    }
    
    
}
?>