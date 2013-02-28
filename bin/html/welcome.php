<?php
	include_once "header.inc.php";
	include_once "../functions.inc.php";
	include_once "../config.inc.php";
	include "include/watermark.inc.php";
?>	

	<br>
Welcome to the new EDPweb....<br>
&nbsp; - this is still a very early beta so lots of bugs might crawling around.. <br>
<br>
<br>
Please report any issues to lsb@osxlatitude.com <br><br>
Thanx
<br>
//EDP-Team


	<?php
		$remoterev      = exec("cd $workpath; svn info -r HEAD --username edp --password edp --non-interactive | grep -i \"Last Changed Rev\"");
		$remoterev      = str_replace("Last Changed Rev: ", "", $remoterev);
		$number_updates = ($remoterev - $localrev);	
			

 
		if ($number_updates > "0") { 
			echo "<script> \n";
			echo "	window.fluid.dockBadge = '$number_updates';  \n";			
			echo "	alert('There is $number_updates updates waiting...'); \n";
			echo "</script> \n";
		}
	?>