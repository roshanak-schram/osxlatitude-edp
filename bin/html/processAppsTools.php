
<?php
	$processType = $_GET['type'];
	$id 	     = $_GET['id'];
	
	include_once "header.inc.php";

	/*
 	 * Process Applications and Tools installation
 	 */
			
	$applogPath = "$workpath/apps/dload";

 	if(is_dir("$applogPath/statFiles")) {
		$fcount = shell_exec("cd $applogPath/statFiles; ls | wc -l");
	}
	
	if ($fcount == 0 && (is_file("$applogPath/success.txt") || is_file("$applogPath/fail.txt")))
		{
			// Get info from db
			$stmt = $edp_db->query("SELECT * FROM appsdata where id = '$id' AND category ='$processType'");
			$stmt->execute();
			$rows = $stmt->fetchAll();
			$row = $rows[0];
			
			echoPageItemTOP("icons/big/$row[icon]", "$row[submenu]");
			echo "<div class='pageitem_bottom'\">";	

			echo "<ul class='pageitem'>";
			if (file_exists("$applogPath/success.txt")) {
			
				system_call("rm -rf /Applications/$row[submenu].app;");
				system_call("cd $workpath/apps/$row[menu]/$row[submenu]; unzip -qq $row[submenu].zip -d /Applications");
				
				echo "<img src=\"icons/big/success.png\" style=\"width:80px;height:80px;position:relative;left:50%;top:50%;margin:15px 0 0 -35px;\">";
				echo "<b><center> Installation finished.</b><br><br><b> You can find this from Applications list.</center></b>";
				echo "<br></ul>";
			}
			else {
				echo "<img src=\"icons/big/fail.png\" style=\"width:80px;height:80px;position:relative;left:50%;top:50%;margin:15px 0 0 -35px;\">";
				echo "<b><center> Installation failed.</b><br><br><b> Check the log for the reason.</center></b>";
				echo "<br></ul>";
				
				echo "<b>Log:</b>\n";
				echo "<pre>";
				if(is_file("$applogPath/appInstall.log"))
					include "$applogPath/appInstall.log";
				echo "</pre>";
			}					
			echo "</div>";
			exit;
		}	
		else {
		  	echo "<body onload=\"JavaScript:timedRefresh(3000);\">";
			echo "<script type=\"text/JavaScript\"> function timedRefresh(timeoutPeriod) { logVar = setTimeout(\"location.reload(true);\", timeoutPeriod); } </script>\n";
		}

?>