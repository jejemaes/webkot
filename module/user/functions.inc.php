<?php
/**
 * get the Registration form
 * @param string $text : a text to display in the form
 * @return Form $registration : the registration form
 */
function user_form_register($action,$user, $text = null){
		
	
	$mailw = ($user->getMailwatch() == '1' ? true : false);
	$detv = ($user->getViewdet() == '1' ? true : false);
	
	$readonly = false;
	if($action == 'edit'){
		$readonly = true;
	}
	
	// Create the form
	$registration = new JFormer('contactForm', array(
			'submitButtonText' => 'Envoyer',
			'action' => URLUtils::getCompleteActualURL(),
	));
		
	// Create the form page
	$page = new JFormPage($registration->id.'Page', array(
			'description' => $text,

	));
		
		
	// Create the form section
	$JFormSection1 = new JFormSection($registration->id . 'Section1', array(
			'title' => '<h4>Informations obligatoires</h4>',
			//'description' => '<p>Sample Description.</p>'
	));
		
	// Create the form section
	$JFormSection2 = new JFormSection($registration->id . 'Section2', array(
			'title' => '<h4>Informations facultatives</h4>',
			//'description' => '<p>Sample Description.</p>'
	));
		
		
	// Add components to the section
	$JFormSection1->addJFormComponentArray(array(
			new JFormComponentSingleLineText('user-input-username', 'Username:', array(
				 'mask' => '******************************',
				 'validationOptions' => array('required', 'username'),
				 'tip' => '<p>Caracteres alphanumeriques uniquement (4 caracteres min)</p>',
				 'initialValue' => $user->getUsername(),
				 'readOnly' => $readonly
			)),
			new JFormComponentSingleLineText('user-input-mail', 'E-mail address:', array(
				 //'width' => 'long',
				 'validationOptions' => array('required', 'email'),
				 'initialValue' => $user->getMail(),
			)),
			new JFormComponentSingleLineText('user-input-password', 'Password:', array(
				 'type' => 'password',
				 'validationOptions' => array('required', 'password'),
				 'tip' => '<p>4 caracteres min et le plus complique possible ;)</p>',
			)),
			new JFormComponentSingleLineText('user-input-password-confirm', 'Confirm Password:', array(
				 'type' => 'password',
				 'validationOptions' => array('required', 'password', 'matches' => 'user-input-password'),
				 'tip' => '<p>Recopier le mot de passe</p>',
			)),
			new JFormComponentHidden('send', 'yes'),
	));
	
	if($action == 'add'){
		$JFormSection2->addJFormComponentArray(array(
				new JFormComponentSingleLineText('user-input-name', 'Nom :', array(
					 'mask' => '******************************',
					 'tip' => '<p>Caracteres alphanumeriques uniquement (4 caracteres min)</p>',
					 'initialValue' => $user->getName(),
				)),
				new JFormComponentSingleLineText('user-input-firstname', 'Prenom :', array(
					 'initialValue' => $user->getFirstname(),
				)),
				new JFormComponentSingleLineText('user-input-local', 'Localite:', array(
					 'validationOptions' => array(),
					 'tip' => '<p>Introduisez votre ville, si vous le voulez.</p>',
					 'initialValue' => $user->getAddress(),
				)),
				new JFormComponentSingleLineText('user-input-school', 'Ecole:', array(
					 'validationOptions' => array(),
					 'tip' => '<p>Introduisez votre le nom de votre ecole, si vous le voulez.</p>',
					 'initialValue' => $user->getSchool(),
				)),
				new JFormComponentSingleLineText('user-input-section', 'Section:', array(
					 'validationOptions' => array(),
					 'tip' => '<p>Introduisez vos etudes, si vous le voulez.</p>',
					 'initialValue' => $user->getSection(),
				)),
				new JFormComponentMultipleChoice('user-input-mailwatch', '', array(
					 array('value' => 'agree', 'label' => 'Je souhaite recevoir un email lorsque des photos sont mises en ligne','checked'=>$mailw),
				)
				),
				new JFormComponentMultipleChoice('user-input-detview', '', array(
					 array('value' => 'agree', 'label' => 'J\'autorise les autres a voir mon profil','checked'=>$detv),
				)
				),
	
		));
		
	}else{
		$JFormSection2->addJFormComponentArray(array(
				new JFormComponentSingleLineText('user-input-name', 'Nom :', array(
						'mask' => '******************************',
						'tip' => '<p>Caracteres alphanumeriques uniquement (4 caracteres min)</p>',
						'initialValue' => $user->getName(),
				)),
				new JFormComponentSingleLineText('user-input-firstname', 'Prenom :', array(
						'initialValue' => $user->getFirstname(),
				)),
				new JFormComponentSingleLineText('user-input-local', 'Localite:', array(
						'validationOptions' => array(),
						'tip' => '<p>Introduisez votre ville, si vous le voulez.</p>',
						'initialValue' => $user->getAddress(),
				)),
				new JFormComponentSingleLineText('user-input-school', 'Ecole:', array(
						'validationOptions' => array(),
						'tip' => '<p>Introduisez votre le nom de votre ecole, si vous le voulez.</p>',
						'initialValue' => $user->getSchool(),
				)),
				new JFormComponentSingleLineText('user-input-section', 'Section:', array(
						'validationOptions' => array(),
						'tip' => '<p>Introduisez vos etudes, si vous le voulez.</p>',
						'initialValue' => $user->getSection(),
				)),
				new JFormComponentMultipleChoice('user-input-mailwatch', '', array(
						array('value' => 'agree', 'label' => ' Je souhaite recevoir un email lorsque des photos sont mises en ligne','checked'=>$mailw),
				)
				),
				new JFormComponentMultipleChoice('user-input-detview', '', array(
						array('value' => 'agree', 'label' => ' J\'autorise les autres a voir mon profil','checked'=>$detv),
				)
				)		
		));
	}
	
	
	// Add the section to the page
	$page->addJFormSection($JFormSection1);
	$page->addJFormSection($JFormSection2);
		
	// Add the page to the form
	$registration->addJFormPage($page);

		
	return $registration;
}



function user_form_lostpassword($modname){
	$html = '<form action="'.URLUtils::generateURL($modname, array("action" => "lostpassword")).'" method="post" class="form-horizontal">
<fieldset>
<!-- Form Name -->
<legend>Formulaire</legend>

<!-- Text input-->
<div class="control-group">
  <label class="control-label" for="user-input-lostpassword">Votre email : </label>
  <div class="controls">
    <input id="user-input-lostpassword" name="user-input-lostpassword" type="text" placeholder="email" class="input-xlarge">
  </div>
</div>
 <div class="control-group">
    <div class="controls">
      <button type="submit" class="btn">Envoyer</button>
    </div>
  </div>
</fieldset>
</form>';
	return $html;
}



function user_form_facebookconnect($modname, $fbuser){
	$html = '<div class="col-lg-10 col-lg-offset-1">';
	$html .= '<div class="row panel panel-default">';
		$html .= '<div class="col-lg-6">';
		$html .= '<h4>Je n\'ai pas de compte Webkot, cr&eacute;ons-le via mon Facebook !</h4>';
		$html .= '<form method="POST" role="form" action="'.URLUtils::getCompleteActualURL().'">
          <div class="form-group">
          <label for="input-fb-username">Username</label>
          <input type="text" class="form-control" id="input-fb-username" name="input-fb-username" value="'.$fbuser["username"].'" readonly>
        </div>
        <div class="form-group">
          <label for="input-fb-password">Password</label>
          <input type="text" class="form-control" id="input-fb-password" name="input-fb-password" value="'.$fbuser["password"].'">
        </div>
        <button type="submit" class="btn btn-primary">
          Cr&eacute;er
        </button>
      </form>';
		$html .= '</div>';
		$html .= '<div class="col-lg-6">';
		$html .= '<h4>J\'ai un compte Webkot et je veux le matcher avec mon login Facebook.</h4>';
		$html .= '<form method="POST" role="form" action="'.URLUtils::getCompleteActualURL().'">
        <div class="form-group">
          <label for="input-wk-username">Username</label>
          <input type="text" class="form-control" id="input-wk-username" name="input-wk-username">
        </div>
        <div class="form-group">
          <label for="input-wk-password">Password</label>
          <input type="password" class="form-control" id="input-wk-password" name="input-wk-password" >
        </div>
        <button type="submit" class="btn btn-success">
          Matcher
        </button>
      </form>';
		$html .= '</div>';
	$html .= '</div>'; 
	$html .= '</div>';
	return $html;
}



function user_admin_html_list($modname, $listUser, $count){
	
	$HTML = '<h3>Liste des utilisateurs</h3>';
	
	// adding button
	$HTML .= $count . ' Occurrences - ';
	$HTML .= '<a class="btn btn-primary" href="'.URLUtils::generateURL($modname, array('action' => 'add')).'"><i class="fa fa-plus"></i> Ajouter</a>  ';
	
	$HTML .= '<form class="form-search pull-right" action="'.URLUtils::generateURL($modname, array('action' => 'search')).'" method="POST">
  <input type="text" class="input-medium search-query" name="field">
  <button type="submit" class="btn">Search</button>
</form>';

	//$HTML .= '<div class="alert alert-danger">Abonnement/Desbonnement Mailwatch non implem !</div>';
	$HTML .= '<div id="user-div-message" style="margin-top:15px;margin-bottom:15px;"></div>';
	
	$HTML .= '<table class="table table-striped  table-hover tablesorter">';
	$HTML .= '<thead>
		<tr>
			<th>Id</th>
			<th>Username</th>
			<th>Mail</th>
			<th>Name</th>
			<th>Firstname</th>
			<th>School</th>
			<th>Section</th>
			<th>Role</th>
			<th><u>Actions</u></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th>Id</th>
			<th>Username</th>
			<th>Mail</th>
			<th>Name</th>
			<th>Firstname</th>
			<th>School</th>
			<th>Section</th>
			<th>Role</th>
			<th><u>Actions</u></th>
		</tr>
	</tfoot>';
	
	$HTML .= '<tbody>';
	for ($i = 0; $i < count($listUser); $i++) {
		$u = $listUser[$i];
		$HTML .= '<tr>';
		$HTML .= '<td>'.$u->getId().'</td><td>'.($u->getUsername()).'</td><td>'.$u->getMail().'</td><td>'.($u->getName()).'</td><td>'.($u->getFirstname()).'</td><td>'.($u->getSchool()).'</td><td>'.($u->getSection()).'</td>';
		$HTML .= '<td><span class="label label-default" id="user-label-role-'.$u->getId().'">'.$u->getRole().'</span></td>';
		$HTML .= '<td>';
		$HTML .= '<div class="btn-group">
    <a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#">Actions <span class="caret"></span></a>
    <ul class="dropdown-menu">
    	<li><a href="'.URLUtils::generateURL($modname, array('action' => 'edit', 'id' => $u->getId())).'"><i class="fa fa-pencil"></i> Editer</a></li>
    	<li><a href="'.URLUtils::generateURL($modname, array('action' => 'delete', 'id' => $u->getId())).'" onclick=\'return(confirm("Etes vous certain de vouloir supprimer cet utilisateur ?"));\'"><i class="fa fa-trash-o"></i> Supprimer</a></li>';
		if(RoleManager::getInstance()->hasCapabilitySession('user-grant-user')){		
			$HTML .= '<li><a href="javascript:userGrantModalAction(\''.URLUtils::builtServerUrl('system',array("part"=>"role","action"=>"getroles")).'\',\''.$u->getRole().'\',\''.$u->getId().'\',\''.URLUtils::builtServerUrl('user',array("action"=>"grant")).'\');"><i class="fa fa-user"></i> Role</a></li>';
		}
		if($u->getMailwatch()){
			$HTML .= '<li id="user-action-mailwatch-'.$u->getId().'"><a href="#" onclick="return userMailwatchAction(\''.URLUtils::builtServerUrl($modname, array("action" => "mailwatch")).'\', '.$u->getId().', \'false\');"><i class="fa fa-envelope"></i> D&eacute;sabonner</a></li>';
		}else{
			$HTML .= '<li id="user-action-mailwatch-'.$u->getId().'"><a href="#" onclick="return userMailwatchAction(\''.URLUtils::builtServerUrl($modname, array("action" => "mailwatch")).'\', '.$u->getId().', \'true\');"><i class="fa fa-envelope"></i> Abonner</a></li>';
		}
    $HTML .= '</ul>
    </div>';
		$HTML .= '</td>';
		$HTML .= '</tr>';
	}
	$HTML .= '</tbody></table>';
	
	
	$HTML .= '<div class="modal fade" id="user-privilege-modal" tabindex="-1" role="dialog" aria-labelledby="user-privilege-modal-label" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 id="user-privilege-modal-label">Roles de l\'utilisateur</h3>
      </div>
      <div class="modal-body">
        LOADING ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="user-privilege-modal-save-button">Save changes</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->';
	
	return $HTML;
	
}


function user_admin_html_form($modname, $action, $user){
	
	if($action == 'add'){
		$label = 'Ajouter';
		$url = URLUtils::generateURL($modname, array('action' => 'add'));
		$readonly = "";
	}else{
		$label = 'Modifier';
		$url = URLUtils::generateURL($modname, array('action' => 'edit', 'id' => $user->getId()));
		$readonly = "readonly";
	}
	
	
		$HTML = '<h3>Modification d\' un utilisateur</h3>';
			
			
		$HTML .= '<form action="'.$url.'" method="post" class="form-horizontal">
			<legend>Formulaire</legend>
  <div class="control-group">
    <label class="control-label" for="user-input-id">Id : </label>
    <div class="controls">
      <input type="text" id="user-input-id" placeholder="id" name="user-input-id" value="' . $user->getId() . '" maxlength="20" readonly="readonly">
    </div>
  </div>
   <div class="control-group">
    <label class="control-label" for="user-input-username">Login : </label>
    <div class="controls">
      <input type="text" id="user-input-username" placeholder="Username" name="user-input-username" value="' . $user->getUsername() . '" readonly="readonly">
    </div>
  </div>
   <div class="control-group">
    <label class="control-label" for="user-input-password">Password : </label>
    <div class="controls">
      <input type="text" id="user-input-password" placeholder="password" name="user-input-password" value="' . $user->getPassword() .'">
      <span class="help-block">Modifier : <input type="checkbox" name="input-passmd5" value="ok" /> Sera automatiquement transform&eacute; en md5.</span>
    </div>
  </div>
   <div class="control-group">
    <label class="control-label" for="user-input-email">Email : </label>
    <div class="controls">
      <input type="text" id="user-input-email" placeholder="Email" name="user-input-email" value="' . $user->getMail() .'">
    </div>
  </div>
   <div class="control-group">
    <label class="control-label" for="user-input-name">Nom : </label>
    <div class="controls">
      <input type="text" id="user-input-name" placeholder="nom" name="user-input-name" value="' . $user->getName() .'">
    </div>
  </div>
   <div class="control-group">
    <label class="control-label" for="user-input-firstname">Prenom : </label>
    <div class="controls">
      <input type="text" id="user-input-firstname" placeholder="prenom" name="user-input-firstname" value="' . $user->getFirstname() .'">
    </div>
  </div>
  <div class="control-group">
    <label class="control-label" for="user-input-school">Ecole : </label>
    <div class="controls">
      <input type="text" id="user-input-school" placeholder="School" name="user-input-school" value="' . $user->getSchool() .'">
    </div>
  </div>
	
<div class="control-group">
    <label class="control-label" for="user-input-section">Section : </label>
    <div class="controls">
      <input type="text" id="user-input-section" placeholder="School" name="user-input-section" value="' . $user->getSection() .'">
    </div>
  </div>
<div class="control-group">
    <label class="control-label" for="user-input-address">Adresse : </label>
    <div class="controls">
      <input type="text" id="user-input-address" placeholder="Adresse" name="user-input-address" value="' . $user->getAddress() .'">
    </div>
  </div>
<div class="control-group">
    <label class="control-label" for="user-input-isadmin">IsAdmin (ne sera pas modifie !) : </label>
    <div class="controls">
      <input type="text" id="user-input-isadmin" placeholder="0" name="user-input-isadmin" value="' . $user->getIsAdmin() .'">
    </div>
  </div>
<div class="control-group">
    <label class="control-label" for="user-input-iswebkot" >IsWebkot (ne sera pas modifie !) : </label>
    <div class="controls">
      <input type="text" id="user-input-iswebkot" placeholder="0" name="user-input-iswebkot" value="' . $user->getIsWebkot() .'">
    </div>
  </div>
<div class="control-group">
    <label class="control-label" for="user-input-viewdet">ViewDet : </label>
    <div class="controls">
      <input type="text" id="user-input-viewdet" placeholder="0" name="user-input-viewdet" value="' . $user->getViewdet() .'">
    </div>
  </div>
  <div class="control-group">
    <label class="control-label" for="user-input-mailwatch">MailWatch : </label>
    <div class="controls">
      <input type="text" id="user-input-mailwatch" placeholder="1" name="user-input-mailwatch" value="' . $user->getMailwatch() .'">
    </div>
  </div>
  <div class="control-group">
    <label class="control-label" for="user-input-lastlogin">Dernier login : </label>
    <div class="controls">
      <input type="text" id="user-input-lastlogin" placeholder="1" name="user-input-lastlogin"  value="' . $user->getLastLogin() .'" readonly="readonly">
    </div>
  </div>
 <div class="control-group">
    <label class="control-label" for="user-input-subscription">Date Inscription : </label>
    <div class="controls">
      <input type="text" id="user-input-subscription" placeholder="1" name="user-input-subscription"  value="' . $user->getSubscription() .'" readonly="readonly">
    </div>
  </div>
  <div class="control-group">
    <div class="controls">
    	<input type="hidden" name="sendform" value="fanfanlatulipe" />
      <button type="submit" class="btn">Modifier</button>
    </div>
  </div>
</form>';
		return $HTML;
	
	
	
}
