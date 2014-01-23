<?php


class OptionManager{
	
	protected static $_instance;
	private $_db;
	private $_apc;
	private $_optionsArray;
	
	public $apc_options;

	
	/**
	 * getInstance
	 * @return WidgetManager $instance : the instance of WidgetManager
	 */
	public static function getInstance(){
		if (!isset(self::$_instance)) {
			$c = __CLASS__;
			self::$_instance = new $c;
			self::$_instance->__construct();
		}
		return self::$_instance;
	}
	
	
	public function  __construct(){
		$this->_db = Database::getInstance();
		$this->_apc = ((extension_loaded('apc') && ini_get('apc.enabled')) && APC_ACTIVE ? true : false);
		if($this->apc_options){
			$this->apc_options = APC_PREFIX . "options";
		}
		$options = $this->getOptions();
		$tmp = array();
		foreach ($options as $option){
			$tmp[$option->getKey()] = $option->getValue();
		}
		$this->setOptionsArray($tmp);
	}
	
	
	public function getOption($key){
		if(array_key_exists($key, $this->getOptionsArray())){
			if($this->getOptionObject($key)->getType() == 'boolean'){
				$tmp = $this->getOptionsArray();
				return $tmp[$key] === 'true'? true: false;
			}
			$tmp = $this->getOptionsArray();
			return $tmp[$key];
		}else{
			throw new NullObjectException("L'Option <i>".$key."</i> demand&eacute;e est inexistante.");
		}
	}
	
	
	public function getOptions(){
		if(false && $this->_apc && apc_exists($this->apc_options)){
			return apc_fetch($this->apc_options);
		}else{
			try {
				$sql = "SELECT * FROM options ORDER BY options.key ASC";
				$stmt = $this->_db->prepare($sql);
				$stmt->execute();
				if($stmt->errorCode() != 0){
					$error = $stmt->errorInfo();
					throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste des Options");
				}
				$options = array();
				while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
					$options[] = new Option($data);
				}
				if($this->_apc){
					apc_store($this->apc_options, $options, 86000);
				}
				return $options;
			}catch(PDOException $e){
				throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir la liste des Options");
			}
		}
	}
	
	public function getOptionObject($key){
		foreach($this->getOptions() as $option){
			if($option->getKey() == $key){
				return $option;
			}
		}
		return null;
	}


	public function setOptionsArray( $_optionsArray ){
		$this->_optionsArray = $_optionsArray;
	}
	
	public function getOptionsArray(){
		return $this->_optionsArray;
	}
	
	
	
	
	public function update(array $options){
		try{	
			$sql = "UPDATE options SET options.value = :value WHERE options.key=:key";
			$stmt = $this->_db->prepare($sql);
			foreach($options as $key => $value){
				$n = $stmt->execute(array('value' => $value, 'key' => $key));
				if($stmt->errorCode() != 0){
					$error = $stmt->errorInfo();
					throw new SQLException($error[2], $error[0], $sql, "Impossible d'effectuer la mise a jour");
				}
			}
			if($this->_apc){
				apc_delete($this->apc_options);
			}
			return ($n > 0);
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'effectuer la mise a jour");
		}
	}

	
	
}