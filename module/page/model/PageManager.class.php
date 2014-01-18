<?php

class PageManager {

	protected static $_instance;
	private $_db; // Instance of Database


	/**
	 * getInstance
	 * @return PageManager $instance : the instance of PageManager
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
	 * Add an Page in the DB
	 * @param string $id : the identifier of the Page
	 * @param string $title : the title of the Page
	 * @param string $content : the content of the Page
	 * @param boolean $isactive : the boolean indicate if the page is active, or not
	 * @param string $file : the name of the file associated to the page (which will be executed)
	 * @return boolean $b : true if the Activity was added, false otherwise
	 * @throws SQLException : this exception is raised if the Query is refused
	 * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
	 */
	public function addPage($id, $title,$content,$isactive, $file){
		try {
			if($file){
				$fileStmt = ", :file";
				$sql = "INSERT INTO page (id,title,content,isactive,file) VALUES (:id, :title, :content, :isactive, :file)";
				$param = array('id' => $id, 'title' => $title, 'content' => $content, 'isactive' => $isactive, 'file' => $file);
			}else{
				$sql = "INSERT INTO page (id,title,content,isactive) VALUES (:id, :title, :content, :isactive)";
				$param = array('id' => $id, 'title' => $title, 'content' => $content, 'isactive' => $isactive);
			}
			$stmt = $this->_db->prepare($sql);
			$stmt->execute($param);
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
				throw new SQLException($error[2], $error[0], $sql, "Impossible d'ajouter un Page.");
				return false;
			}
			return true;
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'ajouter un Page.");
			return false;
		}
	}
	

	/**
	 * get a specified Page
	 * @param string $id : the identifier of the Page
	 * @return Page $page : the specified Page
	 * @throws SQLException : this exception is raised if the Query is refused
	 * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
	 * @throws NullObjectException : this exception is raised when the specified Object didn't exist
	 */
	public function getPage($id){
		try {
			$sql = "SELECT * FROM page WHERE id = :id LIMIT 1";
			$stmt = $this->_db->prepare($sql);
			$stmt->execute(array( 'id' => $id));
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
				throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir une Page specifiee");
			}
			$data = $stmt->fetch(PDO::FETCH_ASSOC);
			if($data == null){
				throw new NullObjectException();
			}
			$page = new Page($data);
			return $page;
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir une Page specifiee");
		}
	}
	
	
	/**
	 * get all the list of Page Objects
	 * @return array $list : array of Page Objects
	 * @throws SQLException : this exception is raised if the Query is refused
	 * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
	 */
	public function getListPage(){
		try{
			$sql = "SELECT * FROM page";
			$stmt = $this->_db->prepare($sql);
			$stmt->execute();
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
				throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste des pages");
			}
			$list = array();
			while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
				$list[] = new Page($data);
			}
			return $list;
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir la liste des pages");
		}
	}
	
	
	
	
	/**
	 * update a Post
	 * @param string $id : the identifier of the Post
	 * @param string $title : the title of the Post
	 * @param string $content : the content of the Post
	 * @param string $file : the name of the file associated to the page (which will be executed)
	 * @return boolean $b : true if the update was a success
	 * @throws SQLException : this exception is raised if the Query is refused
	 * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
	 */
	public function updatePage($id, $title, $content, $isactive, $file){
		try{
			$sql = "UPDATE page SET title = :title, content = :content, isactive = :isactive, file = :file WHERE id=:id";
			$stmt = $this->_db->prepare($sql);
			$n = $stmt->execute(array('title' => $title, 'content' => $content, 'isactive' => $isactive, 'file' => $file, 'id' => $id));
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
				throw new SQLException($error[2], $error[0], $sql, "Impossible d'effectuer la mise a jour d'une Page.");
			}
			return ($n > 0);
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'effectuer la mise a jour d'une Page.");
		}
	}
	
	/**
	 * Delete an Page Object
	 * @param int $id : the identifier of the Page to remove
	 * @return boolean $b : true if the removing was successful
	 * @throws SQLException : this exception is raised if the Query is refused
	 * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
	 */
	public function delete($pid){
		try {
			$sql = "DELETE FROM page WHERE id= :id";
			$stmt = $this->_db->prepare($sql);
			$stmt->execute(array( 'id' => $pid));
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
				throw new SQLException($error[2], $error[0], $sql, "Impossible de supprimer une page");
			}
			return true;
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible de supprimer une page");
		}
	}

}