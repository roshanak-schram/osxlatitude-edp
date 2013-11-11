<?php

class builder {

	//Handles the pre-defined build process step by step, should only be called when we are sure that the global var $modelID is fully populated with needed vars...
	public function EDPdoBuild() {
		global $modeldb, $modeldbID; global $modelID; global $workpath; global $rootpath; global $chamModules; global $edp;

		//Start by defining our log file and cleaning it..
		$log = "$workpath/build.log";
		if (is_file("$log")) { 
			system_call("rm -Rf $log"); 
			system_call("<br>echo Building....<br><br> >$log");
		}
		
		
		echo "<div id='model_download'>";
		echo "<p><B><center>Please wait for few minutes(will take approx 2-15 mnts which depends on your speed), while we download the files of your model................</center></p><br>";
		echo "<p><B><center>When the download is finished then you will automatically redirected to build process and its Log</center></p><br>";
		echo "</div>";
		
		//Check if myhack is up2date and ready for combat
		myHackCheck();
			
		
		//Step 1
		global $modelNamePath;
		$modelName = $modeldb[$modeldbID]["name"];
		$ven = builderGetVendorValuebyID($modelID);
		$ser = builderGetSeriesValuebyID($modelID);
		$modelNamePath = "$ven/$ser/$modelName";
		if(!is_dir("$workpath/model-data/$ven"))
			system("mkdir $workpath/model-data/$ven");
		if(!is_dir("$workpath/model-data/$ven/$ser"))
			system("mkdir $workpath/model-data/$ven/$ser");
			
		$edp->writeToLog("$workpath/build.log", "<br><b>Step 1) Download/Update $modelName  model data... </b><br>");
		// We will use old way also until we move all the models data into their respective category
		//New way
		loadModeldata();
		//use old way if there is no files in new folder structure 
		if(!file_exists("$workpath/model-data/$modelNamePath/common/dsdt.aml")) {
			svnModeldata("$modelName");
			$modelNamePath = "$modelName";
		}
			
		//Step 2
		$edp->writeToLog("$workpath/build.log", "<br><br><b>Step 2) Copying Essential files to $workpath </b><br>");
		copyEssentials();

		//Step 3
		$edp->writeToLog("$workpath/build.log", "<br><b>Step 3) Preparing kexts for myHack.kext </b><br>");
		copyKexts();
			
		//Step 4
		$edp->writeToLog("$workpath/build.log", "<br><br><b>Step 4) Applying Chameleon settings.. </b><br>");
		updateCham();
		$edp->writeToLog("$workpath/build.log", "  Copying selected modules...</b><br>");
		$chamModules->copyChamModules($modeldb[$modeldbID]);
			
		$edp->writeToLog("$workpath/build.log", "<br><b>Step 5) Applying last minut fixes...</b><br>");
		lastMinFixes();
					
		//Step 5
		$edp->writeToLog("$workpath/build.log", "<br><b>Step 6) Calling myFix to copy kexts and generate kernelcache</b><br><pre>");
		system_call("stty -tostop; sudo myfix -q -t / >>$workpath/build.log 2>&1 &");
		$edp->writeToLog("$workpath/build.log", "<a name='myfix'></a>");
				
		echo "<script> document.location.href = 'workerapp.php?action=showBuildLog#myfix'; </script>";

		exit;
        		
	}









		
}


$builder = new builder();


?> 
