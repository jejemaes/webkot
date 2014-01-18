<?php


class ChallengeAdminView extends AdminView implements iAdminView{
	
	
	public function __construct(Template $template, Module $module){
		parent::setTemplate($template);
		parent::setModule($module);
	}
	
	public function configureTemplate(){
		$this->getTemplate()->addStyle('<link rel="stylesheet" href="'.DIR_MODULE.$this->getModule()->getName().'view/css/style.css" />');
	}
	
	
	public function pageListChallenge($list){
		$content .= '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
		
		$man = SessionMessageManager::getInstance();
		$content .= $man->getSessionMessage();
		$content .= challenge_admin_html_list($this->getModule()->getName(), $list);
		
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		
		$t = $this->getTemplate();
		$t->setContent($content);	
	}
	
	
	
	public function pageDetailChallenge($challenge, $listAnswer){
		$content .= '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
		
		$man = SessionMessageManager::getInstance();
		$content .= $man->getSessionMessage();
		$content .= challenge_admin_html_detail($this->getModule()->getName(), $challenge, $listAnswer);
		
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		
		$t = $this->getTemplate();
		$t->setContent($content);
	}
	
	
	public function pageFormChallenge($action, $message, $challenge){
		$content .= '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
		
		$man = SessionMessageManager::getInstance();
		$content .= $message;
		$content .= challenge_admin_html_form($action, $this->getModule()->getName(), $challenge);
		
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		$t = $this->getTemplate();
		$t->setContent($content);
		system_load_plugin(array('bootstrap-datepicker' => array('text-input-id' => 'challenge-input-date', 'template'=>$t)));
	}
	
}