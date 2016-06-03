<?php
/**
 * Maes Jerome
 * ModuleController.class.php, created at May 30, 2016
 *
 */
namespace module\base\controller;
use system\core\BlackController as BlackController;


class ModuleController extends BlackController{
	
	public function testAction($id){
		return $this->render('base.test', array('text' => 'CACACACA---', 'body_classname' => 'danger'));
	}
	
	public function installAction($name){
		$module = $this->env['ir_module'];
		$module::install_module($name);
		return true;
	}
	
	public function updateAction($name){
		$module = $this->env['ir_module'];
		$module::update_module([$name]);
		return true;
	}
	
	public function updateListAction(){
		$module = $this->env['ir_module'];
		$module::update_module_list();
		return true;
	}
	
}