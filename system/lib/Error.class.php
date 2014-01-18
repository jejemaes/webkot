<?php


class Error{
	
	private $title;
	private $description;
	
	
	public function __construct($title, $descri){
		$this->title = $title;
		$this->description = $descri;
	}
	
	
	public function __toString(){
		$err = '<div class="error">';
		$err .= '<p><strong>'.$this->getTitle().'</strong><br clear>' . $this->getDescription() . '<br clear><p><i>Le '.date('Y-m-d', time()).' &agrave; '. date('H:i:s', time()) .'</i>';
		$err .= '<p class="right"><a href="javascript:history.back()">Retour a la page precedente</a></p>';
		$err .= '</div>';
		return $err;
	}


	public function setTitle( $title )
	{
		$this->title = $title;
	}
	
	public function setDescription( $description )
	{
		$this->description = $description;
	}
	
	public function getTitle()
	{
		return $this->title;
	}
	
	public function getDescription()
	{
		return $this->description;
	}
	
}