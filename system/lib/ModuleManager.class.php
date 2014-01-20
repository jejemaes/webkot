<?php

/*
 * Created on 9 Aug. 2013
*
* MAES Jerome, Webkot 2012-2013
* Class description : manage the Module Object (bridge with the DB). Only for the frontend
*
* Convention : the setters & getters are lowercase, but their first letter is a capital letter
*
*/


class ModuleManager{
	
	protected static $_instance;
	private $_db; // Instance of Database
	private $_apc;
	
	const APC_MODULES_ALL = 'module-all-modules';
	const APC_MODULES_MENU = 'module-menu-modules';
	const APC_MODULES_FRONTEND_MENU = 'module-frontend-modules';
	const APC_MODULES_BACKEND_MENU = 'module-backend-modules';
	
	/**
	 * getInstance
	 * @return ActivityManager $instance : the instance of ActivityManager
	 */
	public static function getInstance(){
		if (!isset(self::$_instance)) {
			$c = __CLASS__;
			self::$_instance = new $c;
			self::$_instance->__construct();
		}
		return self::$_instance;
	}
	
	/**
	 * Constructor
	 */
	public function __construct(){
		$this->_db = Database::getInstance();
		$this->_apc = ((extension_loaded('apc') && ini_get('apc.enabled')) ? true : false);
	}
	
	
	
	/**
	 * get a specified Module
	 * @param string $name : the name of the Module
	 * @return Module $mod : the specified Module
	 * @throws SQLException : this exception is raised if the Query is refused
	 * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
	 * @throws NullObjectException : this exception is raised when the specified Object didn't exist
	 */
	public function getModule($name){
		try {
			$sql = "SELECT id as id, name as name, displayed_name as displayedName, location as location, is_active as isActive, in_menu as inMenu, config as config, place as place, loader as loader, isbackend as isbackend, isfrontend as isfrontend  FROM module WHERE name= :name";
			$stmt = $this->_db->prepare($sql);
			$stmt->execute(array( 'name' => $name));
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
				throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir un module specifie");
			}
			$data = $stmt->fetch(PDO::FETCH_ASSOC);
			if(empty($data)){
				throw new NullObjectException();
			}
			$mod = new Module($data);
			return $mod;
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir un module specifie");
		}
	}
	
	/**
	 * get a specified Module
	 * @param string $name : the name of the Module
	 * @return Module $mod : the specified Module
	 * @throws SQLException : this exception is raised if the Query is refused
	 * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
	 * @throws NullObjectException : this exception is raised when the specified Object didn't exist
	 */
	public function getModuleById($id){
		try {
			$sql = "SELECT id as id, name as name, displayed_name as displayedName, location as location, is_active as isActive, in_menu as inMenu, config as config, place as place, loader as loader, isbackend as isbackend, isfrontend as isfrontend  FROM module WHERE id= :id";
			$stmt = $this->_db->prepare($sql);
			$stmt->execute(array( 'id' => $id));
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
				throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir un module specifie");
			}
			$data = $stmt->fetch(PDO::FETCH_ASSOC);
			if(empty($data)){
				throw new NullObjectException();
			}
			$mod = new Module($data);
			return $mod;
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir un module specifie");
		}
	}
	
	
	
	/**
	 * get all the Module of the Frontend menu
	 * @return array $mod : all the active Module
	 * @throws SQLException : this exception is raised if the Query is refused
	 * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
	 */
	public function getFrontendMenuModule(){
		if($this->_apc && apc_exists(self::APC_MODULES_FRONTEND_MENU)){
			return apc_fetch(self::APC_MODULES_FRONTEND_MENU);
		}else{
			try {
				$sql = "SELECT id as id, name as name, displayed_name as displayedName, location as location, is_active as isActive, in_menu as inMenu, config as config, place as place, loader as loader, isbackend as isbackend, isfrontend as isfrontend FROM module WHERE in_menu = '1' and is_active = '1' and isfrontend = '1' ORDER BY place ASC";
				$stmt = $this->_db->prepare($sql);
				$stmt->execute();
				if($stmt->errorCode() != 0){
					$error = $stmt->errorInfo();
					throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste des modules du menu Frontend.");
				}
				$mod = array();
				while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
					$mod[] = new Module($data);
				}
				if($this->_apc){
					apc_store(self::APC_MODULES_FRONTEND_MENU, $mod, 86000);
				}
				return $mod;
			}catch(PDOException $e){
				throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir la liste des modules du menu Frontend");
			}
		}
	}
	
	
	/**
	 * get all the Module of the Backend menu
	 * @return array $mod : all the active Module
	 * @throws SQLException : this exception is raised if the Query is refused
	 * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
	 */
	public function getBackendMenuModule(){
		if($this->_apc && apc_exists(self::APC_MODULES_BACKEND_MENU)){
			return apc_fetch(self::APC_MODULES_BACKEND_MENU);
		}else{
			try {
				$sql = "SELECT id as id, name as name, displayed_name as displayedName, location as location, is_active as isActive, in_menu as inMenu, config as config, place as place, loader as loader, isbackend as isbackend, isfrontend as isfrontend FROM module WHERE is_active = '1' and isbackend = '1' ORDER BY place ASC";
				$stmt = $this->_db->prepare($sql);
				$stmt->execute();
				if($stmt->errorCode() != 0){
					$error = $stmt->errorInfo();
					throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste des modules du menu Backend.");
				}
				$mod = array();
				while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
					$mod[] = new Module($data);
				}
				if($this->_apc){
					apc_store(self::APC_MODULES_BACKEND_MENU, $mod, 86000);
				}
				return $mod;
			}catch(PDOException $e){
				throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir la liste des modules du menu Backend");
			}
		}
	}
	
	/**
	 * get all the Module
	 * @return array $mod : all the Module
	 * @throws SQLException : this exception is raised if the Query is refused
	 * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
	 */
	public function getAllModule(){
		if($this->_apc && apc_exists(self::APC_MODULES_ALL)){
			return apc_fetch(self::APC_MODULES_ALL);
		}else{		
			try {
				$sql = "SELECT id as id, name as name, displayed_name as displayedName, location as location, is_active as isActive, in_menu as inMenu, config as config, place as place, loader as loader, isbackend as isbackend, isfrontend as isfrontend FROM module ORDER BY place ASC";
				
				$stmt = $this->_db->prepare($sql);
				$stmt->execute(array());
				if($stmt->errorCode() != 0){
					$error = $stmt->errorInfo();
					var_dump($error);
					throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste des modules");
				}
				$mod = array();
				while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
					$mod[] = new Module($data);
				}
				if($this->_apc){
					apc_store(self::APC_MODULES_ALL, $mod, 86000);
				}
				return $mod;
			}catch(PDOException $e){
				throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir la liste des modules");
			}
		}
	}
	
	
	
	/**
	 * 
	 * @param unknown $role
	 * @return multitype:
	 */
	public function getUserRoleCapabilities($role){
		$modules = $this->getAllModule();
		$capabilities = array();
		foreach ($modules as $module){
			$cap = $module->getCapabilities();
			if(!empty($cap[$role])){
				$capabilities = array_merge($capabilities, $cap[$role]);
			}
		}
		return $capabilities;
	}
	
	/**
	 * get the url for the suscriber user (when connected) of all the modules
	 * @return multitype:string
	 */
	public function getSubscriberModuleAction(){
		$modules = $this->getAllModule();
		$action = array();
		for($i=0 ; $i<count($modules) ; $i++){
			$mod = $modules[$i];
			if($mod->getIsActive()){
				$param = $mod->getParameters();
				if(!empty($param["subscriber-url"])){	
					$suburl = $param["subscriber-url"];
					if($suburl){
						foreach($suburl as $name => $vals){
							$url = "index.php?mod=".$mod->getName();
							foreach($vals as $key => $value){
								$url .= "&".$key."=".$value;
							}
							$action[$name] = $url;
						}
					}
				}
			}
		}
		return $action;
	}
	
	
	
	
	public function updateModule($nameid, $name, $menu, $actif){
 		try{
	 		$sql = "UPDATE module SET displayed_name = :name, in_menu  = :menu, is_active = :actif WHERE name=:id";
 			$stmt = $this->_db->prepare($sql);
 			$n = $stmt->execute(array('name' => $name, 'menu' => $menu, 'actif' => $actif, 'id' => $nameid));
	        if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'effectuer la mise a jour du module");
		    }
		    if($this->_apc){
		    	apc_delete(self::APC_MODULES_FRONTEND_MENU);
		    	apc_delete(self::APC_MODULES_BACKEND_MENU);
		    	apc_delete(self::APC_MODULES_ALL);
		    }
			return ($n > 0);
 		}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'effectuer la mise a jour du module");
        }
 	}
	
 	
 	public function updateModuleConfig($name, array $config){
 		$config = json_encode($config);
 		try{
 			$sql = "UPDATE module SET config = :config WHERE name=:name";
 			$stmt = $this->_db->prepare($sql);
 			$n = $stmt->execute(array('name' => $name, 'config' => $config));
 			if($stmt->errorCode() != 0){
 				$error = $stmt->errorInfo();
 				throw new SQLException($error[2], $error[0], $sql, "Impossible d'effectuer la mise a jour de la config du module");
 			}
 			if($this->_apc){
 				apc_delete(self::APC_MODULES_FRONTEND_MENU);
 				apc_delete(self::APC_MODULES_BACKEND_MENU);
 				apc_delete(self::APC_MODULES_ALL);
 			}
 			return ($n > 0);
 		}catch(PDOException $e){
 			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'effectuer la mise a jour de la config du module");
 		}
 	}
 	

	
	
}