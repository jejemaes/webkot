<?php

class GossipView extends View implements iView{

	/**
	 * Constructor
	 * @param iTemplate $template
	 */
	public function __construct(iTemplate $template, Module $module){
		parent::setTemplate($template);
		parent::setModule($module);
		$this->configureTemplate();
	}

	/**
	 * Set up the Layout according to the config file of the module, and init its content
	 * @param String $state : the state of the module which define the layout
	 * @param String $content : the html code of the content
	 */
	private function configureLayout($state, $content){
		$lname = $this->getModule()->getLayout($state);
		$this->getTemplate()->setLayout($lname);
		$this->getTemplate()->setContent($content);
	}

	/**
	 * Set some parameters for the Template : add css style, js code, ...
	 */
	private function configureTemplate(){
		$template = $this->getTemplate();
		$template->addJSFooter('<script type="text/javascript" src="'.DIR_MODULE . $this->getModule()->getLocation() . 'view/js/gossipscript.js"></script>');
		//$template->addJSFooter("<script>$('.btn-popover').popover();</script>");
		
		$template->setPageTitle($this->getModule()->getDisplayedName());
	}



	public function pageList(array $list, $nbrpage, $numpage, $message){
		$HTML = '<div class="col-lg-12">';
		
		system_load_plugin(array('social-ring' => array("template" => $this->getTemplate(), "level" => 0, "appId" => OptionManager::getInstance()->getOption("facebook-appid"))));
		 
		if(RoleManager::getInstance()->hasCapabilitySession('gossip-add-gossip')){
			$HTML .= '<p class="lead">Vous pouvez aimez (ou pas) les potins, et même en ajouter !</p>';
			$HTML .= '<a data-toggle="modal" href="#gossip-form" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Ajouter</a>';
		}else{		
			$HTML .= '<p class="lead">Connectez-vous et aimez ou d&eacute;testez les potins ! Vous pouvez &eacute;galement en ajouter ;)</p>';
		}
		$HTML .= '<div class="clearfix"></div><br>';
		$HTML .= $message;
		$HTML .= '<hr class="clearfix">';
		
		$HTML .= '<div id="gossip-page-content">';
		$HTML .= gossip_html_list($list, $numpage, $this->getModule()->getName());
		$HTML .= '</div>';
		
		$HTML .= '<!-- Modal -->
  <div class="modal fade" id="gossip-form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialogBAD">
      <div class="modal-content">    
        <div class="modal-body">
	         <form class="form-horizontal" method="POST" action="'.URLUtils::generateURL($this->getModule()->getName(), array("action" => "add")).'">
				<fieldset>
				
				<!-- Form Name -->
				<legend>Ajouter un potin ...</legend>
				
				<!-- Textarea -->
				<div class="control-group">
				  <label class="control-label" for="gossip-input-content">Il parait que ...</label>
				  <div class="controls">                     
				    <textarea id="gossip-input-content" name="gossip-input-content"></textarea>
				  </div>
				</div>
				
				<!-- Multiple Checkboxes (inline) 
				<div class="control-group">
				  <label class="control-label" for="gossip-input-anonymus">Anonyme :</label>
				  <div class="controls">
				    <label class="checkbox inline" for="gossip-input-anonymus">
				      <input type="checkbox" name="gossip-input-anonymus" id="gossip-input-anonymus" value="Ce potin sera anonyme.">
				      Ce potin sera anonyme.
				    </label>
				  </div>
				</div>
	         		-->
				
				<div class="control-group">
				    <div class="controls">
				      <button type="submit" class="btn">Soumettre</button>
				    </div>
				</div>
				
				</fieldset>
			</form>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->';
		
		$url = URLUtils::builtServerUrl($this->getModule()->getName(), array("action" => "getpage"));
		$callback = "$('#gossip-page-content').html(gossipGetPageContent('".$url."', num));";
		$HTML .= system_load_plugin(array('bootpag' => array("template" => $this->getTemplate(), "call-on-change" => $callback, 'total' => $nbrpage)));
		
		$HTML .= '</div>';
		
		$this->configureLayout('page-list',$HTML);
		$this->getTemplate()->setPageSubtitle("La liste");
	}

	public function pagePotin(Gossip $gossip){
		$html = '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
		$html .= gossip_htm_gossip($gossip, $this->getModule()->getName());
		$html .= '</div>';
		$this->configureLayout('page-gossip',$html);
		$this->getTemplate()->setPageSubtitle("Un potin ...");
	}


}
