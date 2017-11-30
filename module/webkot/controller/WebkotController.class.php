<?php
/**
 * Maes Jerome
 * WebkotController.class.php, created at Nov 30, 2017
 *
 */

namespace module\webkot\controller;

use module\website\controller\WebsiteController as WebsiteController;
use module\webkot\model\WebkotteurManager as WebkotteurManager;


class WebkotController extends WebsiteController{

	public function webkotAction(){
		$manager = WebkotteurManager::getInstance();
		$team = $manager->getYoungestTeam();
		$team_years = $manager->getListYear();
		
		return $this->render('webkot.index_page', array(
				'team' => $team,
				'team_years' => $team_years,
				'website_title' => 'Webkot',
		));
	}

	public function webkotTeamsAction(){
		$manager = WebkotteurManager::getInstance();
		$members = $manager->getAllOldWebkotTeam();

		$teams = array();
		foreach ($members as $m){
			$year = $m->year;
			if(!array_key_exists($year, $teams)){
				$teams[$year] = array();
			}
			$teams[$year][] = $m;
		}
		return $this->render('webkot.teams_page', array(
				'teams' => $teams,
				'website_title' => 'Les Vieux du Webkot',
		));
	}
	
}