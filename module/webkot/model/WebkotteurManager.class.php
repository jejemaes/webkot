<?php
/*
 * Created on 29 Sept. 2012
 *
 * MAES Jerome, Webkot 2012-2013
 * Class description : manage the data between the object and the DB
 *
 * Convention : Use of the Singleton pattern
 * 				Use PreparedStatement
 */
 
 
class WebkotteurManager {
	
	protected static $_instance;
	private $_db; // Instance de Database
	private $_apc;
	
	const APC_WEBKOT_OLD_TEAM = 'webkot-old-team';
	const APC_WEBKOT_YOUNG_TEAM = 'webkot-young-team';
	

	/**
	 * GetInstance 
	 * @return WebkotteurManager $instance : the instance of WebkotteurManager
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
     * Add a member to a team.
     * @param int $wid : the id of the webkotteur
     * @param string $earString : the year of the team (YYYY-YYYY)
     * @param string $studies : the title of the studies (can be null)
     * @param string $function : the function of the webkotteur in the team (can be null)
     * @param string $pathimg : the path to the avatar (can be null)
     * @param int $age : the age of the webkotteur
     * @param int $order : the display order
     * @return boolean $b : true if the member was successfully added, false otherwise
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function addMember($wid,$yearString, $studies, $function, $pathimg,$age,$order){
    	try {		
    		if(empty($studies)){ $studies = null;}
    		if(empty($function)){ $function = null;}
    		if(empty($pathimg)){ $pathimg = null;}
    		
    		$sql = "INSERT INTO webkot_team_member VALUES (:id, :year, :study, :function, :pathimg,:age, :place)";
    	
        	$stmt = $this->_db->prepare($sql);
        	$stmt->execute(array("id" => $wid, "year" => $yearString, "study" => $studies, "function" => $function, "pathimg" => $pathimg, "age" => $age, "place" => $order));
	        
        	if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'ajouter un membre a une equipe. id=" .$wid);
		    } 
		    if($this->_apc){
		    	apc_delete(self::APC_WEBKOT_OLD_TEAM);
		    	apc_delete(self::APC_WEBKOT_YOUNG_TEAM);
		    }
	        return true;       
        }catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'ajouter un membre a une equipe");
        }
    }
    
    
    /**
     * Add a webkotteur profil
     * @param string $name :  the name of the webkotteur
     * @param string $firstname : the firstname of the webkotteur
     * @param string $nickname : the title of the studies (can be null)
     * @param string $mail : the mail of the webkotteur
     * @param int $userid : the id of the user account of the webkotteur
     * @param string $valuetolike : values identifiying the webkotteur in the 'author' columns of Activity
     * @return boolean $b : true if the member was successfully added, false otherwise
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function addWebkotteur($name, $firstname, $nickname, $mail,$userid){
    	try {		
    		if(empty($nickname)){ $nickname = 'NULL';}else{ $nickname = "'" . $nickname . "'";}
    		
    		$sql = "INSERT INTO webkot_webkotteur (name,firstname,nickname,mail) VALUES ('".($name)."', '".($firstname)."',".($nickname).",'".($mail)."')";
        	$stmt = $this->_db->prepare($sql);
        	$stmt->execute();
        	if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'ajouter un membre a une equipe");
		    } 
		    if($this->_apc){
		    	apc_delete(self::APC_WEBKOT_OLD_TEAM);
		    	apc_delete(self::APC_WEBKOT_YOUNG_TEAM);
		    }
	        return true;       
        }catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'ajouter un membre a une equipe");
        }
    }
    
    
    /**
     * Get all the webkotteur Object that were part of the team $year
     * @param int $year : the year beginning the academic year (YYYY)
     * @return array $team : array of Webkotteur Objects
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function getTeam($year){
    	try{
			$year = (int) $year;
			$yearString = $year .'-'.($year+1); 
		 
	    	$sql = "SELECT W.id as id, W.name as name, W.firstname as firstname, W.nickname as nickname, T.age as age, W.mail as mail, T.function as function, T.pathimg as img, T.studies as studies, W.valuetolike as valuetolike, W.userid as userid FROM webkot_team_member T, webkot_webkotteur W WHERE (T.webkotteurid = W.id) and (T.year = :year) order by T.place";
	       	$stmt = $this->_db->prepare($sql);
	        $stmt->execute(array( 'year' => $yearString ));
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir un equipe specifique");
		    } 
		    
			$team = array();
			while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){  
	         	$team[] = new Webkotteur($data);
	        }   
	        return $team;
    	}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir un equipe specifique");
        }
    }
    
    
    /**
     * Get the webkotteur Object $wid in the team $year
     * @param int $year : the academic year (YYYY-YYYY)
     * @param int $wid : the id of the Webkotteur Object
     * @return Webkotteur $webk : the Webkotteur profil of $wid in the year $year
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function getMember($year,$wid){
		try{
	    	$sql = "SELECT W.id as id, W.name as name, W.firstname as firstname, W.nickname as nickname, T.age as age, W.mail as mail, T.function as function, T.pathimg as img, T.studies as studies, T.place as place, T.year as year FROM webkot_team_member T, webkot_webkotteur W WHERE (T.webkotteurid = W.id) and (T.year = :year) and (W.id = :id)";
	     
	       	$stmt = $this->_db->prepare($sql);
	        $stmt->execute(array( 'year' => $year, 'id' => $wid));
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir un membre specifique pour un annee donnee");
		    }
		    
			if ($data = $stmt->fetch(PDO::FETCH_ASSOC)){  
	         	$webk = new Webkotteur($data);
	        }   
	        return $webk;
		}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir un equipe specifique");
        }
    }
    
       
    /**
     * Get the webkotteur Object identified by $wid
     * @param int $wid : the id of the webkotteur
     * @return Webkotteur $wk : the Webkotteur Object (only the attribute of the table webkot_webkotteur will be filled)
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     * @throws NullObjectException : this exception is raised when the specified Object didn't exist
     */
    public function getWebkotteur($wid){
    	try{
			$sql = "SELECT * FROM webkot_webkotteur WHERE id = :wid";
			$stmt = $this->_db->prepare($sql);
        	$stmt->execute(array( 'wid' => $wid ));
			if($stmt->errorCode() != 0){
			    $error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir un webkotteur specifie");
		    }
			$data = $stmt->fetch(PDO::FETCH_ASSOC);
			if(empty($data)){
				throw new NullObjectException();
			}		
			$wk = new Webkotteur($data);
     		return $wk;	
    	}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir un webkotteur specifique");
        }
    }
    
    
    
    /**
     * Get all the webkotteur Object that were part of the youngest team
     * @return array $team : array of Webkotteur Objects
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function getYoungestTeam(){
    	if($this->_apc && apc_exists(self::APC_WEBKOT_YOUNG_TEAM)){
    		return apc_fetch(self::APC_WEBKOT_YOUNG_TEAM);
    	}else{
	    	try{
		    	$sql = "SELECT W.id as id, W.name as name, W.firstname as firstname, W.nickname as nickname, T.age as age, W.mail as mail, T.function as function, T.pathimg as img, T.studies as studies,W.userid as userid, W.valuetolike as valuetolike, T.place as place FROM webkot_team_member T, webkot_webkotteur W WHERE (T.webkotteurid = W.id) and (T.year = (select max(year) from webkot_team_member)) order by T.place";
		       	$stmt = $this->_db->prepare($sql);
		        $stmt->execute();
				if($stmt->errorCode() != 0){
				    $error = $stmt->errorInfo();
			        throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir l'equipe la plus recente");
			    }
				$team = array();
				while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){  
		         	$team[] = new Webkotteur($data);
		        }   
		        if($this->_apc){
		        	apc_store(self::APC_WEBKOT_YOUNG_TEAM, $team, 175000);
		        }
		        return $team;	
	    	}catch(PDOException $e){
	        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir l'equipe la plus recente");
	        }
    	}
    }
    
    /**
     * get all the Webkotteurs appartenance in a team (except the last team)
     * @return array $team : array of Webkotteur Objects
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function getAllOldWebkotTeam(){
    	if($this->_apc && apc_exists(self::APC_WEBKOT_OLD_TEAM)){
    		return apc_fetch(self::APC_WEBKOT_OLD_TEAM);
    	}else{   		
	    	try{
		    	$sql = "SELECT T.year, W.id as id, W.name as name, W.firstname as firstname, W.nickname as nickname, T.age as age, W.mail as mail, T.function as function, T.pathimg as img, W.userid as userid, T.studies as studies , W.valuetolike as valuetolike FROM webkot_team_member T, webkot_webkotteur W WHERE (T.webkotteurid = W.id) and (T.year <> (SELECT max(year) FROM webkot_team_member)) order by T.year DESC, T.place";
		       	$stmt = $this->_db->prepare($sql);
		        $stmt->execute();
				if($stmt->errorCode() != 0){
				    $error = $stmt->errorInfo();
			        throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir les vieilles equipes");
			    }
				$team = array();
				while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){  
		         	$team[] = new Webkotteur($data);
		        }  
		        if($this->_apc){
		        	apc_store(self::APC_WEBKOT_OLD_TEAM, $team, 175000);
		        } 
		        return $team;
	    	}catch(PDOException $e){
	        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir les vieilles equipes");
	        }
    	}
    }
    
    
    /**
     * Get All webkotteur membership
     * @return array $team : array of Webkotteur Objects
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function getAllMemberShip(){
    	try{
	    	$sql = "SELECT T.year, W.id as id, W.name as name, W.firstname as firstname, W.nickname as nickname, T.age as age, W.mail as mail, T.function as function, T.pathimg as img, T.studies as studies, W.userid as userid, T.place as place FROM webkot_team_member T, webkot_webkotteur W WHERE (T.webkotteurid = W.id) order by T.year DESC, T.place";
	       	$stmt = $this->_db->prepare($sql);
	        $stmt->execute();
			if($stmt->errorCode() != 0){
			    $error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir tout les Memberships");
		    }
		    
			$team = array();
			while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){  
	         	$team[] = new Webkotteur($data);
	        }   
	        return $team;
    	}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir tout les Memberships");
        }
    }
    
    
    /**
     * Get the list of the year of the teams in the DB but the last
     * @return array $years : array of string. Each cell contain a different year (YYYY-YYYY).
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function getListYear(){
    	try{
	    	$sql = "SELECT DISTINCT year FROM  webkot_team_member WHERE year <> (SELECT max(year) FROM webkot_team_member) ORDER BY year DESC ";
	       	$stmt = $this->_db->prepare($sql);
	        $stmt->execute() or die(mysql_error());
	        if($stmt->errorCode() != 0){
			    $error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir l'equipe la plus recente");
		    }
	        $years = array();
			while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){  
	         	$years[] = $data['year'];
	        }   
	        return $years;	
    	}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir l'equipe la plus recente");
        }
    }
    
    
    /**
     * get all the team except a specified year (use in the creation form to add a Team)
     * @return ??
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function getAllExceptYear($yearString){
    	try{
	    	$sql = "SELECT * FROM webkot_webkotteur W WHERE W.id not in (SELECT W.id FROM webkot_team_member T WHERE W.id = T.webkotteurid and year = :year) ORDER BY firstname";
	       	$stmt = $this->_db->prepare($sql);
	        $stmt->execute(array( 'year' => $yearString ));
			if($stmt->errorCode() != 0){
			    $error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir toutes les equipes sauf celle d'une annee specifiee");
		    } 
			$team = array();
			while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
				$team[] = new Webkotteur($data);
			}
			return $team;
    	}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir toutes les equipes sauf celle d'une annee specifiee");
        }
    	
    }
    
    /**
     * Count the number user respecting the where clause $where
     * @param string $where : the where clause, empty if not.
     * @return int $nb : the number of Webkotteur Objects respecting $where
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function getCountWebkotteur($where){
    	try{
	    	$w = "";
	    	if((!empty($where)) && ($where != null)){
	    		$w = "WHERE " . $where;
	    	}
	    	$sql = 'SELECT id FROM webkot_webkotteur ' . $w;
	    	$stmt = $this->_db->prepare($sql);
	    	$stmt->execute();
	    	if($stmt->errorCode() != 0){
			    $error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir le nombre de Webkotteur respectant une clause donnee");
		    }
	        $nb = (int) $stmt->rowCount();   
	        return $nb;
    	}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir le nombre de Webkotteur respectant une clause donnee");
        }
    }
    
    
    /** Obtain a list of the webkotteur order by the id. The lenght of the list is $nbr and it start from the $limit e element and respecting the $where
 	 * @param $limit : the number where start the list
 	 * @param $nbr : the lenght of the list
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
 	 */
 	public function getListWebkotteur($where,$limit, $nbr){
 		try{
	 		$w = "";
	    	if((!empty($where)) && ($where != null)){
	    		$w = "WHERE " . $where;
	    	}
	        $sql = "SELECT * FROM webkot_webkotteur ".$w." ORDER BY id ASC LIMIT " . $limit . ",".$nbr;
	        
	      	$stmt = $this->_db->prepare($sql);
	        $stmt->execute();   
	        if($stmt->errorCode() != 0){
			    $error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste des Webkotteurs");
		    }
		    
	    	$users = array();
	        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){   	
	        	$users[] = new Webkotteur($data);
	        }   
	         return $users;
 		}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir la liste des Webkotteurs");
        }
    }
    
    
    /**
     * Update a Webkotteur row
     * @param int $wid : the identifier of the Row
     * @param string $name : the name of the Webkotteur (not null)
     * @param string $firstname : the firstname of the Webkotteur (not null)
     * @param string $nickname : the nickname of the Webkotteur
     * @param string $mail : the email address of the Webkotteur (not null)
     * @param int $userid : the userid referencing the user account of the webkotteur (not null)
     * @return boolean $b : true if the update was a success, false otherwise
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function updateKoteur($wid, $name, $firstname, $nickname, $mail, $userid){
    	try{
	    	if(empty($nickname)){
	    		$nicknameReq = 'nickname = NULL'; 
	    	}else{
	    		$nicknameReq = "nickname = '".$nickname."'";
	    	}
	    	
	    	$sql = "UPDATE webkot_webkotteur SET name = :name, firstname = :firstname, $nicknameReq ,mail = :mail, userid = :userid  WHERE id=:id";
	
	 		$stmt = $this->_db->prepare($sql);
	      	$n =  $stmt->execute(array('id' => $wid, 'firstname' => ($firstname), 'name' => ($name), 'mail' => ($mail), 'userid' => $userid));   
	        if($stmt->errorCode() != 0){
			    $error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible de mettre a jour un Webkotteur");
		    }
		    if($this->_apc){
		    	apc_delete(self::APC_WEBKOT_OLD_TEAM);
		    	apc_delete(self::APC_WEBKOT_YOUNG_TEAM);
		    }
			return ($n > 0);	
    	}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible de mettre a jour un Webkotteur");
        }
    }
    
    
    
    public function updateMembership($wid, $year, $function, $age, $studies, $pathimg, $order){
    	try{
    		if(empty($studies)){ $studies = null;}
    		if(empty($function)){ $function = null;}
    		if(empty($pathimg)){ $pathimg = null;}
    		
    		$sql = "UPDATE webkot_team_member SET function = :function, age = :age, pathimg = :pathimg, studies = :studies, place = :place WHERE webkotteurid=:id AND year=:year";
    		$stmt = $this->_db->prepare($sql);
    		$param = array('id' => $wid, 'year' => $year, 'function' => $function, 'age' => $age, 'studies' => $studies, 'pathimg' => $pathimg, 'place' => $order);
    		$n =  $stmt->execute($param);
    var_dump($param);
    		if($stmt->errorCode() != 0){
    			$error = $stmt->errorInfo();
    			throw new SQLException($error[2], $error[0], $sql, "Impossible de mettre a jour un Membership");
    		}
    		if($this->_apc){
    			apc_delete(self::APC_WEBKOT_OLD_TEAM);
    			apc_delete(self::APC_WEBKOT_YOUNG_TEAM);
    		}
    		return ($n > 0);
    	}catch(PDOException $e){
    		throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible de mettre a jour un Membership");
    	}
    }
    
    
    
    /**
     * Delete the membership for a given team of a given Webkotteur
     * @param int $wid : the id of the Webkotteur profile
     * @param string $year : the team (identifier) in the format YYYY-YYYY
     * @throws SQLException
     * @throws DatabaseException
     * @return boolean
     */
    public function deleteMembership($wid, $year){
    	try {
    		$sql = "DELETE FROM webkot_team_member WHERE webkotteurid = :webkotteurid AND year = :year";
    		$stmt = $this->_db->prepare($sql);
    		$stmt->execute(array( 'webkotteurid' => $wid, 'year' => $year));
    		if($stmt->errorCode() != 0){
    			$error = $stmt->errorInfo();
    			throw new SQLException($error[2], $error[0], $sql, "Impossible de supprimer un membership.");
    		}
    		if($this->_apc){
    			apc_delete(self::APC_WEBKOT_YOUNG_TEAM);
    			apc_delete(self::APC_WEBKOT_OLD_TEAM);
    		}
    		return true;
    	}catch(PDOException $e){
    		throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible de supprimer un membership.");
    	}
    }

}
?>