<?php
namespace module\gossip\controller;

use module\website\controller\WebsiteController as WebsiteController;
use module\gossip\model\Gossip as Gossip;
use module\gossip\model\GossipManager as GossipManager;
use system\exception\SQLException;


class GossipController extends WebsiteController{
	
	public static $gossip_per_page = 30;
	
	public function pageGossipListAction($page=1){
		$manager = GossipManager::getInstance();
		
		$page = intval($page);
		$limit = static::$gossip_per_page;
		$offset = ($page-1)*$limit;
		
		$total = $manager->getCountGossip();
		$num_page = ceil($total / $limit);
		$gossips = $manager->getListGossip($offset, $limit);
		
		$pager = array();
		for($i=1;$i <= $num_page; $i++){
			$pager[$i] = $this->url_for('page_gossip_list', array('page' => $i));
		}
		
		return $this->render('gossip.page_gossip_list', array(
				'gossips' => $gossips,
				'pager' => $pager,
				'page' => $page,
				'total_page' => $num_page,
				'website_title' => 'Potins',
		));
	}
	
	public function pageGossipAction($gossip_id){
		$message = new Message(1);
	
		return $message;
	}
	
	public function likeGossipAction($action, $gossip_id){
		$manager = GossipManager::getInstance();
		$user = $this->session()->user;
		try{
			$rep = false;
			if($action == 'like'){
				$rep = $manager->like($gossip_id, $user->id);
			}else{
				$rep = $manager->dislike($gossip_id, $user->id);
			}
		}catch (SQLException $e){
			return $this->error_page($e);
		}
		$redirect_url = $this->request()->getReferrer() . '#gossip-' . $gossip_id;
		return $this->redirect($redirect_url, 301);
	}

}