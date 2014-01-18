<?php

$m = new Message();

if(isset($_POST['contactus-name']) && isset($_POST['contactus-email']) && isset($_POST['contactus-message'])){
	if(!empty($_POST['contactus-name']) && !empty($_POST['contactus-email']) && !empty($_POST['contactus-message'])){
		if (!preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $_POST ['contactus-email'])) {
			$m->setType ( 3 );
			$m->addMessage ( "L'adresse mail ne respecte pas le format suivant xxxxxx@xxxxx.xxx" );
		}else{	
			$text = "Bonjour l'&eacute;quipe !<br><br>";
			$text .= "Un mail a &eacute;t&eacute; envoy&eacute; par <strong>" . $_POST['contactus-name'] . "</strong> (" . $_POST['contactus-email'].") via le formulaire de contact. Voici son message :";
			$text .= "<br><i>" . $_POST['contactus-message'] ."</i><br>";
			$text .= "<br>Voila ;)";
				
			// Pour envoyer un mail HTML, l'en-tte Content-type doit tre d&eacute;fini
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
			$headers .= 'Reply-To: '.$_POST ['contactus-email'];
			
			$to = OptionManager::getInstance()->getOption("site-contact-email");
			
			if(system_send_mail("Contact Form", $text, $to, $_POST ['contactus-email'], $headers)){
				$m->addMessage("Votre mail a &eacute;t&eacute; envoy&eacute; avec succes.");
				$m->setType(1);
			}else{
				$m->addMessage("Votre mail n'a pas &eacute;t&eacute; envoy&eacute; : une erreur s'est produite. Veuillez r&eacute;essayer plus tard.");
				$m->setType(3);
			}
		}
	}else{
		$m->setTYpe(3);
		$m->addMessage("Au moins un des champs requis est vide.");
	}
}

$smanager = SessionManager::getInstance();
if($smanager->existsUserSession()){
	$profile = $smanager->getUserprofile();
	$username = $profile->getUsername();
	$email = $profile->getMail();
}else{
	$username = "";
	$email = "";
}

$contentFromFile = '<div class="col-lg-12">
  <form method="post" action="'. URLUtils::getCompletePageURL() .'" role="form"><!-- Form Name -->
<legend>Formulaire</legend>'.$m.'
      <div class="form-group row">
          <div class="col-lg-6 col-md-6 col-sm-6"><input id="name" name="name" type="text" class="form-control" placeholder="Name" value="'.$username.'"></div>
          <div class="col-lg-6 col-md-6 col-sm-6"><input id="email" name="email" type="email" class="form-control" placeholder="Email address"  value="'.$email.'"></div>
      </div>
      <div class="form-group">
          <textarea id="message" name="message" class="form-control" placeholder="Your Message" rows="5"></textarea>
      </div>
	
      <div class="controls">
          <button id="contact-submit" type="submit" class="btn input-medium pull-right">Envoyer</button>
      </div>
  </form>
</div>';	
