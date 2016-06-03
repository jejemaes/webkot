<?php
/**
 * Maes Jerome
 * LoginController.class.php, created at Oct 27, 2015
 *
 */
namespace module\base\controller;
use system\http\Session as Session;


class LoginController extends BaseController{

	public function loginAction(){
		$error = False;
		$login = $this->request->getParam('login');
		$password = $this->request->getParam('password');
		
		$referrer = $this->get_referer();
		$redirect = $this->request->getParam('redirect', False);
		
		if(!$redirect){
			$redirect = $referrer ? $referrer : __BASE_URL;
		}
		
		// if user already logged, and want to see login form page, redirect to homepage
		if($this->request->isGet() && $this->session->user){
			return $this->redirect('/');
		}
		
		if($this->request->isPost()){ // login with the receive data
			if(!empty($login) && !empty($password)){
				$is_logged = $this->session->authenticate($login,$password);
				if($is_logged){
					return $this->redirect($redirect);
				}else{
					$this->session->addMessage("Wrong login/password", Session::ERROR);
				}
			}else{
				$this->session->addMessage("Login and password are mandatory fields.", Session::WARNING);
			}
		}
		return $this->redirect($redirect);
	}
	
	
	public function logoutAction(){
		$this->session->destroy();
		// redirect
		$referrer = $this->get_referer();
		$redirect = $referrer ? $referrer : __BASE_URL;
		return $this->redirect($redirect);
	}

}