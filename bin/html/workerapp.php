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


// Ajax Methods to build type, vendor, seris and model values 
//--- Builder page ajax actions

if ($action == "builderVendorValues" || $action == "builderSerieValues" || $action == "builderModelValues" || $action == "builderCPUValues") {

	include_once "edpconfig.inc.php";
	global $edpDBase;
	
	switch($action) {
		case "builderVendorValues":
		 	echo $edpDBase->builderGetVendorValues($_GET['type']);
		break;
		
		case "builderSerieValues":
			echo $edpDBase->builderGetSerieValues($_GET['type'], $_GET['vendor']);
		break;
		
		case "builderModelValues":
			echo $edpDBase->builderGetModelValues($_GET['type'], $_GET['vendor'], $_GET['serie']);
		break;
		
		case "builderCPUValues":
			echo $edpDBase->builderGetCPUValues($_GET['type'], $_GET['model']);
		break;
	}
    exit;
}


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

if ($action == "showCredits") {

	include_once "edpconfig.inc.php";

	//Fetch data for ID
	$stmt = $edp_db->query("SELECT * FROM credits where id = '$id'");
	$stmt->execute();
	$bigrow = $stmt->fetchAll(); $row = $bigrow[0];

	if ($row[category] == "Apps & Tools")
		echoPageItemTOP("icons/apps/$row[icon]", "$row[name]");
	else
		echoPageItemTOP("icons/big/$row[icon]", "$row[name]");
		
	echo "<div class='pageitem_bottom'>\n";
	echo "<br>";
	echo "<span class='graytitle'>Info</span><br>";
	echo "<ul class='pageitem'>\n";
	echo "<li class='textbox'>\n";
	echo "<p><b>Name:</b> $row[name]</p>\n";
	echo "<p><b>Creator:</b> $row[owner]</p>\n";
	echo "<p><b>E-mail:</b> $row[contactemail]</p><br>\n";
	echo "<p><b>Project/Support Website: </b><a href=\"$row[inforurl]\">Click here to Visit</a></p>\n";

	if ($row[donationurl] != "") {
		echo "<form action=\"$row[donationurl]\">";
		echo '<p><b>Want to support the creator? </b><input type="submit" value="Donate"></p>';
		echo "</form>";
	}
	
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

if ($action == "showFixLog")			
 { 
	include_once "edpconfig.inc.php";
	
	$fixKeyArray 	= explode(',', $_GET['fixInfoKeys']);
	$fixValueArray 	= explode(',', $_GET['fixInfoValues']);
	
	$fixData = Array();
	
	$index = 0;
	foreach($fixKeyArray as $fix) {
		$fixData[$fix] = $fixValueArray[$index];
		$index++;
	}	
	
	// var_dump($fixData);
	showFixLog($fixData);
	exit;
 }
 
if ($action == "showBuildLog")			
 { 
	include_once "edpconfig.inc.php";
	$modelPath 	= $_GET['modelPath'];
	$dsdt 	= $_GET['dsdt'];
	$ssdt 	= $_GET['ssdt'];
	$theme 	= $_GET['theme'];
	$smbios	= $_GET['smbios'];
	$chame 	= $_GET['chame'];
	
	showBuildLog($modelPath, $dsdt, $ssdt, $theme, $smbios, $chame); exit ;
 }
 
if ($action == "showLoadingLog")		{ showLoadingLog(); exit ; }
if ($action == "showCustomBuildInfo")	{ showCustomBuildInfo(); exit ; }
if ($action == "showUpdateLog")			{ showUpdateLog(); exit ; }

if ($action == "showInstallLog")		
{ 
	include_once "edpconfig.inc.php";

	$icon 		 	= $_GET['icon'];
	$foldername		= $_GET['foldername'];
	$name		 	= $_GET['name'];
	showInstallLog($id, $foldername, $name, $icon); 
	exit ; 
}

function showBuildLog($modelPath, $dsdt, $ssdt, $theme, $smbios, $chame) {
	$workpath = "/Extra/EDP";
	$buildLogPath = "$workpath/logs/build";
	
	// For log time
	date_default_timezone_set("UTC");
	$date = date("d-m-y H-i");

	echoPageItemTOP("icons/big/logs.png", "System configuration build log");
	echo "<body onload=\"JavaScript:timedRefresh(5000);\">";	
	echo "<div class='pageitem_bottom'\">";	
	
	// Show build logs	
	if(is_file("$buildLogPath/build.log"))
		include "$buildLogPath/build.log";
		
	if(is_file("$buildLogPath/myFix.log"))
		include "$buildLogPath/myFix.log";
	
	// Check the file download status
	if(is_dir("$buildLogPath/dLoadStatus")) {
		$fcount = shell_exec("cd $buildLogPath/dLoadStatus; ls | wc -l");
	}
	if ($fcount > 0)
		echo "<b>Files left to download/update : $fcount</b><br>";
		
	//
	// Run Step 5 to 7 after the prepared files are downloaded
	//
	if ($fcount == 0 && is_dir("$buildLogPath/dLoadStatus") && !is_file("$buildLogPath/Run_myFix.txt"))
	{
		
		writeToLog("$buildLogPath/build.log", "<br><b>All Files downloaded/updated.</b><br>");
		
		//
		// Step 5 : Copy essentials like dsdt, ssdt and plists 
		//
		writeToLog("$buildLogPath/build.log", "<br><b>Step 5) Copy essential files downloaded:</b><br>");

		copyEssentials($modelPath, $dsdt, $ssdt, $theme, $smbios, $chame);
		
		//
		// Step 6 : Copying custom files from /Extra/include
		//
		writeToLog("$buildLogPath/build.log", "<br><b>Step 6) Copy user provided files from /Extra/include:</b><br>");
		
		copyCustomFiles();
		
		//
		// Step 7 : Applying last minute fixes and generating caches
		//
		writeToLog("$buildLogPath/build.log", "<br><b>Step 7) Apply last minute fixes and Call myFix to copy kexts & generate kernelcache:</b><br>");
		
		// Final Chown to SLE and touch (this is due to some issuses with myFix in Mavericks)
		system_call("sudo chown -R root:wheel /System/Library/Extensions/");
		system_call("sudo touch /System/Library/Extensions/");
		
		// Clear NVRAM
		writeToLog("$buildLogPath/build.log", " Clearing boot-args in NVRAM...<br>");
		system_call("nvram -d boot-args");
		writeToLog("$buildLogPath/build.log", " Removing version control of kexts in /Extra/Extensions<br>");
   		system_call("rm -Rf `find -f path /Extra/Extensions -type d -name .svn`");
			
		// Kernel hack for YOS
		writeToLog("$buildLogPath/build.log", " Checking if we are running Yosemite and need to link kernel</b><br>");
		$r = getVersion();
  		if ($r == "yos") { system_call("ln -s /System/Library/Kernels/kernel /mach_kernel"); }
		
		writeToLog("$buildLogPath/build.log", " Calling myFix to fix permissions and genrate cache...<br>");
		
		// End build log and create a lastbuild log
		system_call("echo '<br>*** Logging ended on: $date UTC Time ***<br>' >> $buildLogPath/build.log");
		system_call("cp $buildLogPath/build.log $workpath/logs/lastbuild.log ");
		
		// Append current build log to the builds log 
		$fileContents = file_get_contents("$buildLogPath/build.log");
		file_put_contents("$workpath/logs/build.log", $fileContents, FILE_APPEND | LOCK_EX);
						
		// Create run_myFix text file to start myFix process
		writeToLog("$buildLogPath/Run_myFix.txt", "");
	}
	
	echo "<script type=\"text/JavaScript\"> function timedRefresh(timeoutPeriod) { logVar = setTimeout(\"location.reload(true);\",timeoutPeriod); } function stopRefresh() { clearTimeout(logVar); } </script>\n";
	echo "</div>";
}

function showLoadingLog() {
	$workpath = "/Extra/EDP";	
	$buildLogPath = "$workpath/logs/build";
	
	echo "<div class='pageitem_bottom'\">";	
	
	// Check the file download status
	if(is_dir("$buildLogPath/dLoadStatus")) {
		$fcount = shell_exec("cd $buildLogPath/dLoadStatus; ls | wc -l");
	}
	
	//
	// build log
	//
	if ($fcount == "" || $fcount > 0 || !is_file("$buildLogPath/Run_myFix.txt")) {
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
		if ($fcount == 0 && is_file("$buildLogPath/Run_myFix.txt") && !is_file("$buildLogPath/myFix.log"))
		{
			writeToLog("$buildLogPath/myFix.log", "<br><br><b>* * * * * * * * * * * *  myFix process status * * * * * * * * * * * *</b><br><pre>");
			writeToLog("$buildLogPath/myFix.log", "Running myFix to fix permissions and genrate cache...<br><br>");

			// Run myFix to generate cahe and fix permissions
			shell_exec("sudo myfix -q -t / >> $buildLogPath/myFix.log &");
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

function showUpdateLog() {
	$workpath = "/Extra/EDP";
	$updLogPath = "$workpath/logs/update";

	// For log time
	date_default_timezone_set("UTC");
	$date = date("d-m-y H-i");
	
	if(is_file("$updLogPath/updateFinish.log")) {
		system_call("mv $updLogPath/updateFinish.log $workpath/logs/lastupdate.log ");
		system("sudo killall EDP"); 
    	system("open $workpath/bin/EDPweb.app");
    	exit;
	}
				
	echoPageItemTOP("icons/big/update.png", "EDP Update");
	echo "<body onload=\"JavaScript:timedRefresh(8000);\">";	

	echo "<div class='pageitem_bottom'\">";	
			
	if (is_file("$updLogPath/Updsuccess.txt") || is_file("$updLogPath/Updfail.txt"))
	{
			echo "<ul class='pageitem'>";
			if (file_exists("$updLogPath/Updsuccess.txt")) {
							
				echo "<img src=\"icons/big/success.png\" style=\"width:80px;height:80px;position:relative;left:50%;top:50%;margin:15px 0 0 -35px;\">";
				echo "<b><center> Update success.</b><br><br><b> Please wait 10 sec... the App will reload for the new changes to take effect.</center></b>";
				echo "<br></ul>";
				
				system_call("echo '<br>*** Logging ended on: $date UTC Time ***<br>' >> $updLogPath/update.log");
				system_call("mv $updLogPath/update.log $updLogPath/updateFinish.log ");

				echo "<b>Update Log:</b>\n";
				echo "<pre>";
				if(is_file("$updLogPath/updateFinish.log"))
					include "$updLogPath/updateFinish.log";
				echo "</pre>";
				
				// Append current update log to the updates log 
				$fileContents = file_get_contents("$updLogPath/updateFinish.log");
				file_put_contents("$workpath/logs/update.log", $fileContents, FILE_APPEND | LOCK_EX);
				
				echo "<body onload=\"JavaScript:timedRefresh(8000);\">";
				echo "<script type=\"text/JavaScript\"> function timedRefresh(timeoutPeriod) { logVar = setTimeout(\"location.reload(true);\", timeoutPeriod); } </script>\n";
			}
			else {
				echo "<img src=\"icons/big/fail.png\" style=\"width:80px;height:80px;position:relative;left:50%;top:50%;margin:15px 0 0 -35px;\">";
				echo "<b><center> Update failed.</b><br><br><b> Check the log for the reason.</center></b>";
				echo "<br></ul>";
				
				echo "<b>Update Log:</b>\n";
				echo "<pre>";
				if(is_file("$updLogPath/update.log"))
					include "$updLogPath/update.log";
				echo "</pre>";
				
				system_call("echo '<br>*** Logging ended on: $date UTC Time ***<br>' >> $updLogPath/update.log");
				system_call("mv $updLogPath/update.log $workpath/logs/lastupdate.log ");
				
				// Append current update log to the updates log 
				$fileContents = file_get_contents("$updLogPath/update.log");
				file_put_contents("$workpath/logs/update.log", $fileContents, FILE_APPEND | LOCK_EX);
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
		
function showInstallLog($id, $foldername, $name, $icon) {
	global $workpath, $edp_db;
		
	echoPageItemTOP("icons/apps/$icon", "$name");
	echo "<body onload=\"JavaScript:timedRefresh(8000);\">";	

	echo "<div class='pageitem_bottom'\">";	
		
	$appsLogPath = "$workpath/logs/apps";
	
	if (is_file("$appsLogPath/Success_$foldername.txt") || is_file("$appsLogPath/Fail_$foldername.txt"))
	{
			// Get info from db
			$stmt = $edp_db->query("SELECT * FROM appsdata where id = '$id'");
			$stmt->execute();
			$rows = $stmt->fetchAll();
			$row = $rows[0];
			
			//
			// Install the downloaded app
			//
			echo "<ul class='pageitem'>";
			if (file_exists("$appsLogPath/Success_$foldername.txt")) {
			
				$appPath = "$workpath/apps/$row[menu]/$row[foldername]";
				
				if ($row[foldername] == "CamTwist") {
					system_call("rm -rf /Library/QuickTime/CamTwist.component");
					system_call("rm -rf /Applications/$row[foldername]");
					
					system_call("cd $appPath; rm -rf CamTwist.component; rm -rf CamTwist; unzip -X -qq $row[foldername].zip");
						
					system_call("cp -R $appPath/CamTwist.component /Library/QuickTime/");
					system_call("cp -a $appPath/CamTwist/. /Applications/CamTwist/");
				}
				else {
					system_call("rm -rf /Applications/$row[foldername].app");
					system_call("cd $appPath; unzip -X -qq $row[foldername].zip -d /Applications");
				}		
				
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
				if(is_file("$appsLogPath/appInstall.log"))
					include "$appsLogPath/appInstall.log";
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

function showFixLog($fixData) {
	global $workpath, $edp_db;
		
	echoPageItemTOP("icons/big/$fixData[icon]", "$fixData[name]");
	echo "<body onload=\"JavaScript:timedRefresh(8000);\">";	

	echo "<div class='pageitem_bottom'\">";	
	
	$fixLogPath = "$workpath/logs/fixes";
	
	
	if (is_file("$fixLogPath/Success_$fixData[foldername].txt") || is_file("$fixLogPath/Fail_$fixData[foldername].txt"))
	{
			// Get info from db
			$stmt = $edp_db->query("SELECT * FROM fixesdata where id = '$fixData[id]'");
			$stmt->execute();
			$rows = $stmt->fetchAll();
			$row = $rows[0];
			
			//
			// Install the downloaded fix
			//
			echo "<ul class='pageitem'>";
			if (file_exists("$fixLogPath/Success_$fixData[foldername].txt")) {
							
				switch($fixData[foldername]) {
					case "EAPDFix":
						$fixPath = "$workpath/kextpacks/$fixData[categ]/$fixData[foldername]";
						system_call("sudo /usr/libexec/PlistBuddy -c \"set IOKitPersonalities:EAPDFix:CodecValues:Speakers $fixData[spk]\" $fixPath/EAPDFix.kext/Contents/Info.plist >> $fixLogPath/fixInstall.log");
						system_call("sudo /usr/libexec/PlistBuddy -c \"set IOKitPersonalities:EAPDFix:CodecValues:Headphones $fixData[hp]\" $fixPath/EAPDFix.kext/Contents/Info.plist >> $fixLogPath/fixInstall.log");
						system_call("sudo /usr/libexec/PlistBuddy -c \"set IOKitPersonalities:EAPDFix:CodecValues:ExternalMic $fixData[extMic]\" $fixPath/EAPDFix.kext/Contents/Info.plist >> $fixLogPath/fixInstall.log");
						system_call("sudo /usr/libexec/PlistBuddy -c \"set IOKitPersonalities:EAPDFix:CodecValues:SpkHasEAPD $fixData[spkFix]\" $fixPath/EAPDFix.kext/Contents/Info.plist >> $fixLogPath/fixInstall.log");
						system_call("sudo /usr/libexec/PlistBuddy -c \"set IOKitPersonalities:EAPDFix:CodecValues:HpHasEAPD $fixData[hpFix]\" $fixPath/EAPDFix.kext/Contents/Info.plist >> $fixLogPath/fixInstall.log");
						system_call("rm -rf $fixData[path]/EAPDFix.kext");
						system_call("cp -rf $fixPath/EAPDFix.kext $fixData[path]/");
					
						if ($fixData[path] == "/Extra/Extensions") {
							myHackCheck();
							system_call("sudo myfix -q -t / >> $fixLogPath/myFix.log &");
						}
						else {
							system_call("sudo touch /System/Library/Extensions/ >> $log &");
						}
					break;
					
					Default:
						//
						// Special handling for the PowerMgmt files, due to they are kexts directly instead of folder
						//
						if ($fixData[categ] == "PowerMgmt" && $fixData[foldername] != "VoodooPState") {
							$fixPath = "$workpath/kextpacks/$fixData[categ]/$fixData[foldername]";
							system_call("rm -rf $fixData[path]/$fixData[foldername]");
							system_call("cp -rf $fixPath $fixData[path]/");
						}
						//
						// Folder kext packs
						//
						else {
							$fixPath = "$workpath/kextpacks/$fixData[categ]/$fixData[foldername]";
							system_call("rm -rf $fixData[path]/$fixData[foldername]");
							system_call("cp -rf $fixPath/* $fixData[path]/");
						}
					
						if ($fixData[path] == "/Extra/Extensions") {
							myHackCheck();
							system_call("sudo myfix -q -t / >> $fixLogPath/myFix.log &");
						}
						else {
							system_call("sudo touch /System/Library/Extensions/ >> $log &");
						}
					break;
					
				}
								
				echo "<img src=\"icons/big/success.png\" style=\"width:80px;height:80px;position:relative;left:49%;top:50%;margin:15px 0 0 -35px;\">";
				echo "<b><center> Fix finished.</b><br><br><b> You can now close app (or) reboot your sytem to see the fix in action.</center></b>";
				echo "<br></ul>";
			}
			else {
				echo "<img src=\"icons/big/fail.png\" style=\"width:80px;height:80px;position:relative;left:50%;top:50%;margin:15px 0 0 -35px;\">";
				echo "<b><center> Fix failed.</b><br><br><b> Check the log for the reason.</center></b>";
				echo "<br></ul>";
				
				echo "<b>Log:</b>\n";
				echo "<pre>";
				if(is_file("$fixLogPath/fixInstall.log"))
					include "$fixLogPath/fixInstall.log";
				echo "</pre>";
			}					
			echo "</div>";
			exit;
	}
	else 
	{
		echo "<center><b>Please wait for few minutes while we apply fix... which will take approx 1 to 10 minutes if it requires internet which depends on your speed.</b></center>";
		echo "<img src=\"icons/big/loading.gif\" style=\"width:200px;height:30px;position:relative;left:50%;top:50%;margin:15px 0 0 -100px;\">";
	}
	
	echo "<script type=\"text/JavaScript\"> function timedRefresh(timeoutPeriod) { logVar = setTimeout(\"location.reload(true);\",timeoutPeriod); } function stopRefresh() { clearTimeout(logVar); } </script>\n";
	echo "</div>";
}

?>