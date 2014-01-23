<?php
/*
 * Created on 4 avr. 2012
 *
 * MAES Jerome, Webkot 2011-2012
 * Class description : manage the Activities Object (bridge with the DB)
 *
 * Convention : the setters & getters are lowercase, but their first letter is a capital letter
 * 				Use Singleton Pattern
 * 				Use PreparedStatement
 * Required : 	Activity, Picture, ActivityPicture, AbstractPicture, User
 */


class ActivityManager {

	protected static $_instance;
	private $_db; // Instance of Database
	private $_apc;
	
	const APC_ACTIVITY_ACTIVITY = 'activity-activity-';
	const APC_ACTIVITY_LIST = 'activity-activity-list-';
	
	public $apc_activity_activity;
	public $apc_activity_list;
	
	/**
	 * getInstance 
	 * @return ActivityManager $instance : the instance of ActivityManager
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
		$this->_apc = ((extension_loaded('apc') && ini_get('apc.enabled'))  && APC_ACTIVE ? true : false);
		if($this->_apc){
			$this->apc_activity_activity = APC_PREFIX . 'activity-activity-';
			$this->apc_activity_list = APC_PREFIX . 'activity-activity-list-';
		}
    }
    
    
    /**
     * Add an Activity in the DB
     * @param array $data : key-array containing the information of the Activity 
     * @return boolean $b : true if the Activity was added, false otherwise
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     * @uses APC
     */
    public function add($title,$descri,$date,$directory,$privilegeid){
        try {
        	$sql = "INSERT INTO activity(title,description,date,directory,privilege) VALUES (:title, :descri, :date, :directory, :privilegeid)";
	        $stmt = $this->_db->prepare($sql);
        	$stmt->execute(array( 'title' => $title, 'descri' => $descri, 'date' => $date, 'directory' => $directory, 'privilegeid' => $privilegeid));
	        if($stmt->errorCode() != 0){
		        $error = $stmt->errorInfo();
	        	throw new SQLException($error[2], $error[0], $sql, "Impossible d'ajouter une activit&eacute;.");
	        }
	        $this->apcDeleteActivityLists();
	        return $this->_db->getPDOInstance()->lastInsertId();     
        }catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'ajouter une activit&eacute;.");
        }
    }
    
    
    /**
     * Add an Activity in the DB
     * @param int $aid : the identifier of the Activity
     * @param int $wid : the identifier of the Webkotteur
     * @return boolean $b : true if the Activity was added, false otherwise
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function addAuthors($aid,$uid){
        try {
        	$sql = "INSERT INTO isauthor(activityid,userid) VALUES (:aid, :uid)";
	        $stmt = $this->_db->prepare($sql);
        	$stmt->execute(array( 'aid' => $aid, 'uid' => $uid));
	        if($stmt->errorCode() != 0){
		        $error = $stmt->errorInfo();
	        	throw new SQLException($error[2], $error[0], $sql, "Impossible d'ajouter un auteur.");
	        }
	        if($this->_apc){
	        	apc_delete($this->apc_activity_activity . $aid);
	        }
	        return true;     
        }catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'ajouter un auteur.");
        }
    }

    /**
     * get the authors of a given Activity
     * @param int $aid : the identifier of the Activity
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     * @return array $users : an array of User
     */
    public function getAuthors($aid){
    	try{
    		$sql = "SELECT U.id as id, U.username as username, U.mail as mail, U.name as name, U.firstname as firstname, U.school as school, U.section as section, U.address as address, U.isadmin as isadmin, U.iswebkot as iswebkot FROM user U, isauthor I WHERE U.id = I.userid AND I.activityid = :activityid ORDER BY U.firstname DESC";
    		$stmt = $this->_db->prepare($sql);
    		$stmt->execute(array('activityid' => $aid));
    		if($stmt->errorCode() != 0){
    			$error = $stmt->errorInfo();
    			throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste des auteurs.");
    		}
    		$authors = array();
    		while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
    			$authors[] = new User($data);
    		}
    		return $authors;
    	}catch(PDOException $e){
    		throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir la liste des auteurs.");
    	}
    }
    
    
    /**
     * Delete an Activity Object
     * @param int $aid : the identifier of the Activity to remove
     * @return boolean $b : true if the removing was successful
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function deleteAllAuthor($aid){
    	try {
    		$sql = "DELETE FROM isauthor WHERE activityid = :aid";
    		$stmt = $this->_db->prepare($sql);
    		$stmt->execute(array( 'aid' => $aid));
    		if($stmt->errorCode() != 0){
    			$error = $stmt->errorInfo();
    			throw new SQLException($error[2], $error[0], $sql, "Impossible de supprimer une activit&eacute;.");
    		}
    		return true;
    	}catch(PDOException $e){
    		throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible de supprimer une activit&eacute;.");
    		return false;
    	}
    }
    
    
    
    
    /**
     * Delete an Activity Object
     * @param int $aid : the identifier of the Activity to remove
     * @return boolean $b : true if the removing was successful
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     * @uses APC
     */
    public function delete($aid){
        try {
        	$sql = "DELETE FROM activity WHERE id = :id";
        	$stmt = $this->_db->prepare($sql);
        	$stmt->execute(array( 'id' => $aid));
        	if($stmt->errorCode() != 0){
		        $error = $stmt->errorInfo();
	        	throw new SQLException($error[2], $error[0], $sql, "Impossible de supprimer une activit&eacute;.");
	        }
	        if($this->_apc){
	        	apc_delete($this->apc_activity_activity . $aid);
		       	$this->apcDeleteActivityLists();
	        }
	        return true;       
        }catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible de supprimer une activit&eacute;.");
        	return false;
        }
    }
    
    
    /**
     * get a specified Activity
     * @param int $id : the identifier of the Activity
     * @param int $level : the level of the current Session
     * @return Activity $acti : the specified Activity
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     * @throws NullObjectException : this exception is raised when the specified Object didn't exist
     * @uses APC
     */
    public function getActivity($id, $level){
    	try {
    		if($this->_apc && apc_exists($this->apc_activity_activity . $id)){
    			$activity = apc_fetch($this->apc_activity_activity . $id);
    		}else{	
	    		$sql = "SELECT A.*, P.level as level FROM activity A, privilege P WHERE A.privilege = P.id AND A.id = :id AND P.level <= :level LIMIT 1";
		       	$stmt = $this->_db->prepare($sql);
		        $stmt->execute(array( 'id' => $id, 'level' => $level));
				if($stmt->errorCode() != 0){
				    $error = $stmt->errorInfo();
			        throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir une activit&eacute; sp&eacute;cifi&eacute;e.");
			    }
				$data = $stmt->fetch(PDO::FETCH_ASSOC);
				if(empty($data)){
					throw new NullObjectException("Aucune activit&eacute; n'as &eacute;t&eacute; trouv&eacute;e avec l'identifiant ".$id.".");
				}
		        $activity = new Activity($data);
		        if($this->_apc){
		        	apc_store($this->apc_activity_activity . $id, $activity, 86000);
		        }
    		}
		    //set the pictures
	        $manager = PictureManager::getInstance();
	        $picts = $manager->getListPicture($id);
	        $activity->setPictures($picts);
	        //set the authors
	        $activity->setAuthors($this->getAuthors($id));     
	     	return $activity;
    	}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir une activit&eacute; sp&eacute;cifi&eacute;e.");
        }	
    
    }
    
    /**
     * get a specified number of last Activity Objects
     * @param int $nbr : the number of the last activity to return
     * @return array $activi : array containing $nb Activity Objects
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function getLastActivity($nbr, $level = 0){
    	try {
		    $sql = 'SELECT A.id as id, A.title as title, A.description as description, A.date as date, A.directory as directory, R.level as level, A.viewed as viewed, (SELECT count(P.id) FROM picture P WHERE P.idactivity = A.id) as count, A.ispublished as ispublished FROM activity A, privilege R WHERE R.id = A.privilege AND R.level <= :level AND A.ispublished =\'1\' GROUP BY A.id ORDER BY date DESC LIMIT 0,' . $nbr;
	    	$stmt = $this->_db->prepare($sql);
	    	$stmt->execute(array('level' => $level));  
	    	if($stmt->errorCode() != 0){
		    	$error = $stmt->errorInfo();
		    	throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir les derni&egrave;res activit&eacute;s.");
	    	} 
	    	$manager = PictureManager::getInstance();
	    	$activi = array();
	    	while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){   	
		    	$tmp = new Activity($data);
	         	$picts = $manager->getListPicture($tmp->getId());
	         	$tmp->setPictures($picts);
	         	$activi[] = $tmp;
	    	}   
	    	return $activi;	
    	}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir les derni&egrave;res activit&eacute;s.");
        }
    }
    
   
 
    /**
     * Get the list of all the published Activities, for the level session
     * @return array $activi : array of Activitiy Object
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     * @uses APC
     */
    public function getListActivity($level){
    	if($this->_apc && apc_exists($this->apc_activity_list . $level)){
    		return apc_fetch($this->apc_activity_list . $level);
    	}else{		
	      	try{
		    	 $sql = "SELECT A.*, P.level FROM activity A, privilege P WHERE P.id = A.privilege AND P.level <= :level AND ispublished = '1' ORDER BY date DESC";
		         $stmt = $this->_db->prepare($sql);
		         $stmt->execute(array('level' => $level));
		         if($stmt->errorCode() != 0){
				    $error = $stmt->errorInfo();
			        throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste des activit&eacute;s publi&eacute;es.");
			     }    
		         $i = 0;
		    	 $activi = array();
		         while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){  	
		         	$activi[] = new Activity($data);
		         } 
		         if($this->_apc){
		         	apc_store($this->apc_activity_list . $level, $activi, 43000);
		         }  
		         return $activi;
	      	}catch(PDOException $e){
	        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir la liste des activit&eacute;s publi&eacute;es.");
	        }
    	}
    }
    
    
    
   
    /**
     * get the list of all the unpublished Activities
     * @return array $activi : array of Activitiy Object
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function getListUnpublishedActivity($level){
    	try{
    		$sql = "SELECT A.*, P.level, (SELECT GROUP_CONCAT(concat(U.firstname,' ', U.name,' (',U.username,')')) FROM user U, isauthor I WHERE I.userid = U.id AND activityid = A.id) as authors , ( SELECT COUNT(*) FROM picture WHERE idactivity = A.id ) as nbrpictures FROM activity A, privilege P WHERE P.id = A.privilege AND P.level <= :level AND ispublished = '0' ORDER BY A.date DESC";
		        $stmt = $this->_db->prepare($sql);
    		$stmt->execute(array('level' => $level));
    		if($stmt->errorCode() != 0){
    			$error = $stmt->errorInfo();
    			throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste des activit&eacute;s non publi&eacute;es.");
    		}
    		$i = 0;
    		$activi = array();
    		while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
    			$activi[] = new Activity($data);
    		}
    		return $activi;
    	}catch(PDOException $e){
    		throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir la liste des activit&eacute;s non publi&eacute;es.");
    	}
    }
    
    /**
     * Get the list of the activity of the school year $year
     * @param int $year : begin of the school year
     * @return array $activi : list of the activity during that year
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function getListActivityYear($year, $limit, $nbr,$level){
    	try{
    		//$sql = "SELECT A.id as id, A.title as title, A.description as description, A.date as date, A.directory as directory, A.level as level, A.viewed as viewed, count(P.id) as count, P.filename as coverpicture, A.ispublished as ispublished FROM activity A, picture P WHERE P.idactivity=A.id AND date >= :year AND date < :year2  AND level <= :level GROUP BY A.id ORDER BY date DESC LIMIT " . $limit . ",".$nbr;
	      	$sql = "SELECT A.*, P.level as level FROM activity A, privilege P WHERE A.privilege = P.id AND date >= :year AND date < :year2  AND P.level <= :level GROUP BY id ORDER BY date DESC LIMIT " . $limit . ",".$nbr;
	      	$stmt = $this->_db->prepare($sql);
	        $stmt->execute(array('year' => $year."-".BEGINYEAR_MONTH."-".BEGINYEAR_DAY , 'year2' => ($year+1)."-".BEGINYEAR_MONTH."-".BEGINYEAR_DAY , 'level' => $level));   
	        if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste des activit&eacute;s par ann&eacute;e.");
		    }
	        $manager = PictureManager::getInstance();
	    	$activi = array();
	        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){ 	
	         	$tmp = new Activity($data);
	         	$picts = $manager->getListPicture($tmp->getId());
	         	$tmp->setPictures($picts);
	         	$activi[] = $tmp;
	        }   
	        return $activi;
    	}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir la liste des activit&eacute;s par ann&eacute;e.");
        }
    }
    
    
    /**
     * get a specified number of Activity Objects
     * @param int $nbr : the number of the last activity to return
     * @return array $activi : array containing $nb Activity Objects, but without the field of the table picture !
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function getSelectionActivity($offset,$nbr, $level){
    	try {
    		$sql = "SELECT A.id as id, A.title as title, A.description as description, A.date as date, A.directory as directory, P.level as level, A.viewed as viewed, A.ispublished as ispublished, GROUP_CONCAT(concat(U.firstname,' ', U.name,' (',U.username,')')) as authors, ( SELECT COUNT(*) FROM picture WHERE idactivity = A.id ) as nbrpictures FROM activity A, privilege P, user U, isauthor I WHERE A.privilege = P.id AND P.level <= :level AND A.id = I.activityid AND U.id = I.userid GROUP BY A.id ORDER BY date DESC LIMIT ".$offset.', ' . $nbr;
    		$stmt = $this->_db->prepare($sql);
    		$stmt->execute(array('level' => $level));
    		if($stmt->errorCode() != 0){
    			$error = $stmt->errorInfo();
    			throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir une liste d'activit&eacute;s.");
    		}
    		$activi = array();
    		while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
    			$activi[] = new Activity($data);
    		}
    		return $activi;
    	}catch(PDOException $e){
    		throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir une liste d'activit&eacute;s.");
    	}
    }
    
      
    
    /**
     * get the number of Activity respecting the Where-clause $where
     * @param string $where : the where clause, empty if not.
     * @return int $nb : the number of Activity Objects in the DB
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function getCountActivityPeriod($begin,$end, $level){
    	try{
	    	$sql = 'SELECT count(*) as nbr FROM activity A, privilege P WHERE A.privilege = P.id AND P.level <= :level AND A.date between :begin AND :end';
	    	$stmt = $this->_db->prepare($sql);
	    	$stmt->execute(array('begin' => $begin, 'end' => $end, 'level' => $level));
	    	if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir le nombre d'activit&eacute;.");
		    }  
	        $data = (int) $stmt->fetchColumn();
	        return $data;
        }catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir le nombre d'activit&eacute;.");
        }
    }
    
    /**
     * get the number of Activity
     * @return int $nbr : the number of activities
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function getCountActivity($level){
    	try{
    		$sql = 'SELECT count(*) as nbr FROM activity A, privilege P WHERE A.privilege = P.id AND P.level <= :level';
    		$stmt = $this->_db->prepare($sql);
    		$stmt->execute(array('level' => $level));
    		if($stmt->errorCode() != 0){
    			$error = $stmt->errorInfo();
    			throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir le nombre d'activit&eacute;.");
    		}
    		$data = (int) $stmt->fetchColumn();
    		return $data;
    	}catch(PDOException $e){
    		throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir le nombre d'activit&eacute;.");
    	}
    }
    
    /**
     * get the list of all the distinct value of the column 'directory' in the DB
     * @return array $rep : array of string where every string is the name of a directory
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function getListDirectories(){
    	try{
	    	$rep = array();
	    	$sql = "SELECT distinct directory FROM activity";
	    	$stmt = $this->_db->prepare($sql);
	        $stmt->execute();
	        if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste des dossiers.");
		    }    
	        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){   	
	         	$rep[] = $data['directory'];
	        }   
	        return $rep;
    	}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir la liste des dossiers.");
        }
    }
    
    
    /**
     * Get the list of activity using a given directory
     * @param string $directory
     * @throws SQLException
     * @throws DatabaseException
     * @return array $activities : array of Activity Objects
     */
    public function getListActivityForDirectory($directory){
    	try{
    		$rep = array();
    		$sql = "SELECT * FROM activity WHERE directory = :directory";
    		$stmt = $this->_db->prepare($sql);
    		$stmt->execute(array('directory' => $directory));
    		if($stmt->errorCode() != 0){
    			$error = $stmt->errorInfo();
    			throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste des dossiers.");
    		}
    		$acti = array();
    		while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
    			$acti[] = new Activity($data);
    		}
    		return $acti;
    	}catch(PDOException $e){
    		throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir la liste des dossiers.");
    	}
    }
   
    
    /**
     * Research the activity where the title containt the $textsearch
     * @param string $text : the text researched
     * @return array $activi : array of Activity Objects containing the $text
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function research($textsearch){
		try{
			$sql = "SELECT * FROM activity WHERE title LIKE :text AND ispublished = '1' ORDER BY date ASC";
	       	$stmt = $this->_db->prepare($sql);
	        $stmt->execute(array( 'text' => '%'.$textsearch.'%' ));
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir le resultat de la recherche.");
		    }
			$activi = array();
			while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){   	
	         	$activi[] = new Activity($data);
	        }   
	        return $activi;
		}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir le resutlat de la recherche.");
        }
    }
    
    
    
    
//    /**
//     * Return the number of activity covered by $PseudoUser between $beginY and $endY
//     * @param array of string $PseudosUser : the peudos corresponding to the user
//     * @param date $beginY : the start research date. Format : yyyy-mm-dd
//     * @param date $endY : the finish research date. Format : yyyy-mm-dd
//     */
//    public function getStatUser($PseudosUser, $beginY, $endY){
//    	//where clause : pseudo part
//    	$whereClause = "(";
//		$last = count($PseudosUser);
//		$j=1;
//		foreach($PseudosUser as $pseudo){
//			$whereClause = $whereClause."(autors like '%".$pseudo."%')";
//			if($j < $last){
//				$whereClause = $whereClause." or ";
//			}
//			$j = $j+1;
//		}
//		$whereClause = $whereClause.")";
//		
//		//where clause : date part
//		$whereClause = $whereClause . " and (date between '".$beginY."' and '".$endY."')";
//		
//		$sql = "SELECT id FROM activity WHERE " . $whereClause;
//	
//		// exec request
//		$q = $this->_db->prepare($sql);
//        $q->execute();
//		$nb = (int) $q->rowCount();   
//        return $nb;
//    }
    
    /**
     * get number of activity in the period $begin - $end
     * @param string $begin : the beginning of the period. Format : yyyy-mm-dd
     * @param string $end : the end of the period. Format : yyyy-mm-dd
     * @return int  $nb : the number of Activity during the period
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function getStatActi($begin,$end){
    	try{
	    	$sql = "SELECT id FROM activity WHERE date between :begin and :end";
	    	$stmt = $this->_db->prepare($sql);
	        $stmt->execute(array('begin' => $begin, 'end' => $end));
	        if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir le nombre d'activit&eacute; pendant la p&eacute;riode donn&eacute;e.");
		    }
			$nb = (int) $stmt->rowCount();   
	        return $nb;
    	}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir le nombre d'activit&eacute; pendant la p&eacute;riode donn&eacute;e.");
        }
    }
   
   
 	/**
	 * Update the Activity in the DB. 
	 * @param int s$id : the id of the row (activity)
	 * @param string $title : the title og the activity
	 * @param string $descri : the description (edito) of the activity
	 * @param string $date : the date in the YYYY-MM-DD format
	 * @param int $privilegeid : the identifier of the privilege
	 * @return boolean $b : true if the update is a success, false otherwise
	 * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     * @uses APC
	 */
 	public function update($id, $title, $descri, $date, $privilegeid){
 		try{
	 		$sql = "UPDATE activity SET title = :title, description = :descri, date = :date, privilege = :privilegeid WHERE id=:id";
 			$stmt = $this->_db->prepare($sql);
	        $n = $stmt->execute(array('title' => $title, 'descri' => $descri,'date' => $date, 'privilegeid' => $privilegeid, 'id' => $id));
	        if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'&eacute;ffectuer la mise a jour.");
		    }
		    if($this->_apc){
		    	$this->apcDeleteActivityLists();
		    	apc_delete($this->apc_activity_activity . $id);
		    }
			return ($n > 0);
 		}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'&eacute;ffectuer la mise a jour.");
        }
 	}
 	
 	/**
 	 * Update the number of view
 	 * @param int $aid : the identifier of the Activity
	 * @return boolean $b : true if the update is a success, false otherwise
	 * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     * @uses APC
 	 */
 	public function updateView($aid){
 		try{
	 		$sql = "UPDATE activity SET viewed=viewed+1 WHERE id=:id";
	 		$stmt = $this->_db->prepare($sql);
	        $n = $stmt->execute(array('id' => $aid));
	 		if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'&eacute;ffectuer la mise a jour du nombre de vue.");
		    }
		    if($this->_apc){
		    	if(apc_exists($this->apc_activity_activity . $aid)){
		    		$activity = apc_fetch($this->apc_activity_activity . $aid);
		    		$activity->setViewed($activity->getViewed()+1);
		    		apc_store($this->apc_activity_activity . $aid, $activity, 86000);
		    	}
		    }
	        return ($n > 0);
 		}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'&eacute;ffectuer la mise a jour du nombre de vue.");
        }
 	}
 	
 	/**
 	 * Update the the status of the Activity (published or not)
 	 * @param int $aid : the identifier of the Activity
 	 * @param int $publish : the new status of the Activity ('0' or '1')
 	 * @return boolean $b : true if the update is a success, false otherwise
 	 * @throws SQLException : this exception is raised if the Query is refused
 	 * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
 	 */
 	public function updatePublish($aid, $publish){
 		try{
 			$sql = "UPDATE activity SET ispublished=:publish WHERE id=:id";
 			$stmt = $this->_db->prepare($sql);
 			$n = $stmt->execute(array('publish' => $publish, 'id' => $aid));
 			if($stmt->errorCode() != 0){
 				$error = $stmt->errorInfo();
 				throw new SQLException($error[2], $error[0], $sql, "Impossible d'effectuer la mise a jour du statut de publication.");
 			}
 			if($this->_apc){
 				$this->apcDeleteActivityLists();
 				apc_delete($this->apc_activity_activity . $aid);
 			}
 			return ($n > 0);
 		}catch(PDOException $e){
 			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'effectuer la mise a jour du statut de publication.");
 		}
 	}
 	
 	
 	
 	
 	 /** add a User in th DB
     * @param array $data : key-array containing all the information to complete the user Object
     * @return boolean $b : true if the User was added, false otherwise
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     * @throws NullObjectException : this exception is raised when the specified Object didn't exist
     */
    public function MigrAct($id,$title,$description,$date,$directory,$level,$viewed,$autors, $ispublished = '1'){
 		try {
        	$sql = "INSERT INTO activity(id, title,description, date, directory, level, viewed, autors, ispublished) " .
        			"VALUES (:id,:title, :description, :date, :directory, :level, :viewed,:autors,:ispu)";
	        $stmt = $this->_db->prepare($sql);
        	$stmt->execute(array('id'=>$id,'title' => $title, 'description' => $description, 'date' => $date, 'directory' => $directory, 'level' => $level, 'viewed' => $viewed, 'autors' => $autors, 'ispu' => $ispublished));
	        if($stmt->errorCode() != 0){
		        $error = $stmt->errorInfo();
	        	throw new SQLException($error[2], $error[0], $sql, "Impossible d'ajouter une MyPicture.");
	        }
	        return true;    
        }catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'ajouter une MyPicture.");
        	return false;
        }
        
 	}
 	

    
    /**
     * Delete the list of activities in APC (one flush per Level)
     * @throws SQLException
     * @throws DatabaseException
     * @uses APC
     */
    private function apcDeleteActivityLists(){
    	try{
    		$sql = "SELECT distinct level as level FROM privilege";
    		$stmt = $this->_db->prepare($sql);
    		$stmt->execute();
    		if($stmt->errorCode() != 0){
    			$error = $stmt->errorInfo();
    			throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste des differents levels.");
    		}
    		$i = 0;
    		$levels = array();
    		while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
    			$levels[] = $data['level'];
    		}
    		for($i=0 ; $i<count($levels) ; $i++){
    			if($this->_apc){
    				apc_delete($this->apc_activity_list . $levels[$i]);
    			}
    		}
    		
    	}catch(PDOException $e){
    		throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir la liste des differents levels.");
    	}
    }
    
    
    public function getIdActUser($PseudosUser, $beginY, $endY){
    	//where clause : pseudo part
    	$whereClause = "(";
    	$last = count($PseudosUser);
    	$j=1;
    	foreach($PseudosUser as $pseudo){
    		$whereClause = $whereClause."(autors like '%".$pseudo."%')";
    		if($j < $last){
    			$whereClause = $whereClause." or ";
    		}
    		$j = $j+1;
    	}
    	$whereClause = $whereClause.")";
    
    	//where clause : date part
    	$whereClause = $whereClause . " and (date between '".$beginY."' and '".$endY."')";
    
    	$sql = "SELECT id FROM activity WHERE " . $whereClause;
    	//echo $sql . '<br>';
    	// exec request
    	$q = $this->_db->prepare($sql);
    	$q->execute();
    
    	$activi = array();
    	while ($donnees = $q->fetch(PDO::FETCH_ASSOC)){
    		$activi[] = $donnees['id'];
    	}
    	return $activi;
    }
}
?>