<?php

class builder {

	//
	// Handles the pre-defined build process step by step
	//
	public function EDPdoBuild() {
		global $modeldb, $modeldbID, $modelID; global $workpath; global $rootpath; global $chamModules; global $edp;

		//
		// Start by defining our log file and cleaning it
		//
		$log = "$workpath/build.log";
		if (is_file("$log")) { 
			system_call("rm -Rf $log"); 
			system_call("<br>echo Building....<br><br> >$log");
		}
	
		$log = "$workpath/checkout.log";
		if (is_file("$log")) { 
			system_call("rm -Rf $log"); 
			system_call("<br>echo Building....<br><br> >$log");
		}
		
		//
		// Check if myhack is up2date and ready for build
		//
		myHackCheck();
			
		//
		// Step 1 : Create the folder path and download the model data 
		//
		
		global $modelNamePath;
		$modelName = $modeldb[$modeldbID]["name"];
		$ven = builderGetVendorValuebyID($modelID);
		$gen = builderGetGenValuebyID($modelID);
		$modelNamePath = "$ven/$gen/$modelName";
		
		if (!is_dir("$workpath/model-data"))
			system_call("mkdir $workpath/model-data");
		
		if(!is_dir("$workpath/model-data/$ven"))
			system("mkdir $workpath/model-data/$ven");
		if(!is_dir("$workpath/model-data/$ven/$gen"))
			system("mkdir $workpath/model-data/$ven/$gen");
			
		// Launch the script which provides the summary of the build process 
		echo "<script> document.location.href = 'workerapp.php?action=showBuildLog#myfix'; </script>";

		$edp->writeToLog("$workpath/build.log", "<br><b>Step 1) Download/Update of essential files and custom kexts for the $modelName </b><br>");
		
		system_call("svn --non-interactive --username osxlatitude-edp-read-only list http://osxlatitude-edp.googlecode.com/svn/model-data/$modelNamePath >> $workpath/build.log 2>&1");

		//
		// We use the new method "loadModeldata" for the new modelswill and 
		// old method "svnModeldata" for the old models which is not updated for the new DB to fetch files
		//
		
		// Try new method
		loadModeldata();
		
		//use old method if there are no files in new folder structure 
		if(!file_exists("$workpath/model-data/$modelNamePath/common/dsdt.aml")) {
			svnModeldata("$modelName");
			$modelNamePath = "$modelName";
		}
			
		//
		// Step 2 : Copy essentials like dsdt, ssdt and plists 
		//
		$edp->writeToLog("$workpath/build.log", "<br><br><b>Step 2) Copying Essential files to $workpath </b><br>");
		copyEssentials();

		//
		// Step 3 : Copying kexts
		//
		$edp->writeToLog("$workpath/build.log", "<br><b>Step 3) Downlading and Preparing kexts for myHack.kext </b><br>");
		copyKexts();
			
		//
		// Step 4 : Applying bootloader config
		//	
		$edp->writeToLog("$workpath/build.log", "<br><br><b>Step 4) Applying Chameleon settings.. </b><br>");
		if($modeldb[$modeldbID]["updateCham"] == "on") {
			$edp->writeToLog("$workpath/build.log", "Updating bootloader...<br>");
			system_call("cp -f $workpath/boot /");
		}
			
		$edp->writeToLog("$workpath/build.log", "  Copying selected modules...</b><br>");
		$chamModules->copyChamModules($modeldb[$modeldbID]);
			
		//
		// Step 5 : Applying last minute fixes and generating caches
		//
		$edp->writeToLog("$workpath/build.log", "<br><b>Step 5) Applying last minute fixes and Calling myFix to copy kexts and generate kernelcache</b><br><pre>");
		lastMinFixes();
		system_call("stty -tostop; sudo myfix -q -t / >>$workpath/build.log 2>&1 &");
		$edp->writeToLog("$workpath/build.log", "<a name='myfix'></a>");
				
		exit;
        		
	}	
}

$builder = new builder();

?> 
