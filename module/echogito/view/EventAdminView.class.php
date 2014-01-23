<?php


class EventAdminView extends AdminView implements iAdminView{


	public function __construct(Template $template, Module $module){
		parent::setTemplate($template);
		parent::setModule($module);
	}

	public function configureTemplate(){

	}



	public function pageListPaging($message, $list, $count, $desc, $page){
		$content = '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
		
		$content .= '<h3>Liste des &eacute;v&egrave;nements &agrave; venir</h3>';
		$content .= '<div id="echogito-message" class="template-message">';
		$content .= $message;
		$content .= '</div>';
		$content .= echogito_admin_html_list_events($this->getModule()->getName(), $list);
		$content .= system_html_pagination($this->getModule()->getName(), array("part"=>"event","p"=>"later"),$count,$desc,$page, "&eacute;v&eacute;nements");
		
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		
		$t = $this->getTemplate();
		$t->setContent($content);
	}
	
	
	public function pageListCategory($message, $list){
		$content = '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
	
		$content .= '<h3>Liste des cat&eacute;gories</h3>';
		$content .= '<div id="echogito-message" class="template-message">';
		$content .= $message;
		$content .= '</div>';
		$content .= echogito_admin_html_list_category($this->getModule()->getName(), $list);
	
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
	
		$t = $this->getTemplate();
		$t->setContent($content);
	}
	
	public function pageList($message, $list, array $categories){
		$content = '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
	
		$content .= '<h3>Liste des  &eacute;v&egrave;nements non approuv&eacute;s</h3>';
		$content .= '<div id="echogito-message" class="template-message">';
		$content .= $message;
		$content .= '</div>';
		$content .= echogito_admin_html_list_events($this->getModule()->getName(), $list);
	
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		/*
		$content .= '<div class="modal fade" id="echogito-modal-approve" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Approuver un &eacute;v&eacute;nement</h4>
      </div>
      <div class="modal-body">
     		'.echogito_admin_html_form_approve_category($this->getModule()->getName(), $categories, 0).'
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->';
	*/
		$t = $this->getTemplate();
		$t->setContent($content);
	}


	public function pageFormEvent($action, Message $message, $event, $categories){
		
		$content = '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
		$label = ($action == 'add' ? 'Ajouter' : 'Editer');
		$content .= '<h3>'.$label.' un &eacute;v&eacute;nement</h3>';
		$content .= '<div id="echogito-message" class="template-message">';
		$content .= $message;
		$content .= '</div>';
		$content .= echogito_admin_html_form($action, $this->getModule()->getName(),$this->getTemplate(), $event, $categories);
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		$t = $this->getTemplate();
		$t->setContent($content);
		//system_load_plugin(array('bootstrap-tinymce' => array("template"=> $t, "selector" => ".bootstrap-tinymce")));
	}
	
	public function pageFormEventCategory($action, Message $message, $category = null){
		$content = '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
		$label = ($action == 'add' ? 'Ajouter' : 'Editer');
		$content .= '<h3>'.$label.' une cat&eacute;gorie</h3>';
		$content .= '<div id="echogito-message" class="template-message">';
		$content .= $message;
		$content .= '</div>';
		$content .= echogito_admin_html_form_category($action, $this->getModule()->getName(),$this->getTemplate(), $category);
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		$t = $this->getTemplate();
		$t->setContent($content);
		//system_load_plugin(array('bootstrap-tinymce' => array("template"=> $t, "selector" => ".bootstrap-tinymce")));
	}


	public function pageFormApprove($message, $id, array $categories){
		$content = '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
		$content .= '<h3>Approuver</h3>';
		$content .= '<div id="echogito-message" class="template-message">';
		$content .= $message;
		$content .= '</div>';
		$content .= echogito_admin_html_form_approve_category($this->getModule()->getName(), $categories, $id);
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		$t = $this->getTemplate();
		$t->setContent($content);
	}

}