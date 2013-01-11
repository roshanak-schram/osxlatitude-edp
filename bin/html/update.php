<?php
	include "functions.inc.php";
	
	echo "<br><br><b>.. Checking for updates using svn.... <br><br>";
	echo "<pre>";
	//system_call("ls -la /");
	system_call("svn --non-interactive --username edp --password edp --force update /Extra");
?>
