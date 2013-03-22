<?php

//Writes out the html for the pageitemtop
function echoPageItemTOP($icon, $text) {
	echo "<div class='pageitem_top'><img src='$icon'><span><b>$text</span></div></b>\n";
}


function EDPdoBuild() {
	global $modeldb; global $modelID; global $workpath; global $rootpath;

	//Start by defining our log file and cleaning it..
	$log = "$workpath/build.log";
	if (is_file("$log")) { system_call("rm -Rf $log"); system_call("<br>echo Building....<br><br> >$log"); }
		
	//Check if myhack is up2date and ready for combat
	myHackCheck();
			
	//Step 1
	writeToLog("$workpath/build.log", "<br><b>Step 1) Download/Update local model data... </b><br>");
	$modelName = $modeldb[$modelID]["name"];
	svnModeldata("$modelName");

	//Step 2
	writeToLog("$workpath/build.log", "<br><br><b>Step 2) Copying Essential files to $workpath </b><br>");
	copyEssentials();

	//Step 3
	writeToLog("$workpath/build.log", "<br><b>Step 3) Preparing kexts for myHack.kext </b><br>");
				copyKexts();
			
	//Step 4
	writeToLog("$workpath/build.log", "<br><br><b>Step 4) Applying Chameleon settings.. </b><br>");
	updateCham();
	writeToLog("$workpath/build.log", "  Copying selected modules...</b><br>");
	copyChamModules($modeldb[$modelID]);
			
	writeToLog("$workpath/build.log", "<br><b>Step 5) Applying last minut fixes...</b><br>");
				lastMinFixes();
					
	//Step 5
	writeToLog("$workpath/build.log", "<br><b>Step 6) Calling myFix to copy kexts and generate kernelcache</b><br><pre>");
	system_call("stty -tostop; sudo myfix -q -t / >>$workpath/build.log 2>&1 &");
				
	echo "<script> document.location.href = 'workerapp.php?action=showBuildLog'; </script>";

	exit;
        		
}
	
	
	
?>