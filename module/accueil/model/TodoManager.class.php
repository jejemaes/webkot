<?php


class TodoManager {

    protected static $_instance;
	private $_db; // Instance de Database


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
    }
    
    
    /**
     * Add a Todo in the DB
     * @param string $title : the title of the Todo Object
     * @param string $descri : the description of the Todo
     * @param string $author : the username of the author of the Todo
     * @return : true if the adding is done, false otherwise
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
     public function add($title, $descri, $author){
         try {
        	$sql = "INSERT INTO admin_todo(title,description,author) VALUES (:title, :descri, :author)";
	        $stmt = $this->_db->prepare($sql);
        	$stmt->execute(array( 'title' => $title, 'descri' => $descri, 'author' => $author));
	        if($stmt->errorCode() != 0){
		        $error = $stmt->errorInfo();
	        	throw new SQLException($error[2], $error[0], $sql, "Impossible d'ajouter un todo");
	        }
	        return true;     
        }catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'ajouter un todo");
        	return false;
        }
    }
    
    /**
     * Get a Todo 
     * @param int $id : the id of the Todo object
     * @return Todo : the todo Object
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function getTodo($id){
     	try {
    		$sql = "SELECT * FROM admin_todo WHERE id = :id LIMIT 1";
	       	$stmt = $this->_db->prepare($sql);
	        $stmt->execute(array( 'id' => $id));
			if($stmt->errorCode() != 0){
			    $error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir un todo specifie");
		    }
			$data = $stmt->fetch(PDO::FETCH_ASSOC);
			if(empty($data)){
				throw new NullObjectException();
			}
	        $todo = new Todo($data);
	     	return $todo;
    	}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir un todo specifie");
        }	
        
    }
    
    /**
     * Get the todo list : every todo not done yet
     * @return array $todos : an array of Todo Objects
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function getListTodo(){
	    try{
		    $sql = "(SELECT * FROM admin_todo WHERE executor is NULL)";
		    $stmt = $this->_db->prepare($sql);
		    $stmt->execute();  
		    if($stmt->errorCode() != 0){
			    $error = $stmt->errorInfo();
			    throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste des todos non terminŽs");
		    } 
		    $todos = array();   
		    while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){   	
			    $todos[] = new Todo($data);
		    }   
		    return $todos;	
	    }catch(PDOException $e){
		    throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir la liste des Todos non terminŽs");
	    }
    }
    
    /**
     * Get the todo list : every todo not done yet, and the 5 last accomplished
     * @return array $todos : an array of Todo Objects
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function getAllTodo(){
    	try{
    		$sql = "SELECT * FROM admin_todo ORDER BY creation_date ASC";
    		$stmt = $this->_db->prepare($sql);
    		$stmt->execute();
    		if($stmt->errorCode() != 0){
    			$error = $stmt->errorInfo();
    			throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste des todos");
    		}
    		$todos = array();
    		while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
    			$todos[] = new Todo($data);
    		}
    		return $todos;
    	}catch(PDOException $e){
    		throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir la liste des Todos");
    	}
    }
    
    /**
     * Add the executor on the todo $tid (finish the todo)
     * @param $tid : the id of the todo
     * @param $executor : the username of the executor
     * @return boolean $b : true if the update was a success, false otherwise.
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function check($tid, $executor){		
	    try{
		    $sql = "UPDATE admin_todo SET accomplishment_date = CURRENT_TIMESTAMP, executor = :exec WHERE id=:id";
		    $stmt = $this->_db->prepare($sql);
		    $n = $stmt->execute(array('exec' => $executor, 'id' => $tid));
		    if($stmt->errorCode() != 0){
			    $error = $stmt->errorInfo();
			    throw new SQLException($error[2], $error[0], $sql, "Impossible d'effectuer le check du todo");
		    }
		    return ($n > 0);
	    }catch(PDOException $e){
		    throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'effectuer le check du todo");
	    }
    }
    
    
    /**
     * Update the title and the description of the todo 
     * @param $tid : the id of the todo
     * @param $title : the new title
     * @param $description : the new description
     */
    public function update($tid, $title, $description){
 		try{
	    	$sql = "UPDATE admin_todo SET title =:title, description = :descri WHERE id=:id";
		    $stmt = $this->_db->prepare($sql);
		    $n = $stmt->execute(array('title' => $title, 'descri' => $description,'id' => $tid));
		    if($stmt->errorCode() != 0){
			    $error = $stmt->errorInfo();
			    throw new SQLException($error[2], $error[0], $sql, "Impossible d'effectuer la mise a jour du todo");
		    }
		    return ($n > 0);
	    }catch(PDOException $e){
		    throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'effectuer la mise a jour du todo");
	    }
    }
    
    
    /**
     * remove a given todo from the DB
     * @param int $tid : the identifier of the Todo Object
     * @return boolean $b : true if the removal was a success. False otherwise
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function delete($tid){
 		try {
	    	$sql = "DELETE FROM admin_todo WHERE id=:id";
        	$stmt = $this->_db->prepare($sql);
        	$stmt->execute(array( 'id' => $tid));
        	if($stmt->errorCode() != 0){
		        $error = $stmt->errorInfo();
	        	throw new SQLException($error[2], $error[0], $sql, "Impossible de supprimer une activite");
	        }
	        return true;       
        }catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible de supprimer une activite");
        	return false;
        }
    }
    
    
}
?>