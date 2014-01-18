<?php


class UserView extends View implements iView{
	
	
	/**
	 * Constructor
	 * @param iTemplate $template
	 */
	public function __construct(iTemplate $template, Module $module){
		parent::setTemplate($template);
		parent::setModule($module);
		$this->configureTemplate();
	}
	
	/**
	 * Set up the Layout according to the config file of the module, and init its content
	 * @param String $state : the state of the module which define the layout
	 * @param String $content : the html code of the content
	 */
	private function configureLayout($state, $content){
		$lname = $this->getModule()->getLayout($state);
		$this->getTemplate()->setLayout($lname);
		$this->getTemplate()->setContent($content);
	}
	
	/**
	 * Set some parameters for the Template : add css style, js code, ...
	 */
	private function configureTemplate(){
		$viewdirectory = DIR_MODULE . $this->getModule()->getLocation() . 'view/';
		// add module css
		$template = $this->getTemplate();
		$template->addStyle('<link href="'.$viewdirectory.'css/style.css" rel="stylesheet"/>');
		$this->getTemplate()->setPageTitle($this->getModule()->getDisplayedName());
	}
	
	
	
	public function ListProfilePage(array $users, $count, $page, $desc){
		$fields = array(
				'Id' => 'getId',
				'Username' => array('getUsername',array('index.php?mod=user&amp;profile=','getId')),
				'Nom' => 'getName',
				'Pr&eacute;nom' => 'getFirstname'
		);
		
		$HTML = system_html_list_object($users, $fields);
		
		$HTML .= '<hr>' . system_html_pagination($this->getModule()->getName(), array(),$count,$desc,$page, "utilisateurs");
		
		$this->configureLayout('page-profile',$HTML);
		$this->getTemplate()->setPageSubtitle("Liste des Utilisateurs publiques");
	}
	
	
	
	public function UserProfilePage(User $user, $isMyProfile = false){
		$HTML .= '<div id="user-div-message"></div>';
		if($isMyProfile){		
			if($user->getMailwatch()){
				$mwaction = '<a class="btn btn-primary" href="#" onclick="return userMailwatchAction(\''.URLUtils::builtServerUrl($this->getModule()->getName(), array("action" => "mailwatch")).'\', '.$user->getId().', \'false\');"><i class="fa fa-envelope"></i> D&eacute;sabonner</a>';
			}else{
				$mwaction = '<a class="btn btn-primary" href="#" onclick="return userMailwatchAction(\''.URLUtils::builtServerUrl($this->getModule()->getName(), array("action" => "mailwatch")).'\', '.$user->getId().', \'true\');"><i class="fa fa-envelope"></i> Abonner</a>';
			}
			$HTML .= '<div class="pull-right"><span id="user-action-mailwatch">'.$mwaction.'</span></div>';
		}
		$HTML .= '<p><b>Login : </b>' . $user->getUsername() . '<br>';
		$HTML .= '<b>Email : </b>' . $user->getMail() . '</p>';
			
		$HTML .= '<p><b>Pr&eacute;nom : </b>' . $user->getFirstname() . '<br>';
		$HTML .= '<b>Nom : </b>' . $user->getName() . '<br>';
		$HTML .= '<b>Localit&eacute; : </b>' . $user->getAddress() . '<br>';
		$HTML .= '<b>Ecole : </b>' . $user->getSchool() . '<br>';
		$HTML .= '<b>Section : </b>' . $user->getSection() . '</p>';
		$HTML .= '<p><b>Inscrit le : </b>' . $user->getSubscription() . '</p>';
		
		$HTML .= '<p><b>Niveau d\'acc&egrave;s : </b>' . $user->getRole() . '<br><br>';
		if($user->getViewdet()){
			$viewdet = "Oui";
		}else{
			$viewdet = "Non";
		}
		$HTML .= '<b>Mon profil est visible : </b>' . $viewdet . '<br>';
			
		if($user->getMailwatch()){
			$mailwatch = "Oui";
		}else{
			$mailwatch = "Non";
		}
		$HTML .= '<b>Je souhaite recevoir un mail lorsqu\'une activit&eacute; est ajout&eacute;e sur le Webkot : </b><span id="user-label-mailwatch">' . $mailwatch . '</span></p><br>';
		
		$this->configureLayout('page-list',$HTML);
		$this->getTemplate()->setPageSubtitle("Profil de " . $user->getUsername());
		$this->getTemplate()->addJSHeader('<script type="text/javascript" src="'.DIR_MODULE . $this->getModule()->getLocation() . 'view/js/frontend.js"></script>');
		
	}
	
	
	
	public function pageInscription($action, $user, $message){
		$HTML = $message;
		$HTML .= user_form_register($action, $user);
		$this->configureLayout('page-inscription',$HTML);
		if($user->getId()){
			$this->getTemplate()->setPageSubtitle("Edition du profile");
		}else{
			$this->getTemplate()->setPageSubtitle("Formulaire d'inscription");
		}
	}
	
	
	public function pageLostPassword($action, $message){
		$HTML = $message;
		$HTML .= "Introduisez votre email de votre compte (celui utilis&eacute; par votre compte Webkot), un nouveau mot de passe sera g&eacute;n&eacute;r&eacute; et envoy&eacute; &agrave; l'adresse mail introduite. Vous pouvez donc vous connectez avec ce nouveau mot de passe et le modifier.";
		$HTML .= user_form_lostpassword($this->getModule()->getName());
		$this->configureLayout('page-inscription',$HTML);
		$this->getTemplate()->setPageSubtitle("Mot de passe perdu");
	}
	
	public function pageFacebookConnect($fbuser, $message){
		$HTML = $message;
		$HTML .= "C'est la premi�re fois que vous vous connectez sur le Webkot via Facebook. Deux sc&eacute;narios s'offrent � vous :";
		$HTML .= "<ul>";
		$HTML .= "<li>Si vous avez d&eacute;j� un compte sur le Webkot, remplissez le formulaire avec vos identifiants Webkot. Cela vous permettera de vous connecter sur votre compte Webkot via Facebook, tout en conservant vos anciennes donn&eacute;es.</li>";
		$HTML .= "<li>Si vous n'avez pas de compte Webkot, cliquez sur le NON. Un compte sera cr&eacute;&eacute;. Le login sera votre <i>username</i> Facebook, et votre mot de passe sera g&eacute;n&eacute;r&eacute;. Vous pourrez le changer, si vous d&eacute;sirez un jour pouvoir vous connecter avec vos identifiants Webkot.</li>";
		$HTML .= "</ul>";
		$HTML .= user_form_facebookconnect($this->getModule()->getName(), $fbuser);
		$HTML .= '<br><a href="index.php?logout=index.php" class="btn btn-warning">Annuler</a>';
		$this->configureLayout('page-inscription',$HTML);
		$this->getTemplate()->setPageSubtitle("Facebook Connect");
	}

}
