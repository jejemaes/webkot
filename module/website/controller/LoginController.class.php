<?php
/**
 * Maes Jerome
 * LoginController.class.php, created at Oct 27, 2015
 *
 */
namespace module\website\controller;

use system\core\BlackController as BlackController;
use system\core\ResUser as User;

class LoginController extends BlackController{

	public function loginAction(){
		
		$error = False;
		$login = $this->request()->params('login');
		$password = $this->request()->params('password');
		
		$referrer = $this->request()->getReferrer();
		$redirect = $this->request()->params('redirect', $referrer ? $referrer : __BASE_URL);
		
		if($this->request()->isPost()){ // login with the receive data
			if(!empty($login) && !empty($password)){
				$user = User::login($login, $password);
				if($user){
					$this->session()->authenticate($user->id, $user->login, $user->password);
					//echo $this->session();
					//exit;
					$this->redirect($redirect);
				}else{
					$error = "Wrong login/password";
				}
			}else{
				$error = "Login and password are mandatory fields.";
			}
		}
		// otherwise, display login form
		$this->render('website.login', array(
				'login' => $login,
				'password' => $password,
				'redirect' => $redirect, 
				'error' =>$error
		));
	}
	
	
	public function logoutAction(){
		$this->render('website.home', array());
	}

}