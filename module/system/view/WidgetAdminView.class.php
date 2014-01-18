<?php

class WidgetAdminView extends AdminView implements iAdminView{


	public function __construct(Template $template, Module $module){
		parent::setTemplate($template);
		parent::setModule($module);
	}

	public function configureTemplate(){

	}


	public function pageWidgetList($list, $mods, $message){
		$content .= '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
		$content .= $message;
		$content .= system_admin_html_table_widget($this->getModule()->getName(),$list, $mods);
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		$t = $this->getTemplate();
		$t->setContent($content);
	}

	
	public function pageWidgetForm($widget, $message){
		$content .= '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
		$content .= $message;
		$content .= system_admin_form_widget($this->getModule()->getName(), $widget);
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		$t = $this->getTemplate();
		$t->setContent($content);
	}
	
	
	
	public function pageWidgetPlacement($allwidgets, $mod, $modwidgets, $message){
		$content .= '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
		$content .= $message;
		$content .= system_admin_form_widget_placement($this->getModule()->getName(), $mod, $allwidgets,$modwidgets);
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		$t = $this->getTemplate();
		$t->setContent($content);
	}



}
