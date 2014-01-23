<?php


/**
 * transform a list of Event Object into key-array, adding the field event_url containing the url to the event page
 * @param array $events : the list of Event Objects
 * @param string $modname : the name of the module
 * @return array $array : the array of array event
 */
function echogito_event_array_object_to_array(array $events, $modname){
	$array = system_array_obj_to_data_array($events);
	for($i=0 ; $i<count($array);$i++){
		$array[$i]['event_url'] = URLUtils::generateURL($modname, array("p" => "event", "id" => $array[$i]['id']));
	}
	return $array;
}

/**
 * sort an array of Event Objects by day. It returns a key-array (key are the day names) and the value are array of Event Objects.
 * @param array $events : array of Event Objects
 * @return array $week : a key-array (key are the day names) and the value are array of Event Objects.
 */
function echogito_sort_by_day(array $events){
	$week = array();
	$week['Lundi'] = array();
	$week['Mardi'] = array();
	$week['Mercredi'] = array();
	$week['Jeudi'] = array();
	$week['Vendredi'] = array();
	$week['Samedi'] = array();
	$week['Dimanche'] = array();
	
	foreach ($events as $event){
		$date = $event->getStart_time();
		$date_array = date_parse($date);
		$day = date("l", mktime(0, 0, 0, $date_array['month'], $date_array['day'], $date_array['year']));
		$week[echogito_translate_day_name($day)][] = $event;
	}
	return $week;
}

/**
 * 
 * @param array $events : array of Object. It must be order by 'start_time' ASC
 * @return Ambigous <multitype:multitype: , unknown>
 */
function echogito_sort_by_month(array $events){
	$list = array();
	foreach ($events as $event){
		$date = $event->getStart_time();
		$date_array = date_parse($date);
		$key = date("Y-m", mktime(0, 0, 0, $date_array['month'], $date_array['day'], $date_array['year']));
		if(!array_key_exists($key,$list)){
			$list[$key] = array();
		}
		$list[$key][] = $event;
	}
	//ksort($list); not sure :s
	return $list;
}


/**
 * translate the name of the day in french
 * @param string $day
 * @return string : the name of the day in french
 */
function echogito_translate_day_name($day){
	$day = ucfirst(strtolower($day));
	$list = array();
	$list['Monday'] = 'Lundi';
	$list['Tuesday'] = 'Mardi';
	$list['Wednesday'] = 'Mercredi';
	$list['Thursday'] = 'Jeudi';
	$list['Friday'] = 'Vendredi';
	$list['Saturday'] = 'Samedi';
	$list['Sunday'] = 'Dimanche';
	return $list[$day];
}

/**
 * translate the name of the month in french
 * @param string $month
 * @return string : the name of the month in french
 */
function echogito_translate_month_name($month){
	$month = ucfirst(strtolower($month));
	$list = array();
	$list['January'] = 'Janvier';
	$list['February'] = 'F&eacute;vrier';
	$list['March'] = 'Mars';
	$list['April'] = 'Avril';
	$list['May'] = 'Mai';
	$list['June'] = 'Juin';
	$list['July'] = 'Juillet';
	$list['August'] = 'Aout';
	$list['September'] = 'Septembre';
	$list['October'] = 'Octobre';
	$list['November'] = 'Novembre';
	$list['December'] = 'D&eacute;cembre';
	return $list[$month];
}

/**
 * built the html code of a given event to put in the Calendar (media style)
 * @param string $modname : the name of the module
 * @param Event $event : the Event to display
 * @param string $class : the css class the higher div must have
 * @return string : the html code
 */
function echogito_html_page_event($modname, $event){
	$html = '<div class="col-lg12 col-md-12 col-sm-12 col-xs-12">';
	$html .= '<p class="lead">' . $event->getName() . '</p>';
	$html .= '<p>';
	$dt = preg_split("/[ ]+/",$event->getStart_time());
	$html .= '<i class="fa fa-globe"></i> <strong>Lieu :</strong> '.$event->getLocation();
	$html .= ' |  <i class="fa fa-calendar"></i> <strong>Date :</strong> le '.ConversionUtils::dateToDateFr($dt[0],"/");
	$html .= ' |  <i class="fa fa-clock-o"></i> <strong>Heure :</strong> &agrave; '.ConversionUtils::timeToTimefr($dt[1]);
	$html .= ' |  <i class="fa fa-user"></i> <strong>Organis&eacute; par </strong> '.$event->getOrganizer();
	if($event->getFacebookid()){
		$html .= ' |  <i class="fa fa-facebook-square"></i> <a href="https://www.facebook.com/events/'.$event->getFacebookid().'"  target="_blank">Lien Facebook</a>';
	}
	if($event->getCategoryid()){
		$html .= ' | <span style="color:'.$event->getCategorycolor().'"><i class="fa fa-folder-open"></i> '.$event->getCategoryname().'</span>';
	}
	$html .= '</p>';
	$html .= '<p>'.$event->getDescription().'</p>';
	$html .= '</div>';
	return $html;
}


/**
 * built the html code of a given event to put in the Calendar (media style)
 * @param string $modname : the name of the module
 * @param Event $event : the Event to display
 * @param string $class : the css class the higher div must have
 * @return string : the html code
 */
function echogito_html_media_event($modname, Event $event, $class){
	$html = '<div class="'.$class.'">';
	$html .= '<p class="lead">' . $event->getName() . '</p>';
	$html .= '<p >'.substr(($event->getDescription()),0,550).' ...</p>';
	$html .= '<br><a class="btn btn-primary btn-sm pull-right" href="'.URLUtils::generateURL($modname, array("p"=>"event", "id" => $event->getId())).'">&#187; Lire plus</a>';
	$html .= '<ul class="list-unstyled">';
	$html .= '<li><i class="fa fa-user"></i> <strong>Organis&eacute; par </strong> '.$event->getOrganizer().'</li>';
	$html .= '<li><i class="fa fa-clock-o"></i> <strong>Heure :</strong> Le '.ConversionUtils::timestampToDatetime($event->getStart_time(),"/").'</li>';
	$html .= '<li><i class="fa fa-globe"></i> <strong>Lieu :</strong> '.$event->getLocation().'</li>';
	if($event->getFacebookid()){
		$html .= '<li><i class="fa fa-facebook-square"></i> <a href="https://www.facebook.com/events/'.$event->getFacebookid().'" target="_blank">Lien Facebook</a></li>';
	}
	if($event->getCategoryid()){
		$html .= '<li><span style="color:'.$event->getCategorycolor().'"><i class="fa fa-folder-open"></i> '.$event->getCategoryname().'</span></li>';
	}
	$html .= '</ul>';
	$html .= '<div class="clearfix"></div>';
	$html .= '</div>';
	return $html;
}


/**
 * built the facebook form to add an event
 * @param string $modulename : the name of hte current Module
 * @return string : the html code
 */
function echogito_html_facebook_form($modulename){
	$html = '<form class="form-horizontal"  method="POST" action="'.URLUtils::generateURL($modulename, array("action"=>"submit")).'">
<fieldset>

<!-- Form Name -->
<legend>Formulaire</legend>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-6 control-label" for="echogito-input-facebook">URL</label>  
  <div class="col-md-6">
  <input id="echogito-input-facebook" name="echogito-input-facebook" type="text" placeholder="http://www.facebook.com/events/123456789" class="form-control input-md" required>
  <span class="help-block">Introduisez le lien de l\'&eacute;v&eacute;nement Facebook que vous souhaitez ajouter au calendrier. Nous le validerons le plus rapidement possible pour qu\'il soit affich&eacute; publiquement.</span>  
  </div>
</div>

<div class="control-group">
    <div class="controls">
      <button type="submit" class="btn">Soumettre</button>
    </div>
</div>
			
</fieldset>
</form>';
	return $html;
}


/**
 * built the traditionnal form to submit an event
 * @param string $modulename : the name of hte current Module
 * @param iTemplate $template : the tempalte to add the css and js additionnal code
 * @return string : the html code
 */
function echogito_html_traditionnal_form($modulename, $template){
	$html = '<form class="form-horizontal" method="POST" action="'.URLUtils::generateURL($modulename, array("action"=>"submit")).'">
<fieldset>

<!-- Form Name -->
<legend>Formulaire</legend>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-6 control-label" for="echogito-input-title">Titre</label>  
  <div class="col-md-6">
  <input id="echogito-input-title" name="echogito-input-title" type="text" placeholder="Organisateur" class="form-control input-md" required="">
    
  </div>
</div>

<!-- Textarea -->
<div class="form-group">
  <label class="col-md-6 control-label" for="echogito-input-description">Description</label>
  <div class="col-md-6">                     
    <textarea class="form-control" id="echogito-input-description" name="echogito-input-description"></textarea>
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-6 control-label" for="echogito-input-date">Date</label>  
  <div class="col-md-6">
  '.system_load_plugin(array('bootstrap-datetimepicker' => array("template"=> $template, "id" => "echogito-div-date", "name" => "echogito-input-date", "format" => "YYYY-MM-DD hh:mm:ss", "withTime" => true, "withDate" => true))).'
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-6 control-label" for="echogito-input-location">Location</label>  
  <div class="col-md-6">
  <input id="echogito-input-location" name="echogito-input-location" type="text" placeholder="location" class="form-control input-md" required="">
    
  </div>
</div>
  		
<!-- Text input-->
<div class="form-group">
  <label class="col-md-6 control-label" for="echogito-input-organizer">Organisateur</label>  
  <div class="col-md-6">
  <input id="echogito-input-organizer" name="echogito-input-organizer" type="text" placeholder="titre" class="form-control input-md" required="">
    
  </div>
</div>
  		
<div class="control-group">
    <div class="controls">
      <button type="submit" class="btn">Soumettre</button>
    </div>
</div>

</fieldset>
</form>';
	
	return $html;
}

/**
 * built the html code for th doube form to submit event
 * @param string $modname : the name of the current module
 * @param iTemplate $template : the template
 * @return string : the html code
 */
function echogito_html_double_form($modname, $template){
	
	$html .= '<a href="#" class="btn btn-default btn-sm pull-right" data-toggle="modal" data-target="#echogito-doc-modal">
  <i class="fa fa-question-circle"></i> Comment ca marche ?
</a><br><br>';
$html .= '<div class="clearfix"></div>';
$html .= '<!-- Modal -->
<div class="modal fade" id="echogito-doc-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialogBAD">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Documentation</h4>
      </div>
      <div class="modal-body">
        En tant que Visiteur, vous avez le droit de soumettre des &eacute;v&eacute;nements pour en faire la publicit&eacute; via cet agenda sur notre site. Deux facons de soumettre un &eacute;v&eacute;nement : 
		<ul>
			<li>Soit grace &agrave; un copier/coller de l\'adresse de l\'event Facebook dans le premier formulaire. Nous prenons ensuite les informations de Facebook pour les mettre sur le Webkot.</li>
			<li>Soit en introduisant vous meme les informations, via le second formulaire.</li>
		</ul>
		Une fois votre &eacute;v&eacute;nement soumis, nous le validerons le plus vite possible pour qu\'il soit publiquement affich&eacute;.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
       </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->';
	
	$html .= '<div class="panel-group" id="accordion">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
		<i class="fa fa-facebook-square fa-fw"></i>
        <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
           Importer depuis Facebook
        </a>
      </h4>
    </div>
    <div id="collapseOne" class="panel-collapse collapse in">
      <div class="panel-body">
        '.echogito_html_facebook_form($modname).'
	  </div>
    </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <i class="fa fa-edit fa-fw"></i>
        <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
           Formulaire traditionnel
        </a>
      </h4>
    </div>
    <div id="collapseTwo" class="panel-collapse collapse">
      <div class="panel-body">
        '.echogito_html_traditionnal_form($modname,$template).'
	  </div>
    </div>
  </div>
</div>';
	return $html;
}


/**
 * built the html code for the table of event, with the action buttons
 * @param string $modname : the name of the module
 * @param array $events : the list of Event Objects
 * @return string : the html code
 */
function echogito_admin_html_list_events($modname, array $events){

	// adding button
	$HTML = '<a class="btn btn-primary" href="'.URLUtils::generateURL($modname, array('part'=>'event','action' => 'add')).'"><i class="fa fa-plus "></i> Ajouter</a>';
	
	$HTML .= '<table class="table table-striped table-hover tablesorter">';
	$HTML .= '<thead>
		<tr>
			<th>Id</th>
			<th>Nom</th>
			<th>Date</th>
			<th>Lieu</th>
			<th>Cat&eacute;gorie</th>
			<th>Approuv&eacute;</th>
			<th><u>Actions</u></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th>Id</th>
			<th>Nom</th>
			<th>Date</th>
			<th>Lieu</th>
			<th>Cat&eacute;gorie</th>
			<th>Approuv&eacute;</th>
			<th><u>Actions</u></th>
		</tr>
	</tfoot>';
	
	$HTML .= '<tbody>';
	for ($i = 0; $i < count($events); $i++) {
		$event = $events[$i];
		$HTML .= '<tr>';
		$HTML .= '<td>'.$event->getId().'</td>';
		$HTML.= '<td>'.$event->getName().'</td>';
		$HTML .= '<td>'.$event->getStart_time().'</td>';
		$HTML .= '<td>'.$event->getLocation().'</td>';
		$HTML .= '<td>'.$event->getCategoryname().'</td>';
		$statut = ($event->getIsapproved() == '1' ? '<span class="label label-success">Oui</span>' : '<span class="label label-danger">Non</span>');
		
		$HTML .= '<td>'.$statut.'</td>';	
		
		$HTML .= '<td>';
		$HTML .= '<div class="btn-group">
		    <a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#">Actions <span class="caret"></span></a>
		    <ul class="dropdown-menu">';
		    	$HTML .= '<li><a href="'.URLUtils::generateURL($modname, array('part'=>'event', 'action' => 'edit', 'id' => $event->getId())).'"><i class="fa fa-pencil"></i> Editer</a></li>';
		    	$HTML .= '<li><a href="'.URLUtils::generateURL($modname, array('part'=>'event', 'action' => 'delete', 'id' => $event->getId())).'" onclick=\'return(confirm("Etes vous certain de vouloir supprimer cet Event ?"));\'"><i class="fa fa-trash-o"></i> Supprimer</a></li>';
				if($event->getIsapproved() == '0'){
					$HTML .= '<li><a href="'.URLUtils::generateURL($modname, array('part'=>'event', 'p' => 'approve', 'id' => $event->getId())).'"><i class="fa fa-check-square"></i> Approuver</a></li>';
				}
		$HTML .= '</ul>
		    </div>';
		$HTML .= '</td>';
		$HTML .= '</tr>';
	}
	$HTML .= '</tbody></table>';
	return $HTML;
}



/**
 * built the html code for the table of event, with the action buttons
 * @param string $modname : the name of the module
 * @param array $categories : the list of EventCategories Objects
 * @return string : the html code
 */
function echogito_admin_html_list_category($modname, array $categories){

	// adding button
	$HTML = '<a class="btn btn-primary" href="'.URLUtils::generateURL($modname, array("part"=>"category",'action' => 'add')).'"><i class="fa fa-plus "></i> Ajouter</a>';

	$HTML .= '<table class="table table-striped table-hover tablesorter">';
	$HTML .= '<thead>
		<tr>
			<th>Id</th>
			<th>Nom</th>
			<th>Description</th>
			<th>Couleur</th>
			<th><u>Actions</u></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th>Id</th>
			<th>Nom</th>
			<th>Description</th>
			<th>Couleur</th>
			<th><u>Actions</u></th>
		</tr>
	</tfoot>';

	$HTML .= '<tbody>';
	for ($i = 0; $i < count($categories); $i++) {
		$event = $categories[$i];
		$HTML .= '<tr>';
		$HTML .= '<td>'.$event->getId().'</td>';
		$HTML.= '<td>'.$event->getName().'</td>';
		$HTML .= '<td>'.$event->getDescription().'</td>';
		$HTML .= '<td>'.$event->getColor().'</td>';
		
		$HTML .= '<td>';
		$HTML .= '<div class="btn-group">
		    <a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#">Actions <span class="caret"></span></a>
		    <ul class="dropdown-menu">';
		$HTML .= '<li><a href="'.URLUtils::generateURL($modname, array('part'=>'category', 'action' => 'edit', 'id' => $event->getId())).'"><i class="fa fa-pencil"></i> Editer</a></li>';
		$HTML .= '<li><a href="'.URLUtils::generateURL($modname, array('part'=>'category', 'action' => 'delete', 'id' => $event->getId())).'" onclick=\'return(confirm("Etes vous certain de vouloir supprimer cet categorie ?"));\'"><i class="fa fa-trash-o"></i> Supprimer</a></li>';
		$HTML .= '</ul>
		    </div>';
		$HTML .= '</td>';
		$HTML .= '</tr>';
	}
	$HTML .= '</tbody></table>';
	return $HTML;
}



/**
 * built the traditionnal form to add an event
 * @param string $modulename : the name of hte current Module
 * @param iTemplate $template : the template to add the css and js additionnal code
 * @return string : the html code
 */
function echogito_admin_html_form($action, $modulename, $template, Event $event, array $categories){
	if($action == 'add'){
		$label = 'Ajouter';
		$url = URLUtils::generateURL($modulename, array('part'=>'event', 'action'=>$action));
		
		$options = echogito_html_category_options($categories);
		
	}else{
		$label = 'Envoyer';
		$url = URLUtils::generateURL($modulename, array('part'=>'event', 'action'=>$action, 'id'=>$event->getId()));
		
		$options = echogito_html_category_options($categories, $event->getCategoryid());
	}
	
	
	
	$html = '<form class="form-horizontal" method="POST" action="'.$url.'">
<fieldset>

<!-- Form Name -->
<legend>Formulaire</legend>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-6 control-label" for="echogito-input-title">Titre</label>
  <div class="col-md-6">
  <input id="echogito-input-title" name="echogito-input-title" type="text" placeholder="titre" class="form-control input-md" required="" value="'.$event->getName().'">
  </div>
</div>

<!-- Textarea -->
<div class="form-group">
  <label class="col-md-6 control-label" for="echogito-input-description">Description</label>
  <div class="col-md-6">
    <textarea class="form-control" id="echogito-input-description" name="echogito-input-description">'.$event->getDescription().'</textarea>
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-6 control-label" for="echogito-input-date">Date</label>
  <div class="col-md-6">
  '.system_load_plugin(array('bootstrap-datetimepicker' => array("template"=> $template, "id" => "echogito-div-date", "name" => "echogito-input-date", "format" => "YYYY-MM-DD hh:mm:ss", "withTime" => true, "withDate" => true, "value" => $event->getStart_time()))).'
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-6 control-label" for="echogito-input-location">Location</label>
  <div class="col-md-6">
  <input id="echogito-input-location" name="echogito-input-location" type="text" placeholder="location" class="form-control input-md" required="" value="'.$event->getLocation().'">

  </div>
</div>
  		
<!-- Text input-->
<div class="form-group">
  <label class="col-md-6 control-label" for="echogito-input-organizer">Organisateur</label>  
  <div class="col-md-6">
  <input id="echogito-input-organizer" name="echogito-input-organizer" type="text" placeholder="Organisateur" class="form-control input-md" required="" value="'.$event->getOrganizer().'">
  </div>
</div>
  		
<!-- Text input-->
<div class="form-group">
  <label class="col-md-6 control-label" for="echogito-input-facebookid">Facebookid</label>
  <div class="col-md-6">
  <input id="echogito-input-facebookid" name="echogito-input-facebookid" type="text" placeholder="123456789" class="form-control input-md" value="'.$event->getFacebookid().'">
  </div>
</div>
  		

<!-- Select Basic -->
'.$options.'
  		
<div class="control-group">
    <div class="controls">
      <button type="submit" class="btn">'.$label.'</button>
    </div>
</div>

</fieldset>
</form>';

	return $html;
}



/**
 * built the traditionnal form to add an EventCategory
 * @param string $modulename : the name of hte current Module
 * @param iTemplate $template : the template to add the css and js additionnal code
 * @return string : the html code
 */
function echogito_admin_html_form_category($action, $modulename, $template, EventCategory $category){
	if($action == 'add'){
		$label = 'Ajouter';
		$url = URLUtils::generateURL($modulename, array('part'=>'category', 'action'=>$action));
	}else{
		$label = 'Envoyer';
		$url = URLUtils::generateURL($modulename, array('part'=>'category', 'action'=>$action, 'id'=>$category->getId()));
	}
	$html = '<form class="form-horizontal" method="POST" action="'.$url.'">
<fieldset>

<!-- Form Name -->
<legend>Formulaire</legend>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="echogito-input-name">Nom :</label>  
  <div class="col-md-5">
  <input id="echogito-input-name" name="echogito-input-name" type="text" placeholder="name" class="form-control input-md" required="" value="'.$category->getName().'">
    
  </div>
</div>

<!-- Textarea -->
<div class="form-group">
  <label class="col-md-4 control-label" for="echogito-input-description">Description : </label>
  <div class="col-md-4">                     
    <textarea class="form-control" id="echogito-input-description" name="echogito-input-description">'.$category->getDescription().'</textarea>
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="echogito-input-color">Couleur :</label>  
  <div class="col-md-5">
  <input id="echogito-input-color" name="echogito-input-color" type="text" placeholder="" class="form-control input-md" required="" value="'.$category->getColor().'">
  <span class="help-block">Introduisez une couleur HTML ("red" or "#000")</span>  
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

/**
 * built the form to attribute a category to an event
 * @param string $modulename : the name of hte current Module
 * @return string : the html code
 */
function echogito_admin_html_form_approve_category($modulename, $categories, $eventid){
	$html = '<form class="form-horizontal" method="POST" action="'.URLUtils::generateURL($modulename, array("action"=>"approve")).'">
<fieldset>

<!-- Form Name -->
<legend>Formulaire</legend>
    <!-- Text input-->
	<div class="form-group">
		<label class="col-md-4 control-label" for="echogito-input-id">Identifiant</label>  			
		<div class="col-md-4">
			<input id="echogito-input-id" name="echogito-input-id" type="text" placeholder="" class="form-control input-md" value="'.$eventid.'" readonly="readonly">
		</div>			    
	</div>
'. echogito_html_category_options($categories).'
		
<div class="control-group">
    <div class="controls">
      <button type="submit" class="btn">Envoyer</button>
    </div>
</div>

</fieldset>
</form>';

	return $html;
}

/**
 * built the list of Categories for the form
 * @param array $categories : the categories
 * @param int $selectedid : the actual selected category
 * @return $html : the html code of the select html object
 */
function echogito_html_category_options(array $categories, $selectedid = 0){
	if(!empty($categories)){
		$options = "";
		foreach ($categories as $cat){
			if($selectedid == $cat->getId()){
				$options .= '<option value="'.$cat->getId().'" selected="selected">'.$cat->getName().'</option>';
			}else{
				$options .= '<option value="'.$cat->getId().'">'.$cat->getName().'</option>';
			}
		}
	}else{
		$options .= '<option value="" selected="selected">NULL</option>';
	}
	$html = '<div class="form-group">
  <label class="col-md-4 control-label" for="echogito-input-categoryid">Cat&eacute;gorie</label>
  <div class="col-md-5">
    <select id="echogito-input-categoryid" name="echogito-input-categoryid" class="form-control">
      '.$options.'
    </select>
  </div>
</div>';
	return $html;
}


/**
 * built the javascript code (with tag or not) for the overlay page
 * @param string $modaltitle : the title of the modal (overlay)
 * @param string $modulename : the name of the current module
 * @param boolean $withtag : true if tags are required, false otherwise
 * @return string : the js code
 */
function echogito_js_remote_call($modulename, $withtag = true){
	$js = "";
	if($withtag){
		$js .= "<script>";
	}
	$js .= "$( document ).ready(function() {
					$(document).on('click','.".ECHOGITO_JS_CLASS_CALL_ANCHOR."',function(e)  {
					    var href = ($(this).attr('href'));
						echogito_remote_call('".URLUtils::builtServerUrl($modulename, array())."', href);
						e.preventDefault();
					});
				});";
	if($withtag){
		$js .= "</script>";
	}
	return $js;
}
