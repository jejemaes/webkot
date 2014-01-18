<?php



class VideoManager {
	
	protected static $_instance;
	private $_db; // Instance of Database
	private $_apc;
	
	const APC_VIDEO_LIST = 'video-list';

	/**
	 * getInstance 
	 * @return VideoManager $instance : the instance of VideoManager
	 */
	public static function getInstance(){
		if (!isset(self::$_instance)) {
			$c = __CLASS__;
			self::$_instance = new $c;
			self::$_instance->__construct();
		}
		return self::$_instance;
	}
	
	/**
	 * Constructor
	 */
	public function __construct(){
		$this->_db = Database::getInstance();
		$this->_apc = ((extension_loaded('apc') && ini_get('apc.enabled')) ? true : false);
    }
    
    
    
    public function getVideo($vid){
    	$feedURL = 'https://gdata.youtube.com/feeds/api/videos/'.$vid.'?v=2';
    	$sxml = simplexml_load_file($feedURL);
    	if($sxml){
    		$video = $this->parse($sxml);
    		return $video;
    	}
    	throw new NullObjectException();
    }
    
    /**
     * 
     * @param string $ytUserId
     * @return array $videos : key-array of Video Object
     */
    public function getListVideos($ytUserId){	
    	//TODO
    	//get the number of video total with http://gdata.youtube.com/feeds/api/users/jejemaes (number is 41)	
    	//apply the request wiht max-resutlt = #nbr
    	// if the number isn't the same than the size of the array of Video Object in cache, then relaod it !
    	//+ add throw in case of error
    	if($this->_apc && apc_exists(self::APC_VIDEO_LIST)){
    		return apc_fetch(self::APC_VIDEO_LIST);
    	}else{		
	    	$videos = array();
	    	$feedURL = 'https://gdata.youtube.com/feeds/api/users/'.$ytUserId.'/uploads?max-results=50';
	    	$sxml = simplexml_load_file($feedURL);
	    	$i=0;
	    	foreach ($sxml->entry as $entry) {
	    		$video = $this->parse($entry); 		
	    		$videos[$video->getId()] = $video;
	    	}
	    	if($this->_apc){
	    		apc_store(self::APC_VIDEO_LIST, $videos, 86000);
	    	}
	    	return $videos;
    	}
    }
    
    
    
    public function getLastVideo($ytUserId){
    	$videos = $this->getListVideos($ytUserId);
    	foreach ($videos as $v){
    		return $v;
    	}
    	throw new NullObjectException();
    }
    
    
    public function getSelectionList($ytUserId,$startIndex, $number){
    	$videos = $this->getListVideos($ytUserId);
    	return array_slice($videos, $startIndex, $number);
    }
    
    
    public function getCountVideos($ytUserId){
    	$videos = $this->getListVideos($ytUserId);
    	return count($videos);
    }
    
    
    
    public function flushApc(){
    	if($this->_apc && apc_exists(self::APC_VIDEO_LIST)){
	    	return apc_delete(self::APC_VIDEO_LIST);
    	}else{
	    	return false;
    	}
    }
    
    
    
    /**
     * extract the Identifier of the Youtube URL, or the id tag of the specified video
     * @param string $idtag : a string
     * @return string $id : the identifier of the video
     */
    private function getVideoId($idtag){
    	$pattern = "/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i";
    	if(preg_match($pattern, $idtag)){
    		$arr = preg_split("/[\/]+/", $idtag);
    		return (string)$arr[count($arr)-1];
    	}
    	// for the tag from $this->getVideo();
    	$arr = preg_split("/[:]+/", $idtag);
    	return (string)$arr[count($arr)-1];
    }
    
    /**
     * parse an entry of the XML file from Youtube, and create the Video Object
     * @param XML Element $entry
     * @return Video 
     */
    private function parse($entry){
    	$stat = $entry->children('yt', true);
    	$media = $entry->children('media', true);
    	$watch = (string)$media->group->player->attributes()->url;
    	
    	$vid = $this->getVideoId($entry->id);
    	
    	$param = array();
    	$param['id'] = $vid;
    	$param['title'] = (string)$entry->title;
    	$param['description'] = (string)$media->group->description;//(string)$entry->content;
    	$param['publisheddate'] = (string)date("d/m/Y", strtotime($entry->published));
    	$param['author'] = (string)$entry->author->name;
    	$param['view'] = (string)$stat->statistics->attributes()->viewCount;
    	$param['duration'] = (string)$media->group->children('yt', true)->duration->attributes()->seconds;
    	$param['thumbnail'] = (string)$media->group->thumbnail[0]->attributes()->url;
    	
    	return new Video($param);
    }
    
    
}
?>