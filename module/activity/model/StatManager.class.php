<?php


class StatManager{
	

	protected static $_instance;
	private $_db; // Instance of Database
	private $_apc;
	
	
	public $apc_activity_team_stat;
	public $apc_activity_acti_stat;
	
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
		$this->_apc = ((extension_loaded('apc') && ini_get('apc.enabled'))  && APC_ACTIVE ? true : false);
		if($this->_apc){
			$this->apc_activity_team_stat = APC_PREFIX . "statistics-team-";
			$this->apc_activity_acti_stat = APC_PREFIX . "statistics-years-total";
		}
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
		if($this->_apc && apc_exists($this->apc_activity_team_stat . $year)){
			return apc_fetch($this->apc_activity_team_stat . $year);
		}else{
			try{
				$sql = 'SELECT startYear as year, username as username, name as name, firstname as firstname, activities as stat FROM statistics_author WHERE startYear = :year';
				$stmt = $this->_db->prepare($sql);
				$stmt->execute(array( 'year' => $year));
				if($stmt->errorCode() != 0){
					$error = $stmt->errorInfo();
					throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir les stats de l'&eacute;quipe de l'ann&eacute;e " . $year);
				}
				$stats = array();
	    		while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
	    			$stats[] = new StatUser($data);
	    		}
				if($this->_apc){
					apc_store($this->apc_activity_team_stat . $year, $stats, 7200);
				}
	    		return $stats;
			}catch(PDOException $e){
				throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir les stats de l'&eacute;quipe de l'ann&eacute;e " . $year);
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
		if($this->_apc && apc_exists($this->apc_activity_acti_stat)){
			return apc_fetch($this->apc_activity_acti_stat);
		}else{
			try{
				$sql = 'SELECT startYear as year, activities as activities, pictures as pictures, comments as comments FROM statistics_year ORDER BY startYear ASC';
				$stmt = $this->_db->prepare($sql);
				$stmt->execute();
				if($stmt->errorCode() != 0){
					$error = $stmt->errorInfo();
					throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir les stats de toutes les ann&eacute;es.");
				}
				$stats = array();
				while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
					$stats[] = new StatActivity($data);
				}
				if($this->_apc){
					apc_store($this->apc_activity_acti_stat, $stats, 7200);
				}
				return $stats;
			}catch(PDOException $e){
				throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir les stats de toutes les ann&eacute;es.");
			}
		}
	}
	
}