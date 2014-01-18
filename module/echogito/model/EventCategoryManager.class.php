<?php

class EventCategoryManager{
	
	
	protected static $_instance;
	private $_db; // Instance of Database
	private $_apc;
	
	
	/**
	 * getInstance
	 * @return EventCategoryManager $instance : the instance of EventCategoryManager
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
	 * add a Category of Event
	 * @param string $name : the name of the category
	 * @param string $descri : the description of the category
	 * @param string $color : the color (html code)
	 * @throws SQLException : this exception is raised if the Query is refused
	 * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
	 */
	public function add($name,$descri, $color){
		try {
			$sql = "INSERT INTO echogito_category (name, description, color) VALUES (:name, :descri, :color)";
			$stmt = $this->_db->prepare($sql);
			$stmt->execute(array( 'name' => $name, 'descri' => $descri, 'color' => $color));
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
				throw new SQLException($error[2], $error[0], $sql, "Impossible d'ajouter une catégorie.");
			}
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'ajouter une catégorie.");
		}
	}
	
	
	/**
	 * get a specified EventCategory
	 * @param int $id : the identifier of the desired category
	 * @throws NullObjectException : this is thrown if the event asked doesn't exist
	 * @throws SQLException : this exception is raised if the Query is refused
	 * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
	 * @return Event $event : the desired EventCategory Object
	 */
	public function getEventCategory($id){
		try{
			$sql = "SELECT * FROM echogito_category C WHERE C.id = :id";
			$stmt = $this->_db->prepare($sql);
			$stmt->execute(array('id' => $id));
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
				throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la catégorie spécifié.");
			}
			$data = $stmt->fetch(PDO::FETCH_ASSOC);
			if(empty($data)){
				throw new NullObjectException();
			}
			$event = new EventCategory($data);
			return $event;
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir la catégorie spécifié.");
		}
	}
	
	/**
	 * get the all the category of event
	 * @throws SQLException : this exception is raised if the Query is refused
	 * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
	 * @return array : an array of EventCategory Objects
	 */
	public function getAllEventCategory(){
		try {
			$sql = 'SELECT * FROM echogito_category C ORDER BY C.name ASC';
			$stmt = $this->_db->prepare($sql);
			$stmt->execute();
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
				throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir les catégories.");
			}
			$events = array();
			while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
				$events[] = new EventCategory($data);
			}
			return $events;
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir les catégories.");
		}
	}
	
	

	/**
	 * Update a Category of Event
	 * @param int îd : the identifer of the Category
	 * @param string $name : the name of the category
	 * @param string $descri : the description of the category
	 * @param string $color : the color (html code)
	 * @throws SQLException : this exception is raised if the Query is refused
	 * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
	 */
	public function update($id, $name, $descri, $color){
		try{
			$sql = "UPDATE echogito_category SET name = :name, description = :descri, color = :color WHERE id=:id";
			$stmt = $this->_db->prepare($sql);
			$n = $stmt->execute(array('name' => $name, 'descri' => $descri, 'color' => $color, 'id' => $id));
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
				throw new SQLException($error[2], $error[0], $sql, "Impossible d'effectuer la mise a jour.");
			}
			return ($n > 0);
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'effectuer la mise a jour.");
		}
	}
	
	
	/**
	 * delete an EventCategory Object
	 * @param int $id : the identifier of the EventCategory to remove
	 * @return boolean $b : true if the removing was successful
	 * @throws SQLException : this exception is raised if the Query is refused
	 * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
	 */
	public function delete($id){
		try {
			$sql = "DELETE FROM echogito_category WHERE id = :id";
			$stmt = $this->_db->prepare($sql);
			$stmt->execute(array( 'id' => $id));
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
				throw new SQLException($error[2], $error[0], $sql, "Impossible de supprimer un EventCategory.");
			}
			return true;
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible de supprimer un EventCategory.");
			return false;
		}
	}
	
}