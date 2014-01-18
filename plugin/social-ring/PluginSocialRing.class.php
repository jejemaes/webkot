<?php


class PluginSocialRing extends Plugin implements iPlugin{

	private $options;
	
	public function __construct(array $options = array()){
		
		$default = array (
				"level" => 0,
				"url" => $this->get_url(),
				"appId" => 516806348339496,
				"template" => null
		);
		$this->options = array_merge($default, $options);
	}

	public function load(){
		$html = "";
		if($this->options["level"] == 0){	
			$path = DIR_PLUGIN.'social-ring/css/style.css';
			if($this->options["template"]){
				//plugin style
				$this->options["template"]->addStyle('<link href="'.$path.'" rel="stylesheet"/>');
				
				//Google+
				$js = '<!-- Place this tag in your head or just before your close body tag. -->
		<script type="text/javascript" src="https://apis.google.com/js/plusone.js">
		  {lang: \'fr\', parsetags: \'explicit\'}
		</script>';
				$this->options["template"]->addJSHeader($js);
			}
			
			$html = '<!-- Social Ring Buttons Start -->
			<div class="social-ring"><br>
				<div class="social-ring-button">
					'.$this->twitter_button().'
				</div>
				
				<div class="social-ring-button">
					'.$this->google_plus_button().'
				</div>					
							
				<div class="social-ring-button">
					'.$this->facebook_share_button().'
				</div>
							
				<div class="social-ring-button">
					'.$this->facebook_like_button().'
				</div>
			</div>
			<div style="clear:both;">&nbsp;</div><!-- Social Ring Buttons End -->';
		}
	
		return $html;
		
	}

	
	private function facebook_like_button(){
		$js = '<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/fr_FR/all.js#xfbml=1&appId='.$this->options["appId"].'";
  fjs.parentNode.insertBefore(js, fjs);
}(document, \'script\', \'facebook-jssdk\'));</script>';
		//$this->getTemplate()->addJSHeader($js);
		
		//$html = '<iframe src="http://www.facebook.com/plugins/like.php?href='.urlencode($this->options["url"]).'&amp;width=450&amp;height=80&amp;colorscheme=light&amp;layout=standard&amp;action=like&amp;show_faces=true&amp;send=true&amp;appId=342667409169839" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:450px; height:80px;" allowTransparency="true"></iframe>';
		$html = '<iframe src="//www.facebook.com/plugins/like.php?href='.urlencode($this->options["url"]).'&amp;width=50&amp;height=21&amp;colorscheme=light&amp;layout=button_count&amp;action=like&amp;show_faces=true&amp;send=false&amp;appId='.$this->options["appId"].'" scrolling="no" frameborder="0" style="border:none; overflow:hidden; height:21px;" allowTransparency="true"></iframe>';
		//$html = '<div class="fb-like" data-href="http://www.webkot.be" data-width="450" data-show-faces="true" data-send="false"></div>';
		return $html;
	}
	
	
	private function facebook_share_button(){
		$html = '<a href="'.$this->options["url"].'" 
			  onclick="
			    window.open(
			      \'https://www.facebook.com/sharer/sharer.php?u=\'+encodeURIComponent(\''.$this->options["url"].'\'), 
			      \'facebook-share-dialog\', 
			      \'width=626,height=436\'); 
			    return false;" class="social-ring-facebook-share-button">
			  Share
			</a>';
		return $html;
	}
	
	
	private function google_plus_button(){		
		$html = '<!-- Place this tag where you want the +1 button to render. -->
		<div class="g-plusone" data-size="medium" data-href="'.$this->options["url"].'"></div>
		<!-- Place this render call where appropriate. -->
		<script type="text/javascript">gapi.plusone.go();</script>';
		return $html;
	}
	
	
	private function twitter_button(){
		return '<a href="https://twitter.com/share" class="twitter-share-button" data-url="'.$this->options["url"].'" data-lang="fr">Twitter</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>';
	}
	

	
	
	
	
	/**
	 * generate the Absolute URL of the page when the Exception is raised
	 * @@return string $url;
	 */
	private function get_url(){
		$pageURL = 'http';
		if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
	
		return $pageURL;
	}

}