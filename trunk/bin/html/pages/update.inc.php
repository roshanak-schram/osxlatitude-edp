<?php
include_once "../config.inc.php";	
			
echo "Waiting updates: $number_updates";
 
if ($number_updates > "0") { 
	echo "<script> \n";
	echo "	window.fluid.dockBadge = '$number_updates';  \n";			
	echo "</script> \n";
}
?>
	