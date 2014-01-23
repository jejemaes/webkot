<?php

class WidgetFollowus extends Widget implements iWidget{
	
	/**
	 * (non-PHPdoc)
	 * @see iWidget::__toString()
	 */
	public function __toString(){
		try{	
			$omanager = OptionManager::getInstance();
			$fb = $omanager->getOption("facebook-page");
			
			$html = '<a href="'.URL.RSS_FILE.'"><img src="img/social/rss-icone.png" class="followus_logo" alt="followus-RSS"></a>
					<a href="'.$fb.'"><img src="img/social/facebook-icone.png" class="followus_logo" alt="followus-Facebook"></a>
							<a href="http://www.twitter.com"><img src="img/social/twitter-icone.png" class="followus_logo" alt="followus-Twitter"></a>';
			$html = '<ul class="list-unstyled">
						<li><a href="'.URL.RSS_FILE.'" class="hover-orange"><i class="fa fa-rss-square fa-fw fa-2x"></i> Flux RSS</a></li>
						<li><a href="'.$fb.'" class="hover-dark-blue"><i class="fa fa-facebook-square fa-fw fa-2x"></i> Facebook</li>
						<li><a href="http://www.twitter.com" class="hover-turquoise"><i class="fa fa-twitter-square fa-fw fa-2x"></i> Twitter</a></li>
						<li><a href="http://www.youtube.com/user/LeWebkot" class="hover-red"><i class="fa fa-youtube-square fa-fw fa-2x"></i> Youtube</a></li>
					</ul>';
			
			return $html;
		}catch (Exception $e){
			return '<p class="text-danger">Erreur interne du Widget.</p>';
		}
	}
	
}