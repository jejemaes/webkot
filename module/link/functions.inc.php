<?php

function link_admin_html_table_link($modname, $list){
	$HTML = '<h3>Les Liens</h3>';
	
	// adding button
	$HTML .= '<a class="btn btn-primary" href="'.URLUtils::generateURL($modname, array('part' => 'link', 'action' => 'add')).'"><i class="fa fa-plus "></i> Ajouter</a>';
	
	$HTML .= '<table class="table table-striped tablesorter">';
	$HTML .= '<thead>
		<tr>
			<th>ID</th>
			<th>Nom</th>
			<th>URL</th>
			<th>Categorie</th>
			<th><u>Actions</u></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th>ID</th>
			<th>Nom</th>
			<th>URL</th>
			<th>Categorie</th>
			<th><u>Actions</u></th>
		</tr>
	</tfoot>';
	
	$HTML .= '<tbody>';
	for ($i = 0; $i < count($list); $i++) {
		$l = $list[$i];
		$HTML .= '<tr>';
		$HTML .= '<td>'.$l->getId().'</td><td>'.$l->getName().'</td><td>'.$l->getUrl().'</td><td>'.$l->getCategory().'</td>' .
				'<td>';
		$HTML .= '<div class="btn-group">
    <a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#">Actions <span class="caret"></span></a>
    <ul class="dropdown-menu">
    	<li><a href="'.URLUtils::generateURL($modname, array('part' => 'link', 'action' => 'edit', 'id' => $l->getId())).'"><i class="fa fa-pencil"></i> Editer</a></li>
    	<li><a href="'.URLUtils::generateURL($modname, array('part' => 'link', 'action' => 'delete', 'id' => $l->getId())).'" onclick=\'return(confirm("Etes vous certain de vouloir supprimer ce lien ?"));\'"><i class="icon-trash"></i> Supprimer</a></li>
    </ul>
    </div>';
		$HTML .= '</td>';
		$HTML .= '</tr>';
	}
	$HTML .= '</tbody></table>';
	return $HTML;
}


function link_admin_link_form($modname, $action, $categories, $link){
	
	if ($action == 'add') {
		$label = 'Ajouter';
		$url = URLUtils::generateURL ( $modname, array (
				'part' => 'link',
				'action' => 'add'
		));
	} else {
		$label = 'Modifier';
		$url = URLUtils::generateURL ( $modname, array (
				'part' => 'link',
				'action' => 'edit',
				'id' => $link->getId ()
		));
	}
	
	// built the drop list wih categories names
	$droplist = '<select name="link-input-category" id="link-input-category">';
	for($i =0 ; $i < count($categories) ; $i++){
		$cat = $categories[$i];
		if($cat->getDescription() == $link->getCategory() || $cat->getName() == $link->getCategory()){
			$droplist .= '<option value="'.$cat->getName().'" selected="selected">' . $cat->getDescription() .'</option>';
		}else{
			$droplist .= "<option value=".$cat->getName().">" . $cat->getDescription() ."</option>";
		}
	}
	$droplist .= '</select>';
	
	
	
	$HTML = '<h3>'.$label.' un lien</h3>';
	
	$HTML .= '<form class="form-horizontal" method="post" action="'.$url.'">
<fieldset>

<!-- Form Name -->
<legend>Formulaire</legend>

<!-- Text input-->
<div class="control-group">
  <label class="control-label" for="link-input-name">Nom :</label>
  <div class="controls">
    <input id="link-input-name" name="link-input-name" type="text" placeholder="nom" class="input-xlarge" value="'.$link->getName().'">
    
  </div>
</div>

<!-- Text input-->
<div class="control-group">
  <label class="control-label" for="link-input-url">URL :</label>
  <div class="controls">
    <input id="link-input-url" name="link-input-url" type="text" placeholder="http://www.lesite.com" class="input-xlarge" value="'.$link->getUrl().'">
    
  </div>
</div>

<!-- Select Basic -->
<div class="control-group">
  <label class="control-label" for="link-input-category">Categorie :</label>
  <div class="controls">
    '.$droplist.'
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







function link_admin_html_table_link_category($modname, $list){
	$HTML = '<h3>Les cat&eacute;gories de lien</h3>';

	// adding button
	$HTML .= '<a class="btn btn-primary" href="'.URLUtils::generateURL($modname, array('part' => 'category', 'action' => 'add')).'"><i class="fa fa-plus "></i> Ajouter</a>';

	$HTML .= '<table class="table table-striped tablesorter">';
	$HTML .= '<thead>
		<tr>
			<th>Nom</th>
			<th>Description</th>
			<th>Place</th>
			<th><u>Actions</u></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th>Nom</th>
			<th>Description</th>
			<th>Place</th>
			<th><u>Actions</u></th>
		</tr>
	</tfoot>';
	$HTML .= '<tbody>';
	for ($i = 0; $i < count($list); $i++) {
		$l = $list[$i];
		$HTML .= '<tr>';
		$HTML .= '<td>'.$l->getName().'</td><td>'.$l->getDescription().'</td><td>'.$l->getPlace().'</td>' .
				'<td>';
		$HTML .= '<div class="btn-group">
    <a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#">Actions <span class="caret"></span></a>
    <ul class="dropdown-menu">
    	<li><a href="'.URLUtils::generateURL($modname, array('part' => 'category', 'action' => 'edit', 'name' => $l->getName())).'"><i class="fa fa-pencil"></i> Editer</a></li>
    	<li><a href="'.URLUtils::generateURL($modname, array('part' => 'category', 'action' => 'delete', 'name' => $l->getName())).'" onclick=\'return(confirm("Etes vous certain de vouloir supprimer cette categorie ?"));\'"><i class="fa fa-trash-o"></i> Supprimer</a></li>
    </ul>
    </div>';
		$HTML .= '</td>';
		$HTML .= '</tr>';
	}
	$HTML .= '</tbody></table>';
	return $HTML;
}



function link_admin_category_form($modname, $action, $category){
	
	if ($action == 'add') {
		$label = 'Ajouter';
		$url = URLUtils::generateURL ( $modname, array (
				'part' => 'category',
				'action' => 'add'
		));
	} else {
		$label = 'Modifier';
		$url = URLUtils::generateURL ( $modname, array (
				'part' => 'category',
				'action' => 'edit',
				'name' => $category->getName()
		));
	}
	
	$html = '<h3>'.$label.' une categorie</h3>';
	$html .= '<form class="form-horizontal" method="post" action="'.$url.'">
<fieldset>

<!-- Form Name -->
<legend>Formulaire</legend>

<!-- Text input-->
<div class="control-group">
  <label class="control-label" for="link-input-catid">Identifiant : </label>
  <div class="controls">
    <input id="link-input-catid" name="link-input-catid" type="text" placeholder="identifiant" class="input-xlarge" value="'.$category->getName().'">
    
  </div>
</div>

<!-- Text input-->
<div class="control-group">
  <label class="control-label" for="link-input-catdescri">Description : </label>
  <div class="controls">
    <input id="link-input-catdescri" name="link-input-catdescri" type="text" placeholder="nom" class="input-xlarge" value="'.$category->getDescription().'">
    <p class="help-block">Il s\'agit du nom qui sera affich&eacute;</p>
  </div>
</div>

<!-- Text input-->
<div class="control-group">
  <label class="control-label" for="link-input-catplace">Place : </label>
  <div class="controls">
    <input id="link-input-catplace" name="link-input-catplace" type="text" placeholder="1" class="input-xlarge" value="'.$category->getPlace().'">
    <p class="help-block">Le num&eacute;ro d\'ordre d\'affichage</p>
  </div>
</div>

<div class="control-group">
    <div class="controls">
      <button type="submit" class="btn">'.$label.'</button>
    </div>
</div>
      		
</fieldset>
</form>';
	
	return $html;
}