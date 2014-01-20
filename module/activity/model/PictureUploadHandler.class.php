<?php
/**
 * This class is the PictureUploadHandler : it manages the upload of image files from a form, their versions (thumbnail, medium, originals, ...), and their relation with the database
 * It was made to replace the Publisher.
 * @tutorial : 
 * 		- Project : https://github.com/blueimp/jQuery-File-Upload
 * 		- Dynamaic subdirectories : https://github.com/blueimp/jQuery-File-Upload/wiki/PHP-$_GET-dynamic-Directories
 * 		- Database Integration : https://github.com/blueimp/jQuery-File-Upload/wiki/PHP-MySQL-database-integration 
 * @uses	ActivityMananger
 * 			PictureManager
 * 			UploadHandler
 * @author jeromemaes
 * On the 19th January 2014
 */


class PictureUploadHandler extends UploadHandler {

	
	public function __construct(array $parameters) {

		$options = array();
		$options['script_url'] = $parameters['url'];
		$options['upload_dir'] = dirname($this->get_server_var('SCRIPT_FILENAME')) . "/". $parameters['directory_original'] . $parameters['activity']['directory'] . "/";
		$options['upload_url'] = $this->get_full_url() . "/" . $parameters['directory_original'] . $parameters['activity']['directory'] . "/";
		$options['activity'] = $parameters['activity'];
		
		$options['image_versions'] = array(
				// The empty image version key defines options for the original image:
				'' => array(
						// Automatically rotate images based on EXIF meta data:
						'auto_orient' => true
				),
				// Uncomment the following to create medium sized images:
				'medium' => array(
						'upload_dir' => dirname($this->get_server_var('SCRIPT_FILENAME')) . "/" . $parameters['directory_medium'] . $parameters['activity']['directory'] . "/",
						'upload_url' => $this->get_full_url() . "/". $parameters['directory_medium'] . $parameters['activity']['directory'] . "/",
						'max_width' => 800,
						'max_height' => 800
				),
				'thumbnail' => array(
						// Uncomment the following to use a defined directory for the thumbnails
						// instead of a subdirectory based on the version identifier.
						// Make sure that this directory doesn't allow execution of files if you
						// don't pose any restrictions on the type of uploaded files, e.g. by
						// copying the .htaccess file from the files directory for Apache:
						'upload_dir' => dirname($this->get_server_var('SCRIPT_FILENAME')) . "/" . $parameters['directory_medium'] . $parameters['activity']['directory'] . "/small/",
						'upload_url' => $this->get_full_url() . "/" . $parameters['directory_medium'] . $parameters['activity']['directory'] . "/small/",
						// Uncomment the following to force the max
						// dimensions and e.g. create square thumbnails:
						//'crop' => true,
						'max_width' => 150,
						'max_height' => 150
				)
		);

		parent::__construct($options);
	}

	
	/**
	 * handle the upload file : create the new name, move the file to the correct directory, and add it to the database
	 * @see UploadHandler::handle_file_upload()
	 */
	protected function handle_file_upload($uploaded_file, $name, $size, $type, $error, $index = null, $content_range = null) {
		// get the file from the normal Handler
		$file = parent::handle_file_upload($uploaded_file, $name, $size, $type, $error, $index, $content_range);
		
		// add it to the database
		if (empty($file->error)) {
			
			$idactivity = $this->options['activity']['id'];
			$filename = $file->name;
			$datetime = $this->getExifDatetime($this->options['upload_dir'] . $filename);
			$time = preg_split("/[ ]/", $datetime)[1];
			
			$pmanager = PictureManager::getInstance();
			$pmanager->add($idactivity,$filename,$time,'0','0');
			
		}
		return $file;
	}
	
	
	/**
	 * delete the file for each of its version (medium, thumbnail, ...) and delete the corresponding line in the database
	 * @see UploadHandler::delete()
	 */
	public function delete($print_response = true) {
		$response = parent::delete(false);
		
		$idactivity = $this->options['activity']['id'];
		$pmanager = PictureManager::getInstance();
		
		foreach ($response as $name => $deleted) {
			if ($deleted) { // TODO : or check if the file still exists (too heavy ?)
				$pmanager->delete($idactivity, $name);
				/*
				$sql = 'DELETE FROM `'
						.$this->options['db_table'].'` WHERE `name`=?';
				$query = $this->db->prepare($sql);
				$query->bind_param('s', $name);
				$query->execute();
				*/
			}
		}
		return $this->generate_response($response, $print_response);
	}
	
	
	
	protected function gd_create_scaled_image($file_name, $version, $options) {
		if($version == "medium"){
			list($file_path, $new_file_path) = $this->get_scaled_image_file_paths($file_name, $version);
			$success = ImgUtils::gd_create_scale_thumbnail_watermaked($file_path,$new_file_path,$options['max_width'], dirname(__FILE__)."/../fonts/Harabara.ttf", true);
		}else{
			$success = parent::gd_create_scaled_image($file_name, $version, $options);
		}
		return $success;
	}
	
	
	
	/**
	 * built the unique name of a given file in a directory. In case of 2 same names, we append "-1" to the second.
	 * @see UploadHandler::upcount_name_callback()
	 */
	protected function upcount_name_callback($matches) {
        $index = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
        $ext = isset($matches[2]) ? $matches[2] : '';
        return '-'.$index.''.$ext;
	}

	
	/**
	 * get EXIF datetime of a given file
	 * @param string $path : the path to the file
	 * @return string $datetime : the datetime in the "Y:m:d H:i:s" format
	 */
	public function getExifDatetime($path){
		//Récupération de l'heure via l'EXIF
		if (file_exists($path)) {
			$exif = exif_read_data($path,'EXIF');
			if($exif){
				if($exif["DateTimeOriginal"]){
					return $exif["DateTimeOriginal"];
				}
				return $exif["CreateDate"];
			}
			$exif = exif_read_data($path,'FILE');
			return date("Y:m:d H:i:s",$exif[FileDateTime]);
		}
	}
	
	
}
