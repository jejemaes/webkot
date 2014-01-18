<?php



class GossipManager {

   	protected static $_instance;
	private $_db; // Instance de Database


	/**
	 * GetInstance 
	 * @return GossipMananger $instance : the only one instance of the manager
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
     * add a Gossip
     * @param string $content : the content of the gossip
     * @param int $uid : the identifier of the user (authors)
     * @return boolean $b : true if the Gossip was added.
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
     public function add($content,$uid){
        try {
        	$sql = "INSERT INTO potins (content, userid, timestamp) VALUES (:content, :uid,'".time()."')";
	        $stmt = $this->_db->prepare($sql);
        	$stmt->execute(array( 'content' => $content, 'uid' => $uid));
	        if($stmt->errorCode() != 0){
		        $error = $stmt->errorInfo();
	        	throw new SQLException($error[2], $error[0], $sql, "Impossible d'ajouter un potin");
	        }
	        return true;     
        }catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'ajouter un potin");
        	return false;
        }
    }
    
    /**
     * get the Gossip identified by $gid
     * @param int $gid : the id of the Gossip object
     * @return Gossip $gossip : the Gossip Object
     * @throws NullObjectException : this exception is raised when the specified Object didn't exist
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function getGossip($gid){
    	try{  
	    	$sql = "SELECT P.id as id, P.content as content, P.userid as userid, U.username as user, P.timestamp as timestamp, P.censure as censure FROM potins P, user U WHERE (P.userid = U.id) AND (P.id = :id) LIMIT 1";
	       	$stmt = $this->_db->prepare($sql);
	        $stmt->execute(array( 'id' => $gid ));
			if($stmt->errorCode() != 0){
			    $error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir un Potin");
		    }
			$data = $stmt->fetch(PDO::FETCH_ASSOC);
			if($data == null){
				throw new NullObjectException();
			}
			$data['liker'] = $this->getLikerList($data['id']);
	        $data['disliker'] = $this->getDislikerList($data['id']);
	        $gossip = new Gossip($data);
	     	return $gossip;		
    	}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir un potin");
        	return false;
        }
    }
    
    
     
    /**
     * Count the number Gossip respecting the where clause $where
     * @param $where : the where clause, empty if not.
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function getCountGossip(){
    	try{
	    	$sql = 'SELECT id FROM potins ';
	    	$stmt = $this->_db->prepare($sql);
	    	$stmt->execute();
	    	if($stmt->errorCode() != 0){
			    $error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenirle nombre de Potin");
		    }
	        $nb = (int) $stmt->rowCount();   
	        return $nb;		
    	}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir le nombre de potin");
        	return false;
        }
    }
    
 	/**
 	 * Obtain a list of the Gossip order by the id. The lenght of the list is $nbr and it start from the $limit e element
 	 * @param int $limit : the number where start the list
 	 * @param int $nbr : the lenght of the list
 	 * @return array $list : the list of Gossip Object
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
 	 */
 	public function getListGossip($limit, $nbr){
    	try{
	        $sql = "SELECT P.id as id, P.content as content, P.userid as userid, U.username as user, P.timestamp as timestamp, P.censure as censure FROM potins P, user U WHERE (P.userid = U.id) ORDER BY timestamp DESC LIMIT " . $limit . ",".$nbr;
	      	$stmt = $this->_db->prepare($sql);
	        $stmt->execute();   
	        if($stmt->errorCode() != 0){
			    $error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste des Potin");
		    }
	    	$list = array();
	        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
	         	$tmp = new Gossip($data);
	         	$tmp->setLiker($this->getLikerList($data['id']));
	         	$tmp->setDisliker($this->getDislikerList($data['id']));
	         	$list[] = $tmp;
	        }   
	        return $list;	
    	}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir la liste des potin");
        	return false;
        }
    }
    
    
    /**
     * get the list of the liker of a specified Gossip
     * @param int $gid : the identifier of the Gossip
     * @return array $stmt : an array where the key (userid) id the userid, and the value (username) is the username of the liker
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function getLikerList($gid){
	    try{
		    $sql = 'SELECT U.id as userid, U.username as username FROM potins_comments P, user U WHERE (P.userid = U.id) AND (P.potinid = :id) AND (P.type =1)';
		    $stmt = $this->_db->prepare($sql);
		    $stmt->execute(array( 'id' => $gid )); 
		    if($stmt->errorCode() != 0){
			    $error = $stmt->errorInfo();
			    throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste des Liker");
		    }
		    $result = array();
		    while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
			    $result[$data['userid']] = $data['username'];
		    }
		    return $result;
	    }catch(PDOException $e){
		    throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir la liste des Liker");
	    }
    }
    
     /**
     * get the list of the disliker of a specified Gossip
     * @param int $gid : the identifier of the Gossip
     * @return array $stmt : an array where the key (userid) id the userid, and the value (username) is the username of the disliker
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function getDislikerList($gid){
    	try{
	    	$sql = 'SELECT U.id as userid, U.username as username FROM potins_comments P, user U WHERE (P.userid = U.id) AND (P.potinid = :id) AND (P.type = -1)';
	    	$stmt = $this->_db->prepare($sql);
	        $stmt->execute(array( 'id' => $gid )); 
	        if($stmt->errorCode() != 0){
			    $error = $stmt->errorInfo();
			    throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste des Disliker");
		    }
	        $result = array();
	        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
	        	$result[$data['userid']] = $data['username'];
	        }
	        return $result;
    		
    	}catch(PDOException $e){
		    throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir la liste des Disliker");
	    }
    }
    
    /**
     * make like a Gossip by a User
     * @param in $gid : the Gossip id
     * @param int uid : the User id
     * @return boolean $b : true if the like was added, false otherwise
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function like($gid,$uid){
    	 try {
    	 	$sql = "INSERT INTO potins_comments(userid,type,potinid) VALUES(:uid, '1',:gid)";
        	$stmt = $this->_db->prepare($sql);
	        $stmt->execute(array( 'uid' => $uid, 'gid' => $gid )); 
	        if($stmt->errorCode() != 0){
			    $error = $stmt->errorInfo();
			    throw new SQLException($error[2], $error[0], $sql, "Impossible d'aimer un Potin");
		    }
	        return true;       
        }catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'aimer un Potin");
        	return false;
        }
    }
    
     /**
     * unmake like a Gossip by a User
     * @param in $gid : the Gossip id
     * @param int uid : the User id
     * @return boolean $b : true if the like was deleted, false otherwise
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function unlike($gid,$uid){
	    try {
		    $sql = "DELETE FROM potins_comments WHERE userid=:uid AND potinid=:gid AND type='1'";
		    $stmt = $this->_db->prepare($sql);
		    $stmt->execute(array( 'uid' => $uid, 'gid' => $gid )); 
		    if($stmt->errorCode() != 0){
			    $error = $stmt->errorInfo();
			    throw new SQLException($error[2], $error[0], $sql, "Impossible de ne plus aimer un Potin");
		    }
		    return true;       
	    }catch(PDOException $e){
		    throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible de ne plus aimer de potin");
		    return false;
	    }
    }
    
    
    /**
     * make dislike a Gossip by a User
     * @param in $gid : the Gossip id
     * @param int $uid : the User id
     * @return boolean $b : true if the dislike was added, false otherwise
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function dislike($gid,$uid){
    	 try {
    	 	$sql = "INSERT INTO potins_comments(userid,type,potinid) VALUES(:uid, '-1',:gid)";
        	$stmt = $this->_db->prepare($sql);
	        $stmt->execute(array( 'uid' => $uid, 'gid' => $gid )); 
	        if($stmt->errorCode() != 0){
			    $error = $stmt->errorInfo();
			    throw new SQLException($error[2], $error[0], $sql, "Impossible de detester un Potin");
		    }
	        return true;       
        }catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible de detester un Potin");
        	return false;
        }
    }
    
    
     /**
     * unmake dislike a Gossip by a User
     * @param in $gid : the gossip id
     * @param int uid : the user id
     * @return boolean $b : true if the dislike was deleted, false otherwise
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function undislike($gid,$uid){
    	 try {
    	 	$sql = "DELETE FROM potins_comments WHERE userid=:uid AND potinid=:gid AND type=\"-1\"";
        	$stmt = $this->_db->prepare($sql);
	        $stmt->execute(array( 'uid' => $uid, 'gid' => $gid )); 
	        if($stmt->errorCode() != 0){
			    $error = $stmt->errorInfo();
			    throw new SQLException($error[2], $error[0], $sql, "Impossible de ne plus detester un Potin");
		    }
	        return true;       
        }catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible de ne plus detester un Potin");
        	return false;
        }
    }
    
  	
  	/**
     * censure a Gossip
     * @param in $gid : the Gossip id
     * @return boolean $b : true if the gossip is censured now, false otherwise
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function censure($gid){
    	 try {
    	 	$sql = "UPDATE potins SET censure=\"1\" WHERE id=:gid";
        	$stmt = $this->_db->prepare($sql);
	        $stmt->execute(array('gid' => $gid )); 
	        if($stmt->errorCode() != 0){
			    $error = $stmt->errorInfo();
			    throw new SQLException($error[2], $error[0], $sql, "Impossible de censurer un Potin");
		    }
	        return true;       
        }catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible de censurer un Potin");
        	return false;
        }
    }
    
    /**
     * uncensure a Gossip
     * @param in $gid : the Gossip id
     * @return true if the gossip is uncensured now, false otherwise
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function uncensure($gid){
    	 try {
    	 	$sql = "UPDATE potins SET censure=\"0\" WHERE id=:gid";
        	$stmt = $this->_db->prepare($sql);
	        $stmt->execute(array('gid' => $gid )); 
	        if($stmt->errorCode() != 0){
			    $error = $stmt->errorInfo();
			    throw new SQLException($error[2], $error[0], $sql, "Impossible de decensurer un Potin");
		    }
	        return true;       
        }catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible de censurer un Potin");
        	return false;
        }
    }
    
    
    public function delete($pid){
    	try {
    		$sql = "DELETE FROM potins WHERE id = :id";
    		$stmt = $this->_db->prepare($sql);
    		$stmt->execute(array( 'id' => $pid));
    		if($stmt->errorCode() != 0){
    			$error = $stmt->errorInfo();
    			throw new SQLException($error[2], $error[0], $sql, "Impossible de supprimer un potin.");
    		}
    		return true;
    	}catch(PDOException $e){
    		throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible de supprimer un potin.");
    		return false;
    	}
    }
    
    
    
    
 	
 	 /** add a User in th DB
     * @param array $data : key-array containing all the information to complete the user Object
     * @return boolean $b : true if the User was added, false otherwise
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     * @throws NullObjectException : this exception is raised when the specified Object didn't exist
     */
    public function MigrPotin($id,$content,$userid,$timestamp,$censure){
 		try {
        	$sql = "INSERT INTO potins(id, content,userid, timestamp,censure) " .
        			"VALUES (:id,:content, :userid, :timestamp, :censure)";
	        $stmt = $this->_db->prepare($sql);
        	$stmt->execute(array('id'=>$id,'content' => $content, 'userid' => $userid, 'timestamp' => $timestamp, 'censure' => $censure));
	        if($stmt->errorCode() != 0){
		        $error = $stmt->errorInfo();
	        	throw new SQLException($error[2], $error[0], $sql, "Impossible d'ajouter une Potin");
	        }
	        return true;    
        }catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'ajouter une Potin");
        	return false;
        }
        
 	}
    
}
?>