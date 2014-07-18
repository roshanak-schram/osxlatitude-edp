<?php

$action = $_GET['action'];
$url 	= $_GET['url'];
$id 	= $_GET['id'];

if ($action == "goto_hell") {
	echo "If the window does not close automatically you may close it now";
	echo "<script>top.window.close();</script>";
	
	exit;
}

include_once "functions.inc.php";
include_once "edpconfig.inc.php";

// Ajax Methods to build type, vendor, seris and model values 
//--- Builder page ajax actions
global $edpDBase;
if ($action == "builderVendorValues") {
    echo $edpDBase->builderGetVendorValues($_GET['type']);
    exit;
}

if ($action == "builderSerieValues") {
    echo $edpDBase->builderGetSerieValues($_GET['type'], $_GET['vendor']);
    exit;
}

if ($action == "builderModelValues") {
    echo $edpDBase->builderGetModelValues($_GET['type'], $_GET['vendor'], $_GET['serie']);
    exit;
}
//---


// Non Ajax methods below
include_once "header.inc.php";

?><script> 
    //Reference to the edp javascript core
    var edp = top.edp;
</script><?

if ($action == "") {
    echo "No action defined..";
    exit;
}

if ($action == "browseURL") {
	echoPageItemTOP("icons/big/globe.png", "Browsing remote url...");
	echo "<div class='pageitem_bottom'>\n";	
	echo "<ul class='pageitem'>\n";
	echo "<li class='textbox'>\n";
	echo "<iframe id=\"browser\" marginwidth=\"0\" marginheight=\"0\" border=\"0\" frameborder=\"0\" height=\"600px\" width=\"100%\" src=\"$url\"></iframe>\n";			
	echo "</li>\n";
	echo "</ul>\n";	
}

if ($action == "showCredits") {
	//Fetch data for ID
	$stmt = $edp_db->query("SELECT * FROM credits where id = '$id'");
	$stmt->execute();
	$bigrow = $stmt->fetchAll(); $row = $bigrow[0];

	echoPageItemTOP("icons/big/$row[icon]", "$row[name]");
	echo "<div class='pageitem_bottom'>\n";
	echo "<br>";
	echo "<span class='graytitle'>Info</span><br>";
	echo "<ul class='pageitem'>\n";
	echo "<li class='textbox'>\n";
	echo "<p><b>Name:</b> $row[name]</p>\n";
	echo "<p><b>Type:</b> $row[type]</p>\n";	
	echo "<p><b>Creator:</b> $row[owner]</p>\n";
	echo "<p><b>E-mail:</b> $row[contactemail]</p><br>\n";
	echo "<p><b>Website:</b><a href=\"$row[inforurl]\"> Click here to Visit</a></p>\n";
	$url = $row[donationurl];
	//echo "<p><b>Donate to support:</b> <input type='button' value='Donate' onclick='edp.openlink(\"$url\");'> ($url)</p>\n";
	echo "<p><b>Donate to support:</b><a href=\"$row[donationurl]\"> Click here to Donate</a></p>\n";			
	echo "</li>\n";
	echo "</ul>\n";
	echo "<br>";	
	echo "<span class='graytitle'>About $row[name]</span><br>";
	echo "<ul class='pageitem'>\n";
	echo "<li class='textbox'>\n";
	echo "<p>$row[description]</p>\n";
	echo "</li>\n";
	echo "</ul>\n";
	
	exit;	
}

if ($action == "close-edpweb") 			{ echo "<pre>"; close - edpweb(); exit; }

//
// Log functions
//

if ($action == "showBuildLog")			{ showBuildLog(); exit ; }
if ($action == "showLoadingLog")		{ showLoadingLog(); exit ; }
if ($action == "showCustomBuildInfo")	{ showCustomBuildInfo(); exit ; }
if ($action == "showUpdateLog")			{ showUpdateLog(); exit ; }

if ($action == "showInstallLog")		
{ 
	$icon 		 = $_GET['icon'];
	$name		 = $_GET['name'];
	showInstallLog($id, $name, $icon); 
	exit ; 
}

function showBuildLog() {
	global $workpath, $edp, $ee;
	echoPageItemTOP("icons/big/logs.png", "System configuration build log");
	echo "<body onload=\"JavaScript:timedRefresh(5000);\">";	

	echo "<div class='pageitem_bottom'\">";	
	
	echo "<b>Build process:</b><br>";
	
	if(is_file("$workpath/build.log"))
		include "$workpath/build.log";
		
	if(is_file("$workpath/myFix.log"))
		include "$workpath/myFix.log";
		
	if(is_file("$workpath/myFix2.log"))
		include "$workpath/myFix2.log";
	
	if(is_dir("$workpath/kpsvn/dload/statFiles")) {
		$fcount = shell_exec("cd $workpath/kpsvn/dload/statFiles; ls | wc -l");
	}
	if ($fcount > 0)
		echo "<b>Files left to download/update : $fcount</b><br>";
		
	//
	// Run Step 5 and 6 after the kexts are downloaded
	//
	if ($fcount == 0 && is_dir("$workpath/kpsvn/dload/statFiles") && !is_file("$workpath/myFix.log"))
	{
		
		writeToLog("$workpath/build.log", "<br><b>All Files downloaded/updated.</b><br>");
		
		//
		// Step 5 : Copying custom files from /Extra/include
		//
		writeToLog("$workpath/build.log", "<br><b>Step 5) Copying custom files from /Extra/include:</b><br>");
		copyCustomFiles();
		
		//
		// Step 6 : Applying last minute fixes and generating caches
		//
		writeToLog("$workpath/myFix.log", "<br><b>Step 6) Applying last minute fixes and Calling myFix to copy kexts & generate kernelcache:</b><br>");
		
		// Final Chown to SLE and touch (this is due to some issuses with myFix in Mavericks)
		system_call("sudo chown -R root:wheel /System/Library/Extensions/");
		system_call("sudo touch /System/Library/Extensions/");
		
		// Clear NVRAM
		writeToLog("$workpath/myFix.log", "Clearing boot-args in NVRAM...<br>");
		system_call("nvram -d boot-args");
		writeToLog("$workpath/myFix.log", "Removing version control of kexts in $ee<br>");
   		system_call("rm -Rf `find -f path \"$ee\" -type d -name .svn`");
   		
   		//writeToLog("$workpath/kpsvn/dload/myFix.sh", "sudo myfix -q -t / >> $workpath/myFix.log &");
		writeToLog("$workpath/myFix.log", "<a name='myfix'></a>");
		writeToLog("$workpath/myFix.log", "Running myFix to fix permissions and genrate cache...<br><br>");
		writeToLog("$workpath/myFix.log", "<b>* * * * * * * * * * * *  myFix process status * * * * * * * * * * * *</b><br><pre>");

		// writeToLog("$workpath/kpsvn/dload/myFix.sh", "sudo myfix -q -t / >> $workpath/myFix.log &");
		// system_call("sh $workpath/kpsvn/dload/myFix.sh &");
	}
	
	echo "<script type=\"text/JavaScript\"> function timedRefresh(timeoutPeriod) { logVar = setTimeout(\"location.reload(true);\",timeoutPeriod); } function stopRefresh() { clearTimeout(logVar); } </script>\n";
	echo "</div>";
}

function showLoadingLog() {
	global $workpath, $edp, $ee;	

	echo "<div class='pageitem_bottom'\">";	
	if(is_dir("$workpath/kpsvn/dload/statFiles")) {
		$fcount = shell_exec("cd $workpath/kpsvn/dload/statFiles; ls | wc -l");
	}
	
	//
	// build log
	//
	if ($fcount == "" || $fcount > 0 || !is_file("$workpath/myFix.log")) {
		echo "<body onload=\"JavaScript:timedRefresh(8000);\">";
		echo "<center><b>After starting the build process, please wait for few minutes while we download the files needed for your model.</b> [which will take approx 5 to 15 minutes depending on your internet speed] <br><br><b>Shortly you will be redirected to the build process log which will show the status of the build.</center></b>";
		echo "<img src=\"icons/big/loading.gif\" style=\"width:200px;height:30px;position:relative;left:50%;top:50%;margin:15px 0 0 -100px;\">";
		
		if ($fcount > 0)
			echo "<br><b> Files left to download/update : $fcount</b><br>";
		
		echo "<script type=\"text/JavaScript\"> function timedRefresh(timeoutPeriod) { logVar = setTimeout(\"location.reload(true);\",timeoutPeriod); } function stopRefresh() { clearTimeout(logVar); } </script>\n";
	}
	else {
	
		//
		// myFix log
		//
		if ($fcount == 0 && is_file("$workpath/myFix.log") && !is_file("$workpath/myFix2.log"))
		{
			// Run myFix to generate cahe and fix permissions
			shell_exec("sudo myfix -q -t / >> $workpath/myFix2.log &");
		}
		echo "<img src=\"icons/big/success.png\" style=\"width:80px;height:80px;position:relative;left:50%;top:50%;margin:15px 0 0 -35px;\">";
		echo "<b><center> Build Finished, Please wait for the myFix process to finish fixing permissions and generating caches.</b> [check the build log on right side for status] <br><br><b> You can then reboot your system (or) close this app.</center></b>";
	}	
	echo "</div>";
}

function showCustomBuildInfo() {
	echo "<div class='pageitem_bottom'\">";	
	echo "<img src=\"icons/big/success.png\" style=\"width:80px;height:80px;position:relative;left:50%;top:50%;margin:15px 0 0 -35px;\">";
	echo "<b><center> Build Finished, Please wait for the myFix process to finish fixing permissions and generating caches.</b> [check the build log on right side for status] <br><br><b> You can then reboot your system (or) close this app.</center></b>";
	echo "</div>";
}

function showUpdateLog($id, $name, $icon) {
	global $workpath, $ee, $edp_db;
		
	if(is_file("$workpath/updateFinish.log")) {
		system_call("mv $workpath/updateFinish.log $workpath/lastupdate.log ");
		system("sudo killall EDP"); 
    	system("open $workpath/bin/EDPweb.app");
    	exit;
	}
				
	echoPageItemTOP("icons/big/update.png", "EDP Update");
	echo "<body onload=\"JavaScript:timedRefresh(8000);\">";	

	echo "<div class='pageitem_bottom'\">";	
		
	$UpdlogPath = "$workpath/kpsvn/dload";

	if(is_dir("$UpdlogPath/statFiles")) {
		$fcount = shell_exec("cd $UpdlogPath/statFiles; ls | wc -l");
	}
	
	if ($fcount == 0 && is_dir("$UpdlogPath/statFiles") && is_file("$UpdlogPath/Updsuccess.txt") || is_file("$UpdlogPath/Updfail.txt"))
	{
			// Get info from db
			$stmt = $edp_db->query("SELECT * FROM appsdata where id = '$id'");
			$stmt->execute();
			$rows = $stmt->fetchAll();
			$row = $rows[0];
			
			echo "<ul class='pageitem'>";
			if (file_exists("$UpdlogPath/Updsuccess.txt")) {
			
				system_call("mv $workpath/update.log $workpath/updateFinish.log ");
				
				echo "<img src=\"icons/big/success.png\" style=\"width:80px;height:80px;position:relative;left:50%;top:50%;margin:15px 0 0 -35px;\">";
				echo "<b><center> Update Finished.</b><br><br><b> Please wait 10 sec... the App will reload for the new changes to take effect.</center></b>";
				echo "<br></ul>";
				
				echo "<b>Update Log:</b>\n";
				echo "<pre>";
				if(is_file("$workpath/update.log"))
					include "$workpath/update.log";
				echo "</pre>";
				echo "<body onload=\"JavaScript:timedRefresh(10000);\">";
				echo "<script type=\"text/JavaScript\"> function timedRefresh(timeoutPeriod) { logVar = setTimeout(\"location.reload(true);\", timeoutPeriod); } </script>\n";
			}
			else {
				echo "<img src=\"icons/big/fail.png\" style=\"width:80px;height:80px;position:relative;left:50%;top:50%;margin:15px 0 0 -35px;\">";
				echo "<b><center> Installation failed.</b><br><br><b> Check the log for the reason.</center></b>";
				echo "<br></ul>";
				
				echo "<b>Update Log:</b>\n";
				echo "<pre>";
				if(is_file("$applogPath/update.log"))
					include "$applogPath/update.log";
				echo "</pre>";
			}					
			echo "</div>";
	}
	else 
	{
		echo "<center><b>Please wait for few minutes while we download the updates... which will take approx 1 to 10 minutes depending on your internet speed</b></center>";
		echo "<img src=\"icons/big/loading.gif\" style=\"width:200px;height:30px;position:relative;left:50%;top:50%;margin:15px 0 0 -100px;\">";
		
		echo "<script type=\"text/JavaScript\"> function timedRefresh(timeoutPeriod) { logVar = setTimeout(\"location.reload(true);\",timeoutPeriod); } function stopRefresh() { clearTimeout(logVar); } </script>\n";
		echo "</div>";
	}

}
		
function showInstallLog($id, $name, $icon) {
	global $workpath, $ee, $edp_db;
		
	echoPageItemTOP("icons/big/$icon", "$name");
	echo "<body onload=\"JavaScript:timedRefresh(8000);\">";	

	echo "<div class='pageitem_bottom'\">";	
		
	$applogPath = "$workpath/apps/dload";

 	if(is_dir("$applogPath/statFiles")) {
		$fcount = shell_exec("cd $applogPath/statFiles; ls | wc -l");
	}
	
	if ($fcount == 0 && is_dir("$applogPath/statFiles") && is_file("$applogPath/success.txt") || is_file("$applogPath/fail.txt"))
	{
			// Get info from db
			$stmt = $edp_db->query("SELECT * FROM appsdata where id = '$id'");
			$stmt->execute();
			$rows = $stmt->fetchAll();
			$row = $rows[0];
			
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
	else 
	{
		echo "<center><b>Please wait for few minutes while we download and install the app... which will take approx 1 to 10 minutes depending on your internet speed.</b></center>";
		echo "<img src=\"icons/big/loading.gif\" style=\"width:200px;height:30px;position:relative;left:50%;top:50%;margin:15px 0 0 -100px;\">";
	}
	
	echo "<script type=\"text/JavaScript\"> function timedRefresh(timeoutPeriod) { logVar = setTimeout(\"location.reload(true);\",timeoutPeriod); } function stopRefresh() { clearTimeout(logVar); } </script>\n";
	echo "</div>";
}

?>