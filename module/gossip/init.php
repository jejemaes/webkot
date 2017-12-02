<?php
/**
 * Maes Jerome
 * init.php, created at Dec 1, 2017
 *
 */
global $Router;

// route : link frontend
$controller = 'module\gossip\controller\GossipController';
$Router->addRoute('/gossip(/:page)', $controller . ':pageGossipListAction', 'GET', 'public', array('page' => '[1-9]*'), 'page_gossip_list');
$Router->addRoute('/gossip/:gossip_id', $controller . ':pageGossipAction', 'GET', 'public', array('gossip_id' => '[1-9]*'), 'page_gossip');
$Router->addRoute('/gossip/:action/:gossip_id', $controller . ':likeGossipAction', 'GET', 'user', array('action' => '[a-z]*', 'gossip_id' => '[1-9]*'), 'action_gossip_like');
