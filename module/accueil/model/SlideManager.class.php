<?php



class SlideManager {

	protected static $_instance;
	private $_db; // Instance de Database
	private $_apc;
	
	public $apc_slide_list_active;
	public $apc_slide_list_total;
	
	/**
	 * GetInstance
	 * @return : get the instance of the manager
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
		$this->_apc = ((extension_loaded('apc') && ini_get('apc.enabled')) && APC_ACTIVE ? true : false);
		// init the apc var
		if($this->_apc){
			$this->apc_slide_list_active = APC_PREFIX . 'home-slider-active';
			$this->apc_slide_list_total = APC_PREFIX . 'home-slider-total';
		}
	}
	
	
	

	/**
	 * Add a Todo in the DB
	 * @param string $title : the title of the Todo Object
	 * @param string $descri : the description of the Todo
	 * @param string $img : the path of the image
	 * @param string $isactive : '0' or '1'
	 * @throws SQLException : this exception is raised if the Query is refused
	 * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
	 */
	public function add($title, $descri, $img, $statut){
		try {
			$sql = "INSERT INTO slide(title,description,pathimg, isactive) VALUES (:title, :descri, :img, :statut)";
			$stmt = $this->_db->prepare($sql);
			$stmt->execute(array( 'title' => $title, 'descri' => $descri, 'img' => $img, 'statut' => $statut));
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
				throw new SQLException($error[2], $error[0], $sql, "Impossible d'ajouter un slide.");
			}
			if($this->_apc){
				apc_delete($this->apc_slide_list_active);
				apc_delete($this->apc_slide_list_total);
			}
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'ajouter un slide.");
		}
	}
	
	
	
	/**
	 * Get a Todo
	 * @param int $id : the id of the Slide object
	 * @return Slide : the Slide Object
	 * @throws SQLException : this exception is raised if the Query is refused
	 * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
	 */
	public function getSlide($id){
		try {
			$sql = "SELECT * FROM slide WHERE id = :id LIMIT 1";
			$stmt = $this->_db->prepare($sql);
			$stmt->execute(array( 'id' => $id));
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
				throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir un slide sp&eacute;cifi&eacute;.");
			}
			$data = $stmt->fetch(PDO::FETCH_ASSOC);
			if(empty($data)){
				throw new NullObjectException();
			}
			$slide = new Slide($data);
			return $slide;
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir un Slide sp&eacute;cifi&eacute;.");
		}
	
	}
	
	
	/**
	 * Get the list of active Slides
	 * @return array $slides : an array of Slide Objects
	 * @throws SQLException : this exception is raised if the Query is refused
	 * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
	 */
	public function getActiveSlides(){
		try{
			if($this->_apc && apc_exists($this->apc_slide_list_active)){
				$slides = apc_fetch($this->apc_slide_list_active);
			}else{		
				$sql = "SELECT * FROM slide WHERE isactive = '1'";
				$stmt = $this->_db->prepare($sql);
				$stmt->execute();
				if($stmt->errorCode() != 0){
					$error = $stmt->errorInfo();
					throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste des slides actifs.");
				}
				$slides = array();
				while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
					$slides[] = new Slide($data);
				}
				if($this->_apc){
					apc_store($this->apc_slide_list_active, $slides, 86000);
				}
			}
			return $slides;
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir la liste des slides actifs.");
		}
	}
	
	
	/**
	 * Get the list of active Slides
	 * @return array $slides : an array of Slide Objects
	 * @throws SQLException : this exception is raised if the Query is refused
	 * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
	 */
	public function getAllSlides(){
		try{
			if($this->_apc && apc_exists($this->apc_slide_list_total)){
				$slides = apc_fetch($this->apc_slide_list_total);
			}else{		
				$sql = "SELECT * FROM slide";
				$stmt = $this->_db->prepare($sql);
				$stmt->execute();
				if($stmt->errorCode() != 0){
					$error = $stmt->errorInfo();
					throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste des slides actifs.");
				}
				$slides = array();
				while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
					$slides[] = new Slide($data);
				}
				if($this->_apc){
					apc_store($this->apc_slide_list_total, $slides, 86000);
				}
			}
			return $slides;
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir la liste des slides actifs.");
		}
	}
	
	
	/**
	 * Update the title and the description of the Slide
	 * @param $tid : the id of the slide
	 * @param $title : the new title
	 * @param $description : the new description
	 */
	public function update($id, $title, $description, $img, $statut){
		try{
			$sql = "UPDATE slide SET title =:title, description = :descri, pathimg = :img, isactive = :statut WHERE id=:id";
			$stmt = $this->_db->prepare($sql);
			$n = $stmt->execute(array('title' => $title, 'descri' => $description, 'img' => $img, 'statut' => $statut, 'id' => $id));
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
				throw new SQLException($error[2], $error[0], $sql, "Impossible d'effectuer la mise a jour du slide.");
			}
			if($this->_apc){
				apc_delete($this->apc_slide_list_active);
				apc_delete($this->apc_slide_list_total);
			}
			return ($n > 0);
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'effectuer la mise a jour du slide.");
		}
	}
	
	
	/**
	 * remove a given slide from the DB
	 * @param int $id : the identifier of the Slide Object
	 * @return boolean $b : true if the removal was a success. False otherwise
	 * @throws SQLException : this exception is raised if the Query is refused
	 * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
	 */
	public function delete($id){
		try {
			$sql = "DELETE FROM slide WHERE id=:id";
			$stmt = $this->_db->prepare($sql);
			$stmt->execute(array( 'id' => $id));
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
				throw new SQLException($error[2], $error[0], $sql, "Impossible de supprimer une slide.");
			}
			if($this->_apc){
				apc_delete($this->apc_slide_list_active);
				apc_delete($this->apc_slide_list_total);
			}
			return true;
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible de supprimer un slide.");
		}
	}
	
}