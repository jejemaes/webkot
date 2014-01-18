<?php

class CensureManager{
	

	protected static $_instance;
	private $_db; // Instance de Database
	
	
	/**
	 * GetInstance
	 * @return CensureManager $instance : the instance of CensureManager
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
	}
	
	
	/**
	 * Add an Activity in the DB
	 * @param int $pid : the identifier of the Picture
	 * @param string $comment : the content of the Censure
	 * @param string $email : the email of the asker 
	 * @return boolean $b : true if the Censure was added, false otherwise
	 * @throws SQLException : this exception is raised if the Query is refused
	 * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
	 * @uses APC
	 */
	public function add($pid, $comment, $email){
		try {
			$sql = "INSERT INTO censure(pictureid,comment,email) VALUES (:pid, :comment, :email)";
			$stmt = $this->_db->prepare($sql);
			$stmt->execute(array( 'pid' => $pid, 'comment' => $comment, 'email' => $email));
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
				throw new SQLException($error[2], $error[0], $sql, "Impossible d'ajouter une demande de censure");
			}
			return true;
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'ajouter une demande de censure");
		}
	}
	
	
	
	/**
	 * get the authors of a given Activity
	 * @param int $aid : the identifier of the Activity
	 * @throws SQLException : this exception is raised if the Query is refused
	 * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
	 * @return array $users : an array of User
	 */
	public function getUnapprovedCensure(){
		try{
			$sql = "SELECT C.id as id, C.comment as comment, C.email as email, C.pictureid as pictureid, C.date as date FROM censure C, picture P WHERE P.id = C.pictureid AND P.iscensured = '0' ORDER BY C.date DESC";
			$stmt = $this->_db->prepare($sql);
			$stmt->execute();
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
				throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste des censures non approuv�es");
			}
			$censures = array();
			while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
				$censures[] = new Censure($data);
			}
			return $censures;
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir la liste des censures non approuv�es");
		}
	}
	
	
	/**
	 * Delete an Censure Object
	 * @param int $id : the identifier of the Censure to remove
	 * @return boolean $b : true if the removing was successful
	 * @throws SQLException : this exception is raised if the Query is refused
	 * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
	 */
	public function delete($id){
		try {
			$sql = "DELETE FROM censure WHERE id = :id";
			$stmt = $this->_db->prepare($sql);
			$stmt->execute(array( 'id' => $id));
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
				throw new SQLException($error[2], $error[0], $sql, "Impossible de supprimer une demande de censure");
			}
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible de supprimer une demande de censure");
		}
	}
	
	
}