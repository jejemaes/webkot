<?php

class CommentManager {

	protected static $_instance;
	private $_db; // Instance de Database
	private $_apc;
	
	const APC_ACTIVITY_PICTURE_COMM = "activity-picture-comments-";
	

	/**
	 * GetInstance 
	 * @return CommentManager $instance : the instance of CommentManager
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
     * Add an Comment in the DB
     * @param array $data : key-array containing the information of the Comment 
     * @return boolean $b : true if the Comment was added, false otherwise
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     * @uses APC
     */
    public function add($data){
    	try {
        	$sql = "INSERT INTO comment(userid,pictureid,comment,ip) VALUES (:userid, :pictureid,:comment ,:ip)";
	         $stmt = $this->_db->prepare($sql);
        	$stmt->execute(array( 'userid' => $data['userid'], 'pictureid' => $data['pictureid'], 'comment' => $data['comment'], 'ip' => $data['ip']));
	        if($stmt->errorCode() != 0){
		        $error = $stmt->errorInfo();
	        	throw new SQLException($error[2], $error[0], $sql, "Impossible d'ajouter un commentaire");
	        }
    	 	if($this->_apc){
	        	apc_delete(self::APC_ACTIVITY_PICTURE_COMM . $data['pictureid']);
    	 		apc_delete(PictureManager::APC_ACTIVITY_LASTCOM);
    	 		$picture = PictureManager::getInstance()->getPicture($data['pictureid']);
    	 		apc_delete(PictureManager::APC_ACTIVITY_PICTURES . $picture->getIdactivity());
	        }
	        return true;       
        }catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'ajouter un commentaire");
        	return false;
        }
    }
    
    
    /**
     * get a given Comment Object
     * @param int $id
     * @throws SQLException
     * @throws NullObjectException
     * @throws DatabaseException
     * @return Comment
     */
    public function getComment($id){
    		try{
    			$sql = 'SELECT * FROM comment WHERE id = :id';
    			$stmt = $this->_db->prepare($sql);
    			$stmt->execute(array( 'id' => $id ));
    			if($stmt->errorCode() != 0){
    				$error = $stmt->errorInfo();
    				throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir le Commentaire specifie");
    			}
    			$data = $stmt->fetch(PDO::FETCH_ASSOC);
    			if(empty($data)){
    				throw new NullObjectException();
    			}
    			$comm = new Comment($data);
    			return $comm;
    		}catch(PDOException $e){
    			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir le Commentaire specifie");
    		}
    	
    }
    
    
    
    /**
     * Delete an Comment Object
     * @param int $aid : the identifier of the Comment to remove
     * @return boolean $b : true if the removing was successful
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     * @uses APC
     */
    public function delete($comid){
    	try {
    		$tmp = $this->getComment($comid);
    		
        	$sql = "DELETE FROM comment WHERE id= :id";
	        $stmt = $this->_db->prepare($sql);
        	$stmt->execute(array( 'id' => $comid));
        	if($stmt->errorCode() != 0){
		        $error = $stmt->errorInfo();
	        	throw new SQLException($error[2], $error[0], $sql, "Impossible de supprimer une commentaire");
	        }
	        if($this->_apc){
	        	apc_delete(self::APC_ACTIVITY_PICTURE_COMM . $tmp->getPictureid());
	        	apc_delete(PictureManager::APC_ACTIVITY_LASTCOM);
	        	$picture = PictureManager::getInstance()->getPicture($tmp->getPictureid());
	        	apc_delete(PictureManager::APC_ACTIVITY_PICTURES . $picture->getIdactivity());
	        }
	        return true;      
        }catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible de supprimer un commentaire");
        	return false;
        }
    }
    
    
    /**
     * get all the Comment for a specified Picture
     * @return array $list : array of Comment Objects
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     * @uses APC
     */
    public function getCommentsPicture($pictid){
    	if($this->_apc && apc_exists(self::APC_ACTIVITY_PICTURE_COMM . $pictid)){
    		return apc_fetch(self::APC_ACTIVITY_PICTURE_COMM . $pictid);
    	}else{		
	    	try{
		        $sql = "SELECT C.id, C.pictureid, U.username as userid, C.comment, C.date, C.ip, C.rank FROM user U, comment C WHERE (C.userid = U.id) and (C.pictureid in (SELECT pictureid FROM comment WHERE pictureid = :pid)) ORDER BY date ASC";
		    	 $stmt = $this->_db->prepare($sql);
		         $stmt->execute(array( 'pid' => $pictid ));
		         if($stmt->errorCode() != 0){
				    $error = $stmt->errorInfo();
			        throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste des commentaires");
			     }   
		         $tab = array();
		         while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){  
		         	$tab[] = new Comment($data); 
		         } 
		         if($this->_apc){
		         	apc_store(self::APC_ACTIVITY_PICTURE_COMM . $pictid, $tab, 175000);
		         } 
		         return $tab;
	    	}catch(PDOException $e){
	        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir la liste des commentaires");
	        }
    	}
    }
    
    
    /**
     * get the NBR_LASTCOM Comment of a given User
     * @param int $uid :the identifier of the User
     * @return array $list : array of Challenge Objects
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function getCommentsUser($uid, $nbr){
         try{
	         $sql = "SELECT * FROM comment WHERE userid = :uid ORDER BY date DESC LIMIT 0,".$nbr;
	    	 $stmt = $this->_db->prepare($sql);
	         $stmt->execute(array( 'uid' => $uid ));
	         if($stmt->errorCode() != 0){
			    $error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste des commentaires");
		     }   
	         $list = array();
	         while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){  
	         	$list[] = new Comment($data); 
	         }  
	         return $list;
    	}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir la liste des commentaires");
        }
    }
    
    /**
     * get the list of number of Comment by Picture, for a given Activity
     * @param int $aid : the identifier of the Activity
     * @return array $tab : array where key = idPicture and value = #comment on the Picture idPict
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function getCommentsActivity($aid){
	    try{
		    $sql = "SELECT P.id as pid, count(C.id) as nbr FROM picture P, comment C WHERE C.pictureid = P.id AND P.idactivity = :aid group by C.pictureid";
		    $stmt = $this->_db->prepare($sql);
		    $stmt->execute(array( 'aid' => $aid ));
		    if($stmt->errorCode() != 0){
			    $error = $stmt->errorInfo();
			    throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste des commentaires");
		    }   
		    $tab = array();
		    while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
			    $tab[$data['pid']] = $data['nbr'];
		    }
		    return $tab; 
	    }catch(PDOException $e){
		    throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir la liste des commentaires");
	    }
    }
    
    
    /**
     * get the number of Comment for a specified Activity
     * @param int $aid : the identifier of the Activity
     * @return int $nbr : the number of Comment for the Activity
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function getNbrCommentsAct($aid){
    	try{
		    $sql = 'SELECT id as nbr FROM comment WHERE pictureid IN (SELECT id FROM picture WHERE idactivity=:aid)';
	        $stmt = $this->_db->prepare($sql);
	        $stmt->execute(array( 'aid' => $aid ));
	        if($stmt->errorCode() != 0){
			    $error = $stmt->errorInfo();
			    throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la nombre de commentaires");
		    }
	        $nb = (int) $stmt->rowCount();   
	        return $nb;
    	}catch(PDOException $e){
		    throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir le nombre de commentaire");
	    }
    }
    
    
    
    /**
     * get number of comment in the period $begin - $end
     * @param string $begin : the beginning of the period. Format : yyyy-mm-dd
     * @param string $end : the end of the period. Format : yyyy-mm-dd
     * @return int $nbr : the number of Comment for the period
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function getStatComm($begin,$end){    
	    try{
		    $sql = "SELECT id FROM comment WHERE date between :begin and :end";
		    $stmt = $this->_db->prepare($sql);
		    $stmt->execute(array('begin' => $begin . " 00:00:00", 'end' => $end . " 00:00:00"));
		    if($stmt->errorCode() != 0){
			    $error = $stmt->errorInfo();
			    throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la nombre de commentaires");
		    }
		    $nb = (int) $stmt->rowCount();   
		    return $nb;
	    }catch(PDOException $e){
		    throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir le nombre de commentaire");
	    }
    }
    
    
    
    
    /** add a User in th DB
     * @param array $data : key-array containing all the information to complete the user Object
     * @return boolean $b : true if the User was added, false otherwise
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     * @throws NullObjectException : this exception is raised when the specified Object didn't exist
     */
    public function MigrCom($id,$userid,$pictureid,$comment,$rank,$ip,$date){
 		try {
        	$sql = "INSERT INTO comment(id, userid, pictureid, comment, rank, ip, date) " .
        			"VALUES (:id,:userid, :pictureid, :comment, :rank, :ip, :date)";
	        $stmt = $this->_db->prepare($sql);
        	$stmt->execute(array('id'=>$id,'userid' => $userid, 'pictureid' => $pictureid, 'comment' => $comment, 'rank' => $rank, 'ip' => $ip, 'date' => $date));
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
    
}
?>