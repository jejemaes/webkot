<?php
/*
 * Created on 4 avr. 2012
 *
 * MAES Jerome, Webkot 2011-2012
 * Description de la class : Manager of the link between a picture and a user
 *
 */
 

class MyPictureManager {

    protected static $_instance;
	private $_db; // Instance de Database


	/**
	 * GetInstance 
	 * @return MyPictureManager $instance : the instance of MyPictureManager 
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
     * Add to the DB, the link between a user and a picture
     * @param $userid : the id of the User
     * @param $pictid : the id of the Picture
     * @return boolean $b : true if it was correctly added, false otherwise
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function addFavorite($userid,$pictid){
    	 try {
        	$sql = "INSERT INTO my_picture (userid, pictureid) VALUES (:uid,:pid)";
	        $stmt = $this->_db->prepare($sql);
        	$stmt->execute(array('uid' => $userid, 'pid' => $pictid));
	        if($stmt->errorCode() != 0){
		        $error = $stmt->errorInfo();
	        	throw new SQLException($error[2], $error[0], $sql, "Impossible d'ajouter une MyPicture");
	        }
	        return true;    
        }catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'ajouter une MyPicture");
        	return false;
        }
    }
    
    /**
     * Remove a specified MyPicture of a User
     * @param int $userid : the identifier of the User
     * @param int $pictid : the identifier of the Picture
     * @return boolean $b : true if a line of the DB was removed, false otherwise
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function removeFavorite($userid,$pictid){
    	try {
    		$sql = "DELETE FROM my_picture WHERE userid = :userid AND pictureid = :pictid";
 	      	$stmt = $this->_db->prepare($sql);       	
        	$stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
        	$stmt->bindParam(':pictid', $pictid, PDO::PARAM_INT);
      	
        	$nbr = $stmt->execute();
        	if($stmt->errorCode() != 0){
		        $error = $stmt->errorInfo();
	        	throw new SQLException($error[2], $error[0], $sql, "Impossible de supprimer une MyPicture");
        	}
	        return ($nbr > 0);       
        }catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible de supprimer une MyPicture");
        	return false;
        }
    }
    
    /**
     * get the existance of a MyPicture
     * @param int $userid : the identifier of the User
     * @param int $pictid : the identifier of the Picture
     * @return boolean $b : true the specified MyPicture exists , false otherwise
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function exists($userid,$pictid){
    	try{
    		$sql = "SELECT * FROM my_picture WHERE userid = :userid AND pictureid = :pictid";
	    	$stmt = $this->_db->prepare($sql);
	        $stmt->execute(array( 'userid' => $userid,
	        					 'pictid'=>$pictid
	         					));
	        if($stmt->errorCode() != 0){
		        $error = $stmt->errorInfo();
	        	throw new SQLException($error[2], $error[0], $sql, "Impossible de voir si une MyPicture existe");
        	}
			$nbr = $stmt->rowCount();
    		return ($nbr > 0);
    	}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible de exister une MyPicture");
        	return false;
        }
    }
    
    /**
     * Get the list of favorite picture of the user $userid
     * @param int $lim : the limit, the number of line to start te list
     * @param int $nbr : the length of the list
     * @param int $userid : the identifier of the User
     * @return array : object list of informations of the picture
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function getListPicture($userid, $lim = 0, $nbr = false){
    	$limitClause = "";
    	if($nbr != false){
    		$limitClause = "LIMIT ".$lim.",".$nbr;
    	}
    	try{
	    	$sql = "SELECT A.title as title, A.date as date, A.directory as directory, P.id as id, P.filename as filename,P.time as time, P.viewed as viewed, M.date as addeddate, P.iscensured as iscensured 
	FROM activity A, picture P, my_picture M
	WHERE (A.id = P.idactivity) and (P.id = M.pictureid) and (M.userid = :userid) ORDER BY A.date DESC ".$limitClause;		
	    	$stmt = $this->_db->prepare($sql);
	        $stmt->execute(array('userid' => $userid)); 
	        if($stmt->errorCode() != 0){
		        $error = $stmt->errorInfo();
	        	throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir une liste de MyPicture");
        	}
	    	$pict = array();
	        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){   	
	         	$pict[] = new MyPicture($data);
	        }   
	        return $pict;		
    	}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir une liste de MyPicture");
        }
    }
    
    
    
    
     
    /**
     * Count the number MyPicture belonging to the specified user
     * @param int $uid : the identifier of the user
     * @return int $nb : the number of MyPicture Object of this User
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function getCountMyPict($uid){
  		try{
	    	$sql = 'SELECT pictureid FROM my_picture WHERE userid = :uid';
	    	$stmt = $this->_db->prepare($sql);
	    	$stmt->execute(array('uid' => $uid));
	    	if($stmt->errorCode() != 0){
		        $error = $stmt->errorInfo();
	        	throw new SQLException($error[2], $error[0], $sql, "Impossible de voir si une MyPicture existe");
        	}
	        $nb = (int) $stmt->rowCount();   
	        return $nb;
  		}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir le conmpte de MyPicture pour un utilisateur");
        }
    }
    
    
  
    
    
}
?>