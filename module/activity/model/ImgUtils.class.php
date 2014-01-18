<?php

class ImgUtils{
	
	/**
	 * make the specified image file rotated with specified degree
	 * @param string $sourceFile : the path to the source image file
	 * @param string $destImageName : the path to the source image file
	 * @param int $degree : the degree of the ratation
	 */
	public static function rotation($sourceFile,$destImageName,$degreeOfRotation){
		//function to rotate an image in PHP
		//developed by Roshan Bhattara (http://roshanbh.com.np)
	
		//get the detail of the image
		$imageinfo=getimagesize($sourceFile);
		switch($imageinfo['mime']){
			//create the image according to the content type
			case "image/jpg":
			case "image/jpeg":
			case "image/pjpeg": //for IE
				$src_img=imagecreatefromjpeg("$sourceFile");
				break;
			case "image/gif":
				$src_img = imagecreatefromgif("$sourceFile");
				break;
			case "image/png":
			case "image/x-png": //for IE
				$src_img = imagecreatefrompng("$sourceFile");
				break;
		}
		//rotate the image according to the spcified degree
		$src_img = imagerotate($src_img, $degreeOfRotation, 0);
		//output the image to a file
		imagejpeg ($src_img,$destImageName);
	}
	
	
	
	/**
	 * create a thumbnail
	 * @param string $source : the source picture
	 * @param string $dest : the destination picture
	 * @param string $size : the size of the largest side of the thumbnail
	 */
	public static function createThumbnail($source,$dest,$size, $option = array(), $addtext = false){
		$n_width = $size;
		$n_height = $size;
		$new_img = imagecreatefromjpeg ( $source );
		$width = imagesx ( $new_img );
		$height = imagesy ( $new_img );
		$aspect = $width / $height;
		if ($width > $height) {
			$n_height = $n_width * (1 / $aspect);
		} else {
			$n_width = $n_height * $aspect;
		}
		$mythumb = imagecreatetruecolor ( $n_width, $n_height );
		$bcopy = imagecopyresized ( $mythumb, $new_img, 0, 0, 0, 0, $n_width, $n_height, $width, $height );
	
		if ($addtext == true) {
			$white = imagecolorallocate($mythumb, 255, 255, 255);
			$grey = imagecolorallocate($mythumb, 128, 128, 128);
			$black = imagecolorallocate($mythumb, 0, 0, 0);
	
			// Donne la bonne promo Webkot
			$actual_year = date('Y', $_SERVER['REQUEST_TIME']);
			$actual_month = date('n', $_SERVER['REQUEST_TIME']);
			if($actual_month >= 9){
				$from = $actual_year;
				$to = $actual_year + 1;
			}
			else{
				$from = $actual_year - 1;
				$to = $actual_year;
			}
	
			$text = 'Webkot ' . $from . '-' . $to;
			$font = $option['font_path'];
	
			imagettftext($mythumb, 14, 0, 6, $n_height - 4, $grey, $font, $text);
			imagettftext($mythumb, 14, 0, 5, $n_height - 5, $white, $font, $text);
		}
	
		$brender = imagejpeg ( $mythumb, $dest );
		return $brender && $bcopy;
	}
	
	
	/**
	 * get EXIF informations
	 * @param String $path
	 * @return string
	 */
	public static function getExifDatetime($path){
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