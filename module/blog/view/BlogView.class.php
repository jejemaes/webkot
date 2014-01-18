<?php


class BlogView extends View implements iView{

	
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
		$template->addJSHeader('<script src="'.DIR_MODULE . $this->getModule()->getLocation().'view/js/blogscript.js" type="text/javascript"></script>');
		$template->setPageTitle($this->getModule()->getDisplayedName());
	}
	
	
	public function PostPage(BlogPost $post, Message $message){
		$comments = $post->getComments();
		$HTML = '<div class="row">';
		$HTML .= '<div class="col-lg-12">';
			$HTML .= '<h4>'.$post->getTitle().'</h4>';
		
			// infos about the post
			$comment = (count($comments) === 0 ) ? "commentaire" : "commentaires";
			$HTML .= '<small>';
				$HTML .= '<i class="fa fa-user"></i> par <a href="'.URLUtils::getUserPageURL($post->getAuthor()).'">'.ucfirst($post->getAuthor()).'</a>';
				$HTML .= ' | <i class="fa fa-calendar"></i> Le '.ConversionUtils::timestampToDatetime($post->getDate(), "/");
				$HTML .= ' | <i class="fa fa-comment"></i> <a href="#blog-comments">'.count($comments).' '.$comment.'</a>'; 
			$HTML .= '</small>';
			
			// content of the post
			$HTML .= '<div class="blog-post-content">'.ConversionUtils::decoding($post->getContent()).'</div>';
			
			//SocialRing plugin
			$HTML .= system_load_plugin(array('social-ring' => array("template" =>$this->getTemplate(), "appId" => OptionManager::getInstance()->getOption("facebook-appid"))));
			
			//comments
			$HTML .= '<div>';
			$HTML .= '<div id="blog-message">';
			$HTML .= $message;
			$HTML .= '</div>';
			if(count($comments) > 0){
				$HTML .= '<h4><a id="blog-comments"></a>Commentaires</h4>';
				$HTML .= '<div id="blog-comments-div">';
				for($i=0 ; $i < count($comments) ; $i++){
					$currentComment = $comments[$i];
					$HTML .= '<div id="blog-comment-'.$currentComment->getId().'" class="blog-comment">';
					$HTML .= '<b><a href="'.URLUtils::getUserPageURL($currentComment->getUser()).'">' . $currentComment->getUser() . '</a></b>';
					$HTML .= ', <i>le ' . ConversionUtils::timestampToDatetime($currentComment->getDate()) . '</i>';
					if (RoleManager::getInstance()->hasCapabilitySession('blog-delete-comment')) {
						//$HTML .= ' - <a class="btn btn-danger btn-xs" href="'.URLUtils::generateURL($this->getModule()->getName(), array("action" => "deletecom","comment" => $currentComment->getId())).'" onclick="return(confirm(\'Etes vous certain de vouloir supprimer ce commentaire ?\'));">Supprimer</a>';
						$HTML .= ' - <a class="btn btn-danger btn-xs" href="javascript:blogDeleteComment(\''.URL.'server.php?module='.$this->getModule()->getName().'&action=deletecomment\','.$currentComment->getId().');">Supprimer</a>';
							
					}
					$HTML .= '<p>'. ConversionUtils::smiley(ConversionUtils::encoding($currentComment->getComment())) . '</p>';
					$HTML .= '</div>';
				}
				$HTML .= '</div>';
			}
			// add a comment
			if (RoleManager::getInstance ()->hasCapabilitySession ('blog-add-comment')) {
				$HTML .= '<div class="well">';
				$HTML .= '<h4>Laisser un commentaire';
				$HTML .= '<span id="blog-loading-comment" class="blog-invisible"> <img src="'.DIR_MODULE . $this->getModule()->getLocation().'view/img/loader.gif"></span>';
				$HTML .= '</h4>';
				$HTML .= '<form id="blog-comment-form" role="form" method="post" action="'.URLUtils::getCompletePageURL().'">
						    <div class="form-group">
						        <textarea class="form-control" id="blog-input-comment" name="blog-input-comment" rows="3"></textarea>
						    </div>
						    <button class="btn btn-primary" type="submit">
						        Envoyer
						    </button>
						</form>';
				$HTML .= '</div>';
			}
			$HTML .= '</div><!-- end of the comment div -->';
		$HTML .= '</div>';
		$HTML .= '</div><!-- row -->';
		
		$profile = SessionManager::getInstance()->getUserprofile();
		$js = "";
		if($profile){		
			$js = '<script>
					$( "#blog-comment-form" ).submit(function( event ) {
						blogSendComment(\''.URL.'server.php?module='.$this->getModule()->getName().'&action=sendcomment\','.$post->getId().',\''.$profile->getUsername().'\');
						event.preventDefault();
					});
				</script>';
		}
		$this->getTemplate()->addJSFooter($js);
		$this->getTemplate()->setPageSubtitle($post->getTitle());
		$this->configureLayout('page-post',$HTML);
	}
	
	
	
	public function PostList(array $posts){
		$HTML = '<div class="row">';
		for($i=0 ; $i<count($posts) ; $i++){
			$post = $posts[$i];
			$HTML .= '
			<div class="col-lg-12">
				<div class="row">
					<div class="col-lg-12">
						<h4><a href="'.URLUtils::generateURL($this->getModule()->getName(), array("post" => $post->getId())).'">'.$post->getTitle().'</a></h4>';
						$comment = ($post->getNbrcomment() == 0 ) ? "commentaire" : "commentaires";
						$HTML .= '<small>';
							$HTML .= '<i class="fa fa-user"></i> par <a href="'.URLUtils::getUserPageURL($post->getAuthor()).'">'.ucfirst($post->getAuthor()).'</a>';
							$HTML .= ' | <i class="fa fa-calendar"></i> Le '.ConversionUtils::timestampToDatetime($post->getDate(), "/");
							$HTML .= ' | <i class="fa fa-comment"></i> <a href="'.URLUtils::generateURL($this->getModule()->getName(), array("post" => $post->getId())).'">'.$post->getNbrcomment().' '.$comment.'</a>'; 
						$HTML .= '</small>';
					$HTML .= '</div>
				</div>';
			$HTML .= '<div class="blog-post-content-ellipsis ellipsis">'.$post->getContent().'...</div>';
				$HTML .= '<br><a class="btn btn-primary pull-right" href="'.URLUtils::generateURL($this->getModule()->getName(), array("post" => $post->getId())).'">&#187; Lire plus</a>
				<div class="clearfix"></div>	
			</div>
			<hr>';
			// to add tags
			//| <i class="fa fa-tags"></i> Tags : <a href="#"><span class="label label-info">Snipp</span></a>
			//<a href="#"><span class="label label-info">Bootstrap</span></a>
			//<a href="#"><span class="label label-info">UI</span></a>
			//<a href="#"><span class="label label-info">growth</span></a>
			
		
		}
		$HTML .= '</div>';
		$this->getTemplate()->setPageSubtitle("La liste des articles");
		$this->configureLayout('page-list',$HTML);
	}
	

}
