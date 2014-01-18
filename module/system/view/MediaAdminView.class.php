<?php

class MediaAdminView extends AdminView implements iAdminView{


	public function __construct(Template $template, Module $module){
		parent::setTemplate($template);
		parent::setModule($module);
		$this->configureTemplate();
	}

	public function configureTemplate(){
		$this->getTemplate()->addJSHeader('<script type="text/javascript" src="'.DIR_MODULE.$this->getModule()->getLocation().'view/js/admin.js"></script>');
	}
	
	
	public function pageMediaList($list, $message){
		$content .= '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
		$content .= $message;
		$content .= media_admin_html_table_media($this->getModule()->getName(),$list);
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		$t = $this->getTemplate();
		$t->setContent($content);
	}
	
	
	
	public function pageMediaForm($categories, $message){
		$content .= '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
		$content .= $message;
		$content .= media_admin_html_media_form($this->getModule()->getName(),$categories);
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		$t = $this->getTemplate();
		$t->setContent($content);
	}
	
	
	public function pageMediaCatForm($action,$message){
		$content .= '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
		$content .= $message;
		$content .= media_admin_html_catergory_form($this->getModule()->getName(),$action);
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		$t = $this->getTemplate();
		$t->setContent($content);
	}
	
	
	
	public function pageOptionsForm($options,$message){
		$content .= '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
		$content .= $message;
		$content .= options_admin_html_form($this->getModule()->getName(),$options);
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		$t = $this->getTemplate();
		$t->setContent($content);
	}
	
	
	public function pageModuleList($modules, $message){
		$content .= '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
		$content .= $message;
		$content .= module_list($this->getModule()->getName(), $modules);
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		$t = $this->getTemplate();
		$t->setContent($content);
	}
	
	public function pageModuleUpdate($module, $message){
		$content .= '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
		$content .= $message;
		$content .= module_update_form($this->getModule()->getName(), $module);
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		$t = $this->getTemplate();
		$t->setContent($content);
	}
	
	public function pageModuleRole($mname, array $available, array $capabilities, array $roles, $message){
		$content .= '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
		$content .= $message;
		$content .= module_update_form_roles($this->getModule()->getName(), $mname, $available, $capabilities, $roles);
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		$t = $this->getTemplate();
		$t->setContent($content);
	}
	
	
	public function pageLog(array $logs){
		$content .= '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
		$content .= '<h3>Liste des Logs</h3>';
		$content .= '<div id="system-log-message"></div>';
		foreach ($logs as $file){
			if(is_file(DIR_LOG . $file)){
				$arr = preg_split("/[.]+/", $file);
				$file = $arr[0];
				$content .= '<div style="margin-bottom:15px">';
				$content .= '<h4>' . $file . '</h4>';
				$content .= '<div class="btn-group">
  <button type="button" class="btn btn-default" onclick="javascript:systemGetLog(\''.URL.'server.php?module='.$this->getModule()->getName().'&part=log\',\''.$file.'\')"><i class="fa fa-eye"></i> Voir</button>
  <button type="button" class="btn btn-default" onclick="javascript:systemDeleteLog(\''.URL.'server.php?module='.$this->getModule()->getName().'&part=log\',\''.$file.'\')"><i class="fa fa-trash-o"></i> Supprimer</button>
</div>';
				$content .= '</div>';
				$content .= '<div id="system-log-content-'.$file.'" class="well">Cliquez sur voir</div>';
			}
		}
		//$content .= module_update_form($this->getModule()->getName(), $module);
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		$t = $this->getTemplate();
		$t->setContent($content);
	}
	
	
	
}
