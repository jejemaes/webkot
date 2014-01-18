<?php

class DatabaseException extends Exception{

    private $_pdocode;
    private $_pdomessage;
    private $_description;
    

    function __construct($code, $message, $descri){ 
        parent::__construct($message, $code); 
        
        $this->_description = $descri;
    } 
    
   
    
    /**
     * toString
     * @return string
     */
    public function __toString(){
    	$html .= '<strong>Database Exception</strong><br>Le serveur de la base de donnees a retourne une erreur.<br>';
    	$html .= '<br>DATE : ' . date('Y-m-d H:i');
    	$html .= '<br>DESCRIPTION : ' . $this->getDescription();
    	if(system_session_privilege() >= 5){
	    	$html .= '<br>MESSAGE : ' . $this->getMessage();
	    	$html .= '<br>PDO MESSAGE : ' . $this->getPdomessage();
    		$html .= '<br>CODE : ' . $this->getCode();
    		$html .= '<br>FILE : ' . $this->getFile();
    		$html .= '<br>LINE : ' . $this->getLine();
    		$html .= '<br>TRACE : ' . $this->getTraceAsString();
    	}
    	return $html;
    }
	    
	     
	
	public function setPdocode( $_pdocode )
	{
		$this->_pdocode = $_pdocode;
	}
	
	public function setPdomessage( $_pdomessage )
	{
		$this->_pdomessage = $_pdomessage;
	}
	
	public function setDescription( $_description )
	{
		$this->_description = $_description;
	}
	
	public function getPdocode()
	{
	 	return $this->_pdocode;
	}
	
	public function getPdomessage()
	{
	 	return $this->_pdomessage;
	}
	
	public function getDescription()
	{
	 	return $this->_description;
	}

}
?>