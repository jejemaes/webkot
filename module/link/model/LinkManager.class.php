<?php

/*
 * Created on 12 avr. 2012
 *
 * MAES Jerome, Webkot 2011-2012
 * Class Description : manager of the link class (request to the DB, ...)
 * 
 * Using the singleton pattern
 */

 
class LinkManager {

    protected static $_instance;
	private $_db; // Instance of DB


	/**
	 * GetInstance 
	 * @return LinkManager $instance : return the instance of the Manager
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
     * Add an Link in the DB
     * @param array $data : key-array containing the information of the Link 
     * @return boolean $b : true if the Activity was added, false otherwise
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function addLink($name, $category, $url){
        try {
        	$sql = "INSERT INTO link (category,name,url) VALUES (:category, :name, :url)";
	        $stmt = $this->_db->prepare($sql);
        	$stmt->execute(array( 'category' => $category, 'name' => $name, 'url' => $url));
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
     * get a specified Link Object
     * @param int $lid : the identifier of the link
     * @return Link $link :  the specific Link Object
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function getLink($lid){
    	try{
    		$sql = "SELECT L.id as id, C.description as category, C.place as place, L.name as name, L.url as url FROM link L, link_category C WHERE C.name = L.category AND L.id = :id";
    		$stmt = $this->_db->prepare($sql);
        	$stmt->execute(array('id' => $lid));
        	if($stmt->errorCode() != 0){
		        $error = $stmt->errorInfo();
	        	throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir le lien");
	        }
        	$data = $stmt->fetch(PDO::FETCH_ASSOC);
			if(empty($data)){
				throw new NullObjectException();
			}
	        $link = new Link($data);
	     	return $link;
    	}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir le lien");
        }
    }
    
     /**
     * get the list of the Link Object
     * @return array $list : array of Link Object
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function getListLink(){
    	try{
    		$sql = "SELECT L.id as id, C.description as category, C.place as place, L.name as name, L.url as url FROM link L, link_category C WHERE C.name = L.category ORDER BY place ASC";
    		$stmt = $this->_db->prepare($sql);
        	$stmt->execute();
        	if($stmt->errorCode() != 0){
		        $error = $stmt->errorInfo();
	        	throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste des liens");
	        }
        	$list = array();
        	while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){   	
	         	$list[] = new Link($data);
	        } 
	        return $list;
    	}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir la liste des liens");
        }
    }
    
    
    /**
     * get the list of the link category
     * @return array $list : array of LinkCategory Object
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function getListCategory(){
    	try{
    		$sql = "SELECT * FROM link_category ORDER BY place ASC";
	        $stmt = $this->_db->prepare($sql);
	        $stmt->execute();   
	        if($stmt->errorCode() != 0){
		        $error = $stmt->errorInfo();
	        	throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste des categories de lien");
	        }
	    	$list = array();
	        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){   	
	         	$list[] = new LinkCategory($data);
	        }   
	        return $list;
    	}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir la liste des categories de lien");
        	return false;
        }
    }
    
    
    
 	/**
	 * Update the Link in the DB. 
	 * @param int $id : the id of the row (link)
	 * @param string $name : the name of the Link
	 * @param string $cat : the identifier of the category of the Link
	 * @param string $url : the url of the Link
	 * @return boolean $b : true if the update is a success, false otherwise
	 * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
	 */
 	public function updateLink($id, $name, $cat, $url){
 		try{
	 		$sql = "UPDATE link SET name = :name, category = :cat, url = :url  WHERE id=:id";
 			$stmt = $this->_db->prepare($sql);
	        $n = $stmt->execute(array('name' => $name, 'cat' => $cat, 'url' => $url, 'id' => $id));
	        if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'effectuer la mise a jour");
		    }
			return ($n > 0);
 		}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'effectuer la mise a jour");
        }
 	}
   
    
      
    /**
     * Delete an Link Object
     * @param int $aid : the identifier of the Link to remove
     * @return boolean $b : true if the removing was successful
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function deleteLink($lid){
        try {
        	$sql = "DELETE FROM link WHERE id = :id";
        	$stmt = $this->_db->prepare($sql);
        	$stmt->execute(array( 'id' => $lid));
        	if($stmt->errorCode() != 0){
		        $error = $stmt->errorInfo();
	        	throw new SQLException($error[2], $error[0], $sql, "Impossible de supprimer un lien");
	        }
	        return true;       
        }catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible de supprimer un lien");
        	return false;
        }
    }
    
    
    
}
?>