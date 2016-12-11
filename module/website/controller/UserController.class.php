<?php
/**
 * Maes Jerome
 * UserController.class.php, created at Jun 6, 2016
 *
 */
namespace module\website\controller;
use module\website\controller\WebsiteController;


class UserController extends WebsiteController{
	
	const item_per_page = 30;
	
	public function indexAction($page=1){
		$user = $this->env['user'];
		$total = $user::count_public_user();
		$limit = self::item_per_page;
		
		$pager = $this->pager('user/', $total, $page, $limit, 20);
		
		$values = [
			'user_list' => $user::get_public_users($pager['offset'], $limit),
			'pager' => $pager,
		];
		
		return $this->render('website.user_list', $values);
	}
	
	public function profileAction($id){
		$user = $this->env['user'];
		$profile = $user::browse($id);
		
		$values = [
			'user' => $profile,
		];
		return $this->render('website.user_profile', $values);
	}
	
	public function signupAction(){
		$error = false;
		
		$required_params = ['login', 'password', 'confirm_password', 'email'];
		$optional_params = ['firstname', 'lastname', 'study', 'school', 'city', 'profile_public'];
		
		$data = $this->get_post_params(array_merge($required_params, $optional_params));
		
		if($this->request->isPost()){
			if($this->check_mandatory_params($required_params)){
				$error = "";
				if(strlen($data['login']) < 4 || strlen($data['password']) < 4){
					$error .= "Les champs 'Username' et 'password' doivent contenir au moins 4 caractres \n";
				}
				if($data['password'] !== $data['confirm_password']){
					$error .= "Les champs 'password' et 'confirm password' ne sont pas identiques \n";
				}
				if(!preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $data['email'])){
					$error .= "L'adresse mail ne respecte pas le format suivant xxxxxx@xxxxx.xx \n";
				}
				if(!preg_match("/^[A-Za-z]{1}[A-Za-z0-9._-]{5,31}$/", $data['login'])){
					$error .= "Votre username contient des caractres interdits : il ne doit contenir que des chiffres et des lettres \n";
				}
				
				if(!$error){
					$user_model = $this->env['user'];
					$user = $user_model::create(array(
						'login' => $data['login'],
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
			}
		}
		$data['error'] = $error;
		return $this->render('website.user_singup', $data);
	}
}