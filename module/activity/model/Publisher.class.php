<?php


class Publisher{
	
	private $target_directory;
	private $origin_directory;
	private $idactivity;
	private $pictures;
	
	private $stats;
	
	protected $options;
	
	const SESSION_VAR = "activity-publish-stat";
	
	
	public function __construct($idactivity,$origin_directory,$target_directory, $options = null){
		if(!$this->endsWith($origin_directory,'/')){
			$origin_directory .= '/';
		}
		if(!$this->endsWith($target_directory,'/')){
			$target_directory .= '/';
		}
		// hydrate the attribute
		$this->target_directory = $target_directory;
		$this->origin_directory = $origin_directory;
		$this->idactivity = $idactivity;
		$this->pictures = array();
		
		
		// options
		$this->options = array (
				"regexp_file" => '/\.(jpe?g)$/i',
				"medium_size" => 800,
				"thumb_size" => 100 ,
				"font_path" => dirname(__FILE__) . "/../fonts/Harabara.ttf",
				"file" => DIR_TMP . "activity-publishing.json",
				"file_backup" => DIR_TMP . "activity-publishing-backup.json",
				"log_path" => "activity-publishing-log.log",
				"mail_notification" => true
		);
		if ($options) {
			$this->options = array_merge($this->options, $options);
		}
		
		// initialize stats to zero
		if($this->options["mail_notification"]){
			$umanager = UserManager::getInstance();
			$users = $umanager->getListUserToMail();
			$stat = array("id" => $idactivity,"rename" => 0, "copy" => 0, "total" => 0, "currentmail" => 0, "totalmail" => count($users));
		}else{		
			$stat = array("id" => $idactivity,"rename" => 0, "copy" => 0, "total" => 0);
		}
		$this->setStat($stat);
		
	}
	
	
	public function start(){
		if(file_exists($this->options["log_path"])){
			unlink($this->options["log_path"]);
		}
		// log the informations
		error_log("INFOS\n", 3 , $this->options["log_path"]);
		error_log("\t".$this->idactivity."\n", 3 , $this->options["log_path"]);
		error_log("\t".$this->origin_directory."\n", 3 , $this->options["log_path"]);
		error_log("\t".$this->target_directory."\n", 3 , $this->options["log_path"]);
		error_log("\t".$this->options["mail_notification"]."\n", 3 , $this->options["log_path"]);
		// general process
		$this->initPictures();
		$this->renamePictures();
		$this->generatePictures();
		if($this->options["mail_notification"]){
			$this->sendMail();
		}
		// remove the thumbnail directory in the HD directory
		if(is_dir($this->origin_directory . "thumbnail/")){
			system_remove_directory($this->origin_directory . "thumbnail/");
		}
		error_log("FINISHED ! ".$this->origin_directory . "thumbnail/ removed !\n", 3 , $this->options["log_path"]);
	}
	
	
	/**
	 * init the pictures array, ordered by exif date
	 */
	protected function initPictures(){
		if ($handle = opendir($this->getOriginDirectory())) {
			$tab = array();
			while (false !== ($entry = readdir($handle))) {
				if ($entry != "." && $entry != "..") {
					if(preg_match($this->options['regexp_file'], $entry)){
						$tab[] = $entry;
					}
				}
			}
			closedir($handle);
			
			$pictures = array();
			$size=count($tab);
			for($i=0 ; $i<$size ; $i++) {
				$date = ImgUtils::getExifDatetime($this->getOriginDirectory() . $tab[$i]);
				$pictures[$tab[$i]] = $date;
			}
			asort($pictures);
			$this->setPictures($pictures);
			$this->makeStat('total', count($pictures), count($pictures));
		}
	}
	
	
	/**
	 * rename the file in the origin folder
	 */
	protected function renamePictures(){
		$pictures = $this->getPictures();
		$i = 1;
		$size = count($pictures);
		$newPictures = array();
		error_log("RENAME\n", 3 , $this->options["log_path"]);
		foreach ($pictures as $key => $value){
			$newname = str_pad($i, 4, '0', STR_PAD_LEFT);
			$ext = pathinfo($this->getOriginDirectory() . $key, PATHINFO_EXTENSION);
			rename($this->getOriginDirectory() . $key, $this->getOriginDirectory() . $newname . "." . $ext);
			error_log("     Rename HD Picture " .$this->getOriginDirectory() . $key . " --> " .$this->getOriginDirectory() . $newname . "." . $ext . "\n", 3 , $this->options["log_path"]);
			$newPictures[$newname . "." . $ext] = $value;
			$this->makeStat('rename', $size, ($i-1));
			$i++;
		}
		$this->setPictures($newPictures);
		$this->makeStat('rename', $size, $size);
	}
	
	
	protected function generatePictures(){
		//create the target directories
		mkdir($this->getTargetDirectory(),CHMOD, true);
		mkdir($this->getTargetDirectory() . "small/",CHMOD, true);
		
		$manager = PictureManager::getInstance();
		
		$i = 1;
		$pictures = $this->getPictures();
		$size = count($pictures);
		error_log("RESIZE and COPY\n", 3 , $this->options["log_path"]);
		foreach ($pictures as $key => $value){
			$t = ImgUtils::createThumbnail($this->getOriginDirectory() . $key, $this->getTargetDirectory() . $key, 800, $this->getOptions(), true);
			if($t){
				error_log("     Creation Medium Picture " .$key . "\n", 3 , $this->options["log_path"]);
			}else{
				error_log("     Creation Medium Picture " .$key . " ---> ECHEC !!\n", 3 , $this->options["log_path"]);
			}
			$t = ImgUtils::createThumbnail($this->getOriginDirectory() . $key, $this->getTargetDirectory() . "small/" . $key, 150, $this->getOptions());
			if($t){
				error_log("     Creation Thumbnail Picture " .$key . "\n", 3 , $this->options["log_path"]);
			}else{
				error_log("     Creation Thumbnail Picture " .$key . " ---> ECHEC !!\n", 3 , $this->options["log_path"]);
			}
			$this->makeStat('copy', $size, ($i-1));
			
			$time = preg_split("/[ ]/", $value);
			$manager->add($this->getIdactivity(),$key,$time[1],0,0);
			
			$i++;
			sleep(1);
		}
		$this->makeStat('copy', $size, $size);
		
		// publish the activity !
		$amanager = ActivityManager::getInstance();
		$amanager->updatePublish($this->getIdactivity(),1);
	}
	
	
	
	protected function sendMail(){
		$role = RoleManager::getInstance()->getMinrole();
		$amanager = ActivityManager::getInstance();
		$title = $amanager->getActivity($this->getIdactivity(),$role->getLevel());
		
		$umanager = UserManager::getInstance();
		$users = $umanager->getListUserToMail();
		$cpt = 0;
		error_log("SEND MAIL : ".count($users)."\n", 3 , $this->options["log_path"]);
		foreach ($users as $user){
			$subject = "Nouvelles photos mises en ligne";
			$message = "Bonjour " . $user->getUsername() . ",<br><br>";
			$message .= "L'equipe du Webkot vient de publier une nouvelle activitŽ sur le Webkot. 
					Vous pouvez consulter les " . $this->getStat()['total'] . " photos sur <a href=\"" . URL . URLUtils::generateURL('activity', array('p'=>'activity', 'id'=>$this->getIdactivity())) . "\">".$title."</a>
							<br><br> A bientot ;) <br><br><i>Le Webkot.</i>";
			
			// Pour envoyer un mail HTML, l'en-tte Content-type doit tre dŽfini
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
			$to = $user->getMail();
			
			$rep = system_send_mail($subject, $message, $to, "noreply@webkot.be", $headers);
			if($rep){
				error_log("\t ".$to." : true\n", 3 , $this->options["log_path"]);
			}else{
				error_log("\t ".$to." : false\n", 3 , $this->options["log_path"]);
			}
			$cpt++;
			$this->makeStat('currentmail', count($users), $cpt);
		}
		$this->makeStat('currentmail', count($users), count($users));
	}
	

	private function startsWith($haystack, $needle){
    	return $needle === "" || strpos($haystack, $needle) === 0;
	}
	
	
	private function endsWith($haystack, $needle){
	    return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
	}
	
	

	/**
	 * generate and save the new statistics
	 * @param unknown $field
	 * @param unknown $total
	 * @param unknown $current
	 */
	private function makeStat($field, $total, $current){
		$stat = $this->getStat();
		$stat[$field] = $current;
		$this->setStat($stat);
	}

	public function setStat(array $stat){
		$this->stats = $stat;
		unlink($this->options["file"]);
		error_log(utf8_encode(json_encode($stat)), 3 , $this->options["file"]);
		unlink($this->options["file_backup"]);
		error_log(utf8_encode(json_encode($stat)), 3 , $this->options["file_backup"]);
		/*
		$fp = fopen($this->getOptions()["file"], "w");
		fwrite($fp, utf8_encode(json_encode($stat)));
		fclose($fp);
		copy($this->getOptions()["file"], $this->getOptions()["file_backup"]);*/
	}
	public function getStat(){
		return $this->stats;
	}
	

	public function setTargetDirectory( $target_directory )
	{
		$this->target_directory = $target_directory;
	}
	
	public function setOriginDirectory( $origin_directory )
	{
		$this->origin_directory = $origin_directory;
	}
	
	public function setIdactivity( $idactivity )
	{
		$this->idactivity = $idactivity;
	}
	
	public function setOptions( $options )
	{
		$this->options = $options;
	}
	
	public function getTargetDirectory()
	{
		return $this->target_directory;
	}
	
	public function getOriginDirectory()
	{
		return $this->origin_directory;
	}
	
	public function getIdactivity()
	{
		return $this->idactivity;
	}
	
	public function getOptions()
	{
		return $this->options;
	}


	public function setPictures( $pictures )
	{
		$this->pictures = $pictures;
	}
	
	public function getPictures()
	{
		return $this->pictures;
	}


	
}