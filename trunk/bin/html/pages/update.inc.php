	<?php
		$remoterev      = exec("cd $workpath; svn info -r HEAD --username edp --password edp --non-interactive | grep -i \"Last Changed Rev\"");
		$remoterev      = str_replace("Last Changed Rev: ", "", $remoterev);
		$number_updates = ($remoterev - $localrev);	
			
		echo "Waiting updates: $number_updates";
 
		if ($number_updates > "0") { 
			echo "<script> \n";
			echo "	window.fluid.dockBadge = '$number_updates';  \n";			
			echo "</script> \n";
		}
	?>
	