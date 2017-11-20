<?php
/**
 * Maes Jerome
 * UserController.class.php, created at Oct 30, 2015
 *
 */
namespace module\user\controller;

use module\user\model\User as User;
use module\website\controller\WebsiteController as WebsiteController;
use system\tools\Url as Url;


class UserController extends WebsiteController{
		
	public static $user_per_page = 30;
	
	/**
	 * display the list of users
	 * @param int $page : number page
	 */
	public function listAction($page=1){
		$page = intval($page);
		$limit = static::$user_per_page;
		$offset = ($page-1)*$limit;
		
		$total = User::count_public();
		$num_page = ceil($total / $limit);
		
		$users = User::find_public_user($limit, $offset);
		
		$pager = array();
		for($i=1;$i <= $num_page; $i++){
			$pager[$i] = Url::url_from_path(sprintf("/user/list/%s", $i));
		}
		
		return $this->render('user.user_list', array(
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
		
		return $this->render('user.user_profile', array(
				'user' => $user,
				'website_title' => 'Utilisateur',
		));
	}
	
	public function signupAction(){
		$error = false;
		
		$required_params = ['username', 'password', 'confirm_password', 'email'];
		$optional_params = ['firstname', 'lastname', 'study', 'school', 'city', 'profile_public'];
		
		$data = $this->params(array_merge($required_params, $optional_params));
		
		if($this->request()->isPost()){
			if($this->checkMandatoryParams($required_params)){
				
				$error = "";
				if(strlen($data['username']) < 4 || strlen($data['password']) < 4){
					$error .= "Les champs 'Username' et 'password' doivent contenir au moins 4 caractres \n";
				}
				if($data['password'] !== $data['confirm_password']){
					$error .= "Les champs 'password' et 'confirm password' ne sont pas identiques \n";
				}
				if(!preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $data['email'])){
					$error .= "L'adresse mail ne respecte pas le format suivant xxxxxx@xxxxx.xx \n";
				}
				if(!preg_match("/^[A-Za-z]{1}[A-Za-z0-9._-]{3,31}$/", $data['username'])){
					$error .= "Votre username contient des caractres interdits : il ne doit contenir que des chiffres et des lettres \n";
				}
				if(!$error){
					$user = User::create(array(
						'login' => $data['username'],
						'password' => $data['password'],
						'mail' => $data['email'],
						'name' => $data['lastname'],
						'firstname' => $data['firstname'],
						'school' => $data['school'],
						'section' => $data['study'],
						'address' => $data['city'],
						'viewdet' => $this->param('profile_public', false) ? 1 : 0,
					));
					
					return $this->redirect('/login');
				}
				$data['error'] = $error;
			}else{
				$data['error'] = "Des champs requis sont vides !";
			}
		}
		return $this->render('user.user_subscription', $data);
	}
	
}
