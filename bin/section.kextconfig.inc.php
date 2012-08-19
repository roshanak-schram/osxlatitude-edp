<?

	function loadKextConfig() {
		global $rootpath; global $workpath;
		//This is the main function for the next configuration
		
		//Start by checking for myhack
		myHackCheck();
		
		//Lets start by loading the next configuration menu, the selection made at the menu will be return into $modelID
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
			global $cachepath;
			system("clear");
			
			echo "Step 1) Copying Essential files to $workpath \n";
			copyEssentials();
			
			echo "Step 2) Preparing kexts for myHack.kext \n";
			copyKexts();
			
			
			//if ("$cachepath" != "/" && "$cachepath" != "") {
			//	echo "Step 3) Cleaning $cachepath before calling myfix \n";
			//	system("rm -Rf $cachepath; mkdir $cachepath");
			//}
  		
			echo "Step 3) Call myFix to do the new build... \n";
			system("myfix -q -t $rootpath  && tput bel");

			exit;
					
		} else { loadMainSystem(); exit; }	
						
		
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
		echo " a. Make build from custom folder... \n\n\n";
		echo "-------------------------------------------------------------------------------------------------------------\n";
		echo " PRESS (x) to go back to last menu or (q) to Quit\n";
		echo "$footer\n";
		echo "Please choose: ";
		$choice = getChoice();
		
		if ($choice == "a") { 
			doCustomBuild();
			exit;
		}
		if ($choice == "q") { exit; }
		if ($choice == "x") { loadMainSystem(); exit; }
		
		global $modelname; $modelname = $modeldb[$choice]["name"];
		return "$choice";
	}
				

?>