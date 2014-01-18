<?php

class FormatUtils{
	
	public static function isUrlFormat($url){
		return filter_var($url, FILTER_VALIDATE_URL);
	}
	
	public static function isDatetimeSqlFormat($date){
		return preg_match('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/',$date);
	}
	
	
	public static function startsWith($haystack, $needle){
		return $needle === "" || strpos($haystack, $needle) === 0;
	}
	
	public static function endsWith($haystack, $needle){
		return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
	}
	
}