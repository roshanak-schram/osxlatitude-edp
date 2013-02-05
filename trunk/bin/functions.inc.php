<?php

	function updateEDP() {
        include_once "config.inc.php";
        global $edpmode;
		system_call("svn --non-interactive --username edp --password edp --force update $workpath");
		echo " Your EDP have been updated...<br>\n";
		exit;
		
	}
	
	function updateCham() {
		//Note: Overtime we will add a function to make sure that the user have the latest version of cham distrobuted with EDP - until then, we will force the update on each build
		global $workpath; global $rootpath;
		echo "  Updating Chameleon to latest versions from EDP \n";
		system_call("cp -f /Extra/storage/boot /");
	}
	function checkSVNrevs() {
		global $localrev; global $workpath;
		
		$remoterev	= exec("cd $workpath; svn info -r HEAD --username edp --password edp --non-interactive | grep -i \"Last Changed Rev\"");
		$remoterev	= str_replace("Last Changed Rev: ", "", $remoterev);  
    		
		if ($localrev < $remoterev) { 
		  echo "\n   ---------------------------------------------------------------------------------------\n";
			echo "        !!! There is an update of EDP, please run option 5 to download the update !!!\n";
		  echo "   ---------------------------------------------------------------------------------------\n\n";
		}		
	}
	
	//Function to check if the model is allready checked out, if the model is not checked out it will check it out
	function svnModeldata($model) {
		global $workpath; global $rootpath;
		$modelfolder = "$workpath/model-data/$model";
		if (is_dir("$modelfolder")) { 
			echo "  Locale cache found for $model, updating cache....\n";
			system_call("svn --non-interactive --username edp --password edp --force update $modelfolder");
		}
		else {
			echo "  Locale cache NOT found for $model, downloading....\n"; 
			system_call("mkdir $modelfolder; cd $modelfolder; svn --non-interactive --username osxlatitude-edp-read-only --force co http://osxlatitude-edp.googlecode.com/svn/model-data/$model .");
		}
	}
	
	
	//Function to check if myhack.kext exists in ale, and if it dosent for some weird reason... copy it there...
	function myHackCheck() {
		global $verbose; global $workpath; global $edpplugin; global $ee; global $slepath; global $rootpath;
		if (!is_dir("$slepath/myHack.kext")) 	{
			$myhackkext = "$workpath/myHack.kext";
			system_call("rm -Rf `find -f path \"$myhackkext\" -type d -name .svn`"); 
			system_call("cp -R \"$workpath/myHack.kext\" $slepath");
		}
		if (!is_file("/usr/sbin/
		")) 		{ system_call("cp \"$workpath/bin/myfix\" /usr/sbin/myfix; chmod 777 /usr/sbin/myfix"); }
		
	}
	
	function edpCleaner() {
		global $verbose; global $workpath; global $edpplugin; global $ee; global $slepath;
		if ($slepath != "") { if (!is_dir("$slepath/0EDP.kext")) { system_call("rm -Rf $slepath/0EDP.kext"); } }
	}

	function getKextVersion($kext) {
		global $workpath;
		if (!is_dir($kext)) { return "0.00"; } 
		include_once "$workpath/bin/PlistParser.inc";
		$parser = new plistParser();
		$plist = $parser->parseFile("$kext/Contents/Info.plist");
		reset($plist);
		while (list($key, $value) = each($plist)) {
			if ($key == "CFBundleShortVersionString") {
			return "$value";
			}
		}
	}
	
	//Function to apply GMA950brightnessfix
	function GMA950brightnessfixCheck() {
		global $verbose; global $os; global $workpath; global $ee; global $cachepath; global $rootpath; global $modeldb; global $modelID;
		$needfix = $modeldb[$modelID]["useGMA950brightfix"];
		if ($needfix == "yes") {
			if ($os == "lion") { system_call("cp -R $workpath/storage/fixes/gma950-brightness-fix/AppleIntelIntegratedFramebuffer.kext $ee"); }
		}
		
	}	
	//Function to build from existing config
	function buildPresent() {
		global $verbose; global $os; global $workpath; global $ee; global $cachepath; global $rootpath;

		system_call("clear");
		echo "Doing build based on existing configuration \n\n";
		
  		echo "  Removing version control of kexts in $ee \n";
  		system_call("rm -Rf `find -f path \"$ee\" -type d -name .svn`"); 	
  				
  		echo "  Calling myFix to generate new cache…\n";
  		system_call("myfix -q -t /");
  		kernelcachefix();  				
		
	}
	//This function is used to make a build from /Extra/custom
	function doCustomBuild() {
		global $verbose; global $os; global $workpath; global $ee; global $cachepath; global $rootpath;

		$custompath = "$workpath/custom";
		system_call("clear");
		
		echo "  Cleaning up in $workpathn";
			system_call("rm -Rf $workpath/dsdt.aml");
			system_call("rm -Rf $workpath/smbios.plist");
			system_call("rm -Rf $workpath/org.chameleon.boot.plist");
	
		echo "  Copying kexts from $custompath/Extensions to $ee \n";
			system_call("cp -R $custompath/Extensions/* $ee");
	
  		echo "  Copying smbios.plist, dsdt.aml and org.chameleon.boot.plust from $custompath to $workpath \n";
  			system_call("cp $custompath/smbios.plist $workpath");
  			system_call("cp $custompath/org.chameleon.boot.plist $workpath");
  			system_call("cp $custompath/dsdt.aml $workpath");
			  		
  		echo "  Removing version control of kexts in $ee \n";
  			system_call("rm -Rf `find -f path \"$ee\" -type d -name .svn`"); 	
  				
  		echo "  Calling myFix to generate new cache…\n";
  		system_call("myfix -q -t /");
  		kernelcachefix();
  		updateCham();
  		
  				
	}

	//Function to replace system_call() .. works with LWS also
	function system_call($data) {
		global $edpmode;
		passthru("$data");
		if ($edpmode = "web") { echo str_repeat(' ', 254); flush(); }
	}

	
	
	function downloadAndRun($url, $filetype, $filename, $execpath) {
			echo "Making downloads folder in /Downloads and initiating download of $url\n\n";
				system_call("mkdir /downloads; cd /downloads; curl -O $url");
			echo "Mounting $filename... \n\n";
			if ($filetype == "dmg") { system_call("hdiutil attach /downloads/$filename >/dev/null"); }
				echo "Executing the package installer... \n\n";
				system_call("open $execpath");					
	}	
	
	
	function getChoice() {
		$stdin = fopen('php://stdin', 'r'); 
		$choice = fgets($stdin,100);
		$choice = trim($choice);
		fclose($stdin);
		return "$choice";
	}

        function patchAppleIntelCPUPowerManagement() {
                global $workpath; global $ee; global $slepath;
                echo "  Patching AppleIntelCPUPowerManagement.kext \n";
                system_call("cp -R $slepath/AppleIntelCPUPowerManagement.kext $ee/");
                system_call('perl -pi -e \'s|\xE2\x00\x00\x00\x0F\x30|\xE2\x00\x00\x00\x90\x90|g\' /Extra/Extensions/AppleIntelCPUPowerManagement.kext/Contents/MacOS/AppleIntelCPUPowerManagement');
        }
	function copyKextToSLE($kext, $frompath) {
		global $slepath; global $workpath;
		
		//Create backup folder
		date_default_timezone_set('UTC');
		$date = date("d-m-Y");
		$backupfolder = "/backup/$date";
		system_call("mkdir /backup"); system_call("mkdir $backupfolder");
		system_call("rm -Rf $backupfolder/*");
		
		//Do backup
		echo "Copying old $slepath/$kext to $backupfolder \n";		
		system_call("cp -R $slepath/$kext $backupfolder");

		//Remove the present kext
		system_call("rm -R $slepath/$kext");
		
		echo "Copying $workpath/$frompath/$kext to $slepath/ \n";
		system_call("cp -R $workpath/$frompath/$kext $slepath/");
		
		system_call("chown -R root:wheel $slepath/$kext");
		system_call("chmod -R 755 \"$slepath/$kext\"");
		system_call("diskutil repairPermissions $rootpath");
		system_call("clear");	
		echo "Building new cache for kexts in $slepath \n";
		system_call("touch \"$slepath\"");
		echo "Fix applied.. return to menu in 2 secs.."; system_call("sleep 2");	
		loadFixsystem_call();		
	}
	
	
	function getVersion() {
		global $rootpath; global $os_string;
		$path = "".$rootpath."System/Library/CoreServices/SystemVersion";
		$v = exec("defaults read $path ProductVersion");
		if ($v == "10.6")   { $r="sl"; $os_string = "MacOS X Snow Leopard $v"; }		
		if ($v == "10.6.0") { $r="sl"; $os_string = "MacOS X Snow Leopard $v"; }
		if ($v == "10.6.1") { $r="sl"; $os_string = "MacOS X Snow Leopard $v"; }
		if ($v == "10.6.2") { $r="sl"; $os_string = "MacOS X Snow Leopard $v"; }
		if ($v == "10.6.3") { $r="sl"; $os_string = "MacOS X Snow Leopard $v"; }						
		if ($v == "10.6.4") { $r="sl"; $os_string = "MacOS X Snow Leopard $v"; }
		if ($v == "10.6.5") { $r="sl"; $os_string = "MacOS X Snow Leopard $v"; }
		if ($v == "10.6.6") { $r="sl"; $os_string = "MacOS X Snow Leopard $v"; }
		if ($v == "10.6.7") { $r="sl"; $os_string = "MacOS X Snow Leopard $v"; }
		if ($v == "10.6.8") { $r="sl"; $os_string = "MacOS X Snow Leopard $v"; }
		if ($v == "10.6.9") { $r="sl"; $os_string = "MacOS X Snow Leopard $v"; }
		if ($v == "10.7")   { $r="lion"; $os_string = "MacOS X Lion $v"; }			
		if ($v == "10.7.0") { $r="lion"; $os_string = "MacOS X Lion $v"; }
		if ($v == "10.7.1") { $r="lion"; $os_string = "MacOS X Lion $v"; }
		if ($v == "10.7.2") { $r="lion"; $os_string = "MacOS X Lion $v"; }
		if ($v == "10.7.3") { $r="lion"; $os_string = "MacOS X Lion $v"; }
		if ($v == "10.7.4") { $r="lion"; $os_string = "MacOS X Lion $v"; }
		if ($v == "10.7.5") { $r="lion"; $os_string = "MacOS X Lion $v"; }
		if ($v == "10.7.6") { $r="lion"; $os_string = "MacOS X Lion $v"; }
		if ($v == "10.7.7") { $r="lion"; $os_string = "MacOS X Lion $v"; }
		if ($v == "10.8")   { $r="ml"; $os_string = "OSX Mountain Lion $v"; }
		if ($v == "10.8.0") { $r="ml"; $os_string = "OSX Mountain Lion $v"; }	
		if ($v == "10.8.1") { $r="ml"; $os_string = "OSX Mountain Lion $v"; }	
		if ($v == "10.8.2") { $r="ml"; $os_string = "OSX Mountain Lion $v"; }	
		if ($v == "10.8.3") { $r="ml"; $os_string = "OSX Mountain Lion $v"; }
		if ($v == "10.8.4") { $r="ml"; $os_string = "OSX Mountain Lion $v"; }	
		if ($v == "10.8.5") { $r="ml"; $os_string = "OSX Mountain Lion $v"; }	
		if ($v == "10.8.6") { $r="ml"; $os_string = "OSX Mountain Lion $v"; }	
		if ($v == "10.8.7") { $r="ml"; $os_string = "OSX Mountain Lion $v"; }				
		return "$r";
	
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
	
function AppleACPIfixCheck() {
		global $os; global $ee; global $header; global $footer; global $rootpath; global $workpath; global $slepath; global $modeldb; global $modelID;
		
		//Check if ACPIfix is selected
		if ($modeldb[$modelID]["useACPIfix"] == "yes") {
			system_call("cp -R $workpath/storage/fixes/coolbook-fix/AppleACPIPlatform.kext $ee");

			if (is_dir("$slepath/AppleACPIPlatform.kext")) { 
				//Create backup folder
				date_default_timezone_set('UTC');
				$date = date("d-m-Y");
				$backupfolder = "/backup/$date-AppleACPIPlatform.kext-$kver2";
				system_call("mkdir /backup"); system_call("mkdir $backupfolder");
				system_call("mv $slepath/AppleACPIPlatform.kext $backupfolder");
			}
		}
	
}

function kernelcachefix() {
		global $workpath; global $rootpath;
		$chkdir = "".$rootpath."/System/Library/Caches/com.apple.kext.caches/Startup";
		$kerncachefile = "".$rootpath."/System/Library/Caches/com.apple.kext.caches/Startup/kernelcache";
		
		if (!is_dir("$chkdir") && $workpath == "/Extra") {
			system_call("mkdir $chkdir");
			if (file_exists($kerncachefile)) {
				echo "\n\nWARNING: Falling back to EDP kernelcache generation - myfix was not successfull.. \n\n";
				system_call("kextcache -system-prelinked-kernel");
			}
			
		}
	
}
function copyEssentials() {
		global $workpath; global $rootpath; global $modeldb; global $modelID; global $os;
		$modelName = $modeldb[$modelID]["name"];
			
		echo "  Cleaning up by system...\n";
		edpCleaner();
		
		$file = "$workpath/smbios.plist";
		if (file_exists($file)) { system_call("rm $file"); }

		$file = "$workpath/org.chameleon.Boot.plist";
		if (file_exists($file)) { system_call("rm $file"); }
				
		$file = "$workpath/dsdt.aml";
		if (file_exists($file)) { system_call("rm $file"); }
		
		//Remove old SSDT table files
		$file = "$workpath/SSDT.aml"; if (file_exists($file)) { system_call("rm $file"); }
		$file = "$workpath/SSDT-1.aml"; if (file_exists($file)) { system_call("rm $file"); }
		$file = "$workpath/SSDT-2.aml"; if (file_exists($file)) { system_call("rm $file"); }
		$file = "$workpath/SSDT-3.aml"; if (file_exists($file)) { system_call("rm $file"); }
		$file = "$workpath/SSDT-4.aml"; if (file_exists($file)) { system_call("rm $file"); }
		$file = "$workpath/SSDT-5.aml"; if (file_exists($file)) { system_call("rm $file"); }


  		echo "  Copying COMMON system plists and dsdt.aml from $workpath/model-data/$modelName/common to $workpath \n\n";
  			$file = "$workpath/model-data/$modelName/common/smbios.plist"; 				if (file_exists($file)) { system_call("cp -f $file $workpath"); }
  			$file = "$workpath/model-data/$modelName/common/org.chameleon.Boot.plist"; 	if (file_exists($file)) { system_call("cp -f $file $workpath"); }
  			$file = "$workpath/model-data/$modelName/common/dsdt.aml"; 					if (file_exists($file)) { system_call("cp -f $file $workpath"); }
		
  		echo "  Copying OS Specific system plists and dsdt.aml from $workpath/model-data/$modelName/$os to $workpath \n";
  			$file = "$workpath/model-data/$modelName/$os/smbios.plist"; 				if (file_exists($file)) { system_call("cp -f $file $workpath"); }
  			$file = "$workpath/model-data/$modelName/$os/org.chameleon.Boot.plist"; 	if (file_exists($file)) { system_call("cp -f $file $workpath"); }
  			$file = "$workpath/model-data/$modelName/$os/dsdt.aml"; 					if (file_exists($file)) { system_call("cp -f $file $workpath"); }					

		echo "  Checking if your model includes SSDT dump files - will copy if any exists..\n\n";
			$file = "$workpath/model-data/$modelName/common/SSDT.aml"; 		if (file_exists($file)) { system_call("cp -f $file $workpath"); }
			$file = "$workpath/model-data/$modelName/common/SSDT-1.aml"; 	if (file_exists($file)) { system_call("cp -f $file $workpath"); }
			$file = "$workpath/model-data/$modelName/common/SSDT-2.aml"; 	if (file_exists($file)) { system_call("cp -f $file $workpath"); }
			$file = "$workpath/model-data/$modelName/common/SSDT-3.aml"; 	if (file_exists($file)) { system_call("cp -f $file $workpath"); }
			$file = "$workpath/model-data/$modelName/common/SSDT-4.aml"; 	if (file_exists($file)) { system_call("cp -f $file $workpath"); }
			$file = "$workpath/model-data/$modelName/common/SSDT-5.aml"; 	if (file_exists($file)) { system_call("cp -f $file $workpath"); }			
}


function copyKexts() {
	//Get vars from config.inc.php
	global $verbose; global $workpath; global $rootpath; global $slepath; global $ps2db; global $audiodb; global $incpath;
	global $edpplugin; global $cachepath; global $modeldb; global $modelID; global $os; global $ee; global $batterydb;
	
	$modelName = $modeldb[$modelID]["name"];


	echo "  Start by cleaning up in $ee\n";
	system_call("rm -Rf $ee/*");
	
	//Checking if we need to patch AppleIntelCPUPowerManagement.kext
		$pathCPU = $modeldb[$modelID]["patchCPU"];
		if ($pathCPU == "yes") { patchAppleIntelCPUPowerManagement(); }
		
		
	echo "  Copying the PS2 controller kexts (mouse+keyboard driver) to $ee \n";
		$ps2id 	= $modeldb[$modelID]['ps2pack'];
		if ($ps2id != "" && $ps2id != "no") {
			$ps2dir = $ps2db[$ps2id]["foldername"];
			system_call("cp -R $workpath/storage/kextpacks/$ps2dir/. $ee/");
		}				

	echo "  Copying custom kexts from $workpath/include/Extensions to $ee/\n";
		system_call("cp -R $workpath/include/Extensions/* $ee");
		
		
	
		$audioid = $modeldb[$modelID]['audiopack']; $audiodir = $audiodb[$audioid]["foldername"];
		if ($modeldb[$modelID]['audiopack'] != "" && $modeldb[$modelID]['audiopack'] != "no") {
			echo "  Copying the Audio kexts to $ee <br>\n";
			//Clean up
			if (is_dir("$slepath/HDAEnabler.kext")) { system_call("rm -Rf $slepath/HDAEnabler.kext"); }
		
			//Hack for loading STAC9200 correctly durring boot
			if ($audioid == "3") { 
				if (is_dir("$slepath/AppleHDA.kext")) { system_call("rm -Rf $slepath/AppleHDA.kext"); }
				system_call("cp -R $workpath/storage/kextpacks/$audiodir/HDAEnabler.kext $slepath/");
				}
				system_call("cp -R $workpath/storage/kextpacks/$audiodir/. $ee/");
		}
		
		

		
					
	
	if ($modeldb[$modelID]['nullcpu'] == "yes" || $modeldb[$modelID]['nullcpu'] == "y") {
		echo "  Copying NullCPUPowerManagement.kext for disabling Apples native power management.. <br>\n";
		system_call("cp -R $workpath/storage/kexts/NullCPUPowerManagement.kext $ee");
	}

				
	
	if ($modeldb[$modelID]['sleepEnabler'] == "yes" || $modeldb[$modelID]['sleepEnabler'] == "y") {
		echo "  Check if we need SleepEnabler.kext for enabling sleep...\n"; 
		system_call("cp -R $workpath/storage/kexts/$os/SleepEnabler.kext $ee");
	}

	
	if ($modeldb[$modelID]['loadIOATAFamily'] == "yes") {
		echo "  Copying IOATAFamily.kext to $ee.. \n";
		system_call("cp -R $workpath/storage/kexts/IOATAFamily.kext $ee");
	}

	if ($modeldb[$modelID]['loadNatit'] == "yes") {
		echo "  Copying Natit.kext to $ee.. \n";
		system_call("cp -R $workpath/storage/kexts/natit.kext $ee");
	}
	
		
	
	if ($modeldb[$modelID]['tscsync'] == "yes" || $modeldb[$modelID]['tscsync'] == "y") 	{
		echo "  Check if we need VoodooTSCSync.kext for syncing CPU cores...<br>\n"; 
		system_call("cp -R $workpath/storage/kexts/VoodooTSCSync.kext $ee");
	}


	
	if ($modeldb[$modelID]['emulatedST'] == "yes" || $modeldb[$modelID]['emulatedST'] == "y") 	{
		echo "  Check if we are using emulated speedstep via voodoopstate and voodoopstatemenu <br>\n"; 
		system_call("cp -R $workpath/storage/kexts/VoodooPState.kext $ee");
		system_call("cp $workpath/storage/LaunchAgents/PStateMenu.plist /Library/LaunchAgents");
	} else { system_call("rm -rf /Library/LaunchAgents/PStateMenu.plist"); }

								
	//Copy battery kexts
	
	if ($modeldb[$modelID]['batteryKext'] != "" && $modeldb[$modelID]['batteryKext'] != "no") {
		$battid 	= $modeldb[$modelID]['batteryKext'];
		$battkext 	= $batterydb[$battid]["kextname"];
		echo "  Copying Battery kext ($battkext) to $ee \n";
		system_call("cp -R $workpath/storage/kexts/battery/$os/$battkext $ee/");
	}	
		
	
				
	//Check if we need a custom version of chameleon
	if ($modeldb[$modelID]['customCham'] == "yes" || $modeldb[$modelID]['customCham'] == "y") 	{
		echo "  Copying custom chameleon to $rootpath.. \n";
		system_call("rm -f $rootpath/boot");
		system_call("cp $workpath/model-data/$modelName/$os/boot $rootpath");
	}

            	
	//Check if we need a custom made kernel
	if ($modeldb[$modelID]['customKernel'] == "yes" || $modeldb[$modelID]['customKernel'] == "y") 	{
		echo "  Copying custom made kernel to $rootpath.. \n";
		system_call("rm -f $rootpath/custom_kernel");
		system_call("cp $workpath/model-data/$modelName/$os/custom_kernel $rootpath");
	}			


	//Checking if system is using a common network kexts
	if ($modeldb[$modelID]['ethernet'] != "" && $modeldb[$modelID]['ethernet'] != "no") {
		$netkext = $modeldb[$modelID]['ethernet'];
		//echo "  Copying $netkext to $ee...\n";
		//system_call("cp -R $workpath/storage/kexts/networking/$netkext $ee");
	}
		
	

				
	echo "  Copying standard kexts to $ee.. \n\n";
	system_call("cp -R $workpath/storage/standard/common/Extensions/* $ee");
					
	echo "  Copying $os kexts to $ee.. \n\n";
	system_call("cp -R $workpath/storage/standard/$os/Extensions/* $ee");


	echo "  Copying common kexts to $ee.. \n\n";
	$tf = "$workpath/model-data/$modelName/common/Extensions";
	system_call("cp -Rf $tf/* $ee");
		
		
	echo "  Copying $os kexts to $ee.. \n\n";
	$tf = "$workpath/model-data/$modelName/$os/Extensions";
	system_call("cp -Rf $tf/* $ee");
	
	echo "  Checking if we need ACPIfix \n";
	AppleACPIfixCheck();
	
	echo "  Checking if we need GMA950 Brightness fix \n";
	GMA950brightnessfixCheck();
	
	
	echo "  Applying custom plists, kexts etc. (you can ignore copy errors here, it just means the folder is empty)\n";
		//Copying kexts
		system_call("cp -R $incpath/Extensions/* $ee");
		//Copying any .AML files to /Extra
		system_call("cp -R $incpath/*.aml $workpath");
		//Copying any plists files to /Extra
		system_call("cp -R $incpath/*.plist $workpath");
		
		
	
	echo "  Removing version control of kexts in $ee \n\n";
	system_call("rm -Rf `find -f path \"$ee\" -type d -name .svn`");
				
	
}


function isEmptyDir($dir) {
	if ( ($files = @scandir("$dir")) && (count($files) > 2) ) { return "yes"; } else { return "no"; }			
}
	
	
		
?>
