<?php
/*
 * Conventions :
 * 		- timestamp = date and hour in the format YYYY-MM-DD HH:ii:ss
 * 		- timestampUnix : the number of second from 1st January 1970
 * 		- date : YYYY-MM-DD
 * 
 * 		- datetime : the date followed by the time (hour) in the format : DD/MM/YYYY � HH:ii
 * 		- dateshort : the date in the format DD/MM (no Years)
 * 		- dateFr : the date in the format DD/MM/YYYY
 */
class ConversionUtils {

    /**
     * conversion from timestamp (unix) to datetime
     * @param string $timestamp : the Unix timestamp
     * @return string $date : the date in the datetime format
     */
    public static function timestampUnixToDatetime($timestamp){
 		return date('d.m.Y', $timestamp).' &agrave; '.date('H:i', $timestamp);
 	}
 	
 	/**
 	 * conversion from a timestamp SQL to the Unix Timestamp
 	 */
 	public static function timestampTotimestampUnix($timestamp){
 		$dt = preg_split("/[ ]+/",$timestamp); 
	    
	    $split = preg_split("/[-]+/",$dt[0]); 
	    $annee = $split[0]; 
	    $mois = $split[1]; 
	    $jour = $split[2]; 
	    
	    $split = preg_split("/[:]+/",$dt[1]); 
	    
	    return mktime($split[0],$split[1],$split[2],$mois,$jour,$annee);// "$jour"."."."$mois".".".$annee . ' &agrave; ' . $split[0] . 'h' . $split[1]; 
 	}
 	
 	/**
 	 * conversion from a time (hh:mm:ss) to the time in french (HH'h'mm)
 	 */
 	public static function timeToTimefr($time){
 		$split = preg_split("/[:]+/",$time); 
 		return $split[0] .'h' . $split[1];
 	}
 	
 	
 	/**
 	 * transform a given date into a given format
 	 * @param string $date : the date (whatever its format)
 	 * @param string $format : the format, with the syntax of PHP
 	 * @return string : the $date in the given $format
 	 */
 	public static function transformDate($date, $format){
 		$d = date_parse($date);
 		return date($format, mktime($d['hour'], $d['minute'], $d['second'], $d['month'], $d['day'], $d['year']));
 	}
 	
    /**
     * conversion from timestamp to datetime
     * @param string $timestamp : the SQL timestamp
     * @return string $date : the date in the datetime format
     */
    public static function timestampToDatetime($timestamp, $separator = '.'){
	    $dt = preg_split("/[ ]+/",$timestamp); 
	    
	    $split = preg_split("/[-]+/",$dt[0]); 
	    $annee = $split[0]; 
	    $mois = $split[1]; 
	    $jour = $split[2]; 
	    
	    $split = preg_split("/[:]+/",$dt[1]); 
	    
	    return $jour . $separator . $mois . $separator . $annee . ' &agrave; ' . $split[0] . 'h' . $split[1]; 
    }
 	
   
 	
    /**
     * conversion from a date to dateFr
     * @param string $date : the date in the format YYY-MM-DD
     * @return string $dateFr : the date in the format dateFr
     */
    public static function dateToDateFr($date, $separator = '.'){
	    $split = preg_split("/[-]+/",$date); 
	    $annee = $split[0]; 
	    $mois = $split[1]; 
	    $jour = $split[2]; 
	    return $jour.$separator.$mois.$separator.$annee; 
    }
    
    public static function dateToDateRSS($date){
    	$split = preg_split("/[-]+/",$date);
    	$annee = $split[0];
    	$mois = $split[1];
    	$jour = $split[2];
    	return "$jour"."/"."$mois"."/".$annee . " 00:00";
    }

    
    /**
     * Conversion from date to dateshort
     * @param string $date : the date in the Date format
     * @return string $date : the date in the dateshort format
     */
    public static function dateToDateshort($date){
	    $split = preg_split("/[-]+/",$date); 
	    $annee = $split[0]; 
	    $mois = $split[1]; 
	    $jour = $split[2]; 
	    return "$jour"."/"."$mois"; 
    }
    
    
    
    
    
    /**
     * return the day of a date
     * @param string $date : the date in the date format
     * @return string $day : the day of hte date
     */
    public static function getDateDay($date){
    	$split = preg_split("/[-]+/",$date);
    	return $split[2];
    }
    
    /**
     * return the month of a date
     * @param string $date : the date in the date format
     * @return string $month : the month of hte date
     */
    public static function getDateMonth($date){
    	$split = preg_split("/[-]+/",$date);
    	return $split[1];
    }
    
    /**
     * return the year of a date
     * @param string $date : the date in the date format
     * @return string $year : the year of hte date
     */
    public static function getDateYear($date){
    	$split = preg_split("/[-]+/",$date);
    	return $split[0];
    }
    
    
    public static function smiley($text){
    	$smiley[0]=':)'; $img[0]='<img class="smiley" src="img/smilies/icon_smile.gif" title=":)" alt=":)" />';
		$smiley[1]=':-)'; $img[1]='<img class="smiley" src="img/smilies/icon_lol.gif" title=":-)" alt=":-)" />';
		$smiley[2]=':P'; $img[2]='<img  class="smiley" src="img/smilies/icon_razz.gif" title=":P" alt=":P"/>';
		$smiley[3]=':p'; $img[3]='<img class="smiley" src="img/smilies/icon_razz.gif" title=":P" alt=":P" />';
		$smiley[4]=':D'; $img[4]='<img class="smiley" src="img/smilies/icon_biggrin.gif" title=":D" alt=":D" />';
		$smiley[5]=':('; $img[5]='<img class="smiley" src="img/smilies/icon_sad.gif" title=":(" alt=":(" />';
		$smiley[6]=';('; $img[6]='<img class="smiley" src="img/smilies/icon_cry.gif" title=";(" alt=";(" />';
		$smiley[7]=';)'; $img[7]='<img class="smiley" src="img/smilies/icon_wink.gif" title=";)" alt=";)" />';
		$smiley[8]=':S'; $img[8]='<img class="smiley" src="img/smilies/icon_confused.gif" title=":S" alt=":S" />';
		$smiley[9]=':@'; $img[9]='<img class="smiley" src="img/smilies/icon_evil.gif" title=":@" alt=":@" />';
		$smiley[10]=':o)'; $img[10]='<img class="smiley" src="img/smilies/icon_clown.gif" title=":o)" alt=":o)" />';
		$smiley[11]='8)'; $img[11]='<img class="smiley" src="img/smilies/icon_cool.gif" title="8)" alt="8)" />';
		$smiley[12]=':$'; $img[12]='<img class="smiley" src="img/smilies/icon_blush.gif" title=":$" alt=":$" />';
		$smiley[13]='o_o'; $img[13]='<img class="smiley" src="img/smilies/icon_eek.gif" title="o_o" alt="o_o" />';
		$smiley[14]=':-D'; $img[14]='<img class="smiley" src="img/smilies/icon_biggrin.gif" title=":-D" alt=":-D" />';
		ksort($smiley);
		ksort($img);

  		$text = str_replace($smiley,$img,$text);
  		
  		return $text;
    }
    
    
    public static function encoding($elmt){
    	return nl2br(trim(htmlspecialchars($elmt, ENT_QUOTES)));
    }
    
    public static function decoding($text){
    	//return trim(htmlspecialchars_decode(str_replace("<br />", "", $elmt)));
    	//echo $text;
   		$text = htmlentities($text, ENT_NOQUOTES, "UTF-8");
    	$text = htmlspecialchars_decode($text);
 //   	utf8_encode($text);
    	return $text;
    }
    
}
?>