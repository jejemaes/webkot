<?php
/*
 * Created on 13 aug. 2013
 *
 * MAES Jerome, Webkot 2012-2013
 * Class description : representing a picture, with all the comment about it.
 *
 * Convention : setters & getters begin with a capital letter (important for hydratate)
 * 				same attribute names as in the DB
 */
 
 class Picture extends AbstractPicture{
 	


	// all the comment about the current picture. Not added with the constructor.
	private $comments;
	private $directory;
	


	public function setComments( $comments ){
		$this->comments = $comments;
	}
	
	public function getComments(){
		return $this->comments;
	}
	


	public function setDirectory( $directory ){
		$this->directory = $directory;
	}
	
	public function getDirectory(){
		return $this->directory;
	}
	
	
	
	
	
	// the number of comment
	private $nbcomments;
	
	
	
	public function setNbcomments( $nbcomments ){
		$this->nbcomments = $nbcomments;
	}
	
	public function getNbcomments(){
		return $this->nbcomments;
	}
 	
 }
 
 
?>
