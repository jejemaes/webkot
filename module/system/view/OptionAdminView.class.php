<?php


class OptionAdminView extends AdminView implements iAdminView{


	public function __construct(Template $template, Module $module){
		parent::setTemplate($template);
		parent::setModule($module);
		$this->configureTemplate();
	}

	public function configureTemplate(){
		
	}
	
	/**
	 * set the content of the View as the html code of the form to add an option
	 * @param Message $message : the Message Object to display
	 * @param array $types : the list of the names of type
	 */
	public function pageOptionForm(Message $message, Option $option, array $types){
		$content = '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
		$content .= '<h3>Ajouter une option</h3>';
		$content .= $message;
		$content .= $this->built_options_form($this->getModule()->getName(), $option, $types);
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		$t = $this->getTemplate();
		$t->setContent($content);
	}
	
	/**
	 * set the content of the View as the html code of the list (big form) of the given list of options
	 * @param array $options
	 * @param Message $message
	 */
	public function pageOptionList(array $options, Message $message){
		$content = '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
		$content .= '<h3>Liste des options du site</h3>';
		$content .= $message;
		$content .= $this->built_options_list($this->getModule()->getName(),$options);
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		$t = $this->getTemplate();
		$t->setContent($content);
	}
	
	

	/**
	 * built the html code of the form to add an options
	 * @param string $modname : the name of the module
	 * @param Option $option : the Option Object
	 * @param array $types : the list of name of types
	 * @return string : the html code
	 */
	private function built_options_form($modname, Option $option, $types){
		$typesOptions = "";
		foreach ($types as $t){
			if($option->getType() == $t){
				$typesOptions .= '<option value="'.$t.'" selected>'.$t.'</option>';
			}else{
				$typesOptions .= '<option value="'.$t.'">'.$t.'</option>';
			}
		}
	
		$html = '<form class="form-horizontal" method="POST" action="'.URLUtils::generateURL($modname, array('part'=>'options', 'action' => 'add')).'">
		<fieldset>
			
		<!-- Form Name -->
		<legend>Formulaire</legend>
			
		<!-- Text input-->
		<div class="form-group">
		  <label class="col-md-4 control-label" for="input-option-key">Nom (key)</label>
		  <div class="col-md-4">
		  <input id="input-option-key" name="input-option-key" type="text" placeholder="describe-the-option" class="form-control input-md" required="" value="'.$option->getKey().'">
			
		  </div>
		</div>
			
		<!-- Text input-->
		<div class="form-group">
		  <label class="col-md-4 control-label" for="input-option-value">Valeur de l\'option</label>
		  <div class="col-md-4">
		  <input id="input-option-value" name="input-option-value" type="text" placeholder="value" class="form-control input-md" required="" value="'.$option->getValue().'">
			
		  </div>
		</div>
			
		<!-- Textarea -->
		<div class="form-group">
		  <label class="col-md-4 control-label" for="input-option-description">Description</label>
		  <div class="col-md-4">
		    <textarea class="form-control" id="input-option-description" name="input-option-description">'.$option->getDescription().'</textarea>
		  </div>
		</div>
			
		<!-- Select Basic -->
		<div class="form-group">
		  <label class="col-md-4 control-label" for="input-option-type">Type</label>
		  <div class="col-md-4">
		    <select id="input-option-type" name="input-option-type" class="form-control">
		      '.$typesOptions.'
		    </select>
		  </div>
		</div>
			
		<div class="control-group">
		    <div class="controls">
		      <button type="submit" class="btn">Ajouter</button>
		    </div>
		</div>
			
		</fieldset>
		</form>';
		return $html;
	}
	

	/**
	 * the list (a big form) of all the options
	 * @param string $modname : the name of the module
	 * @param array $options : array of Option objects
	 * @return string : the html code
	 */
	private function built_options_list($modname,$options){
		$HTML = '<a href="'.URLUtils::generateURL($modname, array('part'=>'options', 'action'=>'add')).'" class="btn btn-primary"><i class="fa fa-plus"></i> Ajouter</a>';
		$HTML .= '<form class="form-horizontal" method="post" action="'.URLUtils::generateURL($modname, array("part" => "options")).'">
			<fieldset>
			
			<!-- Form Name -->
			<legend>Formulaire</legend>';
	
		foreach($options as $option){
			switch ($option->getType()) {
				case 'boolean':
				case 'integer':
				case 'string':
					$HTML .= '<!-- Text input-->
						<div class="control-group">
						  <label class="control-label" for="option-input[]">'.$option->getKey().'</label>
						  <div class="controls">
						    <input id="media-input-directory" name="option-input['.$option->getKey().']" type="text" class="input-xlarge" value="'.$option->getValue().'">
						    <p class="help-block">Le type est <i>'.$option->getType().'</i></p>
						    <p class="help-block">'.$option->getDescription().'</p>
						  </div>
						</div>';
					break;
				case 'json':
				case 'text':
					$HTML .= '<!-- Textarea -->
						<div class="control-group">
						  <label class="control-label" for="option-input[]">'.$option->getKey().'</label>
						  <div class="controls">
						    <textarea id="option-input[]" name="option-input['.$option->getKey().']" style="width:70%;height:150px;">'.$option->getValue().'</textarea>
							<p class="help-block">Le type est <i>'.$option->getType().'</i></p>
						  	<p class="help-block">'.$option->getDescription().'</p>
						  </div>
						</div>';
					break;
				default:
					break;
			}
		}
	
		$HTML .= '
		<div class="control-group">
		    <div class="controls">
		      <button type="submit" class="btn">Modifier</button>
		    </div>
		</div>
			
		</fieldset>
		</form>';
		return $HTML;
	}
	
	
}