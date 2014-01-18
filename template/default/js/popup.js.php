<?php
/*
 * Created on 24 oct. 2012
 *
 * MAES Jerome, Webkot 2012-2013
 * Class Description :
 *
 */
 
 header("Content-type: text/javascript");
 
 
 if(isset($_GET['next']) && !empty($_GET['next'])){
 	?>
 	function redirectNextPict(){
		window.location.href = 'popup.php?p=view&pid=<?php echo  $_GET['next']; ?>';
	}
 	<?php
 }
 
 if(isset($_GET['prev']) && !empty($_GET['prev'])){
  	?>
  	function redirectPrecPict(){
		window.location.href = 'popup.php?p=view&pid=<?php echo  $_GET['prev']; ?>';
	}
 	<?php 	
 }
?>
