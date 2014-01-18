<?php


class MediaManager{
	
	protected static $_instance;
	private $_db; // Instance of Database
	
	
	/**
	 * getInstance
	 * @return MediaManager $instance : the instance of MediaManager
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
	 * Add an MediaCategory in the DB
	 * @param array $data : key-array containing the information of the MediaCategory
	 * @return boolean $b : true if the MediaCategory was added, false otherwise
	 * @throws SQLException : this exception is raised if the Query is refused
	 * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
	 */
	public function addMediaCategory($name, $description, $directory){
		try {
			$sql = "INSERT INTO media_category (name, description, directory) VALUES ( :name, :description, :directory)";
			$stmt = $this->_db->prepare($sql);
			$stmt->execute(array('name' => $name, 'description' => $description, 'directory' => $directory));
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
				throw new SQLException($error[2], $error[0], $sql, "Impossible d'ajouter un lien");
			}
			return true;
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'ajouter un lien");
			return false;
		}
	}
	
	/**
	 * Add an Media in the DB
	 * @param array $data : key-array containing the information of the Media
	 * @return boolean $b : true if the Activity was added, false otherwise
	 * @throws SQLException : this exception is raised if the Query is refused
	 * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
	 */
	public function addMedia($name, $filename, $category){
		try {
			$sql = "INSERT INTO media (name, filename, category) VALUES ( :name, :filename, :category)";
			$stmt = $this->_db->prepare($sql);
			$stmt->execute(array('name' => $name, 'filename' => $filename, 'category' => $category));
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
				throw new SQLException($error[2], $error[0], $sql, "Impossible d'ajouter un lien");
			}
			return true;
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'ajouter un lien");
			return false;
		}
	}
	
	
	/**
	 * get the list of the Media of a given Category
	 * @return array $cats : array of MediaCategory Object
	 * @throws SQLException : this exception is raised if the Query is refused
	 * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
	 */
	public function getMedias($cid){
		try{
			$sql = 'SELECT * FROM media WHERE category = :cid ORDER BY filename ASC';
			$stmt = $this->_db->prepare($sql);
			$stmt->execute(array('cid' => $cid));
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
				throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste des medias d'une categorie");
			}
			$medias = array();
			while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
				$medias[] = new Media($data);
			}
			return $medias;
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir la liste des medias d'une categorie");
		}
	}
	
	
	/**
	 * get a specified Media Object
	 * @param int $cid : the identifier of the Media
	 * @return MediaCategory $cat :  the specific Media Object
	 * @throws SQLException : this exception is raised if the Query is refused
	 * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
	 */
	public function getMedia($mid){
		try{
			$sql = "SELECT M.id as id, M.name as name, M.filename as filename, C.directory as category FROM media M, media_category C WHERE M.category = C.id AND M.id = :id";
			$stmt = $this->_db->prepare($sql);
			$stmt->execute(array('id' => $mid));
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
				throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir le Media");
			}
			$data = $stmt->fetch(PDO::FETCH_ASSOC);
			if(empty($data)){
				throw new NullObjectException();
			}
			$cat = new Media($data);
			return $cat;
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir le Media");
		}
	}
	
	/**
	 * get the list of the MediaCategory and their content
	 * @return array $cats : array of MediaCategory Object
	 * @throws SQLException : this exception is raised if the Query is refused
	 * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
	 */
	public function getCategoriesAndContent(){
		try{
			$sql = 'SELECT * FROM media_category ORDER BY directory ASC';
			$stmt = $this->_db->prepare($sql);
			$stmt->execute();
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
				throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste des mediacategory");
			}
			$cats = array();
			while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
				$tmp = new MediaCategory($data);
				$tmp->setMedias($this->getMedias($data['id']));
				$cats[] = $tmp;
			}
			return $cats;
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir la liste des mediacategory");
		}
	}
	
	
	/**
	 * get the list of the MediaCategory and their content
	 * @return array $cats : array of MediaCategory Object
	 * @throws SQLException : this exception is raised if the Query is refused
	 * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
	 */
	public function getCategories(){
		try{
			$sql = 'SELECT * FROM media_category';
			$stmt = $this->_db->prepare($sql);
			$stmt->execute();
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
				throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste des mediacategory");
			}
			$cats = array();
			while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
				$cats[] = new MediaCategory($data);
			}
			return $cats;
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir la liste des mediacategory");
		}
	}
	
	
	/**
	 * get a specified MediaCategory Object
	 * @param int $cid : the identifier of the MediaCategory
	 * @return MediaCategory $cat :  the specific Link Object
	 * @throws SQLException : this exception is raised if the Query is refused
	 * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
	 */
	public function getCategory($cid){
		try{
			$sql = "SELECT * FROM media_category WHERE id = :id";
			$stmt = $this->_db->prepare($sql);
			$stmt->execute(array('id' => $cid));
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
				throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir le MediaCategory");
			}
			$data = $stmt->fetch(PDO::FETCH_ASSOC);
			if(empty($data)){
				throw new NullObjectException();
			}
			$cat = new MediaCategory($data);
			return $cat;
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir le MediaCategory");
		}
	}
	
	
	
	
}