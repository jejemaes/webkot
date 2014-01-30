<?php


class ActivityAdminView extends AdminView implements iAdminView{
	
	
	public function __construct(Template $template, Module $module){
		parent::setTemplate($template);
		parent::setModule($module);
		$this->configureTemplate();
	}
	
	public function configureTemplate(){
		$this->getTemplate()->addJSHeader('<script type="text/javascript" src="'.DIR_MODULE . $this->getModule()->getLocation() . 'view/js/admin-script.js"></script>');
		//system_load_plugin(array('tablesorter' => array("template" => $this->getTemplate())));
	}
	
	
	
	public function pageListActivity(Message $message, array $list, $count = 0, $desc = 0, $page = 0){
		$content = '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';	

		$content .= '<h3>Liste des activit&eacute;s</h3>';
		
		// adding button
		$content .= '<a class="btn btn-primary" href="'.URLUtils::generateURL($this->getModule()->getName(), array('action' => 'add')).'"><i class="fa fa-plus"></i> Ajouter</a>  ';
		
		$content .= '<!-- Single button -->
<div class="btn-group">
  <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
    Actions <span class="caret"></span>
  </button>
  <ul class="dropdown-menu" role="menu">
    <li><a href="#" onClick="activityClearCache()"><i class="fa fa-warning"></i> Clear cache</a></li>
    <li><a href="#"><i class="fa fa-list-alt"></i> Publication Log</a></li>
    <li><a href="#" onclick="activityGetCsv(\''.URLUtils::builtServerUrl($this->getModule()->getName(), array('action' => 'getcsv')).'\')"><i class="fa fa-download"></i> Auteurs en CSV</a></li>
  </ul>
</div>';
		
		$content .= '<div id="activity-message" class="template-message">';
		$content .= $message;
		$content .= '</div>';
		
		$content .= activity_admin_html_table_activity_list($list, $this->getModule()->getName());	
		
		if($count != 0 && $desc != 0 && $page != 0){
			$content .= '<hr>' . system_html_pagination($this->getModule()->getName(), array(),$count,$desc,$page, "activit&eacute;s");	
		}
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		$t = $this->getTemplate();
		
		$t->addJsHeader('<script>
				function activityClearCache(){
						var saisie = prompt("Saisissez l\'identifiant de l activitee qui a foiree (et a cause de laquelle, vous ne pouvez plus en publier d\'autres):", "un nombre")
						
						$.get("'.URL.'server.php?module='.$this->getModule()->getName().'&action=clear&id="+saisie, 
											function(data) {
												res = JSON.parse(data);
												if(res.message){
													message = res.message;
													$("#activity-message").append(displayMessage(message));
												}else{
													$("#activity-message").append("ERROR" + data);	
												}
											}
										);
				}
				
				</script>');
		$t->addJsFooter('<script src="'.DIR_MODULE. $this->getModule()->getName().'/view/js/script.js"></script>');
		/*
		$t->addJsFooter("<script>$( document ).ready(function() {
					$(document).on('click','.".ACTIVITY_JS_CLASS_CALL_ANCHOR."',function(e)  { 
					    var href = ($(this).attr('href'));
						activity_remote_call('".URLUtils::builtServerUrl($this->getModule()->getName(), array())."', href);
						e.preventDefault();
					});	
				});</script>");
		*/
		$t->setContent($content);
	}
	
	
	
	public function pageFormActivity($action, Message $message, $activity, array $potentialAuthors, array $roles){
		$content = '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
		
		$label = ($action == 'add' ? 'Ajouter' : 'Editer');
		$content .= '<h3>'.$label.' une activit&eacute;</h3>';
		
		$content .= '<div id="activity-message" class="template-message">';
		$content .= $message;
		$content .= '</div>';
		
		$content .= activity_admin_html_activity_form($action, $this->getModule()->getName(), $activity, $potentialAuthors, $roles);//($action, $this->getModule()->getName(),$post);
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		$t = $this->getTemplate();
		$t->setContent($content);
		system_load_plugin(array('bootstrap-datepicker' => array('text-input-id' => 'activity-input-date', 'template' => $t)));
		system_load_plugin(array('bootstrap-editor' => array("template" => $t)));
	}
	
	
	
	
	public function pageFormManagePicture(Activity $activity, $message = null){
		$directory = $activity->getDirectory();
		$t = $this->getTemplate();
		$content = '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
		//$nbrFile = system_count_files_in_directory(DIR_HD_PICTURES . $directory . "/", array("jpg","jpeg","JPG","JPEG"));
		$content .= '<h3>Gestion des photos de l\'activit&eacute; <i>'.$activity->getTitle().'</i></h3>';
		
		$content .= '<div id="activity-message" class="template-message">';
		$content .= $message;
		$content .= '</div>';
		
		$content .= '<div class="row">';
		$content .= '<div class="col-lg-10">';
		$content .= 'D&eacute;tail de l\'activit&eacute; : <ul>
				<li><strong>Id : </strong> '.$activity->getId().'</li>
				<li><strong>Titre : </strong> '.$activity->getTitle().'</li>
				<li><strong>Description : </strong> '.$activity->getDescription().'</li>
				<li><strong>Date : </strong> '.$activity->getDate().'</li>
				<li><strong>R&eacute;pertoire : </strong> <i>'.$activity->getDirectory().'/</i></li>
				<li><strong>Level : </strong> '.$activity->getLevel().'</li>
				<li><strong>Nombre de photos : </strong> '.$activity->getCountPictures().' <small>(ce nombre ne sera pas en cas de suppression d\'image sans rafraichissement de page)</small></li>
			</ul>';
		$content .= '</div>';
		$content .= '<div class="col-lg-2">';
		if(!$activity->getIspublished()){
			$content .= '<a id="activity-action-publish-'.$activity->getId().'" href="'.URLUtils::generateURL($this->getModule()->getName(), array('action' => 'publish', 'id' => $activity->getId())).'" class="btn btn-success"><i class="fa fa-leaf"></i> Publier</a>';
		}else{
			$content .= '<a id="activity-action-publish-'.$activity->getId().'" href="'.URLUtils::generateURL($this->getModule()->getName(), array('action' => 'unpublish', 'id' => $activity->getId())).'" class="btn btn-warning"><i class="fa fa-fire"></i> D&eacute;publier</a>';
		}
		$content .= '</div>';
		$content .= '</div>';
		
		$content .= system_load_plugin(array('bootstrap-fileuploadhandler' => array("template"=> $t, "url" => URLUtils::builtServerUrl($this->getModule()->getName(), array('action' => 'picturehandler', 'activityid' => $activity->getId())))));
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		$t->setContent($content);
		//system_load_plugin(array('bootstrap-uploadhandler' => array('directory' => $directory, 'url' => URLUtils::builtServerUrl('activity', array('directory' => $directory)), 'module' => 'activity', "template" => $t, 'action' => '??')));
	}
	
	
	
	
	public function pagePublishing($activity){
		$content = '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
		$content .= activity_page_publishing($activity);
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		$t = $this->getTemplate();
		
		$activityid = $activity->getId();
		$modname = $this->getModule()->getName();	
		
		$js = '<script type="text/javascript" charset="utf-8">
				$(document).ready(function(){
				
					$("a.start").click(function() {
						$("a.start").attr("disabled", "disabled");

						window.onbeforeunload = function(){
		    				return "Etes vous certain de vouloir quitter cette page ? Il est deconseille de le faire durant une publication !";
						}
				
						var mailer = "false";
						if( $("input[id=activity-publishing-sendmail]").is(":checked") ){
								mailer = "true";
						}
				
						var queryData = {id : '.$activityid.', sendmail : mailer};	
								
						var progressCheck = function() {activityGetStat("'.URL.'server.php?module='.$modname.'&action=getstat", '.$activityid.');};
								
							
						var jqxhr = $.post("'.URL.'server.php?module='.$modname.'&action=publish&id='.$activityid.'", queryData, 
							function(intvalId) {
								return function(data) {
									res = JSON.parse(data);
									if(res.message){
										message = res.message;
										$("#publishing-progress").html("<div class=\"alert alert-"+message.type+"\">"+message.content+"</div>");
										$.get("'.URL.'server.php?module='.$modname.'&action=clear&id='.$activityid.'", 
											function(data) {
												res = JSON.parse(data);
												if(res.message){
													message = res.message;
													$("#publishing-progress").append(message.content);
													if(message.type == "success"){
														$("#publishing-progress").append("<a href=\"'. URLUtils::generateURL ( $modname, array () ) . '\" class=\"btn\">Retour a la liste</a>");
													}
												}else{
													$("#publishing-progress").append("ERROR" + data);	
												}
												
											window.onbeforeunload = null;
											}
										);
										clearInterval(intvalId);
									}else{
										alert(res);
									}
								}
							} (setInterval(progressCheck, 1000))
						);

						return false;

				}); 	
					

			});
			</script>';
		$t->addJsFooter($js);
		$t->setContent($content);
	}
	
	

	public function pageUnusedDirectories($list){
		$content = '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
		$content .= activity_admin_html_list_directories($list);
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		$t = $this->getTemplate();
		$t->setContent($content);
	}
	
	
	
	public function pageStatistics(array $statUser, array $statActi, array $statCompare){
		$content = '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
		
		$content .= '<div class="panel panel-default">';
 			$content .= ' <div class="panel-heading">';
   				$content .= '<h2 class="panel-title">Statisitiques des &eacute;quipes</h2>';
  			$content .= '</div>';
  			$content .= '<div class="panel-body">';
  			$content .= '<div class="row">';
  			foreach ($statUser as $year => $stat){
  				$content .= activity_admin_html_stat_table($year,$stat);
  			}
  			$content .= '</div>';
  			$content .= '</div>';
		$content .= '</div>';
		
		
		$content .= '<div class="panel panel-default">';
			$content .= ' <div class="panel-heading">';
				$content .= '<h2 class="panel-title">Comparaison avec l\'ann&eacute;e pass&eacute;e au m&ecirc;me moment</h2>';
			$content .= '</div>';
			$content .= '<div class="panel-body">';
			$content .= '<p class="text-info">Nous sommes le '.date('d-m-Y').'.</p>';
			$content .= activity_admin_html_stat_acti_table($statCompare, false);		
			$content .= '</div>';
		$content .= '</div>';
		
		
		$content .= '<div class="panel panel-default">';
			$content .= ' <div class="panel-heading">';
				$content .= '<h2 class="panel-title">Statistiques par ann&eacute;e</h2>';
			$content .= '</div>';
			$content .= '<div class="panel-body">';
				$content .= '<div id="stat-div-graph"></div>';
				$content .= '<hr>';
				$content .= activity_admin_html_stat_acti_table($statActi, true);
			$content .= '</div>';
		$content .= '</div>';
		
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		
		// make graph
		$element = 'stat-graph';
		$xkey = 'year';
		$ykeys = array('activities','pictures','comments');
		$labels = array('Activities','Photos','Commentaires');
		$data = array();
		foreach ($statActi as $s){
			$t = array('year' => $s->getYear());
			$t['activities'] = $s->getActivities();
			$t['pictures'] = $s->getPictures();
			$t['comments'] = $s->getComments();
			$data[] = $t;
		}
		//$morris = array($element, $xkey, $ykeys, $labels, $data);
		
		$content .= system_load_plugin(array('morris-chart' => array('template' => $this->getTemplate(), 'type' => 'bar','element' => 'stat-div-graph', 'data' => $data, 'xkey' => $xkey, 'ykeys' => $ykeys, 'labels' => $labels)));
		system_load_plugin(array('tablesorter' => array("template" => $this->getTemplate())));
		
		
		$t = $this->getTemplate();
		$t->setContent($content);
	}
	
	
	public function pageListCensures($censures, $message){
		$content = '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
		
		$content .= '<h3>Liste des censures</h3>';
		$content .= '<p>Pour supprimer une demande de censure, cliquez sur "Rejeter" (Attention, cette op&eacute;ration est irr&eacute;versible !!). Pour l\'approuver, cliquez sur voir, regarder la photo, et censurer la.</p>';
		$content .= '<p>Il y a actuellement '.count($censures).' demandes en attente. Au boulot les gars !</p><br>';	
		
		$content .= '<div id="activity-message" class="template-message">';
		$content .= $message;
		$content .= '</div>';
		$content .= activity_admin_html_table_censures_list($censures, $this->getModule()->getName());
		
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';

		$t = $this->getTemplate();
		$t->setContent($content);
		
	}
	
}