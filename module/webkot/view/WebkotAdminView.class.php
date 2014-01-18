<?php

class WebkotAdminView extends AdminView implements iAdminView{


	public function __construct(Template $template, Module $module){
		parent::setTemplate($template);
		parent::setModule($module);
	}

	public function configureTemplate(){

	}
	
	
	
	public function pageListWebkotteur($list,$count,$desc,$page){
		$content .= '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
	
		$SMM = SessionMessageManager::getInstance();
		$content .= $SMM->getSessionMessage();
		
		$content .= webkot_admin_html_webkotteur_list($list, $this->getModule()->getName());
		$content .= '<hr>' . system_html_pagination($this->getModule()->getName(), array('part' => 'webkotteur'),$count,$desc,$page, "utilisateurs");
		
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		$t = $this->getTemplate();
		$t->setContent($content);
	}
	
	public function pageWebkotteurForm($action, $message, $webkotteur){
		$content .= '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
		$content .= $message;
		$content .= webkot_admin_html_webkotteur_form($action, $this->getModule()->getName(), $webkotteur);
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		$t = $this->getTemplate();
		$t->setContent($content);
	}
	
	
	
	public function pageListTeam(array $list){
		$content .= '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
		
		$SMM = SessionMessageManager::getInstance();
		$content .= $SMM->getSessionMessage();
		
		$content .= webkot_admin_html_team_list($list, $this->getModule()->getName());
		
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		$t = $this->getTemplate();
		$t->setContent($content);
	}
	
	
	public function pageTeamForm($modname, $webkotteur, $message){
		$content .= '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
		$content .= $message;
		$content .= webkot_html_team_form($modname, $webkotteur, $this->getTemplate());
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		$t = $this->getTemplate();
		$t->setContent($content);
	}
	
	
	public function pageAddTeamStep1($modulename){
		$content .= '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
		$SMM = SessionMessageManager::getInstance();
		$content .= $SMM->getSessionMessage();
		$content .= webkot_admin_html_addteam_step1($modulename);
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		$t = $this->getTemplate();
		$t->setContent($content);
	}
	
	
	public function pageAddTeamStep2($modulename,$year, $list){
		$content .= '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
		$SMM = SessionMessageManager::getInstance();
		$content .= $SMM->getSessionMessage();
		$content .= webkot_admin_html_addteam_step2($modulename,$year,$list);
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		$t = $this->getTemplate();
		$t->setContent($content);
	}
	
	
	public function pageAddTeamStep3($name,$webkotteur, $year){
		$content .= '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
		$SMM = SessionMessageManager::getInstance();
		$content .= $SMM->getSessionMessage();
		$content .= webkot_admin_html_addteam_step3($name,$webkotteur, $year, $this->getTemplate());
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		$t = $this->getTemplate();
		$t->setContent($content);
	}
	
	
	
}