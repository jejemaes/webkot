<?php
/*
 * Created on 6 avr. 2012
 *
 * MAES Jerome, Webkot 2011-2012
 * Class Description : Management of the User Object (with the DB)
 *
 */
 

 class UserManager{
 	
 	protected static $_instance;
	private $_db; // Instance of Database


	/**
	 * GetInstance 
	 * @return UserManager $instance : the instance of UserManager
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
     * add a User in th DB
     * @param array $data : key-array containing all the information to complete the user Object
     * @return boolean $b : true if the User was added, false otherwise
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     * @throws NullObjectException : this exception is raised when the specified Object didn't exist
     */
    public function add($data, $roleFK = 2){
 		try {
        	$sql = "INSERT INTO user(username,password, mail, name, firstname, school, section, address, subscription, mailwatch, viewdet, level, facebookid) " .
        			"VALUES (:username, :password, :mail, :name, :firstname, :school, :section, :address, CURRENT_TIMESTAMP, :mailwatch, :detview, :level, :facebookid)";
	        $stmt = $this->_db->prepare($sql);
        	$stmt->execute(array('username' => $data['username'], 'password' => $data['password'], 'mail' => $data['mail'], 'name' => $data['name'], 'firstname' => $data['firstname'], 'school' => $data['school'], 'section' => $data['section'], 'address' => $data['address'], 'mailwatch' => $data['mailwatch'],'detview' => $data['detview'], 'facebookid' => $data['facebookid'], 'level' => $roleFK));
	        if($stmt->errorCode() != 0){
		        $error = $stmt->errorInfo();
	        	throw new SQLException($error[2], $error[0], $sql, "Impossible d'ajouter un utilisateur");
	        }
	        return true;    
        }catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'ajouter un utilisateur");
        } 
 	}
 	
    
    /**
     * get a specified User Object, by its ID
     * @param int $id : the identifier of the User Object
     * @return User $user : the User Object
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     * @throws NullObjectException : this exception is raised when the specified Object didn't exist
     */
    public function getUserById($id){
    	try{
    		$sql = "SELECT U.id as id, U.username as username, U.password as password, U.mail as mail, U.name as name, U.firstname as firstname, U.school as school, U.section as section, U.address as address, U.lastlogin as lastlogin, U.subscription as subscription, U.mailwatch as mailwatch, U.viewdet as viewdet, U.isadmin as isadmin, U.iswebkot as iswebkot, P.level as level, P.role as role, U.facebookid as facebookid FROM user U, privilege P WHERE U.level = P.id AND U.id = :id LIMIT 1";
	       	$stmt = $this->_db->prepare($sql);
	        $stmt->execute(array( 'id' => $id ));
	        if($stmt->errorCode() != 0){
			    $error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir un le profil d'un User");
		    }
	        $data = $stmt->fetch(PDO::FETCH_ASSOC);
	        if($data == null){
	        	throw new NullObjectException();
	        }
	        $user = new User($data);
	     	return $user;
    	}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir un le profil d'un User");
        }
    }
    
    /**
     * get a specified User Object, by its Username
     * @param int $username : the username of the User Object
     * @return User $user : the User Object
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     * @throws NullObjectException : this exception is raised when the specified Object didn't exist
     */
    public function getUserByLogin($username){
		try{
    		$sql = "SELECT U.id as id, U.username as username, U.password as password, U.mail as mail, U.name as name, U.firstname as firstname, U.school as school, U.section as section, U.address as address, U.lastlogin as lastlogin, U.subscription as subscription, U.mailwatch as mailwatch, U.viewdet as viewdet, U.isadmin as isadmin, U.iswebkot as iswebkot, P.level as level, P.role as role, U.facebookid as facebookid FROM user U, privilege P WHERE U.level = P.id AND U.username = :user LIMIT 1";
	       	$stmt = $this->_db->prepare($sql);
	        $stmt->execute(array( 'user' => $username ));
	        if($stmt->errorCode() != 0){
			    $error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir un le profil d'un User par son username");
		    }
	        $data = $stmt->fetch(PDO::FETCH_ASSOC);
	        if($data == null){
	        	throw new NullObjectException();
	        }
	        $user = new User($data);
	     	return $user;
    	}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir un le profil d'un User par son username");
        } 
    }
    
    /**
     * get a specified User Object, by its Username
     * @param int $username : the username of the User Object
     * @return User $user : the User Object
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     * @throws NullObjectException : this exception is raised when the specified Object didn't exist
     */
    public function getUserByFacebookid($fbid){
    	try{
    		$sql = "SELECT U.id as id, U.username as username, U.password as password, U.mail as mail, U.name as name, U.firstname as firstname, U.school as school, U.section as section, U.address as address, U.lastlogin as lastlogin, U.subscription as subscription, U.mailwatch as mailwatch, U.viewdet as viewdet, U.isadmin as isadmin, U.iswebkot as iswebkot, P.level as level, P.role as role, U.facebookid as facebookid FROM user U, privilege P WHERE U.level = P.id AND U.facebookid = :facebookid LIMIT 1";
    		$stmt = $this->_db->prepare($sql);
    		$stmt->execute(array( 'facebookid' => $fbid ));
    		if($stmt->errorCode() != 0){
    			$error = $stmt->errorInfo();
    			throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir un le profil d'un User");
    		}
    		$data = $stmt->fetch(PDO::FETCH_ASSOC);
    		if($data == null){
    			throw new NullObjectException();
    		}
    		$user = new User($data);
    		return $user;
    	}catch(PDOException $e){
    		throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir un le profil d'un User");
    	} 
    }
    
    /**
     * get a list of User Object using a given email address
     * @param string $mail : the email of the User Object
     * @return User $user : the User Object
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     * @throws NullObjectException : this exception is raised when the specified Object didn't exist
     */
    public function getUserByMail($mail){
    	try{
    		$sql = "SELECT U.id as id, U.username as username, U.password as password, U.mail as mail, U.name as name, U.firstname as firstname, U.school as school, U.section as section, U.address as address, U.lastlogin as lastlogin, U.subscription as subscription, U.mailwatch as mailwatch, U.viewdet as viewdet, U.isadmin as isadmin, U.iswebkot as iswebkot, P.level as level, P.role as role, U.facebookid as facebookid FROM user U, privilege P WHERE U.level = P.id AND U.mail = :mail";
    		$stmt = $this->_db->prepare($sql);
    		$stmt->execute(array( 'mail' => $mail ));
    		if($stmt->errorCode() != 0){
    			$error = $stmt->errorInfo();
    			throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir les profils User pour l'adresse mail donnée.");
    		}
    		$users = array();
	        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){   	
	         	$users[] = new User($data);
	        }   
	        return $users;
    	}catch(PDOException $e){
    		throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir les profils User pour l'adresse mail donnée.");
    	}
    
    }
    
    /**
     * get a specified public User Object, by its ID
     * @param int $id : the identifier of the User Object
     * @return User $user : the User Object
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     * @throws NullObjectException : this exception is raised if the Profil of the specified User is not public
     */
    public function getUserProfil($id){
     	try{
    		$sql = "SELECT U.id as id, U.username as username, U.password as password, U.mail as mail, U.name as name, U.firstname as firstname, U.school as school, U.section as section, U.address as address, U.lastlogin as lastlogin, U.subscription as subscription, U.mailwatch as mailwatch, U.viewdet as viewdet, U.isadmin as isadmin, U.iswebkot as iswebkot, P.level as level, P.role as role, U.facebookid as facebookid FROM user U, privilege P WHERE U.level = P.id AND U.id = :id AND viewdet = '1' LIMIT 1";
	       	$stmt = $this->_db->prepare($sql);
	        $stmt->execute(array( 'id' => $id ));
	        if($stmt->errorCode() != 0){
			    $error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir un le profil d'un User");
		    }
	        $data = $stmt->fetch(PDO::FETCH_ASSOC);
	        if($data == null){
	        	throw new NullObjectException();
	        }
	        $user = new User($data);
	     	return $user;
    	}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir un le profil d'un User");
        }
    }
    
 	
 	/**
	 * Update the user in the DB. The id and the login can't be changed
	 * @param $id, $pass, $mail, $name, $firstname, $school, $section, $adress, $mailwatch, $viewdet : information which can be changed
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
	 */
 	public function update($id, $pass, $mail, $name, $firstname, $school, $section, $address, $mailwatch, $viewdet, $isadmin, $iswebkot){	
		try{
	 		$sql = "UPDATE user SET password = :password, mail = :mail, name = :name, firstname = :firstname, school = :school, section = :section, address = :address, mailwatch = :mailwatch, viewdet = :viewdet, isadmin = :isadmin, iswebkot = :iswebkot  WHERE id=:id";
 			$stmt = $this->_db->prepare($sql);
 			$params = array('password' => $pass, 'mail' => ($mail), 'name' => ($name), 'firstname' => ($firstname), 'school' => ($school), 'section' => ($section), 'address' => ($address), 'mailwatch' => $mailwatch, 'viewdet' => $viewdet, 'isadmin'=>$isadmin, 'iswebkot'=>$iswebkot, 'id' => $id);
 			$n = $stmt->execute($params);
	        if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'effectuer la mise a jour");
		    }
			return ($n > 0);
 		}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'effectuer la mise a jour");
        }
 	}
 	
 	/**
 	 * Update the password of the User
 	 * @param string $email : the email of the User
 	 * @param string $pass : the password encrypted in md5
 	 * @return boolean $b : true if a modification happened, false otherwise
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
 	 */
 	public function updatePassword($email, $pass){		
 		try{
	 		$sql = "UPDATE user SET password = :password WHERE mail=:mail";
	 		$stmt = $this->_db->prepare($sql);
	        $stmt->execute(array('password' => $pass, 'mail' => ($email)));
	        if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'effectuer la mise a jour du mot de passe.");
		    }
			return ($stmt->rowCount() > 0);
 		}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'effectuer la mise a jour du mot de passe.");
        }
 	}
 	
 	
 	/**
 	 * Update the role of the User
 	 * @param int $id : the identifier of the User
 	 * @param int $roleid : the new Role of the User
 	 * @return boolean $b : true if a modification happened, false otherwise
 	 * @throws SQLException : this exception is raised if the Query is refused
 	 * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
 	 */
 	public function updatePrivilege($id, $roleid){
 		try{
 			$sql = "UPDATE user SET level = :roleid WHERE id=:id";
 			$stmt = $this->_db->prepare($sql);
 			$n = $stmt->execute(array('roleid' => $roleid, 'id' => $id));
 			if($stmt->errorCode() != 0){
 				$error = $stmt->errorInfo();
 				throw new SQLException($error[2], $error[0], $sql, "Impossible d'effectuer la mise a jour du role.");
 			}
 			return ($n > 0);
 		}catch(PDOException $e){
 			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'effectuer la mise a jour du role.");
 		}
 	}
 	
 	
 	/**
 	 * Update the mailwatch of the User
 	 * @param int $id : the identifier of the User
 	 * @param int $mv : the new mailwatch of the User
 	 * @return boolean $b : true if a modification happened, false otherwise
 	 * @throws SQLException : this exception is raised if the Query is refused
 	 * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
 	 */
 	public function updateMailwatch($id, $mw){
 		try{
 			$sql = "UPDATE user SET mailwatch = :mw WHERE id=:id";
 			$stmt = $this->_db->prepare($sql);
 			$n = $stmt->execute(array('mw' => $mw, 'id' => $id));
 			if($stmt->errorCode() != 0){
 				$error = $stmt->errorInfo();
 				throw new SQLException($error[2], $error[0], $sql, "Impossible d'effectuer la mise a jour du mailwatch.");
 			}
 			return ($n > 0);
 		}catch(PDOException $e){
 			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'effectuer la mise a jour du mailwatch.");
 		}
 	}
 	
 	
 	/**
 	 * update the FacebookId of a specified User Object
 	 * @param int $id : the identifier of the User
 	 * @param int $fbid : the Facebook Identifier
 	 * @return boolean $b : true if at least one line of the SQL Table was changed, false otherwise
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
 	 */
 	public function updateFacebookId($uid, $fbid){
 		try{
	 		$sql = "UPDATE user SET facebookid = :fbid WHERE id=:id";
	 		$stmt = $this->_db->prepare($sql);
	        $n = $stmt->execute(array('id' => $uid, 'fbid' => $fbid));
	        if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'effectuer la mise a jour sur le Facebook Id");
		    }
			return ($n > 0);
 		}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'effectuer la mise a jour sur le Facebook Id");
        }
 	}
 	
 	/**
 	 * update the 'lastlogin' date of a specified User Object
 	 * @param int $id : the identifier of the User
 	 * @return boolean $b : true if at least one line of the SQL Table was changed, false otherwise
 	 * @throws SQLException : this exception is raised if the Query is refused
 	 * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
 	 */
 	public function updateLastLogin($id){
 		try{
 			$sql = "UPDATE user SET lastlogin = CURRENT_TIMESTAMP WHERE id=:id";
 			$stmt = $this->_db->prepare($sql);
 			$n = $stmt->execute(array('id' => $id));
 			if($stmt->errorCode() != 0){
 				$error = $stmt->errorInfo();
 				throw new SQLException($error[2], $error[0], $sql, "Impossible d'effectuer la mise a jour sur le lastlogin");
 			}
 			return ($n > 0);
 		}catch(PDOException $e){
 			throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'effectuer la mise a jour sur le lastlogin");
 		}
 	}
 	
 	
 	
 	/**
 	 * check if a specified login already exists and is used
 	 * @param string $login : the username to check
 	 * @return boolean : true if there is a row identfied by 'login' in the DB
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
 	 */
 	public function loginAlreadyUsed($login){
     	try{
	 		$sql = "SELECT * FROM user WHERE username = :user";
	 		$stmt = $this->_db->prepare($sql);
	        $stmt->execute(array('user' => addslashes($login)));
	        $n = $stmt->rowCount();
	        if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible de controler si un login existe deja");
		    }
			return ($n != 0);
 		}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible de controler si un login existe deja");
        }
 	}
 	
 	
 	/**
 	 * !! Use in the Admin Panel (the where clause is a vulnerability) !!
 	 * Obtain a list of the user order by the id. The lenght of the list is $nbr and it start from the $limit e element and respecting the $where
 	 * @param $limit : the number where start the list
 	 * @param $nbr : the lenght of the list
 	 * @return array $users : array of User Objects
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
 	 */
 	public function getListUser($where,$limit, $nbr){
 		try{
	 		$w = "";
	    	if((!empty($where)) && ($where != null)){
	    		$w = "AND " . $where;
	    	}
	    	$limitClause = "LIMIT " . $limit . ",".$nbr;
	    	if($limit == 0 && $nbr == '*'){
	    		$limitClause = "";
	    	}
	    	// execute the request	
	        $sql = "SELECT U.id as id, U.username as username, U.password as password, U.mail as mail, U.name as name, U.firstname as firstname, U.school as school, U.section as section, U.address as address, U.lastlogin as lastlogin, U.subscription as subscription, U.mailwatch as mailwatch, U.viewdet as viewdet, U.isadmin as isadmin, U.iswebkot as iswebkot, P.level as level, P.role as role, U.facebookid as facebookid FROM user U, privilege P WHERE U.level = P.id ".$w." ORDER BY id ASC " . $limitClause;
	      	$stmt = $this->_db->prepare($sql);
	        $stmt->execute();
	        if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste des User");
		    }  
	    	$users = array();
	        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){   	
	         	$users[] = new User($data);
	        }   
	        return $users;
 		}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir la liste des User");
        }
    }
    
    public function getListUserLevel($minLevel, $operation){
    	try{
    		// execute the request
    		$sql = "SELECT U.id as id, U.username as username, U.password as password, U.mail as mail, U.name as name, U.firstname as firstname, U.school as school, U.section as section, U.address as address, U.lastlogin as lastlogin, U.subscription as subscription, U.mailwatch as mailwatch, U.viewdet as viewdet, U.isadmin as isadmin, U.iswebkot as iswebkot, P.level as level, P.role as role , U.facebookid as facebookid
    				FROM user U, privilege P 
    				WHERE U.level = P.id AND P.level " . $operation . " :minLevel ORDER BY U.firstname";
    		$stmt = $this->_db->prepare($sql);
    		$stmt->execute(array("minLevel" => $minLevel));
    		if($stmt->errorCode() != 0){
    			$error = $stmt->errorInfo();
    			throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste des User");
    		}
    		$users = array();
    		while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
    			$users[] = new User($data);
    		}
    		return $users;
    	}catch(PDOException $e){
    		throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir la liste des User");
    	}
    }
 	
 	/**
 	 * Obtain a list of the User order by the id and with a public profil. The lenght of the list is $nbr and it start from the $limit of element
 	 * @param $limit : the number where start the list
 	 * @param $nbr : the lenght of the list
 	 * @return array $users : array of User Objects
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
 	 */
 	public function getListPublicUser($limit, $nbr){       
        try{
	      	$sql = "SELECT U.id as id, U.username as username, U.password as password, U.mail as mail, U.name as name, U.firstname as firstname, U.school as school, U.section as section, U.address as address, U.lastlogin as lastlogin, U.subscription as subscription, U.mailwatch as mailwatch, U.viewdet as viewdet, U.isadmin as isadmin, U.iswebkot as iswebkot, P.level as level, P.role as role FROM user U, privilege P WHERE U.level = P.id AND viewdet = '1' ORDER BY id DESC LIMIT " . $limit . ",".$nbr;
	      	$stmt = $this->_db->prepare($sql);
	        $stmt->execute();
	        if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste des User public");
		    }  
	    	$users = array();
	        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){   	
	         	$users[] = new User($data);
	        }   
	        return $users;
 		}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir la liste des User public");
        }
    }
    
    
    /**
     * get the list of the User which have activate the mailwatch
     * @throws SQLException
     * @throws DatabaseException
     * @return array $users : an array of User Object
     */
    public function getListUserToMail(){
    	try{
    		$sql = "SELECT * FROM `user` WHERE mailwatch = '1' ORDER BY id DESC";
    		$stmt = $this->_db->prepare($sql);
    		$stmt->execute();
    		if($stmt->errorCode() != 0){
    			$error = $stmt->errorInfo();
    			throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste des User a notifier par mail");
    		}
    		$users = array();
    		while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
    			$users[] = new User($data);
    		}
    		return $users;
    	}catch(PDOException $e){
    		throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir la liste des User a notifier par mail");
    	}
    }
    
    
    
    /**
     * Count the number of User respecting the where clause $where
     * @param $where : the where clause, empty if not.
     * @return int $nb : the number of Usr Object respecting the $where
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function getCountUsers($where){
         try{
		    //built the Where clause
		    $w = "";
	    	if((!empty($where)) && ($where != null)){
	    		$w = "AND " . $where;
	    	}
	    	$sql = 'SELECT U.*, P.level as level FROM user U, privilege P WHERE U.level = P.id ' . $w;
	    	$stmt = $this->_db->prepare($sql);
	    	$stmt->execute();
	        if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste des User public");
		    }  
	    	$nb = (int) $stmt->rowCount();   
       	 	return $nb;
 		}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir la liste des User public");
        }
    }
    
    
    /** get the User which has the biggest number of Comment since ever
     * @return array $users : the list of User Objects
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function getTopCommentatorEver(){
         try{
	    	$sql = "SELECT U.id as id, U.username AS username, U.mail AS mail, U.name as name, U.firstname as firstname, U.school as school, U.section as section, U.address as address, U.lastlogin as lastlogin, U.subscription as subscription, COUNT( C.id ) AS nbrcomment, U.facebookid as facebookid
					FROM User U, 
					COMMENT C
					WHERE C.userid = U.id
					GROUP BY userid
					ORDER BY  `nbrcomment` DESC 
					LIMIT 0 , 10";
	       	$stmt = $this->_db->prepare($sql);
	        $stmt->execute();
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir le resultat de la recherche");
		    }
			$users = array();
			while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){   	
	         	$users[] = new User($data);
	        }   
	        return $users;
 		}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir la liste des User public");
        }
    }
    
    
    /** get the User which has the biggest number of Comment during a specified year
     * @param int $year : the begin year of the period (academic year)
     * @return array $users : the list of User Objects
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function getTopCommentatorYear($year){
         try{
	    	$sql = "SELECT U.id as id, U.username AS username, U.mail AS mail, U.name as name, U.firstname as firstname, U.school as school, U.section as section, U.address as address, U.lastlogin as lastlogin, U.subscription as subscription, COUNT( C.id ) AS nbrcomment, U.facebookid as facebookid
					FROM User U, 
					COMMENT C
					WHERE C.userid = U.id AND (C.date >= :year AND C.date < :year2)
					GROUP BY userid
					ORDER BY  `nbrcomment` DESC 
					LIMIT 0 , 10";
	       	$stmt = $this->_db->prepare($sql);
	        $stmt->execute(array('year' => ($year)."-".BEGINYEAR_MONTH."-".BEGINYEAR_DAY, 'year2' => ($year+1)."-".BEGINYEAR_MONTH."-".BEGINYEAR_DAY)); 
	        if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir le resultat de la recherche");
		    }
			$users = array();
			while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){   	
	         	$users[] = new User($data);
	        }   
	        return $users;
 		}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir la liste des User public");
        }
    }
    
    
    
    
    /**
 	 * check if a login exists, with the pass (encrypted of md5)
 	 * @param string $login : the username
 	 * @param string $pass : password in md5
 	 * @return boolean $b : true if there is a row identfied by 'login' in the DB
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
 	 */
 	public function exists($login, $pass){
 		try{
 			$sql = "SELECT * FROM user WHERE username = :user and password = :pass LIMIT 1";
	       	$stmt = $this->_db->prepare($sql);
	        $stmt->execute(array( 'user' => $login, 'pass' => $pass ));
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste des User public");
		    }
			$nb = (int) $stmt->rowCount();
			return ($nb != 0);	
 		}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir la liste des User public");
        }
 	}
 	
 	
    
    /**
     * Research the User where the username, mail or name containt the $textsearch
     * @param string $text : the text researched
     * @return array $users : array of User Objects containing the $text
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function research($textsearch){
		try{
			$sql = "SELECT U.id as id, U.username as username, U.password as password, U.mail as mail, U.name as name, U.firstname as firstname, U.school as school, U.section as section, U.address as address, U.lastlogin as lastlogin, U.subscription as subscription, U.mailwatch as mailwatch, U.viewdet as viewdet, U.isadmin as isadmin, U.iswebkot as iswebkot, P.level as level, P.role as role, U.facebookid as facebookid FROM user U, privilege P WHERE U.level = P.id AND ((U.mail LIKE :text) OR (U.username LIKE :text) OR (U.name LIKE :text)) ORDER BY id ASC";
	       	$stmt = $this->_db->prepare($sql);
	        $stmt->execute(array( 'text' => '%'.$textsearch.'%' ));
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir le resultat de la recherche");
		    }
			$users = array();
			while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){   	
	         	$users[] = new User($data);
	        }   
	        return $users;
		}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir le resultat de la recherche");
        }
    }

 	
 	/**
     * Delete a User Object
     * @param int $uid : the identifier of the User to remove
     * @return boolean $b : true if the removing was successful
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function delete($uid){
        try {
        	$sql = "DELETE FROM user WHERE id = :id";
        	$stmt = $this->_db->prepare($sql);
        	$stmt->execute(array( 'id' => $uid));
        	if($stmt->errorCode() != 0){
		        $error = $stmt->errorInfo();
	        	throw new SQLException($error[2], $error[0], $sql, "Impossible de supprimer un User");
	        }
	        return true;       
        }catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible de supprimer un User");
        	return false;
        }
    }
 	
 	
 	 /** add a User in th DB
     * @param array $data : key-array containing all the information to complete the user Object
     * @return boolean $b : true if the User was added, false otherwise
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     * @throws NullObjectException : this exception is raised when the specified Object didn't exist
     */
    public function MigrUser($id,$username,$password,$mail,$name,$firstname,$school,$section,$address,$lastlogin, $sub,$mailwatch,$detview, $isadmin, $iswebkot){
 		try {
        	$sql = "INSERT INTO user(id, username,password, mail, name, firstname, school, section, address, lastlogin,subscription, mailwatch, viewdet, isadmin, iswebkot) " .
        			"VALUES (:id,:username, :password, :mail, :name, :firstname, :school, :section, :address, :lastlogin,:subscription, :mailwatch, :detview, :isadmin, :iswebkot)";
	        $stmt = $this->_db->prepare($sql);
        	$stmt->execute(array('id'=>$id,'username' => $username, 'password' => $password, 'mail' => $mail, 'name' => $name, 'firstname' => $firstname, 'school' => $school, 'section' => $section,
        	 'address' => $address, 'lastlogin' => $lastlogin, 'subscription'=>$sub,'mailwatch' => $mailwatch,'detview' => $detview, 'isadmin'=>$isadmin,'iswebkot'=>$iswebkot));
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
