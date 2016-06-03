<?php

/*
 * Created on 12 avr. 2012
 *
 * MAES Jerome, Webkot 2011-2012
 * Class description : representing the link
 *
 * Convention : setters & getters begin with a capital letter (important for hydratate)
 * 				same attribute names as in the DB
 */
namespace system\core;
use \PDO;
use \NullObjectException;

 
function encode_password($pass){
	return md5($pass);
}


class User extends BlackModel{
	
	static $table_name = 'user';
	
	public $id;
	public $login;
	public $password;
	public $mail;
	public $name;
	public $firstname;
	public $school;
	public $section;
	public $address;
	public $isAdmin;
	public $isWebkot;
	public $mailwatch; // ask for an email when new activity
	public $lastLogin;
	public $subscription;
	public $viewdet;// public profil or not
	public $facebookid;
	
	public $role;
	public $level;
	
	public $nbrcomment;
	
	// indicate if modification
	public $ismodified = false;
	
	
	
 	/**
 	 * Constructor
 	 */
	public function __construct(array $donnees = array()){
		$this->hydrate($donnees);
    }
    
    
    /**
	 * Fonction Hydrate : fill the attribute of the object from an array containing the values
	 * @param : $donnees contains all the attribute (the values)
	 */
	public function hydrate(array $donnees){
		//var_dump($donnees);        
    	foreach ($donnees as $key => $value){
    		$method = 'set'.ucfirst($key);  
			//var_dump($key);           
        	if (method_exists($this, $method)){
            	$this->$method($value);
            }
        }
	}
	
	
	/**
	 * Check if a login exists, with the pass (encrypted of md5)
	 * @param string $login : the username
	 * @param string $pass : password in md5
	 * @return boolean $b : true if there is a row identfied by 'login' in the DB
	 */
	public static function exists($login, $pass){
		try{
			$sql = "SELECT * FROM user WHERE username = :user and password = :pass LIMIT 1";
			$stmt = $this->_db->prepare($sql);
			$stmt->execute(array( 'user' => $login, 'pass' => $pass ));
			if($stmt->errorCode() != 0){
				return False;
			}
			$nb = (int) $stmt->rowCount();
			return ($nb != 0);
		}catch(PDOException $e){
			return False;
		}
	}
	
	/**
	 * Check if a login exists, with the pass (encrypted of md5)
	 * @param string $login : the username
	 * @param string $pass : password in md5
	 * @return boolean $b : true if there is a row identfied by 'login' in the DB
	 */
	public static function login($login, $pass){
		try{
			$pass = encode_password($pass);
			$sql = "SELECT * FROM user WHERE login = :user and password = :pass LIMIT 1";
			$stmt = \Database::getInstance()->prepare($sql);
			$stmt->execute(array( 'user' => $login, 'pass' => $pass ));
			if($stmt->errorCode() != 0){
				return False;
			}
			$data = $stmt->fetch(PDO::FETCH_ASSOC);
			if($data == null){
				throw new NullObjectException();
			}
			return new User($data);
		}catch(PDOException $e){
			return False;
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
	public static function getUserByLogin($username){
		try{
			$sql = "SELECT U.id as id, U.username as username, U.password as password, U.mail as mail, U.name as name, U.firstname as firstname, U.school as school, U.section as section, U.address as address, U.lastlogin as lastlogin, U.subscription as subscription, U.mailwatch as mailwatch, U.viewdet as viewdet, U.isadmin as isadmin, U.iswebkot as iswebkot, P.level as level, P.role as role, U.facebookid as facebookid FROM user U, privilege P WHERE U.level = P.id AND U.login = :user LIMIT 1";
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
	 * Create the string of the object
	 * @return : string describing the object
	 */
	public function __toString(){
		return sprintf("<br>ID : %s <br> Username : %s <br>isAdmin : %s <br>Mail : %s <br>", $this->getId(), $this->getUsername(), $this->getIsAdmin(), $this->getMail());
	}
	
	
	
	// SETTERS & GETTERS
	public function setId($id){
		$id = (int) $id;
     	if ($id > 0){
          $this->id = $id;
          $this->ismodified = true;
		}
 	}
 	
 	public function setUsername($value){
		if (is_string($value)){
          $this->username = $value;
          $this->ismodified = true;
		}
 	}
 	
 	public function setPassword($value){
     	if (is_string($value)){
          $this->password = $value;
          $this->ismodified = true;
		}
 	}
 	
 	public function setName($value){
		if (is_string($value)){
          $this->name = $value;
          $this->ismodified = true;
		}
 	}
 	
 	public function setMail($value){
     	if (is_string($value)){
          $this->mail = $value;
          $this->ismodified = true;
		}
 	}
 	
 	public function setFirstname($value){
          $this->firstname = $value;
 	}
 	
 	public function setSchool($value){
     	if (is_string($value)){
          $this->school = $value;
          $this->ismodified = true;
		}
 	}
 	
 	public function setSection($value){
     	if (is_string($value)){
          $this->section = $value;
          $this->ismodified = true;
		}
 	}
 	
 	public function setAddress($value){
     	if (is_string($value)){
          $this->address = $value;
          $this->ismodified = true;
		}
 	}
 	
 	public function setIsAdmin($value){
 		$value = (int) $value;
        $this->isAdmin = $value;
        $this->ismodified = true;
 	}
 	
 	public function setIswebkot($value){
 		$value = (int) $value;
        $this->isWebkot = $value;
        $this->ismodified = true;
     }
 	
 	public function setMailwatch($value){
 		$value = (int) $value;
        $this->mailwatch = $value;
        $this->ismodified = true;
 	}
 	
 	public function setLastLogin($Value){
		$this->lastLogin = $Value;
		$this->ismodified = true;
	}
 	
 	public function setSubscription($Value){
		$this->subscription = $Value;
		$this->ismodified = true;
	}
	
	public function setViewdet($value){
		$value = (int) $value;
     	$this->viewdet = $value;
		$this->ismodified = true;
 	}
 	
 	public function setNbrcomment($value){
 		$this->nbrcomment = $value;
 	}
	
	
	
	public function getId(){
		return $this->id;
	}
	
	public function getUsername(){
		return $this->username;
	}
	
	public function getPassword(){
		return $this->password;
	}
	
	public function getMail(){
		return $this->mail;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function getFirstname(){
		return $this->firstname;
	}
	
	public function getSchool(){
		return $this->school;
	}
	
	public function getSection(){
		return $this->section;
	}
	
	public function getAddress(){
		return $this->address;
	}
	
	public function getIsAdmin(){
		return $this->isAdmin;
	}
	
	public function getIsWebkot(){
		return $this->isWebkot;
	}
	
	public function getMailwatch(){
		return $this->mailwatch;
	}
	
	public function getLastLogin(){
		return $this->lastLogin;
	}
	
	public function getSubscription(){
		return $this->subscription;
	}
	
	public function getViewdet(){
		return $this->viewdet;
	}
	
	public function getNbrcomment(){
		return $this->nbrcomment;
	}
	
	
	


	public function setFacebookid( $facebookid ){
		$this->facebookid = $facebookid;
	}
	
	public function getFacebookid(){
		return $this->facebookid;
	}
	
	


	public function setRole( $role ){
		$this->role = $role;
	}
	
	public function setLevel( $level ){
		$this->level = $level;
	}
	
	public function getRole(){
		return $this->role;
	}
	
	public function getLevel(){
		return $this->level;
	}
	

	
}
?>