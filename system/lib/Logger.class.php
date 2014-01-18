<?php


class Logger{
	
	private $log;
	private $loglive;
	
	
	
	
	public function __construct($loggingFile,$loggingLive){
		$this->setLog(array());
		$this->setLogLive($loggingLive);
		
	}
	
	
	public function loginfo($comment){
		$line = new DebuggerLogLine('Info',$comment);		
		$this->log[] = $line;
		if($this->isLogLive()){
			echo $line;
		}
	}
	public function logfatal($comment){
		$line = new DebuggerLogLine('Fatal',$comment);
		$this->log[] = $line;
		if($this->isLogLive()){
			echo $line;
		}
	}
	public function logwarn($comment){
		$line = new DebuggerLogLine('Warn',$comment);
		$this->log[] = $line;
		if($this->isLogLive()){
			echo $line;
		}
	}
	
	
	public function toLogFile(){
		$log = '';
		foreach ($this->getLog() as $line){
			$log = $log . $line . '\n';
		}
		return $log;
	}
	


	public function setLog( $log ){
		$this->log = $log;
	}
	
	public function setLogLive( $activated ){
		$this->loglive = $activated;
	}
	
	public function getLog(){
		return $this->log;
	}
	
	public function isLogLive(){
		return $this->loglive;
	}
	
}


class DebuggerLogLine{
	
	private $datetime;
	private $comment;
	private $logtype;
	
	
	/**
	 * Constructor
	 * @param String $type : the type of logger
	 * @param String $comment : the comment to log
	 */
	public function __construct($type,$comment){
		$this->logtype = $type;
		$this->comment = $comment;
		$this->datetime = date('Y-m-d H:i:s');
	}
	
	public function __toString(){
		return "[".$this->getDatetime()."] " . strtoupper($this->getLogtype()) . " : " . $this->getComment();
	}


	public function setDatetime( $datetime ){
		$this->datetime = $datetime;
	}
	
	public function setComment( $comment ){
		$this->comment = $comment;
	}
	
	public function setLogtype( $logtype ){
		$this->logtype = $logtype;
	}
	
	public function getDatetime(){
		return $this->datetime;
	}
	
	public function getComment(){
		return $this->comment;
	}
	
	public function getLogtype(){
		return $this->logtype;
	}
}