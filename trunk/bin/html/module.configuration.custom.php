<?php
	include_once "../functions.inc.php";
	include_once "../config.inc.php";
	include_once "include/functions.edpweb.inc.php";
		
	include "header.inc.php";


//Fetch vars if any was posted and if action is set to "dobuild" then fetch the rest of the vars and run doCustomBuild :)
		$action		= $_POST['action'];
		if ($action == "dobuild") {
			$useIncExtentions 	= $_POST['useIncExtentions']; 		if ($useIncExtentions == "on") 		{ $useIncExtentions = "yes"; } 		else { $useIncExtentions = "no"; } 
			$useIncDSDT 		= $_POST['useIncDSDT']; 			if ($useIncDSDT == "on") 			{ $useIncDSDT = "yes"; } 			else { $useIncDSDT = "no"; } 
			$useIncSSDT 		= $_POST['useIncSSDT']; 			if ($useIncSSDT == "on") 			{ $useIncSSDT = "yes"; } 			else { $useIncSSDT = "no"; } 
			$useIncSMBIOS 		= $_POST['useIncSMBIOS']; 			if ($useIncSMBIOS == "on") 			{ $useIncSMBIOS = "yes"; } 			else { $useIncSMBIOS = "no"; } 
			$useIncCHAM 		= $_POST['useIncCHAM']; 			if ($useIncCHAM == "on") 			{ $useIncCHAM = "yes"; } 			else { $useIncCHAM = "no"; } 
			
			doCustomBuild();
		}
		

function doCustomBuild() {
	global $useIncExtentions; global $useIncDSDT; global $useIncSSDT; global $useIncSMBIOS; global $useIncCHAM; global $workpath;
	$incpath = "$workpath/include"; global $ee;


	//Start by defining our log file and cleaning it..
	$log = "$workpath/build.log";
	if (is_file("$log")) { 
		system_call("rm -Rf $log"); 
		system_call("<br>echo Building....<br><br> >$log");
	}
	
	
	//Step 1
	writeToLog("$workpath/build.log", "<br><b>Step 1) Checking if you have selected any sources from $incpath </b><br>");
	
	if ($useIncExtentions == "yes") { 
		writeToLog("$workpath/build.log", "Copying $incpath/Extensions to $ee <br>");
		system_call("cp -R $workpath/include/Extensions/* $ee");
	}	
	
	if ($useIncDSDT == "yes") {
		writeToLog("$workpath/build.log", "Copying $incpath/dsdt.aml to $workpath <br>");
		system_call("cp -f $workpath/include/dsdt.aml /Extra");
	} 	
	
	
	
	if ($useIncSSDT == "yes") {
		writeToLog("$workpath/build.log", "Copying SSDT files to $workpath <br>");
    	if (is_file("$workpath/include/SSDT.aml")) 					{ system_call("cp -f $workpath/include/SSDT.aml /Extra"); }
    	if (is_file("$workpath/include/SSDT-1.aml")) 				{ system_call("cp -f $workpath/include/SSDT-1.aml /Extra"); }
    	if (is_file("$workpath/include/SSDT-2.aml")) 				{ system_call("cp -f $workpath/include/SSDT-2.aml /Extra"); }
    	if (is_file("$workpath/include/SSDT-3.aml")) 				{ system_call("cp -f $workpath/include/SSDT-3.aml /Extra"); }    
    	if (is_file("$workpath/include/SSDT-4.aml")) 				{ system_call("cp -f $workpath/include/SSDT-4.aml /Extra"); }
    	if (is_file("$workpath/include/SSDT-5.aml")) 				{ system_call("cp -f $workpath/include/SSDT-5.aml /Extra"); } 		
	}
	if ($useIncSMBIOS == "yes")	{
		writeToLog("$workpath/build.log", "Copying $incpath/smbios.plist to $workpath <br>"); 
		system_call("cp -f $workpath/include/smbios.plist /Extra");
	}
	
	if ($useIncCHAM == "yes") { 
		writeToLog("$workpath/build.log", "Copying $incpath/org.chameleon.Boot.plist to $workpath <br>");
		system_call("cp -f $workpath/include/org.chameleon.Boot.plist /Extra");
	}		


	writeToLog("$workpath/build.log", "<br><b>Step 2) Calling myFix to copy kexts and generate kernelcache</b><br><pre>");
	system_call("stty -tostop; sudo myfix -q -t / >>$workpath/build.log 2>&1 &");
	writeToLog("$workpath/build.log", "<a name='myfix'></a>");
				
	echo "<script> document.location.href = 'workerapp.php?action=showBuildLog#myfix'; </script>";
	
	exit;
}		

		

//Custom configration page

	echo "<form action='module.configuration.custom.php' method='post'>";
	//Write out the top menu
	echoPageItemTOP("icons/big/config.png", "Custom build configuration");

	echo "<div class='pageitem_bottom'>\n";
	echo "EDP is a great tool while doing active development of a new package - not only does it contain a large amount of usefull drivers (kexts) but it also contains a bunch of usefull tools to make your job easier. <br><br> Custom configuration allows you to do a build using your existing configuration in /Extra, further more you can choose weather to include other sources - such as your include folder in /Extra.<br<br>";
	
	echo "<br><br><br>";

	
	echo "<div id=\"tabs-2\"><span class='graytitle'>Include specific sources</span>";
	echo "<ul class='pageitem'>";
	checkbox("Include kexts from /Extra/include/Extensions ?", "useIncExtentions", "no");
	checkbox("Include DSDT.aml from /Extra/include ?", "useIncDSDT", "no");
	checkbox("Include SSDT files from /Extra/include ?", "useIncSSDT", "no");
	checkbox("Include smbios.plist from /Extra/include ?", "useIncSMBIOS", "no");
	checkbox("Include org.chameleon.boot.plust from /Extra/include ?", "useIncCHAM", "no");		
	echo "</ul></div>";
	
	echo "</div><br>";
	echo "<input type='hidden' name='action' value='dobuild'>";
	echo "<ul class='pageitem'><li class='button'><input name='Submit input' type='submit' value='Do build!' /></li></ul><br><br>\n";
	echo "</form>";
		
?>

 
