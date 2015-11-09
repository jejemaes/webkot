<?php
/**
 * Maes Jerome
 * UserController.class.php, created at Oct 30, 2015
 *
 */
namespace module\website\controller;

use system\core\BlackController as BlackController;
use system\res\ResUser as User;

class UserController extends WebsiteController{
		
	public static $user_per_page = 30;
	
	/**
	 * display the list of users
	 * @param int $page : number page
	 */
	public function indexAction($page=1){
		$page = intval($page);
		$limit = static::$user_per_page;
		$offset = ($page-1)*$limit;
		
		$total = User::count_public();
		$num_page = ceil($total / $limit);
		
		$users = User::find_public_user($limit, $offset);
		
		$pager = array();
		for($i=1;$i <= $num_page; $i++){
			$pager[$i] = url_from_path(sprintf("/user/list/%s", $i));
		}
		
		return $this->render('website.user_list', array(
				'users' => $users,
				'pager' => $pager,
				'page' => $page,
				'total_page' => $num_page,
				'website_title' => 'Utilisateur',
		));
	}
	
	/**
	 * display user profile
	 * @param int $user_id
	 */
	public function profileAction($user_id){
		// TODO check if user profile is public
		$user = User::find($user_id);
		
		if(!$user->viewdet){
			$this->forbidden("The user profile is not public.");
		}
		
		return $this->render('website.user_profile', array(
				'user' => $user,
				'website_title' => 'Utilisateur',
		));
	}
	
}
