<?php
/**
 * Maes Jerome
 * User.class.php, created at Nov 20, 2017
 *
 */
namespace module\user\model;
use system\core\BlackModel;

class User extends BlackModel{

	static $name = 'User';
	static $rec_name = 'login';
	static $table_name = 'user';

	static $attr_accessible = array(
			'id' => array('label'=> 'Id', 'type' => 'integer', 'required' => true),
			'login' => array('label'=> 'Login', 'type' => 'string', 'length' => 32, 'required' => true),
			'password' => array('label'=> 'Password', 'type' => 'string', 'length' => 128, 'required' => true),
			'mail' => array('label'=> 'Mail', 'type' => 'string', 'length' => 128, 'required' => true),
			'name' => array('label'=> 'Name', 'type' => 'string', 'length' => 128),
			'firstname' => array('label'=> 'Firstname', 'type' => 'string', 'length' => 128),
			'school' => array('label'=> 'School', 'type' => 'string', 'length' => 128),
			'section' => array('label'=> 'Section', 'type' => 'string', 'length' => 128),
			'address' => array('label'=> 'Address', 'type' => 'string', 'length' => 128),
			'create_date' => array('label'=> 'Create Date', 'type' => 'datetime'),
			'lastlogin' => array('label'=> 'Last Login', 'type' => 'datetime')
	);


	public static function login($login, $password){
		$cryptpass = md5($password);
		return static::find('first', array('conditions' => array('login = ? AND password = ?', $login, $cryptpass)));
	}

	public static function find_public_user($limit, $desc){
		return static::find('all', array('conditions' => array('viewdet = ?', 1), 'limit' => $limit, 'offset' => $desc, 'order' => 'id ASC'));
	}

	public static function count_public(){
		$array = self::first(array('select' => 'count(*) AS num_rows', 'conditions' => array('viewdet = ?', 1)))->attributes();
		return $array['num_rows'];
	}
}
