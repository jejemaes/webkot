<?php

class WidgetLogin extends Widget implements iWidget{
	
	/**
	 * (non-PHPdoc)
	 * @see iWidget::__toString()
	 */
	public function __toString(){
		$manager = SessionManager::getInstance();
		if(!$manager->existsUserSession()){
			// Create the form
			$login = new JFormer('loginForm', array(
					'submitButtonText' => 'Login',
					'action' => URLUtils::getCompleteActualURL(),
			));
				
			// Create the form page
			$jFormPage1 = new JFormPage($login->id.'Page', array(
					//'description' => $error,
			));
				
			// Create the form section
			$jFormSection1 = new JFormSection($login->id.'Section', array());
				
			// make the redirection to a string var
			$redirect = URLUtils::getCompleteActualURL();
				
			// Add components to the section
			$jFormSection1->addJFormComponentArray(array(
					new JFormComponentSingleLineText('form-login-input-username', 'Username:', array(
							'validationOptions' => array('required'),
					)),
			
					new JFormComponentSingleLineText('form-login-input-password', 'Password:', array(
							'type' => 'password',
							'validationOptions' => array('required', 'password'),
					)),
					new JFormComponentHidden('form-login-sended', 'fromform'),
			
			));
				
			// Add the section to the page
			$jFormPage1->addJFormSection($jFormSection1);
				
			// Add the page to the form
			$login->addJFormPage($jFormPage1);
				
			$login .= 'Pas encore inscrit ? <a class="RegisterForm" href="index.php?mod=user&action=inscription">Cliquez ici</a>';
			$login .= '<br >Mot de passe perdu ? <a href="index.php?mod=user&action=lostpassword">Cliquez ici</a>';
			
			
			// check Facebook conneciton
			$facebook = new Facebook(array(
					'appId'  => FACEBOOK_APPID,
					'secret' => FACEBOOK_SECRET,
			));
			
			// Get User ID
			$user = $facebook->getUser();
			// Login or logout url will be needed depending on current user state.
			if ($user) {
				$logoutUrl = $facebook->getLogoutUrl();
			} else {
				$loginUrl = $facebook->getLoginUrl();
				
				$login .= '<hr>';
				$login .= '<a href="'.$loginUrl.'" class="btn btn-block btn-social btn-facebook">
					        <i class="fa fa-facebook"></i> Sign in with Facebook
					      </a>';
			}
			return $login;
		}else{
			$manager = SessionManager::getInstance();
			$id = $manager->getUserprofile()->getId();
			$username = $manager->getUSerprofile()->getUsername();
			
			$this->setName("<i class=\"fa fa-user\"></i> <strong>".$username."</strong>");
			
			$tab = array();
			$tab["Mon profil"] = "index.php?mod=user&profile=".$username;
			$tab["Editer profil"] = "index.php?mod=user&action=edit";
			
			$manager = ModuleManager::getInstance();
			$arr = $manager->getSubscriberModuleAction();
			foreach ($arr as $key => $link){
				$tab[$key] = $link;
			}
			
			if(system_session_privilege() >= 5){
				$tab['Admin Panel'] = "admin/index.php";
			}
			return system_html_action_list($tab,"list-unstyled") . ' <a class="btn btn-default template-center" href="index.php?logout"><i class="fa fa-power-off"> </i> Logout</a>';
		}
	}
	
}