
<?php

	include_once "edpconfig.inc.php";
	include_once "functions.inc.php";

	include_once "header.inc.php";

	/*
	 * load the page of the selected side menu
	 */
 
 	//
 	// get category and id from the get and post methods
 	//
	$action = $_GET['action'];
	if ($action == "") {
		$action = $_POST['action'];
	}
	
	$categ	= $_GET['category'];
	if ($categ == "") {
		$categ = $_POST['category'];
	}	
	
	$id 	= $_GET['id'];
	if ($id == "") {
		$id = $_POST['id'];
	}

	switch ($categ) {
		case "Applications":
		case "Tools":
			$query = "SELECT * FROM appsdata";
			$buttonValue = "Proceed to Install/Update";
		break;
	
		case "EDP":
			$buttonValue = "Proceed to Install/Update";
		case "Configuration":
			$query = "SELECT * FROM edpdata";
		break;
	
		case "Fixes":
			$query = "SELECT * FROM fixesdata";
			$buttonValue = "Apply Fix";
		break;
	}

	// Get info from db
	$stmt = $edp_db->query("$query where id = '$id'");
	$stmt->execute();
	$bigrow = $stmt->fetchAll(); $row = $bigrow[0];
			
	if ($action == "")
	{
		echo "<form action='$row[action]' method='post'>";

		// Write out the top menu
		if ($categ != "EDP") {
			echoPageItemTOP("icons/sidebar/$row[icon]", "$row[submenu]");
		}
		else
			echoPageItemTOP("icons/big/$row[icon]", "$row[submenu]");
		
		if ($categ == "Fixes") 
		{
			switch ($row[name]) 
			{
				case "AppleIntelCPUPowerManagementPatch":
				case "BCM4352WiFiPatches":
				case "AR9285AR9287WiFiPatch":
				case "VGA_HDMI_Intel_HD3000_Patch":
					echo "<ul class='pageitem'>";				
					checkbox("Apply patch directly to /System/Library/Extensions instead of myHack kext loading?", "patchSLE", "no");
					echo "</ul>";
					$action = "Patch";
				break;
	
			}
			
		} 
		else if ($categ == "Applications" || $categ == "Tools")
		{
			$action = "Install";
		}
	
		?>
		
		<div class="pageitem_bottom">
		<p><b>About:</b></p>
		<?="$row[brief]";?>
		<br>
		<p><b>Descripton:</b></p>
		<?="$row[description]";?>
		<br>
		<p><b>Website:</b></p>
		<a href='<?="$row[link]";?>'>Project/Support Link</a>
		</div>
		<ul class="pageitem">
			<li class="button"><input name="Submit input" type="submit" value="<?=$buttonValue?>" /></li>
		</ul>
		
		<?php
			echo "<input type='hidden' name='id' value='$id'>";
			echo "<input type='hidden' name='action' value='$action'>";
			echo "<input type='hidden' name='category' value='$categ'>";
		?>

		</form>
		<?php
	}
	elseif ($action == "Install")
	{
		// Start installation process by Launching the script which provides the summary of the build process 
		echo "<script> document.location.href = 'workerapp.php?id=$id&name=$row[name]&submenu=$row[submenu]&icon=$row[icon]&action=showInstallLog#myfix'; </script>";
		
		// Clear logs and scripts
		if(is_dir("$workpath/logs/apps")) {
			system_call("rm -rf $workpath/logs/apps/*");
		}
		
		// Download app
		appsLoader("$row[menu]","$row[name]");
	}
	elseif ($action == "Patch")
	{
		$fixLogPath = "$workpath/logs/fixes";
		
		// Clear logs and scripts
		if(is_dir("$fixLogPath")) {
			system_call("rm -rf $fixLogPath/*");
		}
		
		// create log directory if not found
		if(!is_dir("$workpath/logs")) {
			system_call("mkdir $workpath/logs");
		}
		if(!is_dir("$fixLogPath")) {
			system_call("mkdir $fixLogPath");
		}
		
		echo "<div class='pageitem_bottom'\">";	
		echo "<ul class='pageitem'>";

		$patchSLE = $_POST['patchSLE'];
		
		switch ($row[name]) {
		
			case "AppleIntelCPUPowerManagementPatch":
				if ($patchSLE == "on")
					patchAppleIntelCPUPowerManagement("$fixLogPath/fix.log", "SLE", "yes");
				else
					patchAppleIntelCPUPowerManagement("$fixLogPath/fix.log", "EE", "yes");
			break;
			
			case "BCM4352WiFiPatches":
				if ($patchSLE == "on")
					patchWiFiBTBCM4352("$fixLogPath/fix.log", "SLE", "yes");
				else
					patchWiFiBTBCM4352("$fixLogPath/fix.log", "EE", "yes");
			break;
			
			case "AR9285AR9287WiFiPatch":
				if ($patchSLE == "on")
					patchWiFiAR9285AndAR9287("$fixLogPath/fix.log", "SLE", "yes");
				else
					patchWiFiAR9285AndAR9287("$fixLogPath/fix.log", "EE", "yes");
			break;
			
			case "VGA_HDMI_Intel_HD3000_Patch":
				if ($patchSLE == "on")
					patchAppleIntelSNBGraphicsFB("$fixLogPath/fix.log", "SLE", "yes");
				else
					patchAppleIntelSNBGraphicsFB("$fixLogPath/fix.log", "EE", "yes");
			break;
		}
		
		if (is_file("$fixLogPath/patchSuccess.txt")) {
			echo "<img src=\"icons/big/success.png\" style=\"width:80px;height:80px;position:relative;left:50%;top:50%;margin:15px 0 0 -35px;\">";
			echo "<b><center> Patch finished.</b><br><br><b> You can now reboot the sysem to see the patch in action.</center></b>";
			echo "<br></ul>";
		}
		else {
			echo "<img src=\"icons/big/fail.png\" style=\"width:80px;height:80px;position:relative;left:50%;top:50%;margin:15px 0 0 -35px;\">";
			echo "<b><center> Patch failed.</b><br><br><b> Check the log for the reason.</center></b>";
			echo "<br></ul>";
			
			echo "<b>Log:</b>\n";
			echo "<pre>";
			if(is_file("$fixLogPath/fix.log"))
				include "$fixLogPath/fix.log";
			echo "</pre>";
		}
		echo "</div>";
	}

function appsLoader($categ, $fname) {
		global $workpath, $edp;
    	  
    	$appsLogPath = "$workpath/logs/apps";
    	  	
    	// create app local download directory if not found
    	if(!is_dir("$workpath/apps")) {
			system_call("mkdir $workpath/apps");
		}
		if(!is_dir("$workpath/apps/$categ")) {
			system_call("mkdir $workpath/apps/$categ");
		}
		
		// create log directory if not found
		if(!is_dir("$workpath/logs")) {
			system_call("mkdir $workpath/logs");
		}
		if(!is_dir("$appsLogPath")) {
			system_call("mkdir $appsLogPath");
		}
		
		//
		// Download apps from SVN
		//
    	$appdir = "$workpath/apps/$categ/$fname";
		$svnpath = "apps/$categ/$fname";
			
		if (is_dir("$appdir")) {
			$checkoutCmd = "if svn --non-interactive --username edp --password edp --quiet --force update $appdir; then echo \"$fname file(s) updated finished<br>\"; touch $appsLogPath/Success_$fname.txt; else echo \"$fname file(s) update failed (may be wrong svn path or no internet)<br>\"; touch $appsLogPath/Fail_$fname.txt; fi";

			writeToLog("$appsLogPath/$fname.sh", "$checkoutCmd;");
			system_call("sh $appsLogPath/$fname.sh >> $appsLogPath/appInstall.log &");
		}
		else {
			$checkoutCmd = "cd $workpath/apps/$categ; if svn --non-interactive --username osxlatitude-edp-read-only --quiet --force co http://osxlatitude-edp.googlecode.com/svn/$svnpath; then echo \"$fname file(s) download finished<br>\"; touch $appsLogPath/Success_$fname.txt; else echo \"$fname file(s) download failed (may be wrong svn path or no internet)<br>\"; touch $appsLogPath/Fail_$fname.txt; fi";

			writeToLog("$appsLogPath/$fname.sh", "$checkoutCmd;");
			system_call("sh $appsLogPath/$fname.sh >> $appsLogPath/appInstall.log &");	
		}
} 
?>


