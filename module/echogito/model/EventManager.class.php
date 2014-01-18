<?php



class EventManager {

	protected static $_instance;
	private $_db; // Instance of Database
	private $_apc;
	

	/**
	 * getInstance
	 * @return EventManager $instance : the instance of EventManager
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
	 * add a Event in the table
	 * @param string $name
	 * @param text $descri : the description of the event to add
	 * @param string $date : the date in the YYYY-MM-DD HH:mm:ss format ('Y-M-d H:i:s')
	 * @param string $location : the location of the event
	 * @param int $facebookid : the facebook identifier of the event, if there is one
	 * @param int $isapproved : '0' or '1' for the approvement of the Event
	 * @param int $catid : the category identifier of the Evetn
	 * @throws SQLException : this exception is raised if the Query is refused
	 * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
	 */
	public function add($name,$descri,$starttime,$location,$facebookid, $isapproved, $catid){
		try {
			$sql = "INSERT INTO echogito_event (name, description, start_time, location, facebookid, isapproved, categoryid) VALUES (:name, :descri, :starttime, :location, :facebookid, :isapproved, :categoryid)";
			$stmt = $this->_db->prepare($sql);
			$stmt->execute(array( 'name' => $name, 'descri' => $descri, 'starttime' => $starttime, 'location' => $location, 'facebookid' => $facebookid, 'isapproved' => $isapproved, 'categoryid' => $catid));
			var_dump($stmt->errorInfo());
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
				throw new SQLException($error[2], $error[0], $sql, "Impossible d'ajouter un Event.");
			}
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'ajouter un Event.");
		}
	}
	
	
	/**
	 * get a specified Event
	 * @param int $id : the identifier of the desired event
	 * @throws NullObjectException : this is thrown if the event asked doesn't exist
	 * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made 
	 * @return Event $event : the desired Event Object
	 */
	public function getEvent($id){
		try{
			$sql = 'SELECT E.*, C.id as categoryid, C.name as categoryname, C.color as categorycolor
					FROM echogito_event E
   					LEFT OUTER JOIN echogito_category C
        				ON C.id = E.categoryid
					WHERE E.id = :id';
				
			$stmt = $this->_db->prepare($sql);
			$stmt->execute(array('id' => $id));
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
				throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir l'event spécifié.");
			}
			$data = $stmt->fetch(PDO::FETCH_ASSOC);
			if(empty($data)){
				throw new NullObjectException();
			}
	        $event = new Event($data);
			return $event;
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir l'event spécifié.");
		}
	}
	
	
	/**
	 * get the unapproved event
	 * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made 
	 * @return array : an array of Event Objects
	 */
	public function getUnapprovedEvent(){
		try {
			$sql = 'SELECT * FROM echogito_event E WHERE E.isapproved = \'0\' ORDER BY E.start_time ASC';
			$stmt = $this->_db->prepare($sql);
			$stmt->execute();
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
				throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir les events non approuvés.");
			}
			$events = array();
			while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
				$events[] = new Event($data);
			}
			return $events;
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir les events non approuvés.");
		}
	}
	
	
	
	/**
	 * get the event of a given week (number) of a given Year
	 * @param int $year : the Year
	 * @param int $week_number : the number of the week (1 .. 52)
	 * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made 
	 * @return array $events : array of Event Objects
	 */
	public function getWeekEvent($year, $week_number){
		$dates = $this->getDateOfWeek($year,$week_number,'Y-m-d H:i:s');
		try {
			$sql = 'SELECT E.*, C.id as categoryid, C.name as categoryname, C.color as categorycolor
					FROM echogito_event E
   					LEFT OUTER JOIN echogito_category C 
        				ON C.id = E.categoryid
					WHERE E.start_time >= :dateB AND E.start_time <= :dateE AND E.isapproved = \'1\' ORDER BY DATE_FORMAT(E.start_time, \'%Y-%m-%d\'), C.id ASC';
			
			$stmt = $this->_db->prepare($sql);
			$stmt->execute(array('dateB' => $dates['begin'], 'dateE' => $dates['end']));
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
				throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir les events de la semaine " . $week_number.".");
			}
			$events = array();
			while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
				$events[] = new Event($data);
			}
			return $events;
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir les events de la semaine " . $week_number.".");
		}
	}
	
	
	/**
	 * get the Events having a start_time higher than a given date
	 * @param string $date : the date of the limit in the YYYY-MM-DD format ('Y-M-d')
	 * @param boolean $takeUnapproved : true if the unapproved Event must be take into account in the request.  False if we only want the approved ones.
	 * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made  
	 * @return array $events : an array of Event Objects
	 */
	public function getEventsAfter($date, $takeUnapproved, $limitStart = null, $limitEnd = null){
		try {
			if($takeUnapproved){
				//$sql = 'SELECT * FROM echogito_event E WHERE E.start_time > :date ORDER BY E.start_time ASC';
				$sql = 'SELECT E.*, C.id as categoryid, C.name as categoryname, C.color as categorycolor
					FROM echogito_event E
   					LEFT OUTER JOIN echogito_category C
        				ON C.id = E.categoryid
					WHERE E.start_time > :date ORDER BY E.start_time, C.name ASC';
					
			}else{
				//$sql = 'SELECT * FROM echogito_event E WHERE E.start_time > :date AND E.isapproved = \'1\' ORDER BY E.start_time ASC';
				$sql = 'SELECT E.*, C.id as categoryid, C.name as categoryname, C.color as categorycolor
					FROM echogito_event E
   					LEFT OUTER JOIN echogito_category C
        				ON C.id = E.categoryid
					WHERE E.start_time > :date AND E.isapproved = \'1\' ORDER BY E.start_time, C.name ASC';
					
			}
			if($limitStart && $limitEnd){
				$sql .= ' LIMIT :limitStart, :limitEnd';
			}
			$stmt = $this->_db->prepare($sql);
			$stmt->bindValue(':date', $date );
			if($limitStart && $limitEnd){
				$stmt->bindValue(':limitStart', $limitStart, PDO::PARAM_INT );
				$stmt->bindValue(':limitEnd', $limitEnd, PDO::PARAM_INT );
			}
			$stmt->execute();
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
				throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir les events après ".$date.".");
			}
			$events = array();
			while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
				$events[] = new Event($data);		
			}
			return $events;
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir les events après ".$date.".");
		}
	}
	
	/**
	 * get the number of Event of a given date
	 * @param string $date : the date of the limit in the YYYY-MM-DD format ('Y-M-d')
	 * @param string $isapproved : '0' or '1' for the approvement of the Event
	 * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made  
	 * @return int $data : the number of Event having a strat_time higher than the given date
	 */
	public function getCountEventsAfter($date){
		try {
			$sql = 'SELECT count(*) FROM echogito_event E WHERE E.start_time > :date';
			
			$stmt = $this->_db->prepare($sql);
			$stmt->bindValue(':date', $date );
			
			$stmt->execute();
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
				throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir le nombre d'event apres une date donnee.");
			}
			$data = (int) $stmt->fetchColumn();
	        return $data;
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir le nombre d'event apres une date donnee.");
		}
	}
	
	
	
	public function getDateOfWeek($year, $week_number, $format = 'Y-M-d H:i:s'){
		$dates = array();
		$week_start = new DateTime();
		$week_start->setISODate($year,$week_number,1);
		$dates['begin'] = $week_start->format($format);
		$week_start->setISODate($year,$week_number,7);
		$dates['end'] = $week_start->format($format);
		return $dates;
	}
	
	
	/**
	 * Update the approval of an Event in the DB.
	 * @param int $id : the id of the row (Event)
	 * @param string $approval : '0' or '1' for the approval
	 * @throws SQLException : this exception is raised if the Query is refused
	 * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
	 */
	public function updateApproval($id, $approval){
		try{
			$sql = "UPDATE echogito_event SET isapproved = :app WHERE id=:id";
			$stmt = $this->_db->prepare($sql);
			$n = $stmt->execute(array('app' => $approval, 'id' => $id));
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
				throw new SQLException($error[2], $error[0], $sql, "Impossible d'effectuer la mise a jour du statut.");
			}
			return ($n > 0);
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'effectuer la mise a jour du statut.");
		}
	}
	
	/**
	 * Update the Event in the DB.
	 * @param int s$id : the id of the row (Event)
	 * @param string $title : the name of the event
	 * @param string $descri : the description of the Event
	 * @param string $date : the start_time in the YYYY-MM-DD hh:ii:ss format
	 * @param string $location : the location of the Event
	 * @param int $facebookid : the facebokid, if it is a facebook event
	 * @param int $categoryid : the identifier of the category of the Event
	 * @throws SQLException : this exception is raised if the Query is refused
	 * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
	 */
	public function update($id, $title, $descri, $date, $location, $facebookid, $categoryid){
		try{
			$sql = "UPDATE echogito_event SET name = :title, description = :descri, start_time = :date, location = :loc, facebookid = :facebookid, categoryid = :categoryid WHERE id=:id";
			$stmt = $this->_db->prepare($sql);
			$n = $stmt->execute(array('title' => $title, 'descri' => $descri,'date' => $date, 'loc' => $location, 'facebookid' => $facebookid, 'categoryid' => $categoryid, 'id' => $id));
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
				throw new SQLException($error[2], $error[0], $sql, "Impossible d'effectuer la mise a jour.");
			}
			return ($n > 0);
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'effectuer la mise a jour.");
		}
	}
	
	/**
	 * delete an Event Object
	 * @param int $aid : the identifier of the Event to remove
	 * @return boolean $b : true if the removing was successful
	 * @throws SQLException : this exception is raised if the Query is refused
	 * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
	 */
	public function delete($id){
		try {
			$sql = "DELETE FROM echogito_event WHERE id = :id";
			$stmt = $this->_db->prepare($sql);
			$stmt->execute(array( 'id' => $id));
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
				throw new SQLException($error[2], $error[0], $sql, "Impossible de supprimer un Event.");
			}
			return true;
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible de supprimer un Event.");
			return false;
		}
	}
	
	
}