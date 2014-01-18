<?php



class BlogManager {
	
	protected static $_instance;
	private $_db; // Instance of Database
	private $_apc;
	
	const APC_BLOG_POST = 'blog-post-';
	

	/**
	 * getInstance 
	 * @return BlogManager $instance : the instance of BlogManager
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
    
    
    
    /**
     * Add an BlogPost in the DB
     * @param string $title : the title of hte Post
     * @param string $content : the content of hte Post
     * @param int  $userid : the identfier of the User
     * @return boolean $b : true if the Activity was added, false otherwise
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function addPost($title,$content,$userid){
        try {
        	$sql = "INSERT INTO blog_post (title,content,userid) VALUES (:title, :content, :userid)";
        	echo $sql;
	        $stmt = $this->_db->prepare($sql);
        	$stmt->execute(array( 'title' => $title, 'content' => $content, 'userid' => $userid));
	        if($stmt->errorCode() != 0){
		        $error = $stmt->errorInfo();
	        	throw new SQLException($error[2], $error[0], $sql, "Impossible d'ajouter un Post");
	        	return false;
	        }
	        return true;     
        }catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'ajouter un Post");
        	return false;
        }
    }
     
     /**
     * Add an BlogComment in the DB
     * @param int $postid : the identifier of hte Post
     * @param int  $userid : the identfier of the User
     * @param string $comment : the comment
     * @param string $ip : the ip address of the connected User
     * @return boolean $b : true if the Activity was added, false otherwise
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function addComment($postid,$userid,$comment,$ip){
        try {
        	$sql = "INSERT INTO blog_comment (postid,userid,comment,ip) VALUES (:postid, :userid, :comment, :ip)";
	        $stmt = $this->_db->prepare($sql);
        	$stmt->execute(array( 'postid' => $postid, 'userid' => $userid, 'comment' => $comment, 'ip' => $ip));
	        if($stmt->errorCode() != 0){
		        $error = $stmt->errorInfo();
	        	throw new SQLException($error[2], $error[0], $sql, "Impossible d'ajouter un Commentaire");
	        }
	        if($this->_apc){
	        	apc_delete(self::APC_BLOG_POST . $postid);
	        }
	        return true;//$this->_db->lastInsertId();     
        }catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'ajouter un Commentaire");
        	return false;
        }
    }
    
    
    /**
     * obtain a given comment
     * @param unknown $cid
     * @throws SQLException
     * @throws NullObjectException
     * @throws DatabaseException
     * @return BlogComment
     */
    public function getComment($cid){
    	try {
    		$sql = "SELECT C.id as id, C.timestamp as date, C.comment as comment, C.postid as postid, C.userid as user FROM blog_comment C WHERE C.id = :id LIMIT 1";
    		$stmt = $this->_db->prepare($sql);
    		$stmt->execute(array( 'id' => $cid));
    		if($stmt->errorCode() != 0){
    			$error = $stmt->errorInfo();
    			throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir un commentaire specifie");
    		}
    		$data = $stmt->fetch(PDO::FETCH_ASSOC);
    		if(empty($data)){
    			throw new NullObjectException();
    		}
    		$comm = new BlogComment($data);
    		return $comm;
    	}catch(PDOException $e){
    		throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir un commentaire specifie");
    	}
    }
    
     
    /**
     * get a specified BlogPost
     * @param int $id : the identifier of the BlogPost
     * @return BlogPost $post : the specified BlogPost
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     * @throws NullObjectException : this exception is raised when the specified Object didn't exist
     */
    public function getPost($id){
    	if($this->_apc && apc_exists(self::APC_BLOG_POST . $id)){
    		return apc_fetch(self::APC_BLOG_POST . $id);
    	}else{  		
	    	try {
	    		$sql = "SELECT P.id as id, P.timestamp as date, P.content as content, U.username as author, P.title as title FROM blog_post P, user U WHERE P.id = :id AND U.id = P.userid  LIMIT 1";
		       	$stmt = $this->_db->prepare($sql);
		        $stmt->execute(array( 'id' => $id));
				if($stmt->errorCode() != 0){
				    $error = $stmt->errorInfo();
			        throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir un post specifie");
			    }
				$data = $stmt->fetch(PDO::FETCH_ASSOC);
				if(empty($data)){
					throw new NullObjectException();
				}
		        $post = new BlogPost($data);
		        $post->setComments($this->getListCommentPost($id));
		        if($this->_apc){
		        	apc_store(self::APC_BLOG_POST . $id, $post, 86000);
		        }
		     	return $post;
	    	}catch(PDOException $e){
	        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir un post specifie");
	        }	
    	}
    }
    
      
    /**
     * get the latest BlogPost
     * @return BlogPost $post : the latest BlogPost
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     * @throws NullObjectException : this exception is raised when the specified Object didn't exist
     */
    public function getLastListPost($nbr){
    	try {
    		$sql = "SELECT P.id as id, P.timestamp as date, P.content as content, P.userid as author, P.title as title FROM blog_post P WHERE 1 order by P.timestamp desc limit 0," . $nbr;
	       	$stmt = $this->_db->prepare($sql);
	        $stmt->execute();
			if($stmt->errorCode() != 0){
			    $error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir les derniers posts");
		    }
    		$list = array();
	         while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){   	
	         	$list[] = new BlogPost($data);
	         } 
	     	return $list;
    	}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir les derniers posts");
        }	
    }
    
    /**
     * get the latest BlogPost
     * @return BlogPost $post : the latest BlogPost
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     * @throws NullObjectException : this exception is raised when the specified Object didn't exist
     */
    public function getLastPost(){
    	try {
    		$sql = "SELECT P.id as id, P.timestamp as date, P.content as content, U.username as author, P.title as title FROM blog_post P, user U WHERE U.id = P.userid AND P.timestamp IN (SELECT max(timestamp) FROM blog_post)";
    		$stmt = $this->_db->prepare($sql);
    		$stmt->execute();
    		if($stmt->errorCode() != 0){
    			$error = $stmt->errorInfo();
    			throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir le dernier post");
    		}
    		$data = $stmt->fetch(PDO::FETCH_ASSOC);
    		if(empty($data)){
    			throw new NullObjectException();
    		}
    		$post = new BlogPost($data);
    		return $post;
    	}catch(PDOException $e){
    		throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir le dernier post");
    	}
    }
    
    
      
    /**
     * get the list of all the Posts
     * @return array $list : array of BlogPost Object
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function getListPost(){
      	try{
	    	 $sql = "(SELECT P.id as id, U.username as author, P.title as title, P.content as content, P.timestamp as date , count(C.id) as nbrcomment FROM blog_post P, user U, blog_comment C WHERE P.userid = U.id AND C.postid = P.id GROUP BY C.postid) UNION (SELECT P.id as id, U.username as author, P.title as title, P.content as content, P.timestamp as date , 0 as nbrcomment FROM blog_post P, user U WHERE P.userid = U.id AND P.id NOT IN  (SELECT X.id FROM blog_post X WHERE EXISTS (SELECT * FROM blog_comment Y WHERE Y.postid=X.id))) ORDER BY date DESC";
	         $stmt = $this->_db->prepare($sql);
	         $stmt->execute();
	         if($stmt->errorCode() != 0){
			    $error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste des posts");
		     }    
	    	 $list = array();
	         while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){   	
	         	$list[] = new BlogPost($data);
	         }   
	         return $list;
      	}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir la liste des posts");
        }
    }
    
       
    /**
     * get the list of all the BlogComment for a specifed Post
     * @param int $postid : the identifier of the Post
     * @return array $list : array of BlogComment Object
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function getListCommentPost($postid){	
	      	try{
		    	 $sql = "SELECT C.id as id, C.timestamp as date, C.comment as comment, U.username as user, C.postid as postid FROM blog_comment C, user U WHERE C.postid = :id AND U.id = C.userid ORDER BY C.timestamp ASC";
		         $stmt = $this->_db->prepare($sql);
		         $stmt->execute(array('id' => $postid));
		         if($stmt->errorCode() != 0){
				    $error = $stmt->errorInfo();
			        throw new SQLException($error[2], $error[0], $sql, "Impossible d'obtenir la liste des commentaires");
			     }    
		         $i = 0;
		    	 $list = array();
		         while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){   	
		         	$list[] = new BlogComment($data);
		         }
		      	return $list;
	      	}catch(PDOException $e){
	        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'obtenir la liste des blogcomments");
	        }
    	
    }
    
    /**
     * update a Post
     * @param int $id : the identifier of the Post
     * @param string $title : the title of the Post
     * @param string $content : the content of the Post
     * @return boolean $b : true if the update was a success 
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function updatePost($id, $title, $content){
    	try{
	 		$sql = "UPDATE blog_post SET title = :title, content = :content  WHERE id=:id";
 			$stmt = $this->_db->prepare($sql);
	        $n = $stmt->execute(array('title' => $title, 'content' => $content, 'id' => $id));
	        if($stmt->errorCode() != 0){
				$error = $stmt->errorInfo();
		        throw new SQLException($error[2], $error[0], $sql, "Impossible d'effectuer la mise a jour");
		    }
		    if($this->_apc){
		    	apc_delete(self::APC_BLOG_POST . $id);
		    }
			return ($n > 0);
 		}catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible d'effectuer la mise a jour");
        }
    }
    
    /**
     * Delete an BlogComment Object
     * @param int $aid : the identifier of the BlogComment to remove
     * @return boolean $b : true if the removing was successful
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function deleteComment($comid){
    	try {
	    	if($this->_apc){
	    		$comm = $this->getComment($comid);
		       	apc_delete(self::APC_BLOG_POST . $comm->getPostid());
		   	}
        	$sql = "DELETE FROM blog_comment WHERE id= :id";
	        $stmt = $this->_db->prepare($sql);
        	$stmt->execute(array( 'id' => $comid));
        	if($stmt->errorCode() != 0){
		        $error = $stmt->errorInfo();
	        	throw new SQLException($error[2], $error[0], $sql, "Impossible de supprimer un commentaire");
	        }
	        return true;      
        }catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible de supprimer un commentaire");
        	return false;
        }
    }
    
    
     /**
     * Delete an BlogPost Object
     * @param int $aid : the identifier of the BlogPost to remove
     * @return boolean $b : true if the removing was successful
     * @throws SQLException : this exception is raised if the Query is refused
     * @throws DatabaseException : this exception is raised if the PreparedStatement can't be made
     */
    public function deletePost($pid){
    	try {
        	$sql = "DELETE FROM blog_post WHERE id= :id";
	        $stmt = $this->_db->prepare($sql);
        	$stmt->execute(array( 'id' => $pid));
        	if($stmt->errorCode() != 0){
		        $error = $stmt->errorInfo();
	        	throw new SQLException($error[2], $error[0], $sql, "Impossible de supprimer un post");
	        }
	        if($this->_apc){
	        	apc_delete(self::APC_BLOG_POST . $pid);
	        }
	        return true;      
        }catch(PDOException $e){
        	throw new DatabaseException($e->getCode(), $e->getMessage(), "Impossible de supprimer un post");
        	return false;
        }
    }
}
?>