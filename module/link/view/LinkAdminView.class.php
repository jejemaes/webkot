<?php

class LinkAdminView extends AdminView implements iAdminView{


	public function __construct(Template $template, Module $module){
		parent::setTemplate($template);
		parent::setModule($module);
	}

	public function configureTemplate(){

	}
	
	/**
	 * built the html code of the table of link 
	 * @param array $list : the link to put in the table
	 */
	public function pageLinkTable(array $list){
		$content .= '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
		
		$SMM = SessionMessageManager::getInstance();
		$content .= $SMM->getSessionMessage();
		$content .= link_admin_html_table_link($this->getModule()->getName(),$list);
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		
		$t = $this->getTemplate();
		$t->setContent($content);
	}
	
	
	public function pageLinkForm($action,$message, $categories,$link){
		$content .= '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
		
		$content .= $message;
		$content .= link_admin_link_form($this->getModule()->getName(),$action,$categories,$link);
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		
		$t = $this->getTemplate();
		$t->setContent($content);
	}
	
	
	/**
	 * built the html code of the table of link
	 * @param array $list : the link category to put in the table
	 */
	public function pageLinkCategoryTable(array $list){
		$content .= '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
		
		$SMM = SessionMessageManager::getInstance();
		$content .= $SMM->getSessionMessage();
		
		$content .= link_admin_html_table_link_category($this->getModule()->getName(),$list);
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		
		$t = $this->getTemplate();
		$t->setContent($content);
	}
	
	
	public function pageCategoryForm($action, $message, $category){
		$content .= '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
		
		$content .= $message;
		$content .= link_admin_category_form($this->getModule()->getName(),$action, $category);//,$categories,$link);
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		
		$t = $this->getTemplate();
		$t->setContent($content);
	} 
	
}