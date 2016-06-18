<?php
/**
 * Maes Jerome
 * UserController.class.php, created at Jun 6, 2016
 *
 */
namespace module\website\controller;
use module\website\controller\WebsiteController;


class UserController extends WebsiteController{
	
	const item_per_page = 30;
	
	public function indexAction($page=1){
		$user = $this->env['user'];
		$total = $user::count_public_user();
		$limit = self::item_per_page;
		
		$pager = $this->pager('user/', $total, $page, $limit, 20);
		
		$values = [
			'user_list' => $user::get_public_users($pager['offset'], $limit),
			'pager' => $pager,
		];
		
		return $this->render('website.user_list', $values);
	}
	
	public function profileAction($userid){
		$user = $this->env['user'];
		$profile = $user::browse($userid);
		
		$values = [
			'user' => $profile,
		];
		return $this->render('website.user_profile', $values);
	}
}