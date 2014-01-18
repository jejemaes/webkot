<?php



class ChallengeManager {

    protected static $_instance;
	private $_db; // Instance of Database


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
    }
    
    
    /**
     * Add an Challenge in the DB
     * @param array $data : key-array containing the information of the Challenge 
     * @return boolean $b : true if the Challenge was added, false otherwise
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function add($question, $answer, $descri, $path, $enddate){
        try {
        	$sql = "INSERT INTO challenge (question, answer, description, path_picture, end_date) VALUES (:question,:answer, :descri, :path, :enddate)";
	        $stmt = $this->_db->prepare($sql);
        	$stmt->execute(array( 'question' => $question,'answer' => $answer, 'descri' => $descri, 'path' => $path, 'enddate' => $enddate));
	        if($stmt->errorCode() != 0){
		        $error = $stmt->errorInfo();
	        	throw new SQLException($error[2], $error[0], $sql, "Impossible d'ajouter un concours");
	        }
	        return true;     
        }catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'ajouter un concours");
        	return false;
        }
    }
    
    /**
     * Add an Challenge in the DB
     * @param int $challengeid : the identifier of the Challenge 
     * @param int $userid : the identifier of the User 
     * @param string $answer : the answer for the Challenge from the User
     * @return boolean $b : true if the Answer was added, false otherwise
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function addAnswer($challengeid, $userid, $answer){
        try {
        	$sql = "INSERT INTO challenge_answer (challengeid, userid, answer) VALUES (:challengeid, :userid, :answer)";
	        $stmt = $this->_db->prepare($sql);
        	$stmt->execute(array( 'challengeid' => $challengeid, 'userid' => $userid, 'answer' => $answer));
	        if($stmt->errorCode() != 0){
		        $error = $stmt->errorInfo();
	        	throw new SQLException($error[2], $error[0], $sql, "Impossible d'ajouter une reponse a un concours");
	        }
	        return true;     
        }catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'ajouter une reponses a un concours");
        }
    }
    
    
    
      
    /**
     * get a specified Challenge
     * @param int $id : the identifier of the Challenge
     * @return Challenge $cha : the specified Challenge
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     * @throws NullObjectException : this exception is raised when the specified Object didn't exist
     */
    public function getChallenge($id){
    	try {
    		$id = (int) $id;
    		$sql = "SELECT * FROM challenge WHERE id = :id LIMIT 1";
	       	$stmt = $this->_db->prepare($sql);
	        $stmt->execute(array( 'id' => $id));
			if($stmt->errorCode() != 0){
			    $error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir un Challenge specifiee");
		    }
			$data = $stmt->fetch(PDO::FETCH_ASSOC);
			if($data == null){
				throw new NullObjectException();
			}
	        $cha = new Challenge($data);
	     	return $cha;
    	}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir un Challenge specifiee");
        }	
    }
    
    /**
     * get the last Challenge
     * @return Challenge $cha : the specified Challenge
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     * @throws NullObjectException : this exception is raised when the specified Object didn't exist
     */
    public function getLastChallenge(){
    	try {
    		$sql = "SELECT * FROM challenge WHERE publication_date IN (SELECT max(publication_date) FROM challenge)";
	       	$stmt = $this->_db->prepare($sql);
	        $stmt->execute();
			if($stmt->errorCode() != 0){
			    $error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir le dernier Challenge");
		    }
			$data = $stmt->fetch(PDO::FETCH_ASSOC);
			if($data == null){
				throw new NullObjectException();
			}
	        $cha = new Challenge($data);
	     	return $cha;
    	}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir le dernier Challenge");
        }	
    }
    
    /**
     * get all the list of Challenge Objects
     * @return array $list : array of Challenge Objects
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function getListChallenge(){
    	try{
	    	 $sql = "SELECT * FROM challenge ORDER BY publication_date";
	         $stmt = $this->_db->prepare($sql);
	         $stmt->execute();
	         if($stmt->errorCode() != 0){
			    $error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste des challenges");
		     }    
	    	 $list = array();
	         while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){   	
	         	$list[] = new Challenge($data);
	         }   
	         return $list;
      	}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir la liste des challenges");
        }
    }
    
    
    /**
     * get all the list of the Answer Objects for a specified Challenge
     * @param int $cid : the identifier of a Challenge
     * @return array $list : array of ChallengeAnswer Objects
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function getListAnswer($cid){
    	try{
	    	 $sql = "SELECT * FROM challenge_answer WHERE challengeid = :id";
	         $stmt = $this->_db->prepare($sql);
	         $stmt->execute(array('id' => $cid));
	         if($stmt->errorCode() != 0){
			    $error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste des reponses");
		     }    
	         $i = 0;
	    	 $list = array();
	         while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){   	
	         	$list[] = new ChallengeAnswer($data);
	         	$i++;
	         }   
	         return $list;
      	}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir la liste des reponses");
        }
    } 
    
    
      
    /**
     * get all the list of the Correct Answer Objects for a specified Challenge
     * @param int $cid : the identifier of a Challenge
     * @return array $list : array of ChallengeAnswer Objects
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function getListCorrectAnswer($cid){
    	try{
	    	 $sql = "SELECT * FROM challenge_answer WHERE challengeid = :id AND iscorrect = '1'";
	         $stmt = $this->_db->prepare($sql);
	         $stmt->execute(array('id' => $cid));
	         if($stmt->errorCode() != 0){
			    $error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste des reponses correctes");
		     }    
	         $i = 0;
	    	 $list = array();
	         while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){   	
	         	$list[] = new ChallengeAnswer($data);
	         	$i++;
	         }   
	         return $list;
      	}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir la liste des reponses correctes");
        }
    } 
    
    
    
    /**
     * get all the list of the non Corrected Answer Objects for a specified Challenge
     * @param int $cid : the identifier of a Challenge
     * @return array $list : array of ChallengeAnswer Objects
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function getListUnCorrectAnswer($cid){
    	try{
	    	 $sql = "SELECT * FROM challenge_answer WHERE challengeid = :id AND iscorrect is NULL";
	         $stmt = $this->_db->prepare($sql);
	         $stmt->execute(array('id' => $cid));
	         if($stmt->errorCode() != 0){
			    $error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste des reponses non corriges");
		     }    
	         $i = 0;
	    	 $list = array();
	         while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){   	
	         	$list[] = new ChallengeAnswer($data);
	         	$i++;
	         }   
	         return $list;
      	}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir la liste des reponses non corriges");
        }
    } 
    
    
    /**
     * get the number of the Answer Objects for a given Challenge whiwh has no statut (iscorrect)
     * @param int $cid : the identifier of a Challenge
     * @return int $nb : the number of ChallengeAnswer Objects having no statut
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function getCountNullAnswer($cid){
    	try{
	    	 $sql = "SELECT * FROM challenge_answer WHERE challengeid = :id AND iscorrect is NULL";
	         $stmt = $this->_db->prepare($sql);
	         $stmt->execute(array('id' => $cid));
	         if($stmt->errorCode() != 0){
			    $error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible le nombre de reponse a encore valider");
		     }    
	         return $nb = (int) $stmt->rowCount();
      	}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible le nombre de reponse a encore valider");
        }
    }
    
    /**
 	 * check if a Challenge answer from a specified User
 	 * @param int $userid : the identifier of the User
 	 * @param int $challengeid : the identifier of the Challenge
 	 * @return boolean $b : true if the specified User give an answer for the 
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
 	 */
 	public function existsAnswer($userid, $challengeid){
 		try{
 			$sql = "SELECT * FROM challenge_answer WHERE userid = :userid AND challengeid = :challengeid LIMIT 1";
	       	$stmt = $this->_db->prepare($sql);
	        $stmt->execute(array( 'userid' => $userid, 'challengeid' => $challengeid));
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible de determiner si une reponse pour une Challenge existe");
		    }
			$nb = (int) $stmt->rowCount();
			return ($nb != 0);	
 		}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible de determiner si une reponse pour une Challenge existe");
        }
 	}
 	
    
    
    /**
     * validate od invalide an specified answer
     * @param int $aid : the identifier (technical) of the answer
     * @param boolean $iscorrect : the statut of the answer (true if it's correct, false otherwise)
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function updateCorrect($aid, $iscorrect){
    	if($iscorrect){
    		$iscorrect = '1';
    	}else{
    		$iscorrect = '0';
    	}
    	try{
	 		$sql = "UPDATE challenge_answer SET iscorrect = :iscorrect WHERE id=:id";
 			$stmt = $this->_db->prepare($sql);
	        $n = $stmt->execute(array('iscorrect' => $iscorrect, 'id' => $aid));
	        if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'effectuer la correction");
		    }
			return ($n > 0);
 		}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'effectuer la correction");
        }
    }
    
    /**
     * modify a specified Challenge
     * @param int $cid : the identifier of the Challenge
     * @param string $question : the question of the Challenge
     * @param string $description : the description of the Challenge
     * @param string $PathPicture : the path to the picture of the Challenge
     * @param string $date : the date of the Challenge in the format YYYY-MM-DD
     * @return boolean $b : true if the update was successfully done, false otherwise
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function update($cid,$question,$answer,$description, $PathPicture, $date){
    	try{
	 		$sql = "UPDATE challenge SET question = :question, answer = :answer, description = :description, path_picture = :PathPicture, end_date = :date WHERE id=:cid";
 			$stmt = $this->_db->prepare($sql);
	        $n = $stmt->execute(array('cid' => $cid, 'answer' => $answer, 'PathPicture' => $PathPicture, 'question' => $question, 'description' => $description, 'date' => $date));
	        if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'effectuer la modification");
		    }
			return ($n > 0);
 		}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'effectuer la modification");
        }
    }
    
    
    
    /**
     * set the winner for a specified Challenge
     * @param int $cid : the identifier of the Challenge
     * @param int $winnerid : the userid of the winner of the specified challenge
     * @return boolean $b : true if the update was successfully done, false otherwise
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function setWinner($cid,$winnerid){
    	try{
	 		$sql = "UPDATE challenge SET winnerid = :winner WHERE id=:cid";
 			$stmt = $this->_db->prepare($sql);
	        $n = $stmt->execute(array('cid' => $cid, 'winner' => $winnerid));
	        if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'effectuer l'affectation du gangant du concours");
		    }
			return ($n > 0);
 		}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'effectuer l'affectation du gangant du concours");
        }
    }
    
    
    
      
    /**
     * Delete an Challenge Object
     * @param int $cid : the identifier of the Challenge to remove
     * @return boolean $b : true if the removing was successful
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function delete($cid){
        try {
        	$sql = "DELETE FROM challenge WHERE id = :id";
        	$stmt = $this->_db->prepare($sql);
        	$stmt->execute(array( 'id' => $cid));
        	if($stmt->errorCode() != 0){
		        $error = $stmt->errorInfo();
	        	throw new SQLException($error[2], $error[0], $sql, "Impossible de supprimer un concours");
	        }
	        return true;       
        }catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible de supprimer un concours");
        	return false;
        }
    }
}
?>