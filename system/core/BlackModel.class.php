<?php
/**
 * Maes Jerome
 * BlackModel.class.php, created at May 27, 2016
 *
 */
namespace system\core;
use \PDO as PDO;
use \PDOException;
use system\core\Environment as Env;
//use system\exception\SQLException as SQLException;


class BlackModel{
	
	static $table_name = '_unknown';
	
	/**
	 * Constructor
	 * @param array $data : the data from the file, in a key-array
	 */
	public function __construct(array $data=array()){
		$this->hydrate($data);
	}
	
	/**
	 * Hydrate : fill the field with the array. All attribute should be 'public'.
	 * @param array $data : the data from the file, in a key-array
	 */
	public function hydrate(array $data){
		foreach ($data as $key => $value){
			$this->$key = $value;
		}
	}
	
	/**
	 * perform an SQL update
	 * @param array $value
	 * @throws SQLException
	 * @throws DatabaseException
	 * @return Ambigous <\system\core\Ambigous, \system\core\unknown>
	 */
	public function write(array $values){
		// prepare request
		$set_statement = [];
		foreach (array_keys($values) as $col){
			$set_statement[] = $col . '= :' . $col;
		}
		// do sql query
		$sql = 'UPDATE ' . static::$table_name . ' SET ' . join(',', $set_statement) . ' WHERE id = :id';
		$values['id'] = $this->id;
		try {
			$cr = static::cursor();
			$stmt = $cr->prepare($sql);
			$stmt->execute($values);
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
				throw new SQLException($error[2], $error[0], $sql, "Write exception");
			}
			return True;
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Write exception.");
		}
	}
	
	// -------------------------------
	// STATIC METHODS
	// -------------------------------
	
	/**
	 * Create a record in database
	 * @param array $data
	 * @throws SQLException
	 * @throws DatabaseException
	 * @return string
	 */
	public static function create(array $data=array()){
		$columns = array_keys($data);
		$values = array_values($data);
		$func = function($value) {
			return ':' . $value;
		};
		try {
			$sql = "INSERT INTO " . static::$table_name . "(" . join(',', $columns) . ") VALUES (" . join(',', array_map($func, $columns)) . ")";
			$cr = static::cursor();
			$stmt = $cr->prepare($sql);
			$stmt->execute($data);
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
				throw new SQLException($error[2], $error[0], $sql, "Create error");
			}
			$id = $cr->getPDOInstance()->lastInsertId();
			return static::browse($id);
		}catch(PDOException $e){
			throw new DatabaseException($e->getCode(), $e->getMessage(), "Create error");
		}
	}
	
	/**
	 * Execute a search on database
	 * @param array $domain
	 * @param string $limit
	 * @param number $offset
	 * @param string $order
	 * @throws \SQLException
	 * @throws DatabaseException
	 * @return multitype:unknown
	 */
	public static function search(array $domain, $order=false, $limit=false, $offset=0){
		$sql = "SELECT * FROM " . static::$table_name;
		// where clause
		$where_params = array();
		if(count($domain)){
			$sql .= ' WHERE ' . $domain['_where'];
			unset($domain['_where']);
			$where_params = $domain;
		}
		// order by
		if($order){
			$sql .= ' ORDER BY ' . $order;
		}
		// limit
		if($limit){
			$sql .= ' LIMIT ' . $offset . ',' . $limit;
		}
		// execute query
		try{
			$stmt = static::cursor()->prepare($sql);
			$stmt->execute($where_params);
			if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
				throw new \SQLException($error[2], $error[0], $sql, "Search exception");
			}
			$result = array();
			while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
				$result[] = static::record_object($data);
			}
			if($limit == 1 && count($result)){
				return $result[0];
			}
			return $result;
		}catch(PDOException $e){
	        throw new DatabaseException($e->getCode(), $e->getMessage(), "Search exception");
	    }
	}
	
	/**
	 * get a record from its id
	 * @param int $id
	 * @return Ambigous <unknown>
	 */
	public static function browse($id){
		$domain = array('_where' => 'id = :id', 'id' => intval($id));
		$result = static::search($domain);
		return $result[0];
	}
	
	/**
	 * create a instance (object) of the current model
	 * @param array $data
	 * @return unknown
	 */
	private static function record_object(array $data=array()){
		$c = get_called_class();
		return new $c($data);
	}
	
	/**
	 * return the connection to the database
	 * @return SPDO
	 */
	private static function cursor(){
		return \Database::getInstance();
	}
	
	public static function env($model){
		return Env::get()[$model];
	}
}