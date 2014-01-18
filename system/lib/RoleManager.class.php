<?php


class RoleManager {
	
	protected static $_instance;
	private $_db; // Instance of Database
	private $_apc;
	
	/**
	 * getInstance
	 * @return SessionManager $instance : the instance of SessionManager
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
	 * get the list of the roles
	 * @throws SQLException
	 * @throws DatabaseException
	 * @return array $roles : an array of Role Object
	 */
	public function getRoleList(){
		try {
			$sql = "SELECT id as id, role as role, level as level FROM privilege ORDER BY level ASC";
			$stmt = $this->_db->prepare($sql);
			$stmt->execute(array());
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
				throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste des roles");
			}
			$roles = array();
			while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
				$roles[] = new Role($data);
			}
			return $roles;
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir la liste des roles");
		}
	}
	
	
	/**
	 * control if the given capability is in the list of the Session Capabilities
	 * @param String $capability
	 * @return boolean
	 */
	public function hasCapabilitySession($capability){
		$manager = SessionManager::getInstance();
		$capabilities = $manager->getCapabilities();
		return in_array($capability,$capabilities);
	}
	
	
	/**
	 * get the level associated to a given rolename
	 * @param String $rolename : the name of the Role
	 * @throws SQLException
	 * @throws NullObjectException
	 * @throws DatabaseException
	 */
	public function getLevel($rolename){
		try {
			$sql = "SELECT id as id, role as role, level as level FROM privilege WHERE role = :name";
			$stmt = $this->_db->prepare($sql);
			$stmt->execute(array('name' => $rolename));
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
				throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir le role " . $rolename);
			}
			$data = $stmt->fetch(PDO::FETCH_ASSOC);
			if(empty($data)){
				throw new NullObjectException();
			}
			$role = new Role($data);
			return $role->getLevel();
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir le role " . $rolename);
		}
	}
	
	
	
	public function getMinRole(){
		try {
			$sql = "SELECT id as id, role as role, min(level) as level FROM privilege ORDER BY level ASC";
			$stmt = $this->_db->prepare($sql);
			$stmt->execute(array());
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
				throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir le min role");
			}
			$data = $stmt->fetch(PDO::FETCH_ASSOC);
			if(empty($data)){
				throw new NullObjectException();
			}
			$role = new Role($data);
			return $role;
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir le min role");
		}
	}
	
	
	public function getMinSubscriberRole(){
		$roles = $this->getRoleList();
		if(count($roles) >= 2){
			return $roles[1];
		}else{
			throw new NullObjectException("Il n'y a pas de role minimum pour un utilisateur connecte.");
		}
	}
	
	public function getGreaterRoleLevel($rolename){
		try {
			$sql = "SELECT * FROM privilege WHERE level >= (select level from privilege where role = :rolename)";
			$stmt = $this->_db->prepare($sql);
			$stmt->execute(array("rolename" => $rolename));
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
				throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste des roles");
			}
			$roles = array();
			while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
				$roles[] = new Role($data);
			}
			return $roles;
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir la liste des roles");
		}
	}
	
	
	
	public function existsRole($id){
		try{
			$role = $this->getRole($id);
			return (isset($role));
		}catch(Exception $e){
			return false;
		}
	}
	
	
	public function getRole($id){
		try {
			if(is_numeric($id)){
				$sql = "SELECT id as id, role as role, level as level FROM privilege WHERE id = :id";
			}else{
				$sql = "SELECT id as id, role as role, level as level FROM privilege WHERE role = :id";
			}
			$stmt = $this->_db->prepare($sql);
			$stmt->execute(array('id' => $id));
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
				throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir un role");
			}
			$data = $stmt->fetch(PDO::FETCH_ASSOC);
			if(empty($data)){
				throw new NullObjectException();
			}
			$role = new Role($data);
			return $role;
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir un role");
		}
	}
	
	
	
	
	
	/**
	 * Get the role corresponding to the given param
	 * @param boolean $isWebkot
	 * @param boolean $isAdmin
	 */
	/*public function getRole($isWebkot,$isAdmin){
		$manager = SessionManager::getInstance();
		if($manager->existsSession()){
			if(!$isWebkot && !$isAdmin){
				return 'Subscriber';
			}else{
				if($isAdmin){
					return 'Administrator';
				}else{
					return 'Webkot';
				}
			}
		}
		return 'Visitor';
	}*/
	
}