<?php


function challenge_admin_html_list($modname, $list){
	$HTML = '<h3>Les Challenges</h3>';
	
	$HTML .= '<div class="alert alert-info">Le challenge qui sera affiche sur le site est celui qui a la date de publication (creation) la plus grande.</div>';
	
	// adding button
	$HTML .= '<a class="btn btn-primary" href="'.URLUtils::generateURL($modname, array('action' => 'add')).'"><i class="fa fa-plus "></i> Ajouter</a>';

	
	$HTML .= '<table class="table table-striped tablesorter">';
	$HTML .= '<thead>
		<tr>
			<th>Id</th>
			<th>Question</th>
			<th>Reponse</th>
			<th>Description</th>
			<th>Date</th>
			<th>Date de fin</th>
			<th>Winner</th>
			<th><u>Actions</u></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th>Id</th>
			<th>Question</th>
			<th>Reponse</th>
			<th>Description</th>
			<th>Date</th>
			<th>Date de fin</th>
			<th>Winner</th>
			<th><u>Actions</u></th>
		</tr>
	</tfoot>';
	
	$HTML .= '<tbody>';
	for ($i = 0; $i < count($list); $i++) {
		$a = $list[$i];
		$HTML .= '<tr>';
		$HTML .= '<td>'.$a->getId().'</td><td>'.($a->getQuestion()).'</td><td>'.($a->getAnswer()).'</td><td>'.($a->getDescription()).'</td><td>'.$a->getPublication_date().'</td><td>'.($a->getEnd_date()).'</td><td><a href="#">'.$a->getWinnerid().'</a></td>';
		$HTML .= '<td>';	
		$HTML .= '<div class="btn-group">
			    <a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#">Actions <span class="caret"></span></a>
			    <ul class="dropdown-menu">
					<li><a href="'.URLUtils::generateURL($modname, array('detail' => $a->getId())).'"><i class="fa fa-search"></i> Details</a></li>
			    	<li><a href="'.URLUtils::generateURL($modname, array('action' => 'edit', 'id' => $a->getId())).'"><i class="fa fa-pencil"></i> Editer</a></li>
			    	<li><a href="'.URLUtils::generateURL($modname, array('action' => 'delete', 'id' => $a->getId())).'" onclick=\'return(confirm("Etes vous certain de vouloir supprimer ce concours ainsi que toutes ses reponses ?"));\'"><i class="fa fa-trash"></i> Supprimer</a></li>
			    </ul>
			    </div>';
		$HTML .= '</td>';
		$HTML .= '</tr>';
	}
	$HTML .= '</tbody></table>';
	return $HTML;
}



function challenge_admin_html_detail($modname, $challenge, $listAnswer){

	$HTML = '<h3>Detail d\'un concours</h3>';

	//buttons
	if($challenge->getWinnerid() == null){
		// find a winner button
		$HTML .= '<a href="'.URLUtils::generateURL($modname,array("action" => "genwinner", "id" => $challenge->getId())).'"><button class="btn btn-info" type="button"><i class="fa fa-user "></i> Gagnant aleatoire</button></a>  ';
	}
	$HTML .= '<a href="'.URLUtils::generateURL($modname,array("action" => "edit", "id" => $challenge->getId())).'"><button class="btn btn-primary" type="button"><i class="fa fa-pencil "></i> Editer</button></a>  ';
	

	$HTML .= '<br><br><br>';
	$HTML .= '<b>Id : </b>' . $challenge->getId() . '<br>';
	$HTML .= '<b>Question : </b>' . $challenge->getQuestion() . '<br>';
	$HTML .= '<b>Reponse : </b>' . $challenge->getAnswer() . '<br>';
	$HTML .= '<b>Description : </b>' . $challenge->getDescription() . '<br>';
	$HTML .= '<b>Path : </b>' . $challenge->getPath_picture() . '<br>';
	$HTML .= '<b>Date de publication : </b>' . $challenge->getPublication_date() . '<br>';
	$HTML .= '<b>Date de fin : </b>' . $challenge->getEnd_date() . '<br>';

	$HTML .= '<br><b>Nombre de reponses : </b>' . count($listAnswer) . '<br>';


	$HTML .= '<table class="table table-striped tablesorter">';
	$HTML .= '<thead>
		<tr>
			<th>Userid</th>
			<th>Reponse</th>
			<th>Date</th>
			<th>Statut</th>
			<th><u>Actions</u></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th>Userid</th>
			<th>Reponse</th>
			<th>Date</th>
			<th>Statut</th>
			<th><u>Actions</u></th>
		</tr>
	</tfoot>';

	$HTML .= '<tbody>';
	for ($i = 0; $i < count($listAnswer); $i++) {
		$a = $listAnswer[$i];
		$HTML .= '<tr>';
		$HTML .= '<td>'.$a->getUserid().'</td><td>'.ConversionUtils::decoding($a->getAnswer()).'</td><td>'.$a->getDate().'</td>';
		if($a->getIscorrect() == null){
			$HTML .= '<td>?</td>';
		}else{
			if($a->getIscorrect()){
				$HTML .= '<td><img src="'.DIR_MODULE . $modname . '/view/img/b_validate.png" alt="OK" title"status" class="img_button"></td>';
			}else{
				$HTML .= '<td><img src="'.DIR_MODULE . $modname . '/view/img/b_invalidate.png" alt="KO" title"status" class="img_button"></td>';
			}
		}

		$HTML .= '<td><a href="'.URLUtils::generateURL($modname, array("action" => "validate", "aid" => $a->getId(), "cid" => $challenge->getId())).'"><img src="'.DIR_MODULE . $modname . '/view/img/b_validate.png" alt="Validate" title"modify" class="img_button"></a>
					<a href="'.URLUtils::generateURL($modname, array("action" => "invalidate", "aid" => $a->getId(), "cid" => $challenge->getId())).'"><img src="'.DIR_MODULE . $modname . '/view/img/b_invalidate.png" alt="Invalidate" title"detail" class="img_button"></a></a></td>';
		$HTML .= '</tr>';
	}
	$HTML .= '</tbody>';

	$HTML .= '</table>';
	$HTML .= '<a href="'.URLUtils::generateURL($modname, array()).'" class="btn">Retour a la liste des concours</a>';
	
	return $HTML;

}



function challenge_admin_html_form($action, $modname, $challenge){
	
	if($action == 'add'){
		$label = 'Ajouter';
		$url = URLUtils::generateURL($modname, array('action' => 'add'));
		$date = date('Y-m-d');
		$readonly = "";
	}else{
		$label = 'Modifier';
		$url = URLUtils::generateURL($modname, array('action' => 'edit', 'id' => $challenge->getId()));
		$date = preg_split("/[ ]/", $challenge->getEnd_date());
		$date = $date[0];
		$readonly = "readonly";
	}
	
	$HTML = '<h3>'.$label.' un concours</h3>';
	$HTML .= '<form class="form-horizontal" method="post" action="'. $url .'">
<fieldset>

<!-- Form Name -->
<legend>Formulaire</legend>

<!-- Text input-->
<div class="control-group">
  <label class="control-label" for="challenge-input-question">Question</label>
  <div class="controls">
    <input id="challenge-input-question" name="challenge-input-question" type="text" placeholder="question" class="input-xlarge" value="'.$challenge->getQuestion().'">
    <p class="help-block">La question a laquelle les gens vont r�pondre.</p>
  </div>
</div>

<!-- Text input-->
<div class="control-group">
  <label class="control-label" for="challenge-input-answer">Reponse</label>
  <div class="controls">
    <input id="challenge-input-answer" name="challenge-input-answer" type="text" placeholder="reponse" class="input-xlarge" value="'.$challenge->getAnswer().'">
    <p class="help-block">La reponse sera affichee sur le site � la fin du concours.</p>
  </div>
</div>

<!-- Textarea -->
<div class="control-group">
  <label class="control-label" for="challenge-input-description">Description</label>
  <div class="controls">                     
    <textarea id="challenge-input-description" name="challenge-input-description">'.$challenge->getDescription().'</textarea>
  </div>
</div>

<!-- Text input-->
<div class="control-group">
  <label class="control-label" for="challenge-input-path">Image</label>
  <div class="controls">
    <input id="challenge-input-path" name="challenge-input-path" type="text" placeholder="path/to/image.jpg" class="input-xlarge"  value="'.$challenge->getPath_picture().'">
    <p class="help-block">Le chemin de l\'image concern&eacute;e.</p>
  </div>
</div>
			
<!-- Text input-->
<div class="control-group">
  <label class="control-label" for="challenge-input-date">Date</label>
  <div class="controls">
    <input id="challenge-input-date" name="challenge-input-date" type="text" data-date-format="yyyy-mm-dd" class="input-xlarge" value="'.$date.'" readonly>
  </div>
</div
    		
  <div class="control-group">
    <div class="controls">
      <button type="submit" class="btn">'.$label.'</button>
    </div>
</div>

</fieldset>
</form>';
	
	return $HTML;
			
}