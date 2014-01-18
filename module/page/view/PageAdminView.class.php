<?php


class PageAdminView extends AdminView implements iAdminView{
	
	
	public function __construct(Template $template, Module $module){
		parent::setTemplate($template);
		parent::setModule($module);
	}
	
	public function configureTemplate(){
		
	}
	
	
	/**
	 * built the content (html code) of the list of Pages
	 * @param array $list : array of Page Objects
	 */
	public function pageListPage($list, Message $message){
		$content = '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
		
		$content .= '<h3>La liste des Pages</h3>';
		// adding button
		$content .= '<a class="btn btn-primary" href="'.URLUtils::generateURL($this->getModule()->getName(), array('action' => 'add')).'"><i class="fa fa-plus"></i> Ajouter</a><br>';

		if(!$message->isEmpty()){
			$content .= '<br><br>'.$message;
		}
		
		$content .= '<table class="table table-striped table-hover tablesorter">';
		$content .= '<thead>
		<tr>
			<th>Id</th>
			<th>Titre</th>
			<th>Actif</th>
			<th>URL</th>
			<th>File</th>
			<th><u>Actions</u></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th>Id</th>
			<th>Titre</th>
			<th>Actif</th>
			<th>URL</th>
			<th>File</th>
			<th><u>Actions</u></th>
		</tr>
	</tfoot>';	
		$content .= '<tbody>';
		for ($i = 0; $i < count($list); $i++) {
			$page = $list[$i];
			$statut = ($page->getIsactive() == '1' ? '<span class="label label-success">Oui</span>' : '<span class="label label-danger">Non</span>');
			$content .= '<tr>';
			$content .= '<td>'.$page->getId().'</td><td>'.$page->getTitle().'</td><td>'.$statut.'</td><td><a href="'.URL.URLUtils::generateURL($this->getModule()->getName(),array("id"=>$page->getId())).'">'.URLUtils::generateURL($this->getModule()->getName(),array("id"=>$page->getId())).'</a></td><td>'.$page->getFile().'</td><td>';
			$content .= '<div class="btn-group">
    <a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#">Actions <span class="caret"></span></a>
    <ul class="dropdown-menu">
    	<li><a href="'.URLUtils::generateURL($this->getModule()->getName(), array('action' => 'edit', 'id' => $page->getId())).'"><i class="fa fa-pencil"></i> Editer</a></li>
    	<li><a href="'.URLUtils::generateURL($this->getModule()->getName(), array('action' => 'delete', 'id' => $page->getId())).'" onclick=\'return(confirm("Etes vous certain de vouloir supprimer cette page ?"));\'"><i class="fa fa-trash-o"></i> Supprimer</a></li>
    </ul>
    </div>';
			$content .= '</td>';
			$content .= '</tr>';
		}
		$content .= '</tbody></table>';
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		
		$t = $this->getTemplate();
		$t->setContent($content);
	}
	
	
	
	
	public function pageFormPage($action, Message $message, $page){
		// param
		if($action == 'add'){
			$label = 'Ajouter';
			$url = URLUtils::generateURL($this->getModule()->getName(), array('action' => 'add'));
		}else{
			$label = 'Modifier';
			$url = URLUtils::generateURL($this->getModule()->getName(), array('action' => 'edit', 'id' => $page->getId()));
		}
		
		//built html code
		$content = '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
		
		$content .= '<h3>'.$label.' une page</h3>';	
		$content .= $message;
		$content .= '<form class="form-horizontal" method="post" action="'. $url .'">
<fieldset>

<!-- Form Name -->
<legend>Formulaire</legend>

<!-- Text input-->
<div class="control-group">
  <label class="control-label" for="page-input-id">Id</label>
  <div class="controls">
    <input id="page-input-id" name="page-input-id" type="text" placeholder="id" class="input-xlarge" value="'.$page->getId().'">
    <p class="help-block">La variable qui sera affich&eacute;e dans l\'URL</p>
  </div>
</div>

<!-- Text input-->
<div class="control-group">
  <label class="control-label" for="page-input-title">Titre</label>
  <div class="controls">
    <input id="page-input-title" name="page-input-title" type="text" placeholder="titre" class="input-xlarge" value="'.$page->getTitle().'">
    <p class="help-block">Le titre de la Page</p>
  </div>
</div>

<!-- Textarea -->
<div class="control-group">
  <label class="control-label" for="page-input-content">Contenu</label>
  <div class="controls">                     
    <textarea id="page-input-content" name="page-input-content" class="input-xxlarge bootstrap-tinymce">'.$page->getContent().'</textarea>
  </div>
</div>

<!-- Text input-->
<div class="control-group">
  <label class="control-label" for="page-input-file">Fichier</label>
  <div class="controls">
    <input id="page-input-file" name="page-input-file" type="text" placeholder="filename" class="input-xlarge" value="'.$page->getFile().'">
    <p class="help-block">Le nom du fichier associ&eacute; &agrave; la page (facultatif)</p>
  </div>
</div>

 <!-- Select Basic -->
<div class="control-group">
  <label class="control-label" for="page-input-active">Statut</label>
  <div class="controls">
    <select id="page-input-active" name="page-input-active" class="input-xlarge">';
	
	if($page->getIsactive()){
		$content .= '<option value="true" selected="selected">Actif</option>';
		$content .= '<option value="false">Inactif</option>';
	}else{
		$content .= '<option value="true">Actif</option>';
		$content .= '<option value="false" selected="selected">Inactif</option>';
	}
    $content .= '</select>
  </div>
</div>
    		
<div class="control-group">
    <div class="controls">
      <button type="submit" class="btn">'.$label.'</button>
    </div>
</div>
</fieldset>
</form>';
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		
		// set the param
		$t = $this->getTemplate();
		$t->setContent($content);
		system_load_plugin(array('bootstrap-tinymce' => array("template"=> $t, "selector" => ".bootstrap-tinymce")));
	}
	
	
	
	
}