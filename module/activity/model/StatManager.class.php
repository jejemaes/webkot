<?php


class StatManager{
	

	protected static $_instance;
	private $_db; // Instance of Database
	private $_apc;
	
	
	const APC_ACTIVITY_TEAM_STAT = "statistics-team-";
	const APC_ACTIVITY_ACTI_STAT = "statistics-years-total";
	
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
	
	
	/**
	 *
	 * @param string $startDate : the begin date, in the YYYY-MM-DD format
	 * @param string $endDate : the end date, in the YYYY-MM-DD format
	 */
	public function getStatPeriod($startDate, $endDate){
		try{
			$sql = 'SELECT FLOOR(avg(DATE_FORMAT(S.date, \'%Y\'))) as year, count(S.id) as activities, sum(S.pictures) as pictures, sum(S.comments) as comments FROM statistics_activity S WHERE S.date BETWEEN :begin AND :end';
			$stmt = $this->_db->prepare($sql);
			$stmt->execute(array( 'begin' => $startDate, 'end' => $endDate));
			if($stmt->errorCode() != 0){
				    $error = $stmt->errorInfo();
			        throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir une activite specifiee");
			}
			$data = $stmt->fetch(PDO::FETCH_ASSOC);
			if(empty($data)){
				throw new NullObjectException();
			}	
    		return new StatActivity($data);	
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir les stats par annee.");
		}
	}
	
	
	
	/**
	 * get the number of Activities per author during the given Academic year
	 * @param int $year : the year to extract stat
	 * @throws SQLException : this exception is raised if the Query is refused
	 * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
	 * @throws NullObjectException : this exception is raised if their is no data for the given parameters
	 * @return array $stats : an array of StatUser Object
	 */
	public function getStatTeam($year){
		if($this->_apc && apc_exists(self::APC_ACTIVITY_TEAM_STAT . $year)){
			return apc_fetch(self::APC_ACTIVITY_TEAM_STAT . $year);
		}else{
			try{
				$sql = 'SELECT startYear as year, username as username, name as name, firstname as firstname, activities as stat FROM statistics_author WHERE startYear = :year';
				$stmt = $this->_db->prepare($sql);
				$stmt->execute(array( 'year' => $year));
				if($stmt->errorCode() != 0){
					$error = $stmt->errorInfo();
					throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir les stats de l'Žquipe de l'annŽe " . $year);
				}
				$stats = array();
	    		while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
	    			$stats[] = new StatUser($data);
	    		}
				if($this->_apc){
					apc_store(self::APC_ACTIVITY_TEAM_STAT . $year, $stats, 7200);
				}
	    		return $stats;
			}catch(PDOException $e){
				throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir les stats de l'Žquipe de l'annŽe " . $year);
			}	
		}
	}
	
	
	
	
	/**
	 * get the number of Activities, Pictures and Comments by year
	 * @throws SQLException : this exception is raised if the Query is refused
	 * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
	 * @throws NullObjectException : this exception is raised if their is no data for the given parameters
	 * @return array $stats : an array of StatActivity Object
	 */
	public function getAllStatActivity(){
		
		if($this->_apc && apc_exists(self::APC_ACTIVITY_ACTI_STAT)){
			return apc_fetch(self::APC_ACTIVITY_ACTI_STAT);
		}else{
			try{
				$sql = 'SELECT startYear as year, activities as activities, pictures as pictures, comments as comments FROM statistics_year ORDER BY startYear ASC';
				$stmt = $this->_db->prepare($sql);
				$stmt->execute();
				if($stmt->errorCode() != 0){
					$error = $stmt->errorInfo();
					throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir les stats de toutes les annŽes.");
				}
				$stats = array();
				while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
					$stats[] = new StatActivity($data);
				}
				if($this->_apc){
					apc_store(self::APC_ACTIVITY_ACTI_STAT, $stats, 7200);
				}
				return $stats;
			}catch(PDOException $e){
				throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir les stats de toutes les annŽes.");
			}
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	//####################################
	
	/**
	 * get the number of activity for a given User, in a given Period
	 * @param int $userid : the user identifier
	 * @param date $startDate : the beginning of the period
	 * @param date $endDate : the end of the period
	 * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
	 * @throws NullObjectException : this exception is raised if their is no data for the given parameters
	 * @return number : the number of Activity for the specified userid, in the given period
	 */
	/*public function getStatUser($userid, $startDate, $endDate){
		try{
			$sql = 'SELECT count(id) as stat FROM `statistics` WHERE date > :begin AND date < :end AND userid = :userid';
			$stmt = $this->_db->prepare($sql);
			$stmt->execute(array( 'userid' => $userid, 'begin' => $startDate, 'end' => $endDate));
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
				throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir les stats d'un user specifie");
			}
			$data = $stmt->fetch(PDO::FETCH_ASSOC);
			if(empty($data)){
				throw new NullObjectException();
			}
			$nb = (int) $data['stat'];
	        return $nb;		
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir les stats d'un user specifie");
		}
	}
	*/
	
	/**
	 * get the Authors of a given Period
	 * @param date $startDate : the beginning of the period
	 * @param date $endDate : the end of the period
	 * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
	 * @return array $authors : array of key-array containing each the informations of the author
	 */
/*	public function getAuthorsPeriod($startDate, $endDate){
		try{
			$sql = 'SELECT distinct userid, username, name, firstname FROM `statistics` WHERE date >= :begin AND date < :end';
			$stmt = $this->_db->prepare($sql);
			$stmt->execute(array( 'begin' => $startDate, 'end' => $endDate));
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
				throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir les stats d'un user specifie");
			}
			$authors = array();
    		while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
    			$authors[] = $data;
    		}
    		return $authors;	
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir les stats d'un user specifie");
		}
	}
*/
	

	
	
	/*
	public function getStatActivitiesPeriod($startDate, $endDate){	
		$arr = preg_split("/[-]+/", $startDate);
		$id = $arr[0];
		
		if($this->_apc && apc_exists(self::APC_ACTIVITY_PICT_STAT . $id)){
			return apc_fetch(self::APC_ACTIVITY_PICT_STAT . $id);
		}else{
			try{
				$sql = 'SELECT count(F.id) as activities, sum(F.pictures) as pictures, sum(F.comments) as comments FROM (SELECT distinct id, title, date, pictures, comments FROM statistics WHERE date >= :begin AND date < :end) as F';
				$stmt = $this->_db->prepare($sql);
				$stmt->execute(array( 'begin' => $startDate, 'end' => $endDate));
				if($stmt->errorCode() != 0){
					    $error = $stmt->errorInfo();
				        throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir une activite specifiee");
				}
				$data = $stmt->fetch(PDO::FETCH_ASSOC);
				if(empty($data)){
					throw new NullObjectException();
				}
				if($this->_apc){
					apc_store(self::APC_ACTIVITY_PICT_STAT . $id, $data, 172000);
				}
	    		return $data;	
			}catch(PDOException $e){
				throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir les stats par annee.");
			}	
		}
	}
	*/

}