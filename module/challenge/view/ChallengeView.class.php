<?php


class ChallengeView extends View implements iView{




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
		$viewdirectory = DIR_MODULE . $this->getModule()->getLocation() . 'view/';
		// add module css
		$template = $this->getTemplate();
		$template->addStyle('<link href="'.$viewdirectory.'css/style.css" rel="stylesheet"/>');

		$template->setPageTitle($this->getModule()->getDisplayedName());
	}


	/**
	 * Built yhe page with the form to answer the question
	 * @param Challenge $challenge : the challenge
	 */
	public function pageChallengeForm(Challenge $challenge, Message $message){	
		$HTML .= '<div class="row">';
		$HTML .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
		$HTML .= $message->__toString();
		$HTML .= $challenge->getDescription();
		$HTML .= '<img src="' . DIR_MEDIA .$challenge->getPath_picture().'" alt="photo-concours" class="challenge-img img-responsive">';
		$HTML .= '<b>La question est : </b>' . $challenge->getQuestion() . '<br>';
		$HTML .= '<i>Attention, le concours se termine  le ' . ConversionUtils::timestampToDatetime($challenge->getEnd_date()) . '</i><br><br>';

		
		// Create the form
		$challengeForm = new JFormer ( 'challenge-form', array (
				// 'action' => '/'.str_replace($_SERVER['DOCUMENT_ROOT'], '', __FILE__),
				'action' => URLUtils::getCompleteActualURL (),
				'submitButtonText' => 'Envoyer'
		) );
			
		// Create the form page
		$page = new JFormPage ( $challengeForm->id . 'Page', array (
				'title' => 'Proposer votre r&eacute;ponse'
		) );
			
		// Create the form section
		$section = new JFormSection ( $challengeForm->id . 'Section', array () );
			
		// Add components to the section
		$section->addJFormComponentArray ( array (
				new JFormComponentSingleLineText ( 'challenge-input-answer', 'Votre r&eacute;ponse:', array (
						'width' => 'longest',
						'height' => 'medium',
						'validationOptions' => array (
								'required'
						)
				) ),
				new JFormComponentHidden ( 'challenge-input-challengeid', $challenge->getId())
		) );
			
		// Add the section to the page
		$page->addJFormSection ( $section );
			
		// Add the page to the form
		$challengeForm->addJFormPage ( $page );
		$HTML .= $challengeForm;
		$HTML .= '</div>';
		$HTML .= '</div>';
		
		$this->getTemplate()->setPageSubtitle($challenge->getQuestion());
		$this->configureLayout('page-challenge',$HTML);
	}

	/**
	 * Built yhe page without the form to answer the question
	 * @param Challenge $challenge : the challenge
	 * @param String $text : the text instead of the form
	 */
	public function pageChallengeText(Challenge $challenge, $text, Message $message){
		$HTML .= '<div class="row">';
		$HTML .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
		$HTML .= $message->__toString();
		$HTML .= $challenge->getDescription();
		$HTML .= '<img src="' . DIR_MEDIA .$challenge->getPath_picture().'" alt="photo-concours" class="challenge-img img-responsive">';
		$HTML .= '<b>La question est : </b>' . $challenge->getQuestion() . '<br>';
		$HTML .= '<i>Attention, le concours se termine  le ' . ConversionUtils::timestampToDatetime($challenge->getEnd_date()) . '</i><br><br>';
		$HTML .= '<br>' . $text;
		$HTML .= '</div>';
		$HTML .= '</div>';
		
		$this->configureLayout('page-challenge',$HTML);
	}


}

