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
	
	
	
	public function pageListActivity($list, $count = 0, $desc = 0, $page = 0){
		$content = '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';	
		$man = SessionMessageManager::getInstance();
		$content .= $man->getSessionMessage();
		
		$content .= activity_admin_html_table_activity_list($list, $this->getModule()->getName());	
		
		if($count != 0 && $desc != 0 && $page != 0){
			$content .= '<hr>' . system_html_pagination($this->getModule()->getName(), array(),$count,$desc,$page, "activit�s");	
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
		$t->setContent($content);
	}
	
	
	public function pageListActivityAuthors($list, $count = 0, $desc = 0, $page = 0){
		$content = '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
		$man = SessionMessageManager::getInstance();
		$content .= $man->getSessionMessage();
	
		$content .= activity_admin_html_table_activity_list_authors($list, $this->getModule()->getName());
	
		if($count != 0 && $desc != 0 && $page != 0){
			$content .= '<hr>' . system_html_pagination($this->getModule()->getName(), array("list" => "authors"),$count,$desc,$page, "activit�s");
		}
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		$t = $this->getTemplate();
		$t->setContent($content);
	}
	
	public function pageFormActivity($action, Message $message, $activity, array $potentialAuthors, array $roles){
		$content = '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
		$content .= $message;
		$content .= activity_admin_activity_form($action, $this->getModule()->getName(), $activity, $potentialAuthors, $roles);//($action, $this->getModule()->getName(),$post);
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		$t = $this->getTemplate();
		$t->setContent($content);
		system_load_plugin(array('bootstrap-datepicker' => array('text-input-id' => 'activity-input-date', 'template' => $t)));
		system_load_plugin(array('bootstrap-editor' => array("template" => $t)));
	}
	
	
	
	
	public function pageFormAddPicture($directory){
		$content = '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
		$nbrFile = system_count_files_in_directory(DIR_HD_PICTURES . $directory . "/", array("jpg","jpeg","JPG","JPEG"));
		$content .= activity_form_add_picture($nbrFile,$directory . "/");
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		$t = $this->getTemplate();
		$t->setContent($content);
		system_load_plugin(array('bootstrap-uploadhandler' => array('directory' => $directory, 'url' => URLUtils::builtServerUrl('activity', array('directory' => $directory)), 'module' => 'activity', "template" => $t, 'action' => '??')));
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
		$content .= activity_html_list_directories($list);
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
  				$content .= activity_html_stat_table($year,$stat);
  			}
  			$content .= '</div>';
  			$content .= '</div>';
		$content .= '</div>';
		
		
		$content .= '<div class="panel panel-default">';
			$content .= ' <div class="panel-heading">';
				$content .= '<h2 class="panel-title">Comparaison avec l\'ann&eacute;e pass&eacute;e au meme moment</h2>';
			$content .= '</div>';
			$content .= '<div class="panel-body">';
			$content .= '<p class="text-info">Nous sommes le '.date('d-m-Y').'.</p>';
			$content .= activity_html_stat_acti_table($statCompare, false);		
			$content .= '</div>';
		$content .= '</div>';
		
		
		$content .= '<div class="panel panel-default">';
			$content .= ' <div class="panel-heading">';
				$content .= '<h2 class="panel-title">Statistiques par ann&eacute;e</h2>';
			$content .= '</div>';
			$content .= '<div class="panel-body">';
				$content .= '<div id="stat-div-graph"></div>';
				$content .= '<hr>';
				$content .= activity_html_stat_acti_table($statActi, true);
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
		
		$content .= $message;
		$content .= activity_admin_html_table_censures_list($censures, $this->getModule()->getName());
		
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';

		$t = $this->getTemplate();
		$t->setContent($content);
		
	}
	
}