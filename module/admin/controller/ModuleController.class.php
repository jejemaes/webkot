<?php
/**
 * Maes Jerome
 * ModuleController.class.php, created at Dec 3, 2017
 *
 */
namespace module\admin\controller;

use system\core\IrModel as IrModel;
use system\core\IrModule as IrModule;
use system\core\IrConfigParameter as IrConfig;
use module\web\controller\WebController as WebController;

class ModuleController extends WebController{
	
	public function updateSystemAction(){
		global $app;
		$app->update_core_system();
	}
	
	public function updateAction($module){
		$module = IrModule::get_module($module);
		$module->do_update();
	}
	
}
