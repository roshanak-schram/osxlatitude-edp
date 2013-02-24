<?php

	function selectPS2() {
		global $header; global $footer;	global $ps2db; global $workpath;
		system_call("clear");
		
		echo "$header\n\n";
		echo " What PS2 (keyboard and mouse) controller do you wish to use... \n\n";
		
		$id = 0;
		while ($ps2db[$id] != ""){
			$name 	= $ps2db[$id]["name"];
			$arch	= $ps2db[$id]["arch"];
			$notes	= $ps2db[$id]["notes"];
			$folder	= $ps2db[$id]["foldername"];
			$kname	= $ps2db[$id]["kextname"];
			if ($kname != "") { $kver 	= getKextVersion("$workpath/storage/kextpacks/$folder/$kname"); }
			
			echo "  $id. $name - kext: $kname - version: $kver - arch: $arch\n";
			echo "     $notes \n";
			echo "\n";
			
			$id++;
		}
		echo "$footer\n\n";	
		echo "Please choose: ";
		$choice = getChoice();
		return "$choice";	
	}
	

	function selectBattery() {
		global $header; global $footer;	global $batterydb;
		system_call("clear");
		
		echo "$header\n\n";
		echo " Please select what Battery driver you wish to use... \n\n";
		
		$id = 0;
		while ($batterydb[$id] != ""){
			$name 		= $batterydb[$id]["name"];
			$kextname	= $batterydb[$id]["kextname"];
			$arch		= $batterydb[$id]["arch"];
			
			echo "  $id. $name - $kextname ($arch support)\n\n";
			
			$id++;
		}
		echo "$footer\n\n";	
		echo "Please choose: ";
		$choice = getChoice();
		return "$choice";	
	}
	
	
	
	function selectAUDIO() {
		global $header; global $footer;	global $audiodb;
		system_call("clear");
		
		echo "$header\n\n";
		echo " Please select what Audio driver you wish to use... \n\n";
		
		$id = 0;
		while ($audiodb[$id] != ""){
			$name 	= $audiodb[$id]["name"];
			$arch	= $audiodb[$id]["arch"];
			$notes	= $audiodb[$id]["notes"];
			
			echo "  $id. $name ($arch support)\n";
			echo "     $notes \n";
			echo "\n";
			
			$id++;
		}
		echo "$footer\n\n";	
		echo "Please choose: ";
		$choice = getChoice();
		return "$choice";	
	}
	
	
	
	function selectSE() {
		global $header; global $footer;	
		system_call("clear");
		echo "$header\n\n";
		echo "Do you wish to install SleepEnabler.kext ? \n";
		echo "SleepEnabler.kext can in most cases help to enable sleep on the computer...\n\n";
		echo "$footer\n\n";	
		echo "Please choose (yes or no): ";
		$choice = getChoice();
		return "$choice";	
	}
	
						
	function selectNULLCPU() {
		global $header; global $footer;	
		system_call("clear");
		echo "$header\n\n";	
        echo "Do you wish to install NullCPUPowerManagement.kext ? \n\n";
		echo " yes -> NullCPUPowerManagement.kext seems to make the system cooler by disabling Apples power management, \n";
		echo "    but it will disable sleep, to fix that just install SleepEnabler.kext	\n\n";
        echo " no -> Will use Apples power management, which in some cases causes higher cpu temp.\n\n";
		echo "$footer\n\n";	
		echo "Please choose (yes or no): ";
		$choice = getChoice();
		return "$choice";	
	}	
	
	
	function selectST() {
		global $header; global $footer;	
		system_call("clear");
		echo "$header\n\n";
		echo "Do you want emulated speedstep support ? \n\n";
		echo "Having speedstep support means that you can conserve battery life and keep your machine running cool. \n";
		echo "This is done by lowering the cpu performance when its not needed, and raising it again when you need it \n\n";
		echo "Using voodoopstate.kext and pstatemenu this can be done now (no more need for coolbook) \n\n";
		echo "NOTE: Choosing _NO_ will remove it if you had it installed before \n\n";
		echo "Do you wish to install the emulated speedstep support ? (yes or no) \n";
		echo "$footer\n\n";	
		echo "Please choose (yes or no): ";
		$choice = getChoice();
		return "$choice";
	}		
				

	function selectTSCsync() {
		global $header; global $footer;	
		system_call("clear");
		echo "$header\n\n";
		echo "Do you whant to install VoodooTSCsync ? \n\n";
		echo "VoodooTSCsync helps to sync the CPU cores on most CPUs such as c2d. \n";
		echo "However newer models like i5 and i7 do not seems to need it.\n\n";
		echo "$footer\n\n";	
		echo "Please choose (yes or no): ";
		$choice = getChoice();
		return "$choice";
	}



	function selectBuildMethod() {
			global $header; global $footer;
			system_call("clear");
			echo "$header\n\n";
			echo " EDP includes a set of predefined values (such as what PS2 kexts to use) - for how your model works best. \n\n";
			echo " Do you wish to use the pre-defined values or choose your own ? \n\n";
			echo "  1. Use predefined values \n\n";
			echo "  2. Define your own values \n\n";
			echo "$footer\n\n";	
			echo "Please choose: ";
			$choice = getChoice();
			return "$choice";
	}




					
	function confirmBuild() {
		global $os; global $header; global $footer; global $rootpath; global $workpath; global $slepath; global $modeldb; global $modelID;
		
		system_call("clear");
		echo "$header \n\n";
		echo "MODEL ID: $modelID \n";
		echo "OS: $os \n\n";
		echo "Please confirm that the information below is correct.... \n\n";
	
		echo "Paths: (Please doublecheck these) \n";
		echo " ROOT: $rootpath  -  EDP: $workpath  -  sle path: $slepath \n\n";

		echo " Model selected: ".$modeldb[$modelID]["name"]." (".$modeldb[$modelID]["desc"].")\n";
		echo " PS2 driver: ".$modeldb[$modelID]["ps2pack"]."\n";
		echo " Audio driver: ".$modeldb[$modelID]["audiopack"]."\n";
		echo " GMA950 Brightness fix: ".$modeldb[$modelID]["useGMA950brightfix"]."\n";	
		echo " Battery driver: ".$modeldb[$modelID]['batteryKext']."\n";
		echo " Install NullCPUPowerManagement: ".$modeldb[$modelID]["nullcpu"]."\n";
		echo " Install SleepEnabler: ".$modeldb[$modelID]["sleepEnabler"]."\n";
		echo " Install VoodooTSCsync: ".$modeldb[$modelID]["tscsync"]."\n";
		echo " Update Chameleon to latest version: Yes \n";
		echo " Use custom kernel: ".$modeldb[$modelID]["customKernel"]."\n";
		echo " Use custom chameleon: ".$modeldb[$modelID]["customCham"]."\n";
		echo " Emulated speedstep: ".$modeldb[$modelID]["emulatedST"]."\n\n";
		
		echo "Is this correct ? (y/n) \n\n";
			
		$choice = getChoice();
		return "$choice";		
	}
	
	function prepareCustomCopy() {
		global $modeldb; global $modelID; global $modelname;
		
		$modeldb["name"]						= "$modelname";
		$modeldb[$modelID]["ps2pack"]			= selectPS2();
		$modeldb[$modelID]["nullcpu"]			= selectNULLCPU();
		$modeldb[$modelID]["sleepEnabler"]		= selectSE();
		$modeldb[$modelID]["emulatedST"]		= selectST();
		$modeldb[$modelID]["tscsync"]			= selectTSCsync();
		$modeldb[$modelID]["audiopack"]			= selectAUDIO();
		$modeldb[$modelID]['batteryKext']		= selectBattery();

	}
	
	
	
?>