<?php
/**
 * Maes Jerome
 * BlackView.class.php, created at Sep 22, 2015
 *
 */
namespace system\core;

use system\core\BlackView as BlackView;

class BlackControllerView extends \Slim\View {
		
	protected function render($template, $data = array()){
		$data = array_merge($this->data->all(), (array) $data);
		return BlackView::template_render($template, $data);
	}
	
}
