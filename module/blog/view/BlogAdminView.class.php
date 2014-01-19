<?php


class BlogAdminView extends AdminView implements iAdminView{
	
	
	public function __construct(Template $template, Module $module){
		parent::setTemplate($template);
		parent::setModule($module);
	}
	
	public function configureTemplate(){
		
	}
	
	
	
	public function pageListPost($list){
		$content = '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
		
		$man = SessionMessageManager::getInstance();
		$content .= $man->getSessionMessage();
		
		$content .= blog_admin_html_table_post_list($list, $this->getModule()->getName());
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		$t = $this->getTemplate();
		$t->setContent($content);
	}
	
	
	public function pageFormPost($action, Message $message, $post = null){
		$content = '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
		$content .= $message;
		$content .= blog_admin_form_post($action, $this->getModule()->getName(),$post);
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		$t = $this->getTemplate();
		$t->setContent($content);
		system_load_plugin(array('bootstrap-tinymce' => array("template"=> $t, "selector" => ".bootstrap-tinymce")));
	}
	
	
	
	
}