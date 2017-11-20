<?php
/**
 * Maes Jerome
 * AdminController.class.php, created at Nov 13, 2015
 *
 */
namespace module\admin\controller;

use system\core\BlackView as BlackView;
use system\core\IrModel as IrModel;
use system\core\IrConfigParameter as IrConfig;
use system\http\Session as Session;
use module\web\controller\WebController as WebController;

use module\admin\model\Menu as Menu;

class AdminController extends WebController{
	
	const LIMIT = 20;
	
	public function indexAction(){
		return $this->render('admin.dashboard', array());
	}
	
	public function createAction($model){
		$t = \module\blog\model\BlogPost::read(array(5,7), array('title', 'date', 'user'));
		
		
		$t = BlackView::form_view_render('blog_post', \module\blog\model\BlogPost::find(5));
		
		return;
		
	}
	
	public function editAction($model, $id){
		$class_name = IrModel::get_model($model);
		
		$record = $class_name::find($id);
		
		if($this->request()->isPost()){
			$values = array();
			$params = $this->request()->params();
			$param_names = array_keys($params);
			// scalar params and many2one
			$attributes = array_keys($class_name::$attr_accessible);
			$fields = $class_name::$attr_accessible;
			foreach ($attributes as $attr){
				$field = $fields[$attr];
				// boolean field (checkbox) : if checkbox checked, it will be in the params, and its 
				// value is 'on'. Otherwise, the field is not in the params.
				if($field['type'] == 'boolean'){
					$params[$attr] = in_array($attr, $param_names) ? '1' : '0';
					array_push($param_names, $attr);
				}
				
				if(in_array($attr, $param_names)){
					$record->$attr = $params[$attr];
				}
			}
			$record->save();
			// TODO redirect with message stored in session
			//$url = $this->url_for('admin_view_form_edit', array('model' => $model, 'id' => $record->id));
			//return $this->redirect($url);
		}
		
		$rec_name = $class_name::$rec_name;
		$options = array(
			'css_class' => 'bk_admin_view_form'
		);
		
		$view_html = BlackView::form_view_render($model, $record, $options);
		
		$this->render('admin.view_form', array(
				'title' => $class_name::$name,
				'subtitle' => sprintf('Edition de %s', $record->$rec_name),
				'view_html' => $view_html,
		));
	}
	
	public function deleteAction($model, $id){
	
	}
	
	public function listAction($model, $page=1){
		$class_name = IrModel::get_model($model);
		
		$url = __BASE_URL . ADMIN_PATH .'/'. $model . '/list';
		
		$total = $class_name::search_count();
		$pager = $this->pager($url, $total, $page, 20, 100, $url_args=array());
		
		$offset = self::LIMIT * ($page - 1);
		
		$list = $class_name::find('all', array('limit' => self::LIMIT, 'offset' => $offset));
		$options = array(
			'table_class' => 'table table-striped table-bordered table-hover',
			'table_id' => 'admin_tree_'.$model,
		);
		$view_html = BlackView::tree_view_render($model, $list, $options);
		
		$this->render('admin.view_tree', array(
			'title' => $class_name::$name,
			'subtitle' => sprintf('Liste de %s a %s', $offset, $offset + self::LIMIT),
			'view_html' => $view_html,
			'pager' => $pager,
		));
	}
	
	
	/**
	 * Override render method to add backend global function
	 * @see \SlimController\SlimController::render()
	 */
	public function render($template, $data = array()){
		global $Router;
		$menus = Menu::get_root_menus();
		$website_name = IrConfig::get_param('website.name', 'Go to Website');
		$default = array(
			'menus' => $menus,
			'website_name' => $website_name,
			'admin_url' => __BASE_URL . '/' . ADMIN_PATH,
		);
		$data = array_merge($default, $data);
		return parent::render($template, $data);
	}
	
}