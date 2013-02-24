<?php

	function loadKextConfig() {
		global $rootpath; global $workpath;
		//This is the main function for the next configuration
		
		//Start by checking for myhack
		myHackCheck();
		
		//Step 1: Select the vendor
		$vendorfile = selectVendor();
		include "$vendorfile";
		
		//Step 2: Load the modelinfo for the vendor selected above
		global $modelID; global $os;
		
		$modelID = showKEXTmenu();
		

		//Select what method to use (custom or pre-defined), pre-defined is already loaded, so if custom is chosen
		//we will run a function to get the info we need to do the build.
		
		$method = selectBuildMethod();
				
		if ($method == "2") {
			//Get custom vars
			$model = prepareCustomCopy();
		}
		
		
		//Confirm the build…
		$doBuild = confirmBuild();
		if ($doBuild == "y") {
			global $cachepath; global $workpath; global $rootpath; global $modeldb; global $modelID; global $os;
			system("clear");
			
			echo "Step 1) Download/Update local model data... \n";
			$modelName = $modeldb[$modelID]["name"];
			svnModeldata("$modelName");
			
			echo "Step 2) Copying Essential files to $workpath \n";
			copyEssentials();
			
			echo "Step 3) Preparing kexts for myHack.kext \n";
			copyKexts();
  			
  		
			echo "Step 4) Call myFix to do the new build... \n";
			system("myfix -q -t $rootpath  && tput bel");

			echo "Step 5) Doing sanity check... \n";
			kernelcachefix();
			updateCham();
			
			exit;
					
		} else { loadMainSystem(); exit; }	
						
		
	}

		
		
	//This function parses the vendordb.inc.php and generates a selection menu from it
	function selectVendor() {
		include "config.inc.php";
		global $vendordb;

		system("clear");
		echo "$header\n\n";
		echo "Select your the vendor you wish to build for.... \n\n";
				
		$id = 0;
		while ($vendordb[$id] != ""){
			$name = $vendordb[$id]["name"];		
			echo "  $id. $name	\n";
			$id++;
		}	
			
		echo "\n\n";
		echo " Other options: (x) to go back to last menu  -  (q) to Quit\n";
		echo "$footer\n";
		echo "Please choose: ";
		$choice = getChoice();
				
		if ($choice == "q") { exit; }
		if ($choice == "x") { loadMainSystem(); exit; }
		
		$vendorfile = $vendordb[$choice]["dbfile"];
		return "$vendorfile";
	}
		





	function showKEXTmenu() {
		include "config.inc.php";
		include_once "functions.inc.php";
		global $modeldb;
		
		system("clear");
		echo "$header\n\n";
		echo "Select the computer model you wish to build for.... \n\n";
		
		//List the different models in the model DB
		$id = 1;
		while ($modeldb[$id] != ""){

			$desc = $modeldb[$id]["desc"];
			$name = $modeldb[$id]["name"];
		
			echo "  $id. $desc	\n";
			$id++;
		}

		echo "\n\n";
		echo " Other options: (a) Build from custom folder  -  (b) Build existing config  -  (x) to go back to last menu  -  (q) to Quit\n";
		echo "$footer\n";
		echo "Please choose: ";
		$choice = getChoice();
		
		if ($choice == "a") { 
			doCustomBuild();
			exit;
		}
		if ($choice == "q") { exit; }
		if ($choice == "x") { loadMainSystem(); exit; }
		if ($choice == "b") { buildPresent(); exit; }
		
		global $modelname; $modelname = $modeldb[$choice]["name"];
		return "$choice";
	}
				

?>