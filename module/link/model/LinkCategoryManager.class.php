<?php

/*
 * Created on 12 avr. 2012
 *
 * MAES Jerome, Webkot 2011-2012
 * Class Description : manager of the CategroyLink class (request to the DB, ...)
 * 
 * Using the singleton pattern
 */

 
class LinkCategoryManager {

    protected static $_instance;
	private $_db; // Instance of DB


	/**
	 * GetInstance 
	 * @return LinkCategoryManager $instance : return the instance of the Manager
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
     * Add an LinkCategory in the DB
     * @param String $name
     * @param String $descri
     * @param int $place
     * @return boolean $b : true if the Activity was added, false otherwise
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function addCategory($name, $descri, $place){
        try {
        	$sql = "INSERT INTO link_category (name, description, place) VALUES (:name, :descri, :place)";
	        $stmt = $this->_db->prepare($sql);
        	$stmt->execute(array( 'name' => $name, 'descri' => $descri, 'place' => $place));
	        if($stmt->errorCode() != 0){
		        $error = $stmt->errorInfo();
	        	throw new SQLException($error[2], $error[0], $sql, "Impossible d'ajouter une categorie");
	        }
	        return true;     
        }catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'ajouter une categorie");
        	return false;
        }
    }
    
    
    /**
     * get a specified LinkCategory Object
     * @param String $name : the name of the LinkCategory
     * @return LinkCategory $cat :  the specific LinkCategory Object
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function getCategory($name){
    	try{
    		$sql = "SELECT * FROM link_category WHERE name = :name";
    		$stmt = $this->_db->prepare($sql);
    		$stmt->execute(array('name' => $name));
    		if($stmt->errorCode() != 0){
    			$error = $stmt->errorInfo();
    			throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la categorie");
    		}
    		$data = $stmt->fetch(PDO::FETCH_ASSOC);
    		if(empty($data)){
    			throw new NullObjectException();
    		}
    		$link = new LinkCategory($data);
    		return $link;
    	}catch(PDOException $e){
    		throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir la categorie");
    	}
    }
    
    
     /**
     * get the list of the LinkCategory Object
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
	        	throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste des categories de liens");
	        }
        	$list = array();
        	while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){   	
	         	$list[] = new LinkCategory($data);
	        } 
	        return $list;
    	}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir la liste des categories de liens");
        }
    }
    
    
 	/**
	 * Update the LinkCategory in the DB. 
	 * @param string $name : the name of the LinkCategory
	 * @param string $descri : the description of the category
	 * @param int $place : the order of the LinkCategory
	 * @return boolean $b : true if the update is a success, false otherwise
	 * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
	 */
 	public function updateCategory($name, $descri, $place){
 		try{
	 		$sql = "UPDATE link_category SET description = :descri, place = :place  WHERE name=:name";
 			$stmt = $this->_db->prepare($sql);
	        $n = $stmt->execute(array('name' => $name, 'descri' => $descri, 'place' => $place));
	        if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'effectuer la mise a jour de la categorie 33");
		    }
			return ($n > 0);
 		}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'effectuer la mise a jour de la categorie");
        }
 	}
   
    
      
    /**
     * Delete a LinkCategory Object
     * @param String $name : the name (identifier) of the LinkCategory to remove
     * @return boolean $b : true if the removing was successful
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function deleteCategory($name){
        try {
        	$sql = "DELETE FROM link_category WHERE name = :name";
        	$stmt = $this->_db->prepare($sql);
        	$stmt->execute(array( 'name' => $name));
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