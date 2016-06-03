<?php
/**
 * Maes Jerome
 * User.class.php, created at May 28, 2016
 *
 */
namespace module\base\model;
use \system\core\BlackModel as BlackModel;


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
	
	public $admin;
	public $viewdet;// public profil or not
	public $mailwatch; // ask for an email when new activity
	public $lastLogin;
	public $subscription;
	public $iswebkot;
	public $isadmin;
	public $facebookid;

	public $level;

}