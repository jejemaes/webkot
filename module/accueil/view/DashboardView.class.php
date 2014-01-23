<?php


class DashboardView extends AdminView implements iAdminView{


	public function __construct(Template $template, Module $module){
		parent::setTemplate($template);
		parent::setModule($module);
	}

	public function configureTemplate(){

	}
	
	
	public function pageHome($message, $list, $username, $slides, $censures, $nbrEventNonApproved){
		$content = '<div>';
			$content .= '<div class="jumbotron">
				  <a href="index.php?logout" class="btn btn-default pull-right" role="button"><span class="fa fa-power-off"></span> <br/>Logout</a>
				  <h1>Webkot Administration Panel</h1>
				  <p class="pull-left">Bonjour '.$username.',</p>	
				  
                    <div class="clearfix"></div>     
				  	<div class="row">
                        <div class="col-xs-6 col-md-6">
                          <a href="'.URLUtils::generateURL('activity', array("action"=>"add")).'" class="btn btn-primary btn-lg" role="button"><span class="fa fa-plus"></span> <br/>Ajouter</a>
                          <a href="'.URLUtils::generateURL('activity', array()).'" class="btn btn-info btn-lg" role="button"><span class="fa fa-camera"></span> <br/>Activit&eacute;s</a>
                          <a href="'.URLUtils::generateURL('activity', array("list"=>"stats")).'" class="btn btn-info btn-lg" role="button"><span class="fa fa-bar-chart-o"></span> <br/>Statistiques</a>
                          <a href="'.URLUtils::generateURL('activity', array("list"=>"unpublished")).'" class="btn btn-info btn-lg" role="button"><span class="fa fa-fire"></span> <br/>Unpublished</a>
                        </div>
                        <div class="col-xs-6 col-md-6">
                          <a href="'.URLUtils::generateURL('user', array()).'" class="btn btn-warning btn-lg" role="button"><span class="fa fa-user"></span> <br/>Utilisateurs</a>
                          <a href="'.URLUtils::generateURL('blog', array()).'" class="btn btn-primary btn-lg" role="button"><span class="fa fa-file"></span> <br/>Blog</a>
                          <a href="'.URLUtils::generateURL('video', array()).'" class="btn btn-success btn-lg" role="button"><span class="fa fa-video-camera"></span> <br/>Videos</a>
                          <a href="'.URLUtils::generateURL('gossip', array()).'" class="btn btn-danger btn-lg" role="button"><span class="fa fa-comments"></span> <br/>Potins</a>
                        </div>
                    </div>  
				</div>';
		
		$content .= $message;
		
		
		$content .= '<div class="row">';
          $content .= '<div class="col-lg-3">
            <div class="panel panel-info">
              <div class="panel-heading">
                <div class="row">
                  <div class="col-xs-6">
                    <i class="fa fa-calendar fa fa-5x"></i>
                  </div>
                  <div class="col-xs-6 text-right">
                    <p class="announcement-heading">'.$nbrEventNonApproved.'</p>
                    <p class="announcement-text">Evenements non approuv&eacute;s</p>
                  </div>
                </div>
              </div>
              <a href="#">
                <div class="panel-footer announcement-bottom">
                  <div class="row">
                    <div class="col-xs-8">
                      <a href="'.URLUtils::generateURL('echogito', array("p" => "unapproved")).'">Voir la liste (Module Echogito)</a>
                    </div>
                    <div class="col-xs-4 text-right">
                      <i class="fa fa-circle-arrow-right"></i>
                    </div>
                  </div>
                </div>
              </a>
            </div>
          </div>';
            
          $content .= '<div class="col-lg-3">
            <div class="panel panel-warning">
              <div class="panel-heading">
                <div class="row">
                  <div class="col-xs-6">
                    <i class="fa fa-check fa fa-5x"></i>
                  </div>
                  <div class="col-xs-6 text-right">
                    <p class="announcement-heading">'.count($list).'</p>
                    <p class="announcement-text">To-Do Items</p>
                  </div>
                </div>
              </div>
              <a href="#todo">
                <div class="panel-footer announcement-bottom">
                  <div class="row">
                    <div class="col-xs-8">
                      Voir la liste
                    </div>
                    <div class="col-xs-4 text-right">
                      <i class="fa fa-circle-arrow-right"></i>
                    </div>
                  </div>
                </div>
              </a>
            </div>
          </div>';
          
          $content .= '<div class="col-lg-3">
            <div class="panel panel-danger">
              <div class="panel-heading">
                <div class="row">
                  <div class="col-xs-6">
                    <i class="fa fa-ban fa fa-5x"></i>
                  </div>
                  <div class="col-xs-6 text-right">
                    <p class="announcement-heading">'.count($censures).'</p>
                    <p class="announcement-text">Demandes de censure</p>
                  </div>
                </div>
              </div>
              <a href="'.URLUtils::generateURL('activity',array("list" => "censures")).'">
                <div class="panel-footer announcement-bottom">
                  <div class="row">
                    <div class="col-xs-8">
                      Voir la liste compl&egrave;te
                    </div>
                    <div class="col-xs-4 text-right">
                      <i class="fa fa-circle-arrow-right"></i>
                    </div>
                  </div>
                </div>
              </a>
            </div>
          </div>';
          
          $content .= '<div class="col-lg-3">
            <div class="panel panel-success">
              <div class="panel-heading">
                <div class="row">
                  <div class="col-xs-6">
                    <i class="fa fa-comments fa fa-5x"></i>
                  </div>
                  <div class="col-xs-6 text-right">
                    <p class="announcement-heading">10</p>
                    <p class="announcement-text">Derniers Potins</p>
                  </div>
                </div>
              </div>
              <a href="#">
                <div class="panel-footer announcement-bottom">
                  <div class="row">
                    <div class="col-xs-6">
                      Voir la liste
                    </div>
                    <div class="col-xs-6 text-right">
                      <i class="fa fa-circle-arrow-right"></i>
                    </div>
                  </div>
                </div>
              </a>
            </div>
          </div>';
        $content .= '</div><!-- /.row -->';
		
		
		
		$content .= '<div class="row">';
			$content .= '<div class="col-md-8">';
			$content .= '<div class="well">';
			//$content .= '<div class="casebox">POST IT WALL (to come)<hr>';
			$content .= dashboard_slide_html_table($slides, $this->getModule()->getName());
			$content .= '</div>';
			$content .= '</div>';
			
			$content .= '<div class="col-md-4">';
			$content .= '<div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <span class="fa fa-bookmark"></span> Admin Shortcuts</h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-12 col-md-12">
                          <a href="'.URLUtils::generateURL('system',array("part" => "module")).'" class="btn btn-danger btn-lg" role="button"><span class="fa fa-user"></span> <br/>Roles</a>
                          <a href="'.URLUtils::generateURL('system',array("part" => "log")).'" class="btn btn-info btn-lg" role="button"><span class="fa fa-file"></span> <br/>Logs</a>
						  <a href="'.URLUtils::generateURL('accueil',array("part" => "apc")).'" class="btn btn-default btn-lg" role="button"><span class="fa fa-refresh"></span> <br/>Flush APC</a>
                       </div>
                      <div class="col-xs-12 col-md-12">
                          <a href="http://webmail.webkot.be" target="_blank" class="btn btn-success btn-lg" role="button"><span class="fa fa-envelope"></span> <br/>Webmail</a>
                          <a href="http://www.webkot.be/phpmyadmin" target="_blank" class="btn btn-warning btn-lg" role="button"><span class="fa fa-list"></span> <br/>PhpMyAdmin</a>
                          <a href="http://zpanel.webkot.be" target="_blank" class="btn btn-info btn-lg" role="button"><span class="fa fa-cog"></span> <br/>ZPanel</a>
                        </div>
                    </div>
                  </div>
            </div>';
			/*$content .= '<div class="well">';
			$content .= '<h3>Autres actions</h3>';
			$content .= '<a href="'.URLUtils::generateURL($this->getModule()->getName(), array('part' => 'apc')).'" class="btn btn-danger" onclick="return(confirm(\'Etes-vous certain de vouloir vider les entrees USER du cache APC ?\'));">Flush APC</a>';
			$content .= '</div>';*/
			$content .= '</div>';
			
			$content .= '<div class="col-md-4">';
			$content .= '<div class="well">';
			$content .= '<h3>Capabilities</h3>';
			$cap = SessionManager::getInstance()->getCapabilities();
			foreach ($cap as $c){
				$content .= $c . " , ";
			}
			$content .= '</div>';
			$content .= '</div>';
		$content .= '</div>';
		
		$content .= '<div class="row" id="todo">';
			$content .= '<div class="col-md-12">';
			$content .= '<div class="well">';
			$content .= dashboard_todo_html_table($list, $this->getModule()->getName());
			$content .= '</div>';
			$content .= '</div>';
		$content .= '</div>';
		
		$content .= '</div>';
		
		$t = $this->getTemplate();
		$t->setContent($content);
	}
	
	
	/**
	 * built the page of all the todo
	 * @param unknown $list
	 */
	public function pageListTodo($message, $list){
		$content .= '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
		$content .= $message;
		$content .= dashboard_todo_html_table($list, $this->getModule()->getName());
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		$t = $this->getTemplate();
		$t->setContent($content);
		system_load_plugin(array('tablesorter' => array("template" => $t)));
	}
	
	
	public function pageTodoForm($action, $message, $todo){
		$content .= '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
		$content .= $message;
		$content .= dashboard_todo_html_form($action, $this->getModule()->getName(), $todo);
		$content .= '</div>';
		$content .= '</div>';
		$t = $this->getTemplate();
		$t->setContent($content);
		system_load_plugin(array('bootstrap-editor' => array("template" => $t)));
	}
	
	
	
	public function pageListSlide($message, $list){
		$content .= '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
		$content .= $message;
		$content .= dashboard_slide_html_table($list, $this->getModule()->getName());
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		$t = $this->getTemplate();
		$t->setContent($content);
		system_load_plugin(array('tablesorter' => array("template" => $t)));
	}
	
	
	public function pageSlideForm($action, $message, $slide){
		$content .= '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
		$content .= $message;
		$content .= dashboard_slide_html_form($action, $this->getModule()->getName(), $slide, $this->getTemplate());
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		$t = $this->getTemplate();
		$t->setContent($content);
		system_load_plugin(array('bootstrap-tinymce' => array("template"=> $t, "selector" => ".bootstrap-tinymce")));
	}
	
	
}