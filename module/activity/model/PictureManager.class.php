<?php
/*
 * Created on 4 avr. 2012
 *
 * MAES Jerome, Webkot 2011-2012
 * Class Description : Manager of the Picture
 *
 */
 

class PictureManager {

	protected static $_instance;
	private $_db; // Instance of Database
	private $_apc;
	
	const APC_ACTIVITY_LASTCOM = 'activity-last-commented-picture';
	const APC_ACTIVITY_PICTURES = 'activity-pictures-';
	const APC_ACTIVITY_PICTURE = 'activity-picture-';

	
	/**
	 * getInstance 
	 * @return PictureManager $instance : the instance of PictureManager
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
    
    
    /** add a Picture in th DB
     * @param int $idactivity : the identifier of the Activity
     * @param string $filename : the name of the image file
     * @param string $time : the time the picture was taken in the format HH:mm:ss
     * @param char $iscensured : '0' if the Picture is not censured, '0' otherwise
     * @param char $isvideo : '0' if the Picture is a video, '0' otherwise
     * @return boolean $b : true if the User was added, false otherwise
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     * @throws NullObjectException : this exception is raised when the specified Object didn't exist
     * @uses APC
     */
    public function add($idactivity,$filename,$time,$iscensured,$isvideo){
 		try {
        	$sql = "INSERT INTO picture(idactivity,filename, time, iscensured, isvideo) " .
        			"VALUES (:idactivity, :filename, :time, :iscensured, :isvideo)";
	        $stmt = $this->_db->prepare($sql);
	        $tab = array('idactivity' => $idactivity, 'filename' => $filename, 'time' => $time, 'iscensured' => $iscensured, 'isvideo' => $isvideo);
        	$stmt->execute($tab);
	        if($stmt->errorCode() != 0){
		        $error = $stmt->errorInfo();
	        	throw new SQLException($error[2], $error[0], $sql, "Impossible d'ajouter une Picture" . print_r($tab));
	        }
	        if($this->_apc){
	        	apc_delete(self::APC_ACTIVITY_PICTURES . $idactivity);
	        }
	        return true;    
        }catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'ajouter une Picture" . print_r($tab));
        	return false;
        }
        
 	}
    
    /**
     * get a specified Picture Object
     * @param int $id : the identifier of the Picture Object
     * @return Picture $pict : the specified Picture Object
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     * @throws NullObjectException : this exception is raised when the specified Object didn't exist
     * @uses APC
     */
    public function getPicture($id){
    	try{
    		if($this->_apc && apc_exists(self::APC_ACTIVITY_PICTURE . $id)){
    			$picture = apc_fetch(self::APC_ACTIVITY_PICTURE . $id);
    		}else{	
	    		$sql = 'SELECT * FROM picture WHERE id = :id';
		    	$stmt = $this->_db->prepare($sql);
		        $stmt->execute(array( 'id' => $id ));   
		        if($stmt->errorCode() != 0){
					$error = $stmt->errorInfo();
			        throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la Picture specifiee");
			    } 
				$data = $stmt->fetch(PDO::FETCH_ASSOC);
				if(empty($data)){
					throw new NullObjectException();
				}
		        $picture = new Picture($data);
				if($this->_apc){
					apc_store(self::APC_ACTIVITY_PICTURE . $id, $picture, 43000);
				}
    		}
			$manager = CommentManager::getInstance();
			$picture->setComments($manager->getCommentsPicture($picture->getId()));
	        return $picture;
    	}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir la Picture specifiee");
        }
    }
    
    
    /**
     * get the list of the Picture for an specified Activity
     * @param int $idact : the identifier of the Activity
     * @return array $pict : array of Picture Object
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     * @uses APC
     */
    public function getListPicture($idact){
    	if($this->_apc && apc_exists(self::APC_ACTIVITY_PICTURES . $idact)){
    		return apc_fetch(self::APC_ACTIVITY_PICTURES . $idact);
    	}else{
    		try{
    			//In this request, the 'WHERE idactivity =:idact' is in every syb Select queries for performance reason ;)
    			$sql = 'SELECT * FROM (
							SELECT P.id AS id, P.idactivity AS idactivity, P.filename AS filename, P.time AS time, P.viewed AS viewed, P.iscensured AS iscensured, P.isvideo AS isvideo, count( C.id ) AS nbcomments
							FROM picture P, comment C
							WHERE idactivity =:idact AND P.id = C.pictureid GROUP BY P.id
							UNION
							SELECT P.id AS id, P.idactivity AS idactivity, P.filename AS filename, P.time AS time, P.viewed AS viewed, P.iscensured AS iscensured, P.isvideo AS isvideo, 0 AS nbcomments
							FROM picture P
							WHERE NOT EXISTS ( SELECT * FROM comment WHERE pictureid = P.id ) AND P.idactivity = :idact
						) as T where T.idactivity = :idact ORDER BY T.id ASC';
    			$stmt = $this->_db->prepare($sql);
    			$stmt->execute(array( 'idact' => $idact ));
    			if($stmt->errorCode() != 0){
    				$error = $stmt->errorInfo();
    				throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste Picture d'une activite specifiee");
    			}
    			$pictures = array();
    			while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
    				$pictures[] = new Picture($data);
    			}
    			if($this->_apc){
    				apc_store(self::APC_ACTIVITY_PICTURES . $idact, $pictures, 86000);
    			}
    			return $pictures;
    		}catch(PDOException $e){
    			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir la liste Picture d'une activite specifiee");
    		}
    	}
    }
    

    
    public function getCensuredPicture(){
    	try{
    		$sql = 'SELECT P.id AS id, P.idactivity AS idactivity, P.filename AS filename, P.time AS time, P.viewed AS viewed, P.iscensured AS iscensured, P.isvideo AS isvideo, A.directory as directory
					FROM picture P, activity A
					WHERE P.idactivity = A.id AND P.iscensured = "1" 
    				ORDER BY A.date DESC';
    		$stmt = $this->_db->prepare($sql);
    		$stmt->execute();
    		if($stmt->errorCode() != 0){
    			$error = $stmt->errorInfo();
    			throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste des photos censurées");
    		}
    		$pictures = array();
    		while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
    			$pictures[] = new Picture($data);
    		}
    		return $pictures;
    	}catch(PDOException $e){
    		throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir la lsite des photos censurées");
   		}
    }
    
    /** 
     * get the Top10 of the most viewed Picture ever
     * @return array $pict : array of Picture Objects
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
     public function getTop10ViewedEver(){
     	try{
	     	$sql = "SELECT P.id as id, P.idactivity as idactivity, P.filename as filename, P.time as time, P.viewed as viewed, P.iscensured as iscensured, P.isvideo as isvideo, A.directory as directory FROM picture P, activity A WHERE (A.id = P.idactivity) and P.iscensured<>'1' and A.level = 0 ORDER BY P.viewed DESC LIMIT 0, 10";
	        $stmt = $this->_db->prepare($sql);
	        $stmt->execute(); 
	        if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir le Top10 view Ever");
		    }  
	     	$pict = array();
	        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){   	
	         	$pict[] = new Picture($data);
	        }   
	        return $pict;
     	}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir le Top10 view Ever");
        }
     }
     
      
    /**
     * get the Top10 most view Picture durant the Academic specified Year
     * @param int $year : the begin of the academic year, in the form YYYY
     * @return array $pict : array of Picture Objects
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
     public function getTop10ViewedYear($year){
     	try{
	     	$sql = "SELECT P.id as id, P.idactivity as idactivity, P.filename as filename, P.time as time, P.viewed as viewed, P.iscensured as iscensured, P.isvideo as isvideo, A.directory as directory FROM picture P, activity A WHERE (A.id = P.idactivity) and (A.date >= :year AND A.date < :year2) and P.iscensured<>'1' and A.level=0 ORDER BY P.viewed DESC LIMIT 0, 10";
	        $stmt = $this->_db->prepare($sql);
	        $stmt->execute(array('year' => ($year)."-".BEGINYEAR_MONTH."-".BEGINYEAR_DAY, 'year2' => ($year+1)."-".BEGINYEAR_MONTH."-".BEGINYEAR_DAY)); 
	       	if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir le Top10 view for a specified year");
		    } 
		    $pict = array();
	        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
	         	$pict[] = new Picture($data);
	        }
	        return $pict;
     	}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir le Top10 view for a specified year");
        }
     }
     
     
     /**
      * get the Top10 Picture of most commented ever
      * @return array $pict : array of Picture Objects
      * @throws SQLException : this exception is raised if the Query is refused
      * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
      */
     public function getTop10CommentEver(){
     	try{
	     	$sql= "SELECT P.id as id, P.idactivity as idactivity, P.filename as filename, P.time as time, P.viewed as viewed, P.iscensured as iscensured, P.isvideo as isvideo, count(C.id) as nbcomments, A.directory as directory FROM comment C, picture P, activity A WHERE C.pictureid=P.id and P.idactivity=A.id and P.iscensured<>'1' group by C.pictureid order by count(*) desc limit 0,10";
	        $stmt = $this->_db->prepare($sql);
	        $stmt->execute();
	       	if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir le Top10 commented Ever");
		    } 
	     	$pict = array();
	        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){  	
	         	$pict[] = new Picture($data);
	        }   
	        return $pict; 
     	}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir le Top10 commented Ever");
        }
     }
     
   	/**
      * get the Top10 Picture of most commented for a specified year
      * @param int $year : in the format YYYY
      * @return array $pict : array of Picture Objects
      * @throws SQLException : this exception is raised if the Query is refused
      * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
      */
     public function getTop10CommentYear($year){
     	try{
	     	$sql= "SELECT P.id as id, P.idactivity as idactivity, P.filename as filename, P.time as time, P.viewed as viewed, P.iscensured as iscensured, P.isvideo as isvideo, count(C.id) as nbcomments, A.directory as directory FROM comment C, picture P, activity A WHERE C.pictureid=P.id and P.idactivity=A.id and (A.date >= :year and A.date < :year2) and P.iscensured<>'1' group by C.pictureid order by count(*) desc limit 0,10";
	        $stmt = $this->_db->prepare($sql);
	        $stmt->execute(array('year' => ($year)."-".BEGINYEAR_MONTH."-".BEGINYEAR_DAY, 'year2' => ($year+1)."-".BEGINYEAR_MONTH."-".BEGINYEAR_DAY)); 
	        if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir le Top10 commented pour une annee specifiee");
		    }
		    $pict = array();
		    while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){  	
	         	$pict[] = new Picture($data);
	        }   
	        return $pict;  	
     	}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir le Top10 commented pour une annee specifiee");
        }
     }
     
     
    /**
     * get a specified number of last LastCommentedPicture Objects
     * @param int $nbr : the number of picture to return
     * @return array $lcp : array containing $nb LastCommentedPicture Objects
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     * @uses APC
     */
    public function getLastCommentedPicture($nbr = 12, $level){	
    	if($this->_apc && apc_exists(self::APC_ACTIVITY_LASTCOM)){
    		return apc_fetch(self::APC_ACTIVITY_LASTCOM);
    	}else{
	    	try {
		    	 $sql = 'SELECT P.*, A.directory as directory
						FROM activity A, comment C, user U , picture P 
						WHERE P.idactivity=A.id AND C.pictureid=P.id AND C.userid=U.id AND A.level <= :level
						ORDER by C.date DESC LIMIT 0,' . $nbr;
		         $stmt = $this->_db->prepare($sql);
		         $stmt->execute(array('level' => $level));  
		         if($stmt->errorCode() != 0){
				    $error = $stmt->errorInfo();
			        throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir les dernieres Picture commentees");
			     } 
		    	 $pictures = array();
		    	 $manager = CommentManager::getInstance();
		         while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){   	   	
		         	$tmp = new Picture($data);
			    	$tmp->setComments($manager->getCommentsPicture($tmp->getId()));
		         	$pictures[] = $tmp;
		         } 
		         if($this->_apc){
			         apc_store(self::APC_ACTIVITY_LASTCOM, $pictures, 175000);
		         }
		         return $pictures;	
	    	}catch(PDOException $e){
	        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir les dernieres Picture commentees");
	        }
    	}
    }
     
     /**
      * modify the censured of a Picture
      * @param int $pictid : the identifier of the Picture
      * @param boolean $censured : the value of the censured to put
      * @throws SQLException : this exception is raised if the Query is refused
      * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
      * @uses APC
      */
     public function changeCensure($pictid,$censured){
     	try{
	     	$sql = "UPDATE picture SET iscensured= :censured WHERE id=:pid";
	     	$stmt = $this->_db->prepare($sql);
	        $stmt->execute(array('censured' => $censured, 'pid' => $pictid));
	        if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible de modifier la censure de la photo");
		    }
		    if($this->_apc){
		    	$picture = $this->getPicture($pictid);
		    	apc_delete(self::APC_ACTIVITY_PICTURES . $picture->getIdactivity());
		    	apc_delete(self::APC_ACTIVITY_PICTURE . $pictid);
		    }
		    return true;
     	}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible de modifier la censure de la photo");
        	return false;
        }
     }
    
    /**
     * Check if the picture with id = $id exits
     * @param int $pictid : id of the picture
     * @return bool : true if the picture $pictid exists, false otherwise  
     * @throws SQLException : this exception is raised if the Query is refused 
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
     public function exists($pictid){
     	try{
     		$sql = "SELECT * FROM picture WHERE id = :pictid";
	    	$stmt = $this->_db->prepare($sql);
	        $stmt->execute(array('pictid'=>$pictid));
	        if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible de determiner l'existance d'un Picture specifiee");
		    }
			$nbr = $stmt->rowCount();
			return ($nbr > 0);
     	}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible de determiner l'existance d'un Picture specifiee");
        	return false;
        }
    }
    
    
     /**
     * Delete an Picture Object of a specifeid Activity
     * @param int $aid : the identifier of the Activity
     * @return boolean $b : true if the removing was successful
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function deleteActivityPictures($aid){
        try {
        	$sql = "DELETE FROM picture WHERE idactivity = :id";
        	$stmt = $this->_db->prepare($sql);
        	$stmt->execute(array( 'id' => $aid));
        	if($stmt->errorCode() != 0){
		        $error = $stmt->errorInfo();
	        	throw new SQLException($error[2], $error[0], $sql, "Impossible de supprimer les images d'une activite");
	        }
	        if($this->_apc){
	        	apc_delete(self::APC_ACTIVITY_PICTURES . $aid);
	        	apc_delete(PictureManager::APC_ACTIVITY_LASTCOM);
	        }
	        return true;       
        }catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible de supprimer les images d'une activite");
        	return false;
        }
    }
    
    /**
     * Delete a given picture Object
     * @param int $aid : the identifier of the Activity
     * @param string $filename : the filename of the image file
     * @return boolean $b : true if the removing was successful
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function delete($aid, $filename){
    	try {
    		$sql = "DELETE FROM picture WHERE idactivity = :id AND filename = :filename";
    		$stmt = $this->_db->prepare($sql);
    		$stmt->execute(array( 'id' => $aid, 'filename' => $filename));
    		if($stmt->errorCode() != 0){
    			$error = $stmt->errorInfo();
    			throw new SQLException($error[2], $error[0], $sql, "Impossible de supprimer la photo " . $filename);
    		}
    		if($this->_apc){
    			apc_delete(self::APC_ACTIVITY_PICTURES . $aid);
    			apc_delete(PictureManager::APC_ACTIVITY_LASTCOM);
    		}
    		return true;
    	}catch(PDOException $e){
    		throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible de supprimer la photo " . $filename);
    		return false;
    	}
    }

    /*
    /**
     * get number of picture in the period $begin - $end
     * @param date $begin : the beginning of the period. Format : yyyy-mm-dd
     * @param date $end : the end of the period. Format : yyyy-mm-dd
     * @return int $nb : the number of Picture in the specified period 
     * @throws SQLException : this exception is raised if the Query is refused 
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     
    public function getStatPict($begin,$end){
    	try{
	    	$sql = "SELECT P.id FROM picture P, activity A WHERE (A.date between :begin and :end) and (P.idactivity=A.id)";
	    	$stmt = $this->_db->prepare($sql);
	        $stmt->execute(array('begin' => $begin, 'end' => $end));
	        if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir les statistiques des Pictures sur la periode donnee");
		    }
			$nb = (int) $stmt->rowCount();   
	        return $nb;		
    	}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir les statistiques des Pictures sur la periode donnee");
        	return false;
        }
    }*/
    
   
 	/**
 	 * Update the number of view
 	 * @param int $aid : the identifier of the Activity
	 * @return boolean $b : true if the update is a success, false otherwise
	 * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     * @uses APC
 	 */
 	public function updateView($pid){
 		try{
	 		$sql = "UPDATE picture SET viewed=viewed+1 WHERE id=:id";
	 		$stmt = $this->_db->prepare($sql);
	 		if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'effectuer la mise a jour du nombre de vue");
		    }
	        $n = $stmt->execute(array('id' => $pid));
	        if($this->_apc){
	        	if(apc_exists(self::APC_ACTIVITY_PICTURE . $pid)){
	        		$picture = apc_fetch(self::APC_ACTIVITY_PICTURE . $pid);
	        		$picture->setViewed($picture->getViewed()+1);
	        		apc_store(self::APC_ACTIVITY_PICTURE . $pid, $picture, 43000);
	        	}
	        }
	        return ($n > 0);
 		}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'effectuer la mise a jour du nmbre de vue");
        }
 	}
   
   
   
 	/** add a User in th DB
     * @param array $data : key-array containing all the information to complete the user Object
     * @return boolean $b : true if the User was added, false otherwise
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     * @throws NullObjectException : this exception is raised when the specified Object didn't exist
     */
    public function MigrPict($id,$idactivity,$filename,$time,$viewed,$iscensured,$isvideo){
 		try {
        	$sql = "INSERT INTO picture(id, idactivity,filename, time, viewed, iscensured, isvideo) " .
        			"VALUES (:id,:idactivity, :filename, :time, :viewed, :iscensured, :isvideo)";
	        $stmt = $this->_db->prepare($sql);
	        $tab = array('id'=>$id,'idactivity' => $idactivity, 'filename' => $filename, 'time' => $time, 'viewed' => $viewed, 'iscensured' => $iscensured, 'isvideo' => $isvideo);
        	$stmt->execute($tab);
	        if($stmt->errorCode() != 0){
		        $error = $stmt->errorInfo();
	        	throw new SQLException($error[2], $error[0], $sql, "% Impossible d'ajouter une Picture" . print_r($tab));
	        }
	        return true;    
        }catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'ajouter une Picture" . print_r($tab));
        	return false;
        }
        
 	}
 	
}
 
?>
