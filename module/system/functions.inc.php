<?php


function media_admin_html_table_media($modname,$list){
	$HTML = '<h3>Medias et Categories</h3>';
	// adding button
	$HTML .= '<a class="btn btn-primary" href="'.URLUtils::generateURL($modname, array('action' => 'addmedia','part'=>'media')).'"><i class="fa fa-plus"></i> Ajouter un media</a>  ';
	$HTML .= '<a class="btn btn-primary" href="'.URLUtils::generateURL($modname, array('action' => 'addcat','part'=>'media')).'"><i class="fa fa-plus"></i> Ajouter une categorie</a>  ';
	
	
	$HTML .= '<br><br><div class="tabbable tabs-left"> <!-- Only required for left/right tabs -->
  <ul class="nav nav-tabs">';
	for($i=0 ; $i<count($list) ; $i++){
		$cat = $list[$i];
		$class = "";
		if($i==0){
			$class = 'class="active"';
		}
		$HTML .= '<li '.$class.'><a href="#mediacat'.$cat->getId().'" data-toggle="tab">'.$cat->getName().'</a></li>';
	}
    $HTML .= '</ul>
  <div class="tab-content">';  
    for($i=0 ; $i<count($list) ; $i++){
    	$cat = $list[$i];
    	$class = "";
    	if($i==0){
    		$class = 'active';
    	}
	    $HTML .= '<div class="tab-pane '.$class.'" id="mediacat'.$cat->getId().'">';
    	$HTML .= $cat->getDescription();
    	$HTML .= "<br>Les fichiers sont stock&eacute;s dans le dossier <i>" .DIR_MEDIA . $cat->getDirectory() . '</i><br><br>';
	    $HTML .= '<table class="table table-striped">';
	    $medias = $cat->getMedias();
	    for($j=0 ; $j<count($medias) ; $j++){
	    	$m = $medias[$j];
	    	$HTML .= '<tr>';
	    	$HTML .= '<td>'.$m->getName().'</td>';
	      	$HTML .= '<td>'.$m->getFilename().'</td>';
	      	$HTML .= '<td>'.$m->getAddeddate().'</td>';
	      	$HTML .= '<td>'.basename(DIR_MEDIA) . "/" .$cat->getDirectory(). $m->getFilename().'</td>';
	      	$HTML .= '<td><a href="'.URL.'server.php?module='.$modname.'&part=media&action=getmedia&id='.$m->getId().'" target="_blank" class="btn btn-default"><i class="fa fa-download"></i> </a></td>';
	      	$HTML .= '</tr>';
	    }
	    $HTML .= '</table>';
	    $HTML .= '</div>';
    }
    $HTML .= '</div>
</div>';
	return $HTML;
}



function media_admin_html_mediapicker($list, $id){
		
	$HTML = '<div style="width: 530px;">';
	for($i=0 ; $i<count($list) ; $i++){
		$cat = $list[$i];
		
		//$HTML .= '<div class="tab-pane '.$class.'" id="mediacat'.$cat->getId().'">';
		//$HTML .= $cat->getDescription();
		$HTML .= "<h4>". $cat->getName() . '</h4>';
		$HTML .= '<table class="table table-striped" style="width: 530px;">';
		$medias = $cat->getMedias();
		for($j=0 ; $j<count($medias) ; $j++){
			$m = $medias[$j];
			$HTML .= '<tr>';
			$HTML .= '<td>';
			$HTML .= '<b>Nom : </b> ' . $m->getName();
			$HTML .= '<br clear><b>Fichier :</b> ' . basename(DIR_MEDIA) . "/" .$cat->getDirectory(). $m->getFilename();
			$HTML .= '</td>';
			//$HTML .= '<td>'.$m->getName().'</td>';
			//$HTML .= '<td>'.$m->getFilename().'</td>';
			//$HTML .= '<td>'.$m->getAddeddate().'</td>';
			//$HTML .= '<td>'.basename(DIR_MEDIA) . "/" .$cat->getDirectory(). $m->getFilename().'</td>';
			$HTML .= '<td><a onclick="mediapickerChoose(\''.$id.'\','.$m->getId().',\''.basename(DIR_MEDIA) . "/" .$cat->getDirectory(). $m->getFilename().'\');return false;" href="#" target="_blank" class="btn btn-mediapicker btn-default" id="btn-mediapicker-'.$m->getId().'"><i class="fa fa-ok-circle"></i> </a></td>';
			$HTML .= '</tr>';
		}
		$HTML .= '</table>';
	}
	
		$HTML .= '</div>';
	
	return $HTML;
}




function media_admin_html_media_form($modname, $categories){
	
	$url = URLUtils::generateURL ( $modname, array (
			'part' => 'media',
			'action' => 'addmedia'
	));
	
	$HTML = '<h3>Ajouter un media</h3>';
	$HTML .= '<form class="form-horizontal" method="post" action="'.$url.'" enctype="multipart/form-data">
<fieldset>

<!-- Form Name -->
<legend>Formulaire</legend>

<!-- Text input-->
<div class="control-group">
  <label class="control-label" for="media-input-name">Nom</label>
  <div class="controls">
    <input id="media-input-name" name="media-input-name" type="text" placeholder="nom" class="input-xlarge">
    
  </div>
</div>

<!-- File Button --> 
<div class="control-group">
  <label class="control-label" for="media-input-file">Fichier</label>
  <div class="controls">
    <input id="media-input-file" name="media-input-file" class="input-file" type="file">
  </div>
</div>

<!-- Select Basic -->
<div class="control-group">
  <label class="control-label" for="media-input-category">Categorie</label>
  <div class="controls">
    <select id="media-input-category" name="media-input-category" class="input-xlarge">';
	for($i=0 ; $i<count($categories) ; $i++){
		$c = $categories[$i];
		$HTML .= '<option value="'.$c->getId().'">'.$c->getName().'</option>';
	}
    $HTML .= '</select>
  </div>
</div>
			
		
<div class="control-group">
    <div class="controls">
      <button type="submit" class="btn">Ajouter</button>
    </div>
</div>

</fieldset>
</form>';
	
	return $HTML;
	
}


function media_admin_html_catergory_form($modname, $action){
	if($action == 'add'){
		$label = 'Ajouter';
		$url = URLUtils::generateURL($modname, array('part'=> 'media', 'action' => 'addcat'));
	}else{
		$label = 'Modifier';
		$url = URLUtils::generateURL($modname, array('part'=> 'media', 'action' => 'editcat', 'id' => $post->getId()));
	}
	
	$HTML = '<h3>Ajouter une categorie</h3>';
	$HTML .= '<form class="form-horizontal" method="post" action="'.$url.'">
<fieldset>

<!-- Form Name -->
<legend>Formulaire</legend>
			
<!-- Text input-->
<div class="control-group">
  <label class="control-label" for="media-input-name">Nom</label>
  <div class="controls">
    <input id="media-input-name" name="media-input-name" type="text" placeholder="nom" class="input-xlarge">
    
  </div>
</div>

<!-- Textarea -->
<div class="control-group">
  <label class="control-label" for="media-input-description">Description</label>
  <div class="controls">                     
    <textarea id="media-input-description" name="media-input-description"></textarea>
  </div>
</div>

<!-- Text input-->
<div class="control-group">
  <label class="control-label" for="media-input-directory">Repertoire</label>
  <div class="controls">
    <input id="media-input-directory" name="media-input-directory" type="text" placeholder="dossier" class="input-xlarge">
    <p class="help-block">Ne doit contenir que des lettres, des chiffres, underscore ou tirets</p>
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




function options_admin_html_form($modname,$options){
	$HTML = '<h3>Liste des options du site</h3>';
	$HTML .= '<form class="form-horizontal" method="post" action="'.URLUtils::generateURL($modname, array("part" => "options")).'">
	<fieldset>
	
	<!-- Form Name -->
	<legend>Formulaire</legend>';
	
	foreach($options as $option){
		switch ($option->getType()) {
			case 'boolean':
			case 'integer':
			case 'string':
				$HTML .= '<!-- Text input-->
<div class="control-group">
  <label class="control-label" for="option-input[]">'.$option->getKey().'</label>
  <div class="controls">
    <input id="media-input-directory" name="option-input['.$option->getKey().']" type="text" class="input-xlarge" value="'.$option->getValue().'">
    <p class="help-block">Le type est <i>'.$option->getType().'</i></p>
    <p class="help-block">'.$option->getDescription().'</p>
  </div>
</div>';
				break;
			case 'text':
			case 'json':
				$HTML .= '<!-- Textarea -->
<div class="control-group">
  <label class="control-label" for="option-input[]">'.$option->getKey().'</label>
  <div class="controls">                     
    <textarea id="option-input[]" name="option-input['.$option->getKey().']" style="width:70%;height:150px;">'.$option->getValue().'</textarea>
	<p class="help-block">Le type est <i>'.$option->getType().'</i></p>
  	<p class="help-block">'.$option->getDescription().'</p>
  </div>
</div>';
				break;
			default:
				break;
		}
		
	}
	
	$HTML .= '						
<div class="control-group">
    <div class="controls">
      <button type="submit" class="btn">Modifier</button>
    </div>
</div>
      		
</fieldset>
</form>';
	
	return $HTML;	
}



function module_list($modname, $modules){
	$HTML = '<h3>Liste des modules</h3>';
	
	$HTML .= '<table class="table table-striped tablesorter">';
	$HTML .= '<thead>
		<tr>
			<th>Id</th>
			<th>Nom</th>
			<th>Nom Visible</th>
			<th>Actif</th>
			<th>Dans le menu</th>
			<th><u>Actions</u></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th>Id</th>
			<th>Nom</th>
			<th>Nom Visible</th>
			<th>Actif</th>
			<th>Dans le menu</th>
			<th><u>Actions</u></th>
		</tr>
	</tfoot>';
	
	$HTML .= '<tbody>';
	for ($i = 0; $i < count($modules); $i++) {
		$a = $modules[$i];
		$statut = ($a->getIsActive() == '1' ? '<span class="label label-success">Oui</span>' : '<span class="label label-danger">Non</span>');
		$inMenu = ($a->getInMenu() == '1' ? '<span class="label label-success">Oui</span>' : '<span class="label label-danger">Non</span>');
		$HTML .= '<tr>';
		$HTML .= '<td>'.$a->getId().'</td><td>'.($a->getName()).'</td><td>'.($a->getDisplayedName()).'</td><td>'.$statut.'</td><td>'.$inMenu.'</td>';
		$HTML .= '<td>';
		if(RoleManager::getInstance()->hasCapabilitySession('system-edit-module')){
			$HTML .= '<a href="'.URLUtils::generateURL($modname, array("part"=>"module", "action"=>"edit", "mname"=>$a->getName())).'" class = "btn btn-default"><i class="fa fa-pencil"></i> Editer</a>  ';
		}
		if(RoleManager::getInstance()->hasCapabilitySession('system-edit-role')){
			$HTML .= '<a href="'.URLUtils::generateURL($modname, array("part"=>"module", "action"=>"role", "mname"=>$a->getName())).'" class = "btn btn-default"><i class="fa fa-user"></i> Roles</a> ';
		}
		$HTML .= '</td>';
	
		$HTML .= '</tr>';
	}
	$HTML .= '</tbody></table>';
	
	return $HTML;
	
}



function module_update_form_roles($modname, $moduleName, array $available, array $capabilities, array $roles){
	
	$url = URLUtils::generateURL($modname, array("part" => "module", "action" => "role", "mname" => $moduleName));
	
	$html = '<h3>Roles du module <i>'.$moduleName.'</i></h3>';
	$html .= '<form class="form-horizontal" method="post" action="'.$url.'">
<fieldset>

<!-- Form Name -->
<legend>Formulaire</legend>';
	foreach ($roles as $role){
		$rolename = $role->getRole();
		$capa = $capabilities[$rolename];
		
		$html .= '<!-- Multiple Checkboxes -->
		<div class="control-group">
		  <label class="control-label" for="system-role-checkboxes">Capabilities de '.$rolename.'</label>
		  <div class="controls">';
			
			$i = 0;
			foreach ($available as $cap){
				$checked = "";
				if(in_array($cap, $capa)){
					$checked = "checked";
				}
				$html .= '<label class="checkbox" for="system-role-checkboxes-'.$rolename.'-'.$i.'"><input type="checkbox" name="system-role-checkboxes['.$rolename.'][]" id="system-role-checkboxes-'.$rolename.'-'.$i.'" value="'.$cap.'" '.$checked.'>'.$cap.'</label>';
				$i++;
			}
			
		$html .= '
		  </div>
		</div>';
	}
			
$html .= '<div class="control-group">
    <div class="controls">
      <button type="submit" class="btn">Valider</button>
    </div>
</div>

</fieldset>
</form>';
	
	return $html;
}



function module_update_form($modname, Module $module){

	$url = URLUtils::generateURL($modname, array("part" => "module", "action" => "edit", "mname" => $module->getName()));

	$optionsActive = "";
	if($module->getIsActive()){
		$optionsActive = '<option value="1" selected="selected">true</option><option value="0">false</option>';
	}else{
		$optionsActive = '<option value="1">true</option><option value="0" selected="selected">false</option>';
	}

	$optionsMenu = "";
	if($module->getInMenu()){
		$optionsMenu = '<option value="1" selected="selected">true</option><option value="0">false</option>';
	}else{
		$optionsMenu = '<option value="1">true</option><option value="0" selected="selected">false</option>';
	}

	$html = '<h3>Edition de module</h3>';
	$html .= '<form class="form-horizontal" method="post" action="'.$url.'">
<fieldset>

<!-- Form Name -->
<legend>Formulaire</legend>

<!-- Text input-->
<div class="control-group">
  <label class="control-label" for="module-input-name">Nom</label>
  <div class="controls">
    <input id="module-input-name" name="module-input-name" type="text" placeholder="nom" class="input-xlarge" value="'.$module->getDisplayedName().'">

  </div>
</div>

<!-- Select Basic -->
<div class="control-group">
  <label class="control-label" for="module-input-active">Actif</label>
  <div class="controls">
    <select id="module-input-active" name="module-input-active" class="input-xlarge">
      '.$optionsActive.'
    </select>
  </div>
</div>

<!-- Select Basic -->
<div class="control-group">
  <label class="control-label" for="module-input-inmenu">In Menu</label>
  <div class="controls">
    <select id="module-input-inmenu" name="module-input-inmenu" class="input-xlarge">
      '.$optionsMenu.'
    </select>
  </div>
</div>
		
<div class="control-group">
    <div class="controls">
      <button type="submit" class="btn">Modifier</button>
    </div>
</div>

</fieldset>
</form>';

	return $html;
}



function system_admin_html_table_widget($modname,$list, $mods){
	
		$HTML = '<h3>Liste des Widgets</h3>';
		
		
		//$HTML .= '<a href="'.URLUtils::generateURL($modname, array("part" => "widgets", "action"=>"add")).'" class="btn btn-primary"><i class="fa fa-plus"></i> Ajouter</a>';
		
		
		foreach ($mods as $m){
			$options .= '<option value="'.$m->getId().'">'.$m->getName().'</option>';
		}
		
		$HTML .= '<form class="form-inline" method="GET">
			<a href="'.URLUtils::generateURL($modname, array("part" => "widgets", "action"=>"add")).'" class="btn btn-primary"><i class="fa fa-plus"></i> Ajouter</a>
			| <input type="hidden" name="mod" value="'.$modname.'">
			<input type="hidden" name="part" value="widgets">
			<input type="hidden" name="action" value="place">
  <select id="mid" name="mid">' . $options . '</select>
  <button type="submit" class="btn">Widgets</button>
</form>';
		
	
		$HTML .= '<table class="table table-striped tablesorter">';
		$HTML .= '<thead>
		<tr>
			<th>Id</th>
			<th>Nom</th>
			<th>Dependances (module)</th>
			<th>Actif</th>
			<th>Dans le footer</th>
			<th><u>Actions</u></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th>Id</th>
			<th>Nom</th>
			<th>Dependances (module)</th>
			<th>Actif</th>
			<th>Dans le footer</th>
			<th><u>Actions</u></th>
		</tr>
	</tfoot>';
	
		$HTML .= '<tbody>';
		for ($i = 0; $i < count($list); $i++) {
			$a = $list[$i];
			$statut = ($a->getIsActive() == '1' ? '<span class="label label-success">Oui</span>' : '<span class="label label-danger">Non</span>');
			$inMenu = ($a->getInFooter() == '1' ? '<span class="label label-success">Oui</span>' : '<span class="label label-danger">Non</span>');
			$HTML .= '<tr>';
			$HTML .= '<td>'.$a->getId().'</td><td>'.($a->getName()).'</td><td>'.($a->getModuleName()).'</td><td>'.$statut.'</td><td>'.$inMenu.'</td>';
			$HTML .= '<td><a href="'.URLUtils::generateURL($modname, array("part"=>"widgets", "action"=>"edit","id"=>$a->getId())).'" class = "btn btn-default"><i class="fa fa-pencil"></i> Editer</a> </td>';
			//$HTML .= ' <a href="'.URLUtils::generateURL($modname, array("part"=>"widgets", "action"=>"place","id"=>$a->getId())).'" class = "btn"><i class="fa fa-move"></i> Placer</a></td>';
			$HTML .= '</tr>';
		}
		$HTML .= '</tbody></table>';
	
		return $HTML;
}



function system_admin_add_form_widget($modname, array $modules, array $potentials){

	$options = "";
	foreach ($potentials as $w){
		$options .= '<option value="'.$w.'">'.$w.'</option>';
	}
	
	$optionsModule = '<option value="">Aucune dependance</option>';
	foreach ($modules as $m){
		$optionsModule .= '<option value="'.$m->getId().'">'.$m->getName().'</option>';
	}
	
	
	$url = URLUtils::generateURL($modname, array("part" => "widgets", "action"=>"add"));

	$html = '<form class="form-horizontal" method="post" action="'.$url.'">
<fieldset>

<!-- Form Name -->
<legend>Formulaire</legend>

<!-- Text input-->
<div class="control-group">
  <label class="control-label" for="widget-input-name">Nom</label>
  <div class="controls">
    <input id="widget-input-name" name="widget-input-name" type="text" placeholder="nom" class="input-xlarge" >

  </div>
</div>

<!-- Select Basic -->
<div class="control-group">
  <label class="control-label" for="widget-input-active">Actif</label>
  <div class="controls">
    <select id="widget-input-active" name="widget-input-active" class="input-xlarge">
      	<option value="1">true</option>
    	<option value="0" selected="selected">false</option>
    </select>
  </div>
</div>

<!-- Select Basic -->
<div class="control-group">
  <label class="control-label" for="widget-input-infooter">In Footer</label>
  <div class="controls">
    <select id="widget-input-inmenu" name="widget-input-infooter" class="input-xlarge">
      	<option value="1">true</option>
    	<option value="0" selected="selected">false</option>
    </select>
  </div>
</div>

			
<!-- Select Basic -->
<div class="control-group">
  <label class="control-label" for="widget-input-class">Fichier (Class name)</label>
  <div class="controls">
    <select id="widget-input-class" name="widget-input-class" class="input-xlarge">
      	'.$options.'
    </select>
    <p class="help-block">Le nom de la Class inclue dans le fichier &agrave; inclure du widget.</p>
  </div>
</div>

<!-- Select Basic -->
<div class="control-group">
  <label class="control-label" for="widget-input-module">D&eacute;pendance Module</label>
  <div class="controls">
    <select id="widget-input-module" name="widget-input-module" class="input-xlarge">
      	'.$optionsModule.'
    </select>
    <p class="help-block">Le Widget est d&eacute;pendant d\'un module (requiert des class de model de module).</p>
  </div>
</div>
      			
<div class="control-group">
    <div class="controls">
      <button type="submit" class="btn">Modifier</button>
    </div>
</div>

</fieldset>
</form>';

	return $html;
}




function system_admin_form_widget($modname, Widget $widget){

	$url = URLUtils::generateURL($modname, array("part" => "widgets", "action"=>"edit", "id" => $widget->getId()));

	$optionsActive = "";
	if($widget->getIsActive()){
		$optionsActive = '<option value="1" selected="selected">true</option><option value="0">false</option>';
	}else{
		$optionsActive = '<option value="1">true</option><option value="0" selected="selected">false</option>';
	}

	$optionsMenu = "";
	if($widget->getInFooter()){
		$optionsMenu = '<option value="1" selected="selected">true</option><option value="0">false</option>';
	}else{
		$optionsMenu = '<option value="1">true</option><option value="0" selected="selected">false</option>';
	}

	$html = '<h3>Edition de Widget</h3>';
	$html .= '<form class="form-horizontal" method="post" action="'.$url.'">
<fieldset>

<!-- Form Name -->
<legend>Formulaire</legend>

<!-- Text input-->
<div class="control-group">
  <label class="control-label" for="widget-input-name">Nom</label>
  <div class="controls">
    <input id="widget-input-name" name="widget-input-name" type="text" placeholder="nom" class="input-xlarge" value="'.$widget->getName().'">

  </div>
</div>

<!-- Select Basic -->
<div class="control-group">
  <label class="control-label" for="widget-input-active">Actif</label>
  <div class="controls">
    <select id="widget-input-active" name="widget-input-active" class="input-xlarge">
      '.$optionsActive.'
    </select>
  </div>
</div>

<!-- Select Basic -->
<div class="control-group">
  <label class="control-label" for="widget-input-infooter">In Footer</label>
  <div class="controls">
    <select id="widget-input-inmenu" name="widget-input-infooter" class="input-xlarge">
      '.$optionsMenu.'
    </select>
  </div>
</div>
		
<div class="control-group">
    <div class="controls">
      <button type="submit" class="btn">Modifier</button>
    </div>
</div>

</fieldset>
</form>';

	return $html;
}



function system_admin_form_widget_placement($modname, $mod, $allwidgets, $modwidgets){
	$url = URLUtils::generateURL($modname, array("part" => "widgets", "action"=>"place", "mid" => $mod->getId()));
	
	$html = '<h3>Placement de Widgets pour le module <i>'.$mod->getName().'</i></h3>';
	
	$html .= '<form class="form-horizontal" method="post" action="'.$url.'">
<fieldset>
	
<!-- Form Name -->
<legend>Formulaire</legend>';
	
	foreach ($allwidgets as $wid){
		if(system_utils_is_in_widgetlist($wid, $modwidgets)){
			// place is defined
			$w = system_utils_is_in_widgetlist($wid, $modwidgets);
			$class = ($w->getIsActive() ? "" : "text-error");
			$html .= '<!-- Text input-->
<div class="control-group">
  <label class="control-label" for="widget-input-name"><p class="'.$class.'">'.$w->getName().'</p></label>
  <div class="controls">
    <input id="widget-input-place" name="widget-input-place['.$w->getId().']" type="text" placeholder="chiffre" class="input-xlarge" value="'.$w->getPlace().'">
  </div>
</div>';
		}else{
			// place is null
			$class = ($wid->getIsActive() ? "" : "text-error");
			$html .= '<!-- Text input-->
<div class="control-group">
  <label class="control-label" for="widget-input-name"><p class="'.$class.'">'.$wid->getName().'</p></label>
  <div class="controls">
    <input id="widget-input-place" name="widget-input-place['.$wid->getId().']" type="text" placeholder="chiffre" class="input-xlarge" value="0">
			
  </div>
</div>';
		}
	}
	
$html .= '<div class="control-group">
    <div class="controls">
      <button type="submit" class="btn">Modifier</button>
    </div>
</div>
	
</fieldset>
</form>';
	return $html;
	
}


function system_utils_is_in_widgetlist($widget, $list){
	foreach ($list as $w){
		if($widget->getId() == $w->getId()){
			return $w;
		}
	}
	return false;
}


 
