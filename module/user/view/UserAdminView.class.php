<?php

class UserAdminView extends AdminView implements iAdminView{


	public function __construct(Template $template, Module $module){
		parent::setTemplate($template);
		parent::setModule($module);
		$this->configureTemplate();
	}

	public function configureTemplate(){
		$this->getTemplate()->addJSHeader('<script type="text/javascript" src="'.DIR_MODULE . $this->getModule()->getLocation() . 'view/js/admin-script.js"></script>');
	}

	
	public function pageListUser($message, $list, $count, $desc, $page, $param){
		$content = '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
		$content .= $message;
		$content .= user_admin_html_list($this->getModule()->getName(), $list, $count);
		$content .= '<hr>' . system_html_pagination($this->getModule()->getName(), $param,$count,$desc,$page, "utilisateurs");
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		$t = $this->getTemplate();
		$t->setContent($content);
	}
	
	
	public function pageFormUser($action, $message, $user){
		$content = '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
		$content .= $message;
		$content .= user_admin_html_form($this->getModule()->getName(), $action, $user);
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		$t = $this->getTemplate();
		$t->setContent($content);
		
	}
}