<?php
/**
 * Maes Jerome
 * LoginController.class.php, created at Oct 27, 2015
 *
 */
namespace module\website\controller;

use system\core\BlackController as BlackController;
use system\res\ResUser as User;

class LoginController extends WebsiteController{

	public function loginAction(){
		$error = False;
		$login = $this->request()->params('login');
		$password = $this->request()->params('password');
		
		$referrer = $this->request()->getReferrer();
		$redirect = $this->request()->params('redirect');
		
		if(!$redirect){
			$redirect = $referrer ? $referrer : __BASE_URL;
		}
		
		// if user already logged, and want to see login form page, redirect to homepage
		if($this->request()->isGet() && $this->session()->user){
			return $this->redirect('/');
		}
		
		if($this->request()->isPost()){ // login with the receive data
			if(!empty($login) && !empty($password)){
				$user = User::login($login, $password);
				if($user){
					$this->session()->authenticate($user->id, $user->login, $user->password);
					return $this->redirect($redirect);
				}else{
					$error = "Wrong login/password";
				}
			}else{
				$error = "Login and password are mandatory fields.";
			}
		}
		// otherwise, display login form
		return $this->render('website.login', array(
				'login' => $login,
				'password' => $password,
				'redirect' => $redirect, 
				'error' => $error
		));
	}
	
	
	public function logoutAction(){
		$this->session()->destroy();
		echo 'destroyed';
	}

}