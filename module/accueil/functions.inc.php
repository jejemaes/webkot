<?php


function accueil_activity_hero_unit($activity){
	$picture = accueil_utils_get_random_picture($activity);
	$HTML = '<div class="content-box">
			<a href="'.URLUtils::generateURL("activity", array("p" =>"activity", "id"=>$activity->getId())).'">
				<img class="img-polaroid float-left img-hover" style="max-height:220px;" src="'.DIR_PICTURES . $activity->getDirectory() . "/" .$picture->getFilename().'"></img>
			</a>
			
			<div class="accueil-info-group">
				<h4>'.$activity->getTitle().'</h4>
				<p>Le '.ConversionUtils::dateToDateFr($activity->getDate()).'</p>
			</div>
			<p>'.$activity->getDescription().'</p>
			<a class="btn float-right" href="'.URLUtils::generateURL("activity", array("p" =>"activity", "id"=>$activity->getId())).'">Voir l\'activit&eacute;</a>
			<div class="clearer">
			</div>
			</div>';
	return $HTML;
}


function accueil_activity_secondary($activity){
	$picture = accueil_utils_get_random_picture($activity);
	$HTML = '<div class="content-box content-shadow" style="height:auto;max-height:450px;">
				<div class="accueil-info-group"><h4>'.$activity->getTitle().'</h4>
					<p>Le '.ConversionUtils::dateToDateFr($activity->getDate()).'</p>
				</div>
				<p class="center">
					<a href="'.URLUtils::generateURL("activity", array("p" =>"activity", "id"=>$activity->getId())).'">
						<img class="img-polaroid img-center img-hover" alt="cover" src="'.DIR_PICTURES . $activity->getDirectory() . "/small/" .$picture->getFilename().'"></img>
					</a>
				</p>
			
				<div class="accueil-content-ellipsis">
					<p>'.$activity->getDescription().'</p>
				</div>
				<p class="pull-right pull-bottom"><a href="'.URLUtils::generateURL("activity", array("p" =>"activity", "id"=>$activity->getId())).'" class="btn">Voir l\'activit&eacute;</a></p>
      			<hr class="clearer">
			</div>';
	return $HTML;
}






function accueil_utils_get_random_picture(Activity $activity){
	$count = count($activity->getPictures());
	$found= false;
	$time = 0;
    if ($count){
	while(!$found && $time < 3){
		$i = rand(0,($count-1));
		$pictures = $activity->getPictures();
		$pict = $pictures[$i];
		if(!$pict->getIscensured()){
			return $pict;
		}else{
			$time++;
		}
	}
    }
	$pictures = $activity->getPictures();
	return $pictures[0];
}


/**
 * Compute the number of comment by adding the number of every given ActivityPicture
 * @param array $apicts : the ActivityPicture to count the comments
 * @return number : the number of comments
 */
function accueil_count_comment_actipict(array $apicts){
	$nb = 0;
	for($i=0 ; $i<count($apicts) ; $i++){
		$ap = $apicts[$i];
		$nb = $nb + $ap->getNbcomments();
	}
	return $nb;
}







/**
 * 
 * @param array $list : array of Todo Object
 * @param string $modname : the name of the current module
 * @return string : the html code of the list
 */
function dashboard_todo_html_table($list, $modname){
	$HTML = '<h3>Todo list</h3>';
	
	// adding button
	$HTML .= '<a class="btn btn-primary" href="'.URLUtils::generateURL($modname, array('part' => 'todo', 'action' => 'add')).'"><i class="fa fa-plus "></i> Ajouter</a>  ';
	$HTML .= '<a class="btn btn-info" href="'.URLUtils::generateURL($modname, array('part' => 'todo')).'"><i class="fa fa-th-list "></i> Voir tout</a>';
	
	
	$HTML .= '<table class="table table-condensed table-hover tablesorter">';
	$HTML .= '<thead>
		<tr>
			<th>Id</th>
			<th>Titre</th>
			<th>Description</th>
			<th>Auteur</th>
			<th>Creation</th>
			<th>Executeur</th>
			<th>Fait le</th>
			<th><u>Actions</u></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th>Id</th>
			<th>Titre</th>
			<th>Description</th>
			<th>Auteur</th>
			<th>Creation</th>
			<th>Executeur</th>
			<th>Fait le</th>
			<th><u>Actions</u></th>
		</tr>
	</tfoot>';
	
	$HTML .= '<tbody>';
	for ($i = 0; $i < count($list); $i++) {
		$t = $list[$i];
		$acc = $t->getAccomplishment_date();
		if(!empty($acc)){
			$HTML .= '<tr class="success">';
		}else{
			$HTML .= '<tr class="warning">';
		}
		$HTML .= '<td>'.$t->getId().'</td><td>'.($t->getTitle()).'</td><td>'. $t->getDescription().'</td><td>'.($t->getAuthor()).'</td><td>'.$t->getCreation_date().'</td><td>'.($t->getExecutor()).'</td><td>'.$t->getAccomplishment_date().'</td>' .
				'<td>';
		//$HTML .= '<a href="index.php?comp=home&mod=todo&a=edit&tid='.$t->getId().'"><img src="img/fa fas/b_edit.png" alt="M" title"modify" class="img_button"></a><a href="index.php?comp=home&mod=todo&a=delete&tid='.$t->getId().'" onclick=\'return(confirm("Etes vous certain de vouloir supprimer ce ToDo ?"));\'"><img src="img/fa fas/b_delete.png" alt="M" title"delete" class="img_button"></a><a href="index.php?comp=home&mod=todo&a=check&tid='.$t->getId().'" onclick=\'return(confirm("Etes vous certain de vouloir cloturer ce ToDo ?"));\'"><img src="img/fa fas/b_accept.png" alt="M" title"check" class="img_button"></a>';
		$HTML .= '<div class="btn-group">
  <a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#">
    Action
    <span class="caret"></span>
  </a>
  <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu">
	  <li><a tabindex="-1" href="'.URLUtils::generateURL($modname, array("part"=>"todo", "action"=>"edit","id"=>$t->getId())).'"><i class="fa fa-pencil"></i> Editer</a></li>
	  <li><a tabindex="-1" href="'.URLUtils::generateURL($modname, array("part"=>"todo", "action"=>"delete","id"=>$t->getId())).'" onclick=\'return(confirm("Etes vous certain de vouloir supprimer ce todo ?"));\'"><i class="fa fa-trash-o"></i> Supprimer</a></li>
	  <li><a tabindex="-1" href="'.URLUtils::generateURL($modname, array("part"=>"todo", "action"=>"check","id"=>$t->getId())).'"><i class="fa fa-check"></i> Termin&eacute;</a></li>
</ul>
</div>';
		$HTML .= '</td>';
		$HTML .= '</tr>';
	}
	$HTML .= '</tbody></table>';
	return $HTML;
}


function dashboard_todo_html_form($action, $modname, $todo){
	
	if($action == 'add'){
		$label = 'Ajouter';
		$url = URLUtils::generateURL($modname, array('part'=>'todo', 'action' => 'add'));
	}else{
		$label = 'Modifier';
		$url = URLUtils::generateURL($modname, array('part'=>'todo', 'action' => 'edit', 'id' => $todo->getId()));
	}
	
	$HTML = '<h3>'.$label.' un Todo</h3>';
	
	$HTML .= '<form class="form-horizontal" method="post" action="'. $url .'">
<fieldset>

<!-- Form Name -->
<legend>Formulaire</legend>

<!-- Text input-->
<div class="control-group">
  <label class="control-label" for="input-todo-title">Titre</label>
  <div class="controls">
    <input id="input-todo-title" name="input-todo-title" type="text" placeholder="Title" class="input-xlarge" value="'.$todo->getTitle().'">
    
  </div>
</div>

<!-- Textarea -->
<div class="control-group">
  <label class="control-label" for="input-todo-description">Description</label>
  <div class="controls">                     
    <textarea id="input-todo-description" name="input-todo-description" class="input-xxlarge bootstrap-editor" style="width: 660px; height: 200px">'.$todo->getDescription().'</textarea>
  </div>
</div>

<!-- Text input-->
<div class="control-group">
  <label class="control-label" for="input-todo-author">Auteur</label>
  <div class="controls">
    <input id="input-todo-author" name="input-todo-author" type="text" placeholder="" class="input-xlarge" readonly="readonly" value="'.$todo->getAuthor().'">
    
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



function dashboard_slide_html_table($list, $modname){
	$HTML = '<h3>Slider de la page d\'accueil</h3>';

	// adding button
	$HTML .= '<a class="btn btn-primary" href="'.URLUtils::generateURL($modname, array('part' => 'slider', 'action' => 'add')).'"><i class="fa fa-plus "></i> Ajouter</a>';

	$HTML .= '<table class="table table-striped table-hover tablesorter">';
	$HTML .= '<thead>
		<tr>
			<th>Id</th>
			<th>Titre</th>
			<th>Description</th>
			<th>Visible</th>
			<th><u>Actions</u></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th>Id</th>
			<th>Titre</th>
			<th>Description</th>
			<th>Visible</th>
			<th><u>Actions</u></th>
		</tr>
	</tfoot>';

	$HTML .= '<tbody>';
	for ($i = 0; $i < count($list); $i++) {
		$s = $list[$i];
		$statut = ($s->getIsactive() == '1' ? '<span class="label label-success">Oui</span>' : '<span class="label label-danger">Non</span>');
			
		$HTML .= '<tr>';
		$HTML .= '<td>'.$s->getId().'</td><td>'.($s->getTitle()).'</td><td>'. $s->getDescription().'</td><td>'.$statut.'</td>' .
				'<td>';
		$HTML .= '<div class="btn-group">
  <a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#">
    Action
    <span class="caret"></span>
  </a>
  <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu">
	  <li><a tabindex="-1" href="'.URLUtils::generateURL($modname, array("part"=>"slider", "action"=>"edit","id"=>$s->getId())).'"><i class="fa fa-pencil"></i> Editer</a></li>
	  <li><a tabindex="-1" href="'.URLUtils::generateURL($modname, array("part"=>"slider", "action"=>"delete","id"=>$s->getId())).'" onclick=\'return(confirm("Etes vous certain de vouloir supprimer ce slide ?"));\'"><i class="fa fa-trash-o"></i> Supprimer</a></li>
  </ul>
</div>';
		$HTML .= '</td>';
		$HTML .= '</tr>';
	}
	$HTML .= '</tbody></table>';
	return $HTML;
}


function dashboard_slide_html_form($action, $modname, $slide, $template){

	if($action == 'add'){
		$label = 'Ajouter';
		$url = URLUtils::generateURL($modname, array('part'=>'slider', 'action' => 'add'));
	}else{
		$label = 'Modifier';
		$url = URLUtils::generateURL($modname, array('part'=>'slider', 'action' => 'edit', 'id' => $slide->getId()));
	}

	$HTML = '<h3>'.$label.' un Slide</h3>';

	$HTML .= '<form class="form-horizontal" method="post" action="'. $url .'">
<fieldset>

<!-- Form Name -->
<legend>Formulaire</legend>

<!-- Text input-->
<div class="control-group">
  <label class="control-label" for="input-slide-title">Titre</label>
  <div class="controls">
    <input id="input-slide-title" name="input-slide-title" type="text" placeholder="Title" class="input-xlarge" value="'.$slide->getTitle().'">

  </div>
</div>

<!-- Textarea -->
<div class="control-group">
  <label class="control-label" for="input-slide-description">Description</label>
  <div class="controls">
    <textarea id="input-slide-description" name="input-slide-description" class="input-xxlarge bootstrap-tinymce" >'.$slide->getDescription().'</textarea>
  </div>
</div>

    		
<!-- Input -->
<div class="control-group">
  <label class="control-label" for="input-slide-img">Image de fond</label>
  <div class="controls">

    		'.system_load_plugin(array('media-picker' => array("id" => "input-slide-img", "name" => "input-slide-img", "value" => $slide->getPathimg(), 'template' => $template))).'
    		
  </div>
</div>
    		
<!-- Select Basic -->
<div class="control-group">
  <label class="control-label" for="input-slide-active">Statut</label>
  <div class="controls">
    <select id="input-slide-active" name="input-slide-active" class="input-xlarge">';
	
	if($slide->getIsactive()){
		$HTML .= '<option value="true" selected="selected">Actif</option>';
		$HTML .= '<option value="false">Inactif</option>';
	}else{
		$HTML .= '<option value="true">Actif</option>';
		$HTML .= '<option value="false" selected="selected">Inactif</option>';
	}
    $HTML .= '</select>
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
