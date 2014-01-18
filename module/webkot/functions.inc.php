<?php


/**
 * display the informations about the Webkotteur
 * @param Webkotteur $member : the object to display
 */
function webkot_html_membre(Webkotteur $member) {
	$PrenomPseudoNom = (($member->getNickname() != null)) ?
	($member->getFirstname(). ' "' .$member->getNickname(). '" ' .$member->getName()) :
	($member->getFirstname(). ' ' .$member->getName());
	
	
	$HTML = '<div class="col-lg-5 col-md-5 col-sm-5 col-xs-6">';
    $HTML .= (($member->getImg() != null)) ?
		('<img class="img-rounded img-responsive" src="'.$member->getImg().'" alt="portrait"/>') :
		('<img class="img-rounded img-responsive" src="'.DIR_MEDIA.'portraits/inconnu.jpeg" alt="portrait"/>');
	$HTML .= '<br/></div>';
    $HTML .= '<div class="col-lg-7 col-md-7 col-sm-7 col-xs-6">';
	    $HTML .= ' <blockquote>
	        <p>'.$PrenomPseudoNom.'</p>';
			if($member->getMail() != null)
				$HTML .='<small><cite title="Source Title"> '.$member->getMail().' <i class="fa fa-envelope"></i></cite></small>';
	     $HTML .= '</blockquote>';
	     
	     $HTML .= '<p>';
	     if(($member->getAge() != null))
	     	$HTML .='	<b>Age:</b> '.$member->getAge().'<br/>';
	     
	     if($member->getStudies() != null)
	     	$HTML .='<b>&Eacute;tudes:</b> '.$member->getStudies().'<br/>';
	     
	     if(($member->getFunction() != null))
	     	$HTML .='<b>Fonction:</b> '.$member->getFunction().'<br/>';
	     $HTML .= '</p>';
     
    $HTML .='</div>';
    $HTML .= '<div class="clearfix"></div>';
	return $HTML;

}
/**
 * display a table containing a webkot-team
 * @param array Webkotteur $membres : array of Webkotteur Object
 */
function webkot_html_team($membres){
	//$HTML ='<table class="table">';
	$HTML ='<div class="row">';
	$i = 0;
	for($j=0 ; $j<count($membres) ; $j++){
		$elmt = $membres[$j];
		$HTML .='<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">';
		if($i == 0){
			//$HTML .='<tr><td>';
			$HTML .= webkot_html_membre($elmt);
			//$HTML .='</td>';
			$i = 1;
		}else{
			//$HTML .='<td>';
			$HTML .= webkot_html_membre($elmt);
			//$HTML .='</td></tr>';
			$i = 0;
		}
		$HTML .= '</div>';
		if($i == 0)	
			//$HTML .='<div class="clearfix"></div>';
		
		if($j % 2 != 0){
			$HTML .= '<div class="clearfix"></div><hr>';
		}
	}
	if($i == 1)
		$HTML .='<div class="clearfix"></div><hr>';
	//$HTML .='</table>';
	$HTML .='</div>';

	return $HTML;
}








//######################
//## ADMIN WEBKOTTEUR ##
//######################

/**
 * 
 * @param unknown $list
 * @param unknown $modname
 * @return string
 */
function webkot_admin_html_webkotteur_list($list, $modname){
	
	$HTML = '<h3>Les Webkotteurs</h3>';
	
	// adding button
	$HTML .= '<a class="btn btn-primary" href="'.URLUtils::generateURL($modname, array('part' => 'webkotteur', 'action' => 'add')).'"><i class="fa fa-plus "></i> Ajouter</a>';

	$HTML .= '<table class="table table-striped table-hover tablesorter">';
	$HTML .= '<thead>
		<tr>
			<th>Id</th>
			<th>Prenom</th>
			<th>Nom</th>
			<th>Surnom</th>
			<th>Mail</th>
			<th><u>Actions</u></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th>Id</th>
			<th>Prenom</th>
			<th>Nom</th>
			<th>Surnom</th>
			<th>Mail</th>
			<th><u>Actions</u></th>
		</tr>
	</tfoot>';
	
	
	$HTML .= '<tbody>';
	for ($i = 0; $i < count($list); $i++) {
		$u = $list[$i];
		$HTML .= '<tr>';
		$HTML .= '<td>'.$u->getId().'</td><td>'.($u->getFirstname()).'</td><td>'.($u->getName()).'</td><td>'.($u->getNickname()).'</td><td>'.($u->getMail()).'</td>' .
				'<td>';
		$HTML .= '<div class="btn-group">
    <a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#">Actions <span class="caret"></span></a>
    <ul class="dropdown-menu">
    	<li><a href="'.URLUtils::generateURL($modname, array('part' => 'webkotteur', 'action' => 'edit', 'id' => $u->getId())).'"><i class="fa fa-pencil"></i> Editer</a></li>
    	<li><a href="'.URLUtils::generateURL($modname, array('part' => 'webkotteur', 'action' => 'delete', 'id' => $u->getId())).'" onclick=\'return(confirm("Etes vous certain de vouloir supprimer ce webkotteur ?"));\'"><i class="fa fa-trash-o"></i> Supprimer</a></li>
    </ul>
    </div>';
		$HTML .= '</td>';
		$HTML .= '</tr>';
	}
	$HTML .= '</tbody></table>';
	
	return $HTML;
}

/**
 * 
 * @param unknown $action
 * @param unknown $modname
 * @param unknown $webkotteur
 * @return string
 */
function webkot_admin_html_webkotteur_form($action, $modname, $webkotteur){
	
	if ($action == 'add') {
		$label = 'Ajouter';
		$url = URLUtils::generateURL ( $modname, array (
				'part' => 'webkotteur',
				'action' => 'add' 
		));
	} else {
		$label = 'Modifier';
		$url = URLUtils::generateURL ( $modname, array (
				'part' => 'webkotteur',
				'action' => 'edit',
				'id' => $webkotteur->getId () 
		));
	}

	
	$HTML = '<h3>'.$label.' un Webkotteur</h3>';
	
	$HTML .= '<form class="form-horizontal" method="post" action="'. $url .'">
<fieldset>

<!-- Form Name -->
<legend>Formulaire</legend>

<!-- Text input-->
<div class="control-group">
  <label class="control-label">Nom :</label>
  <div class="controls">
    <input id="webkot-input-name" name="webkot-input-name" type="text" placeholder="nom" class="input-xlarge" value="'.$webkotteur->getName().'">
    
  </div>
</div>

<!-- Text input-->
<div class="control-group">
  <label class="control-label">Pr&eacute;nom :</label>
  <div class="controls">
    <input id="webkot-input-firstname" name="webkot-input-firstname" type="text" placeholder="prenom" class="input-xlarge" value="'.$webkotteur->getFirstname().'">
    
  </div>
</div>

<!-- Text input-->
<div class="control-group">
  <label class="control-label">Surnom :</label>
  <div class="controls">
    <input id="webkot-input-surname" name="webkot-input-surname" type="text" placeholder="surnom" class="input-xlarge" value="'.$webkotteur->getNickname().'">
    
  </div>
</div>

<!-- Text input-->
<div class="control-group">
  <label class="control-label">UserId :</label>
  <div class="controls">
    <input id="webkot-input-userid" name="webkot-input-userid" type="text" placeholder="3" class="input-xlarge" value="'.$webkotteur->getUserid().'">
    
  </div>
</div>

<!-- Text input-->
<div class="control-group">
  <label class="control-label">Mail :</label>
  <div class="controls">
    <input id="webkot-input-mail" name="webkot-input-mail" type="text" placeholder="vous@webkot.be" class="input-xlarge" value="'.$webkotteur->getMail().'">
    
  </div>
</div>

<div class="control-group">
    <div class="controls">
      <button type="submit" class="btn">'.$label.'</button>
    </div>
</div>

</fieldset>
</form>';
	return $HTML;
}



//######################
//#### ADMIN TEAM ######
//######################
function webkot_admin_html_team_list($list, $modname){
	$HTML = '<h3>Les Equipes</h3>';
	
	// adding button
	$HTML .= '<a class="btn btn-primary" href="'.URLUtils::generateURL($modname, array('part' => 'team', 'action' => 'add')).'"><i class="fa fa-plus "></i> Ajouter</a>';

	$HTML .= '<table class="table table-striped tablesorter">';
	$HTML .= '<thead>
		<tr>
			<th>Id</th>
			<th>Prenom</th>
			<th>Nom</th>
			<th>Surnom</th>
			<th>Annee</th>
			<th>Etudes</th>
			<th>Mail</th>
			<th>Fonction</th>
			<th><u>Actions</u></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th>Id</th>
			<th>Prenom</th>
			<th>Nom</th>
			<th>Surnom</th>
			<th>Annee</th>
			<th>Etudes</th>
			<th>Mail</th>
			<th>Fonction</th>
			<th><u>Actions</u></th>
		</tr>
	</tfoot>';
	
	$HTML .= '<tbody>';
	for ($i = 0; $i < count($list); $i++) {
		$u = $list[$i];
		$HTML .= '<tr>';
		$HTML .= '<td>'.$u->getId().'</td><td>'.($u->getFirstname()).'</td><td>'.($u->getName()).'</td><td>'.($u->getNickname()).'</td><td>'.($u->getYear()).'</td><td>'.($u->getStudies()).'</td><td>'.($u->getMail()).'</td><td>'.($u->getFunction()).'</td>' .
				'<td>';
		$HTML .= '<div class="btn-group">
    <a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#">Actions <span class="caret"></span></a>
    <ul class="dropdown-menu">
    	<li><a href="'.URLUtils::generateURL($modname, array('part' => 'team', 'action' => 'edit', 'id' => $u->getId(), 'year' => $u->getYear())).'"><i class="fa fa-pencil"></i> Editer</a></li>
    	<li><a href="'.URLUtils::generateURL($modname, array('part' => 'team', 'action' => 'delete', 'id' => $u->getId(), 'year' => $u->getYear())).'" onclick=\'return(confirm("Etes vous certain de vouloir supprimer ce webkotteur de cette equipe ?"));\'"><i class="fa fa-trash-o"></i> Supprimer</a></li>
    </ul>
    </div>';
		$HTML .= '</td>';
		$HTML .= '</tr>';
	}
	$HTML .= '</tbody></table>';
	return $HTML;
}



function webkot_html_team_form($modname, $webkotteur, $template){
	
	$HTML = '<form class="form-horizontal" method="post" action="'.URLUtils::generateURL($modname, array('part' => 'team', 'action' => 'edit', 'id' => $webkotteur->getId(), 'year' => $webkotteur->getYear())).'">
	<legend>Formulaire : Completez les infos</legend>
	<input type="hidden" id="webkot-input-year" value="'.$webkotteur->getYear().'" name="webkot-input-year" readonly="readonly">
	<div class="control-group"><h4>'.$webkotteur->getName().' '.$webkotteur->getFirstname().' en <i>'.$webkotteur->getYear().'</i></h4>
 	<input type="hidden" id="webkot-input-id" value="'.$webkotteur->getId().'" name="webkot-input-id">
    <label class="control-label" for="webkot-input-function">Fonction</label>
    <div class="controls">
      <input type="text" id="webkot-input-function" placeholder="fonction" name="webkot-input-function" value="'.$webkotteur->getFunction().'">
    </div>
  </div>
   <div class="control-group">
    <label class="control-label" for="webkot-input-age">Age</label>
    <div class="controls">
      <input type="text" id="webkot-input-age" placeholder="age" name="webkot-input-age" value="'.$webkotteur->getAge().'">
    </div>
  </div>
   <div class="control-group">
    <label class="control-label" for="webkot-input-study">Etudes</label>
    <div class="controls">
      <input type="text" id="webkot-input-study" placeholder="etudes" name="webkot-input-study" value="'.$webkotteur->getStudies().'">
    </div>
   </div>
   
  	<!-- Input -->
	<div class="control-group">
	  <label class="control-label" for="webkot-input-img">Image</label>
	  <div class="controls">
	    		'.system_load_plugin(array('media-picker' => array("id" => "webkot-input-img","name" => "webkot-input-img", "value" => $webkotteur->getImg(), "template" => $template))).'
	  </div>
	</div>
    		
  <div class="control-group">
    <label class="control-label" for="webkot-input-order">Ordre d\'affichage</label>
    <div class="controls">
      <input type="text" id="webkot-input-order" placeholder="0" name="webkot-input-order" value="'.$webkotteur->getPlace().'">
    </div>
  </div><hr><div class="control-group">
    <div class="controls">
      <button type="submit" class="btn">Modifier</button>
    </div>
  </div>
</form>';
	return $HTML;
	
}


function webkot_admin_html_addteam_step1($modname){
	$form = '<form class="form-horizontal" method="post" action="'.URLUtils::generateURL($modname, array('part' => 'team', 'action' => 'add', 'step' => '2')).'"><legend>Formulaire : Selectionnez une equipe</legend>';
	
	//generate the possible years
	$options = "";
	for($i=1999 ; $i <= (system_get_begin_year()+1) ; $i++){
		$options .= "<option>".$i."-" .($i+1)."</option>";
	}
	// add warning
	$message = new Message(2);
	$message->addMessage("Avant de commencer, assurez vous que les personnes que vous voulez ajouter a une equipe sont bien dans la liste des webkotteurs");	
	$form .= $message;
	
	$form .= '<div class="control-group">
    <label class="control-label" for="inputYear">Ann&eacute;e</label>
    <div class="controls">
       <select id="webkot-input-year" name="webkot-input-year">
  		'.$options.'
	</select>
    </div>
  </div>
	
  <div class="control-group">
    <div class="controls">
      <button type="submit" class="btn">Choisir</button>
    </div>
  </div>
</form>';
	
	return $form;
}



function webkot_admin_html_addteam_step2($modulename, $year, array $list){
	
	$form = '<form class="form-horizontal" method="post" action="'.URLUtils::generateURL($modulename, array('part' => 'team', 'action' => 'add', 'step' => '3')).'"><legend>Formulaire : Ajoutez des Webkotteurs</legend>';
	
	// generate the list of names which can be added to the team
	$checkboxes = "";
	for($i = 0 ; $i < count($list) ; $i++){
		$w = $list[$i];
		// the value is "firstname name_id"; to not ask the DB in the next step to have the informations (name and firstname) with the id. We jsut split at the _.
		$checkboxes .= '<label class="checkbox inline"> <input type="checkbox" id="inlineCheckbox'.$w->getId().'" value="'.$w->getId().'" name="webkot-input-webkotteurs[]">'.$w->getFirstname().' '.$w->getName().' ('.$w->getId().') </label><br>';
	}
	
	$form .= '<div class="control-group">
    <label class="control-label" for="inputTitle">Equipe</label>
    <div class="controls">
      <input type="text" id="webkot-input-year" name="webkot-input-year" value="'.$year.'" readonly="readonly">
    </div>
  </div>
	
   <div class="control-group">
    <label class="control-label" for="inputDate">Quels webkotteurs ?</label>
    <div class="controls">
      '.$checkboxes.'
    </div>
  </div>
	
  <div class="control-group">
    <div class="controls">
      <button type="submit" class="btn">Ajouter</button>
    </div>
  </div>
</form>';
	
	return $form;
	
}




function webkot_admin_html_addteam_step3($name, array $webkotteur, $year, $template){
	
	$form = '<form class="form-horizontal" method="post" action="'.URLUtils::generateURL($name, array('part' => 'team', 'action' => 'add', 'step' => '4')).'">';
	$form .= '<legend>Formulaire : Completez les infos</legend>';
	$form .= '<input type="hidden" id="webkot-input-year" value="'.$year.'" placeholder="fonction" name="webkot-input-year">';
	
	$message = new Message(0);
	$message->addMessage("Attention, les infos que vous allez indiquee seront affichees directement sur le site. Faites donc attention !");
	$form .= $message;
	
	for($i = 0 ; $i < count($webkotteur) ; $i++){

		$w = $webkotteur[$i];
		
			
		$form .= '<div class="control-group"><h4>'.$w->getName().' '.$w->getFirstname().'</h4>
 	<input type="hidden" id="webkot-input-id" value="'.$w->getId().'" name="webkot-input-id[]">
    <label class="control-label" for="webkot-input-function">Fonction :</label>
    <div class="controls">
      <input type="text" id="webkot-input-function" placeholder="fonction" name="webkot-input-function[]">
    </div>
  </div>
   <div class="control-group">
    <label class="control-label" for="webkot-input-age">Age* :</label>
    <div class="controls">
      <input type="text" id="webkot-input-age" placeholder="age" name="webkot-input-age[]">
    </div>
  </div>
   <div class="control-group">
    <label class="control-label" for="webkot-input-study">Etudes :</label>
    <div class="controls">
      <input type="text" id="webkot-input-study" placeholder="etudes" name="webkot-input-study[]">
    </div>
   </div>
  
	<!-- Input -->
	<div class="control-group">
	  <label class="control-label" for="webkot-input-img">Image :</label>
	  <div class="controls">
	  		'.system_load_plugin(array('media-picker' => array("id" => "webkot-input-img-".$i,"name" => "webkot-input-img[]", "template" => $template))).'	
	  </div>
	</div>
    		
  <div class="control-group">
    <label class="control-label" for="webkot-input-order">Ordre d\'affichage* :</label>
    <div class="controls">
      <input type="text" id="webkot-input-order" placeholder="0" name="webkot-input-order[]">
    </div>
  </div><hr>';
	
	}
	
	 
	$form .= '<div class="control-group">
    <div class="controls">
      <button type="submit" class="btn">Valider</button>
    </div>
  </div>
</form>';
	
	return $form;
	
	
	
}


