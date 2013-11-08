<?php

class builder {

	//Returns foldername from ID, table must have "foldername"
	public function getKextpackNameFromID($table, $id) {
		if ($table != "" && $id != "") {
			global $edp_db; global $workpath; global $ee;
			$stmt = $edp_db->query("SELECT * FROM $table where id = '$id'");
			$stmt->execute();
			$bigrow = $stmt->fetchAll(); $row = $bigrow[0];
			return $row[foldername];
		}		
	}
	
//Returns Category name from ID, table must have "category"
	public function getCategoryNameFromID($table, $id) {
		if ($table != "" && $id != "") {
			global $edp_db; global $workpath; global $ee;
			$stmt = $edp_db->query("SELECT * FROM $table where id = '$id'");
			$stmt->execute();
			$bigrow = $stmt->fetchAll(); $row = $bigrow[0];
			return $row[category];
		}		
	}
	


	public function lastMinFixes() {
		global $workpath; global $edp; global $nvram;
		$stat = $nvram->clear();
		$edp->writeToLog("$workpath/build.log", "Clearing boot-args in NVRAM...$stat<br>");
	}


	//Handles the pre-defined build process step by step, should only be called when we are sure that the global var $modelID is fully populated with needed vars...
	public function EDPdoBuild() {
		global $modeldb; global $modelID; global $workpath; global $rootpath; global $chamModules; global $edp;

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
		$edp->writeToLog("$workpath/build.log", "<br><b>Step 1) Download/Update local model data... </b><br>");
		$modelName = $modeldb[$modelID]["name"];
		svnModeldata("$modelName");
			
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
		$chamModules->copyChamModules($modeldb[$modelID]);
			
		$edp->writeToLog("$workpath/build.log", "<br><b>Step 5) Applying last minut fixes...</b><br>");
		$this->lastMinFixes();
					
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
