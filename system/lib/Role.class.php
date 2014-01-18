<?php


class Role{
	
	
	private $_id;
	private $_role;
	private $_level;
	
	
	/**
	 * Constructor : fill the fields of the objects, and initialize the param config
	 * @param array $data : the data from the file, in a key-array
	 */
	public function __construct(array $data = array()){
		$this->hydrate($data);
	}
	
	
	/**
	 * Hydrate : fill the field with the array
	 * @param array $data : the data from the file, in a key-array
	 */
	public function hydrate(array $donnees){
		foreach ($donnees as $key => $value){
			$method = 'set'.ucfirst($key);
			if (method_exists($this, $method)){
				$this->$method($value);
			}
		}
	}
	


	public function setId( $_id ){
		$this->_id = $_id;
	}
	
	public function setRole( $_role ){
		$this->_role = $_role;
	}
	
	public function setLevel( $_level ){
		$this->_level = $_level;
	}
	
	public function getId(){
		return $this->_id;
	}
	
	public function getRole(){
		return $this->_role;
	}
	
	public function getLevel(){
		return $this->_level;
	}
	
	
	//########################
	//########################
	//########################
	
	/*private static $Roles = array(
		'Visitor' => 0,
		'Subscriber' => 1,
		'Webkot' => 5,
		'Administrator' => 7
	);
	
	
	public static function getValue($rolename){
		$tab = self::$Roles;
		$value = $tab[$rolename];
		if($value == null){
			return -1;
		}
		return $value;
	}
	
	
	public static function getPrivilegeLevel($rolename){
		if(array_key_exists($rolename, self::$Roles)){		
			return self::$Roles[$rolename];
		}else{
			return 0;
		}
	}

	
	public static function getMinRole(){
		$tab = array_keys(self::$Roles);
		return $tab[0];
	}
	
	public static function getRoleList(){
		return self::$Roles;
	}
	*/
}