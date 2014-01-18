<?php

class WidgetLikeBox extends Widget implements iWidget{
	
	/**
	 * (non-PHPdoc)
	 * @see iWidget::__toString()
	 */
	public function __toString(){
		
		$omanager = OptionManager::getInstance();
		$fbappid = $omanager->getOption("facebook-appid");
		$fbpage = $omanager->getOption("facebook-page");
		
		return '<iframe src="//www.facebook.com/plugins/likebox.php?href='.urlencode($fbpage).'&amp;width=292&amp;height=258&amp;colorscheme=light&amp;show_faces=true&amp;header=false&amp;stream=false&amp;show_border=true&amp;appId='.$fbappid.'" scrolling="no" frameborder="0" style="background-color : white; border:none; margin-left:5% ;overflow:hidden; width:90%; height:258px;" allowTransparency="true"></iframe>';
	}
	
}