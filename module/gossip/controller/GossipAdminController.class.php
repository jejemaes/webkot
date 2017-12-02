<?php


class GossipAdminController{
	
	
	/**
	 * delete a given Gossip
	 * @param array $request : the REQUEST variables containing the Gossip identifier
	 * @return Message $message : the return Message
	 */
	public static function deleteAction(array $request){
		$message = new Message(1);
	
		return $message;
	}
	
	/**
	 * censure a given Gossip
	 * @param array $request : the REQUEST variables containing the Gossip identifier
	 * @return Message $message : the return Message
	 */
	public static function censureAction(array $request){
		$message = new Message(1);
	
		return $message;
	}
	
	
	/**
	 * uncensure a given Gossip
	 * @param array $request : the REQUEST variables containing the Gossip identifier
	 * @return Message $message : the return Message
	 */
	public static function uncensureAction(array $request){
		$message = new Message(1);
	
		return $message;
	}
	
	
	/**
	 * like a given Gossip, by a given User
	 * @param array $request : the REQUEST variables containing the User identifier and the Gossip identifier
	 * @return Message $message : the return Message
	 */
	public static function likeAction(array $request){
		$message = new Message(1);
		
		return $message;
	}
	
	
	/**
	 * unlike a given Gossip, by a given User
	 * @param array $request : the REQUEST variables containing the User identifier and the Gossip identifier
	 * @return Message $message : the return Message
	 */
	public static function unlikeAction(array $request){
		$message = new Message(1);
	
		return $message;
	}
	
	/**
	 * dislike a given Gossip, by a given User
	 * @param array $request : the REQUEST variables containing the User identifier and the Gossip identifier
	 * @return Message $message : the return Message
	 */
	public static function dislikeAction(array $request){
		$message = new Message(1);
	
		return $message;
	}
	
	/**
	 * undislike a given Gossip, by a given User
	 * @param array $request : the REQUEST variables containing the User identifier and the Gossip identifier
	 * @return Message $message : the return Message
	 */
	public static function undislikeAction(array $request){
		$message = new Message(1);
	
		return $message;
	}
	
}