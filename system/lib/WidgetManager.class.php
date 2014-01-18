<?php



class WidgetManager{
	protected static $_instance;
	private $_db; // Instance of Database
	private $_apc;
	
	const APC_WIDGET_MOD = 'widget-mod-';
	const APC_WIDGET_DEP = 'widget-dep-';
	const APC_WIDGET_FOOT = 'widget-footer';
	const APC_WIDGET_FOOTDEP = 'widget-footerdep';
	
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
	
	/**
	 * Constructor
	 */
	public function __construct(){
		$this->_db = Database::getInstance();
		$this->_apc = ((extension_loaded('apc') && ini_get('apc.enabled')) ? true : false);
	}
	
	
	
	
	public function addWidgetPlace($mid, $wid, $place){
		try {
			$sql = "INSERT INTO widget_place(moduleid,widgetid,place) VALUES (:mid, :wid, :place)";
			$stmt = $this->_db->prepare($sql);
			$stmt->execute(array( 'mid' => $mid, 'wid' => $wid, 'place' => $place));
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
				throw new SQLException($error[2], $error[0], $sql, "Impossible d'ajouter un widget");
			}
			if($this->_apc){
				apc_delete(self::APC_WIDGET_MOD . $mid);
				apc_delete(self::APC_WIDGET_DEP . $mid);
			}
			return true;
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'ajouter un widget");
			return false;
		}
	}

	
	public function updateWidget($id, $name, $menu, $actif){
		try{
			$sql = "UPDATE widget SET name = :name, in_footer  = :footer, is_active = :actif WHERE id=:id";
			$stmt = $this->_db->prepare($sql);
			$n = $stmt->execute(array('name' => $name, 'footer' => $menu, 'actif' => $actif, 'id' => $id));
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
				throw new SQLException($error[2], $error[0], $sql, "Impossible d'effectuer la mise a jour du widget");
			}
			if($this->_apc){
				apc_delete(self::APC_WIDGET_FOOT);
				apc_delete(self::APC_WIDGET_FOOTDEP);
				$this->apcDeleteWidgetLists();
			}
			return ($n > 0);
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'effectuer la mise a jour du widget");
		}
	}
	
	/**
	 * Delete all the widget for a given module
	 * @param int $mid : the identifier of the Module to remove
	 * @return boolean $b : true if the removing was successful
	 * @throws SQLException : this exception is raised if the Query is refused
	 * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
	 */
	public function deleteAllWidgets($mid){
		try {
			$sql = "DELETE FROM widget_place WHERE moduleid = :mid";
			$stmt = $this->_db->prepare($sql);
			$stmt->execute(array( 'mid' => $mid));
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
				throw new SQLException($error[2], $error[0], $sql, "Impossible de supprimer les widgets associŽs a un module");
			}
			return true;
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible de supprimer les widgets associŽs a un module");
			return false;
		}
	}
	
	
	
	public function getWidgets($moduleid){
		if($this->_apc && apc_exists(self::APC_WIDGET_MOD . $moduleid)){
			return apc_fetch(self::APC_WIDGET_MOD . $moduleid);
		}else{
			try {
				$sql = "(SELECT W.id as id, W.name as name, W.is_active as isActive, D.name as ModuleName, D.location as ModuleLocation ,  W.classname as classname, W.in_footer as infooter, P.place as place FROM module M, widget W, widget_place P, module D WHERE D.id = W.module_id AND P.widgetid = W.id AND M.id = P.moduleid AND W.is_active = '1' AND P.moduleid = :mid)
						UNION
						(SELECT W.id as id, W.name as name, W.is_active as isActive, null, null, W.classname as classname, W.in_footer as infooter, P.place as place FROM module M, widget W, widget_place P WHERE W.module_id is null AND P.widgetid = W.id AND M.id = P.moduleid AND W.is_active = '1' AND P.moduleid = :mid) ORDER BY place ASC";
				//$sql = "SELECT W.id as id, W.name as name, W.allpage as allpage, M.name as ModuleName, M.location as ModuleLocation , W.is_active as isActive, W.classname as classname FROM module M, widget W WHERE W.module_id = M.id AND name= :name";
				$stmt = $this->_db->prepare($sql);
				$stmt->execute(array( 'mid' => $moduleid));
				if($stmt->errorCode() != 0){
					$error = $stmt->errorInfo();
					throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste des widgets");
				}
				// built all the widget, from the classname of the database
				$widgets = array();
				while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
					$w = $data["classname"];
					if($w != null){
						$widgets[] = new $w($data);
					}
				}
				if($this->_apc){
					apc_store(self::APC_WIDGET_MOD . $moduleid, $widgets, 86000);
				}
				return $widgets;
			}catch(PDOException $e){
				throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenirla liste des widgets");
			}	
		}
	}
	
	
	/**
	 * get the list of the Widget dependancies, to load them
	 * @param int $moduleid : the module including the module that require to know the dependancies
	 * @throws SQLException
	 * @throws DatabaseException
	 * @return array $data : a array of key-array containing the name of the widget class, and the name of the module (if so) to load to execute the widget
	 */
	public function getWidgetDependencies($moduleid){
		if($this->_apc && apc_exists(self::APC_WIDGET_DEP . $moduleid)){
			return apc_fetch(self::APC_WIDGET_DEP . $moduleid);
		}else{		
			try {
				$sql = "(SELECT W.name as name, D.name as ModuleName, D.location as ModuleLocation ,  W.classname as classname, W.in_footer as infooter  FROM module M, widget W, widget_place P, module D WHERE D.id = W.module_id AND P.widgetid = W.id AND M.id = P.moduleid AND W.is_active = '1' AND P.moduleid = :mid)
						UNION
						(SELECT W.name as name, null, null ,  W.classname as classname, W.in_footer as infooter  FROM module M, widget W, widget_place P WHERE W.module_id is null AND P.widgetid = W.id AND M.id = P.moduleid AND W.is_active = '1' AND P.moduleid = :mid)";
				$stmt = $this->_db->prepare($sql);
				$stmt->execute(array( 'mid' => $moduleid));
				if($stmt->errorCode() != 0){
					$error = $stmt->errorInfo();
					throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste des dependances des widgets");
				}
				$dep = array();
				while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
					$dep[] = $data;
				}
				if($this->_apc){
					apc_store(self::APC_WIDGET_DEP . $moduleid, $dep, 86000);
				}
				return $dep;	
			}catch(PDOException $e){
				throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenirla liste des dependances des widgets");
			}
		}
	}
	
	
	
	
	/**
	 * get the list of the Widget dependancies footer, to load them
	 * @param int $moduleid : the module including the module that require to know the dependancies
	 * @throws SQLException
	 * @throws DatabaseException
	 * @return array $data : a array of key-array containing the name of the widget class, and the name of the module (if so) to load to execute the widget
	 */
	public function getWidgetFooterDependencies(){
		if($this->_apc && apc_exists(self::APC_WIDGET_FOOTDEP)){
			return apc_fetch(self::APC_WIDGET_FOOTDEP);
		}else{
			try {
				$sql = "(SELECT W.id as id, W.name as name, W.is_active as isActive, D.name as ModuleName, D.location as ModuleLocation ,  W.classname as classname, W.in_footer as infooter FROM module D, widget W WHERE D.id = W.module_id AND W.is_active = '1' AND W.in_footer = '1')
						UNION
						(SELECT W.id as id, W.name as name, W.is_active as isActive, null, null, W.classname as classname, W.in_footer as infooter  FROM widget W WHERE W.module_id is null AND W.is_active = '1' AND W.in_footer = '1')";
				$stmt = $this->_db->prepare($sql);
				$stmt->execute();
				if($stmt->errorCode() != 0){
					$error = $stmt->errorInfo();
					throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste des dependances des widgets footer");
				}
				$dep = array();
				while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
					$dep[] = $data;
				}
				if($this->_apc){
					apc_store(self::APC_WIDGET_FOOTDEP, $dep, 86000);
				}
				return $dep;
			}catch(PDOException $e){
				throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenirla liste des dependances des widgets footer");
			}
		}
	}
	
	
	public function getFooterWidgets(){
		if($this->_apc && apc_exists(self::APC_WIDGET_FOOT)){
			return apc_fetch(self::APC_WIDGET_FOOT);
		}else{
			try {
				$sql = "(SELECT W.id as id, W.name as name, W.is_active as isActive, D.name as ModuleName, D.location as ModuleLocation ,  W.classname as classname, W.in_footer as infooter FROM module D, widget W WHERE D.id = W.module_id AND W.is_active = '1' AND W.in_footer = '1')
						UNION
						(SELECT W.id as id, W.name as name, W.is_active as isActive, null, null, W.classname as classname, W.in_footer as infooter  FROM widget W WHERE W.module_id is null AND W.is_active = '1' AND W.in_footer = '1')";
				$stmt = $this->_db->prepare($sql);
				$stmt->execute();
				if($stmt->errorCode() != 0){
					$error = $stmt->errorInfo();
					throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste des widgets footer");
				}
				// built all the widget, from the classname of the database
				$widgets = array();
				while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
					$w = $data["classname"];
					if($w != null){
						$widgets[] = new $w($data);
					}
				}
				if($this->_apc){
					apc_store(self::APC_WIDGET_FOOT, $widgets, 86000);
				}
				return $widgets;
			}catch(PDOException $e){
				throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenirla liste des widgets footer");
			}
		}
	}
	
	
	/**
	 * get the complete list of Generic Widgets Objects
	 * @throws SQLException
	 * @throws DatabaseException
	 * @return multitype:unknown
	 */
	public function getAllGenericWidgets(){
			try {
				$sql = "SELECT W.id as id, W.name as name, W.in_footer as infooter, W.is_active as isActive, W.classname as classname, M.name as ModuleName FROM widget W, module M WHERE W.module_id = M.id
						UNION
						SELECT W.id as id, W.name as name, W.in_footer as infooter, W.is_active as isActive, W.classname as classname, null FROM widget W WHERE W.module_id is null";
				$stmt = $this->_db->prepare($sql);
				$stmt->execute();
				if($stmt->errorCode() != 0){
					$error = $stmt->errorInfo();
					throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste des widgets generiques");
				}
				$widgets = array();
				while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
					$widgets[] = new Widget($data);
				}
			
				return $widgets;
			}catch(PDOException $e){
				throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenirla liste des widgets generiques");
			}	
		
	}
	
	
	public function getGenericWidgets($mid){
		try {
			$sql = "SELECT W.id as id, W.name as name, W.in_footer as infooter, W.is_active as isActive, W.classname as classname, M.name as ModuleName, P.place as place FROM widget_place P, widget W, module M WHERE W.module_id = M.id AND P.widgetid = W.id AND P.moduleid = :mid
					UNION
					SELECT W.id as id, W.name as name, W.in_footer as infooter, W.is_active as isActive, W.classname as classname, null, P.place as place FROM widget W, widget_place P WHERE W.module_id is null AND P.widgetid = W.id AND P.moduleid = :mid";
			$stmt = $this->_db->prepare($sql);
			$stmt->execute(array('mid' => $mid));
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
				throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste des widgets generiques");
			}
			$widgets = array();
			while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
				$widgets[] = new Widget($data);
			}
				
			return $widgets;
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenirla liste des widgets generiques");
		}
	}
	
	
	/**
	 * get the Widget Object instanced (the classname object)
	 * @param int $wid
	 * @throws SQLException
	 * @throws NullObjectException
	 * @throws DatabaseException
	 * @return unknown
	 */
	public function getWidget($wid){
		try {
			$sql = "SELECT W.id as id, W.name as name, M.name as ModuleName, M.location as ModuleLocation , W.is_active as isActive, W.classname as classname, W.in_footer as infooter FROM module M, widget W WHERE W.module_id = M.id  AND W.id = :wid";
			$stmt = $this->_db->prepare($sql);
			$stmt->execute(array( 'wid' => $wid));
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
				throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir un widget");
			}
			$data = $stmt->fetch(PDO::FETCH_ASSOC);
			if($data == null){
				throw new NullObjectException();
			}
			$w = $data["classname"];
			$widget = new $w($data);
			return $widget;
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir un widget");
		}
	}
	
	/**
	 * get the Widget Object
	 * @param int $wid
	 * @throws SQLException
	 * @throws NullObjectException
	 * @throws DatabaseException
	 * @return unknown
	 */
	public function getGenericWidget($wid){
		try {
			$sql = "SELECT W.id as id, W.name as name, W.is_active as isActive, W.classname as classname, W.in_footer as infooter FROM widget W WHERE  W.id = :wid";
			$stmt = $this->_db->prepare($sql);
			$stmt->execute(array( 'wid' => $wid));
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
				throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir un widget generic");
			}
			$data = $stmt->fetch(PDO::FETCH_ASSOC);
			if($data == null){
				throw new NullObjectException();
			}
			$widget = new Widget($data);
			return $widget;
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir un widget generic");
		}
	}
	
	
	
	
	private function apcDeleteWidgetLists(){
		try{
			$sql = "SELECT id as id FROM module";
			$stmt = $this->_db->prepare($sql);
			$stmt->execute();
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
				throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste des differents id des modules");
			}
			$i = 0;
			$ids = array();
			while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
				$ids[] = $data['id'];
			}
			for($i=0 ; $i<count($ids) ; $i++){
				if($this->_apc){
					apc_delete(self::APC_WIDGET_MOD . $ids[$i]);
					apc_delete(self::APC_WIDGET_DEP . $ids[$i]);
				}
			}
	
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir la liste des differents id des modules");
		}
	}
	
	
}