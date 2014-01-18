<?php
 
class Database{
	/**
	 * Instance de la classe PDO
	 *
	 * @var PDO
	 * @access private
	 */ 
	private $PDOInstance = null;
	
	/**
	 * Instance de la classe SPDO
	 *
	 * @var SPDO
	 * @access private
	 * @static
	 */ 
	private static $instance = null;
	
	
	/**
	 * Constructeur
	 *
	 * @param void
	 * @return void
	 * @see PDO::__construct()
	 * @access private
	 */
	private function __construct(){
		try{
			$this->PDOInstance = new PDO('mysql:dbname='.DB_NAME.';host='.DB_HOST,DB_LOGIN,DB_PASS);    
			$this->PDOInstance->exec('SET NAMES utf8');// = new PDO('mysql:dbname=webkot4dev4;host=localhost;port=3306','webkot4dev4','webkot4dev4');    
		}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "MySQL ne reponds pas (PDOException). Impossible d'instancier la connection a la base de donnees.");
        }
	}
	
	/**
	 * CrŽe et retourne l'objet SPDO
	 *
	 * @access public
	 * @static
	 * @param void
	 * @return SPDO $instance
	 */
	public static function getInstance(){  
		if(is_null(self::$instance)){
			self::$instance = new Database();
		}
		return self::$instance;
	}
	
	/**
	 * ExŽcute une requte SQL avec PDO
	 *
	 * @param string $query La requte SQL
	 * @return PDOStatement Retourne l'objet PDOStatement
	 */
	public function query($query){
		return $this->PDOInstance->query($query);
	}
	
	/**
	 * ExŽcute une requte SQL avec PDO
	 *
	 * @param string $query La requte SQL
	 * @return PDOStatement Retourne le nombre de ligne modifie dans le cas d'un update
	 */
	public function exec($query){
		return $this->PDOInstance->exec($query);
	}
	
	/**
	 * Prepare une requte SQL avec PDO
	 *
	 * @param string $query La requte SQL avec des variales ˆ la place des valeurs (:id).
	 * @return /
	 * !! doit tre suivie par un "execute" avec le tableau contenant la valeur des params
	 */
	public function prepare($query){
		return $this->PDOInstance->prepare($query);
	}
	
	
	
	public function getPDOInstance(){
		return $this->PDOInstance;
	}
	
	
	
}