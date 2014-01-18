<?php
/**
 * Display the array of Post Object in an HTML table
 * @param array $list : list of BlogPost objects
 * @param string $modname : the name of the current module
 * @return $html : the html code
 */
function blog_admin_html_table_post_list($list, $modname){
	
	$HTML = '<h3>Les Posts</h3>';

	// adding button
	$HTML .= '<a class="btn btn-primary" href="'.URLUtils::generateURL($modname, array('action' => 'add')).'"><i class="fa fa-plus "></i> Ajouter</a>';

	//$HTML .= toStringAlertInfos("Ce que vous mettez � jour sera directement visible sur le site. Donc faites gaffe aux fautes d'orthographe !");


	$HTML .= '<table class="table table-striped table-hover tablesorter">';
	$HTML .= '<thead>
		<tr>
			<th>Id</th>
			<th>Titre</th>
			<th>Date</th>
			<th>Auteur</th>
			<th><u>Actions</u></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th>Id</th>
			<th>Titre</th>
			<th>Date</th>
			<th>Auteur</th>
			<th><u>Actions</u></th>
		</tr>
	</tfoot>';

	$HTML .= '<tbody>';
	for ($i = 0; $i < count($list); $i++) {
		$u = $list[$i];
		$HTML .= '<tr>';
		$HTML .= '<td>'.$u->getId().'</td><td>'.$u->getTitle().'</td><td>'.$u->getDate().'</td><td>'.$u->getAuthor().'</td>'.
				'<td><!--<a href="#"><img src="img/icons/b_edit.png" alt="M" title"modify" class="img_button"></a><a href="#" onclick=\'return(confirm("Etes vous certain de vouloir supprimer ce post ?"));\'"><img src="img/icons/b_delete.png" alt="M" title"delete" class="img_button"></a></td>-->';
		$HTML .= '<div class="btn-group">  
    <a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#">Actions <span class="caret"></span></a>
    <ul class="dropdown-menu">
    	<li><a href="'.URLUtils::generateURL($modname, array('action' => 'edit', 'id' => $u->getId())).'"><i class="fa fa-pencil"></i> Editer</a></li>
    	<li><a href="'.URLUtils::generateURL($modname, array('action' => 'delete', 'id' => $u->getId())).'" onclick=\'return(confirm("Etes vous certain de vouloir supprimer ce post ?"));\'"><i class="fa fa-trash-o"></i> Supprimer</a></li>
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
 * @param BlogPost $post
 */
function blog_admin_form_post($action, $modname, $post){
	if($action == 'add'){
		$label = 'Ajouter';
		$url = URLUtils::generateURL($modname, array('action' => 'add'));
	}else{
		$label = 'Modifier';
		$url = URLUtils::generateURL($modname, array('action' => 'edit', 'id' => $post->getId()));
	}
	
	$HTML = '<h3>'.$label.' un Post</h3>';
	
	$HTML .= '<form class="form-horizontal" method="post" action="'. $url .'">
<fieldset>

<!-- Form Name -->
<legend>Formulaire</legend>

<!-- Text input-->
<div class="control-group">
  <label class="control-label" for="blog-input-title">Titre :</label>
  <div class="controls">
    <input id="blog-input-title" name="blog-input-title" type="text" placeholder="titre" class="input-medium" required="" value="'.$post->getTitle().'">
  </div>
</div>

<!-- Textarea -->
<div class="control-group">
  <label class="control-label" for="blog-input-content">Article :</label>
  <div class="controls">                     
    <textarea id="blog-input-content" name="blog-input-content" class="input-xxlarge bootstrap-tinymce" >'.$post->getContent().'</textarea>
  </div>
</div>

<!-- Text input-->
<div class="control-group">
  <label class="control-label" for="blog-input-author">Auteur :</label>
  <div class="controls">
    <input id="blog-input-author" name="blog-input-author" type="text" placeholder="" class="input-medium" readonly="readonly" value="'.$post->getAuthor().'">
  </div>
</div>

<!-- Text input-->
<div class="control-group">
  <label class="control-label" for="blog-input-author">Id :</label>
  <div class="controls">
    <input id="blog-input-id" name="blog-input-id" type="text" placeholder="" class="input-medium" readonly="readonly" value="'.$post->getId().'">
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
