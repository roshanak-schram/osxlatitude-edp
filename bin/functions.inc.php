<?php

  //Include builder class
  include_once "html/classes/builder.php";
  include_once "html/classes/chamModules.php";
  include_once "html/classes/edp.php";
  include_once "html/classes/nvram.php";
  include_once "html/classes/kexts.php";  
  
  
  
//------------------> EDPweb functions -----------------------------------------------------------------------------------------------

function checkbox($title, $formname, $status) {
	if ($status == "yes") { $c = "checked"; }
	echo "<li class='checkbox'><span class='name'>$title</span><input name='$formname' type='checkbox' $c/> </li>\n";
}

//Writes out the html for the pageitemtop
function echoPageItemTOP($icon, $text) {
	echo "<div class='pageitem_top'><img src='$icon'><span><b>$text</span></div></b>\n";
}


//<-----------------------------------------------------------------------------------------------------------------------------------

//--- Get Value from Key in SMbios.plist
include_once __DIR__ . '/vendor/CFPropertyList/CFPropertyList.php';

function getValueFromSmbios($key, $default = null) {
    global $workpath;

    $file = $workpath . '/smbios.plist';

    if (file_exists($file)) {
        $plist = new CFPropertyList\CFPropertyList($file, CFPropertyList\CFPropertyList::FORMAT_XML);
        $dict  = $plist->toArray();

        if (array_key_exists($key, $dict)) {
            return $dict[$key];
        }
    }

    return $default;
}
//---

			
function updateCham() {
    // Note: Overtime we will add a function to make sure that the user have the latest version 
    // of cham distrobuted with EDP - until then, we will force the update on each build    
    echo "  Updating Chameleon to latest versions from EDP \n";
    system_call("cp -f /Extra/storage/boot /");
}


function EDPdoBuild() {
	global $modeldb; global $modelID; global $workpath; global $rootpath;

	//Start by defining our log file and cleaning it..
	$log = "$workpath/build.log";
	if (is_file("$log")) { 
		system_call("rm -Rf $log"); 
		system_call("<br>echo Building....<br><br> >$log");
	}
		
	//Check if myhack is up2date and ready for combat
	myHackCheck();
			
	//Step 1
	writeToLog("$workpath/build.log", "<br><br><b>Step 2) Copying Essential files to $workpath </b><br>");
	copyEssentials();

	//Step 2
	writeToLog("$workpath/build.log", "<br><b>Step 3) Preparing kexts for myHack.kext </b><br>");
	copyKexts();
			
	//Step 3
	writeToLog("$workpath/build.log", "<br><br><b>Step 4) Applying Chameleon settings.. </b><br>");
	updateCham();
	writeToLog("$workpath/build.log", "  Copying selected modules...</b><br>");
	copyChamModules($modeldb[$modelID]);
			
	writeToLog("$workpath/build.log", "<br><b>Step 5) Applying last minut fixes...</b><br>");
				lastMinFixes();
					
	//Step 4
	writeToLog("$workpath/build.log", "<br><b>Step 6) Calling myFix to copy kexts and generate kernelcache</b><br><pre>");
	system_call("stty -tostop; sudo myfix -q -t / >>$workpath/build.log 2>&1 &");
	writeToLog("$workpath/build.log", "<a name='myfix'></a>");
				
	echo "<script> document.location.href = 'workerapp.php?action=showBuildLog#myfix'; </script>";

	exit;
        		
}



function checkSVNrevs() {
    global $localrev, $workpath;

    $remoterev = exec("cd $workpath; svn info -r HEAD --username edp --password edp --non-interactive | grep -i \"Last Changed Rev\"");
    $remoterev = str_replace("Last Changed Rev: ", "", $remoterev);

    if ($localrev < $remoterev) {
        echo "\n   ---------------------------------------------------------------------------------------\n";
        echo "        !!! There is an update of EDP, please run option 2 to download the update !!!\n";
        echo "   ---------------------------------------------------------------------------------------\n\n";
    }
}
	
/**
 * Patch AHCI
 * @see http://www.insanelymac.com/forum/topic/280062-waiting-for-root-device-when-kernel-cache-used-only-with-some-disks-fix/page__st__60#entry1851722
 */
function patchAHCI() {
	global $workpath;
    system_call("cp -R /System/Library/Extensions/IOAHCIFamily.kext /Extra/Extensions");
    system_call("perl /Extra/bin/fixes/patch-ahci-mlion.pl >>$workpath/build.log");
}


/**
 * This function will download a kextpack from SVN if requested (or update it if allready exists) 
 */
function kextpackLoader($name) {
	global $edp_db, $workpath, $edp;
	if ($name != "") {
		$workfolder = "$workpath/storage/kpsvn/$name";
		if (is_dir("$workfolder")) {
			system_call("svn --non-interactive --username edp --password edp --force update $workfolder");
		}
		else {
			system_call("mkdir $workfolder; cd $workfolder; svn --non-interactive --username osxlatitude-edp-read-only --force co http://osxlatitude-edp.googlecode.com/svn/kextpacks/$name .");
		}
	}
} 


 
/**
 * Function to check if the model is allready checked out
 * if the model is not checked out it will check it out
 */
function svnModeldata($model) {
    global $workpath; global $edp;
    $modelfolder = "$workpath/model-data/$model";
    if (is_dir("$modelfolder")) {
        system_call("svn --non-interactive --username edp --password edp --force update $modelfolder");
    } else {
        system_call("mkdir $modelfolder; cd $modelfolder; svn --non-interactive --username osxlatitude-edp-read-only --force co http://osxlatitude-edp.googlecode.com/svn/model-data/$model .");
    }
}
	
/**
 * Function to check if myhack.kext exists in ale, and if it dosent for some weird reason... copy it there...
 */
function myHackCheck() {
    global $workpath, $slepath;

    if (!is_dir("$slepath/myHack.kext")) {
        $myhackkext = "$workpath/myHack.kext";
        system_call("rm -Rf `find -f path \"$myhackkext\" -type d -name .svn`");
        system_call("cp -R \"$workpath/myHack.kext\" $slepath");
    }
    if (!is_file("/usr/sbin/")) {
        system_call("cp \"$workpath/bin/myfix\" /usr/sbin/myfix; chmod 777 /usr/sbin/myfix");
    }
}
	
function edpCleaner() {
    global $slepath;
    
    if ($slepath != "") {
        if (!is_dir("$slepath/0EDP.kext")) {
            system_call("rm -Rf $slepath/0EDP.kext");
        }
    }
}


	
/**
 * Apply GMA950brightnessfix
 */
function GMA950brightnessfixCheck() {
    global $os, $workpath, $ee, $modeldb, $modelID;
    
    $needfix = $modeldb[$modelID]["useGMA950brightfix"];
    
    if ($needfix == "yes") {
        echo "  Applying GMA950 Brightness fix \n";
        system_call("cp -R $workpath/storage/fixes/gma950-brightness-fix/AppleIntelIntegratedFramebuffer.kext $ee");
    }
}	
	

/**
 * replace system_call() .. works with LWS also
 */
function system_call($data) {
    passthru("$data");
    echo str_repeat(' ', 254);
    flush();
}
	
function downloadAndRun($url, $filetype, $filename, $execpath) {
    echo "Making downloads folder in /Downloads and initiating download of $url\n\n";
    system_call("mkdir /downloads; cd /downloads; curl -O $url");
    echo "Mounting $filename... \n\n";
    
    if ($filetype == "dmg") {
        system_call("hdiutil attach /downloads/$filename >/dev/null");
    }
    
    echo "Executing the package installer... \n\n";
    system_call("open $execpath");
}	
	


function patchAppleIntelCPUPowerManagement() {
    global $ee, $slepath;
    system_call("cp -R $slepath/AppleIntelCPUPowerManagement.kext $ee/");
    system_call('perl -pi -e \'s|\xE2\x00\x00\x00\x0F\x30|\xE2\x00\x00\x00\x90\x90|g\' /Extra/Extensions/AppleIntelCPUPowerManagement.kext/Contents/MacOS/AppleIntelCPUPowerManagement');
}

	
function getVersion() {
    global $rootpath, $os_string;

    $path = "".$rootpath."System/Library/CoreServices/SystemVersion";
    $v = exec("defaults read $path ProductVersion");
    $r = '';

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
    if ($v == "10.9") 	{ $r="mav"; $os_string = "OSX Maverick $v"; }	
    if ($v == "10.9.0") { $r="mav"; $os_string = "OSX Maverick $v"; }	
    if ($v == "10.9.1") { $r="mav"; $os_string = "OSX Maverick $v"; }
    if ($v == "10.9.2") { $r="mav"; $os_string = "OSX Maverick $v"; }
    if ($v == "10.9.3") { $r="mav"; $os_string = "OSX Maverick $v"; }				
    return $r;
}
	
function AppleACPIfixCheck() {
    global $ee, $workpath, $slepath, $modeldb, $modelID;

    //Check if ACPIfix is selected
    if ($modeldb[$modelID]["useACPIfix"] == "yes") {
        echo "  Applying ACPI fix (Coolbook fix)\n";
        system_call("cp -R $workpath/storage/fixes/coolbook-fix/AppleACPIPlatform.kext $ee");

        if (is_dir("$slepath/AppleACPIPlatform.kext")) {
            //Create backup folder
            date_default_timezone_set('UTC');
            $date = date("d-m-Y");
            $backupfolder = "/backup/$date-AppleACPIPlatform.kext-$kver2";
            system_call("mkdir /backup");
            system_call("mkdir $backupfolder");
            system_call("mv $slepath/AppleACPIPlatform.kext $backupfolder");
        }
    }
}

function kernelcachefix() {
    global $workpath, $rootpath;
    
    $chkdir = $rootpath . "/System/Library/Caches/com.apple.kext.caches/Startup";
    $kerncachefile = $rootpath . "/System/Library/Caches/com.apple.kext.caches/Startup/kernelcache";

    if (!is_dir("$chkdir") && $workpath == "/Extra") {
        system_call("mkdir $chkdir");
        if (file_exists($kerncachefile)) {
            echo "\n\nWARNING: Falling back to EDP kernelcache generation - myfix was not successfull.. \n\n";
            system_call("kextcache -system-prelinked-kernel");
        }
    }
}
function copyEssentials() {
    global $workpath, $modeldb, $modelID, $os; global $edp;

    $modelName = $modeldb[$modelID]["name"];

    $edp->writeToLog("$workpath/build.log", "Cleaning up by system...<br>");
    edpCleaner();

    $file = "$workpath/smbios.plist";
    if (file_exists($file)) { system_call("rm $file"); }

    $file = "$workpath/org.chameleon.Boot.plist";
    if (file_exists($file)) { system_call("rm $file"); }

    $file = "$workpath/dsdt.aml";
    if (file_exists($file)) { system_call("rm $file"); }
        

    //Remove old SSDT table files
    $file = "$workpath/SSDT.aml";   if (file_exists($file)) { system_call("rm $file"); }
    $file = "$workpath/SSDT-1.aml"; if (file_exists($file)) { system_call("rm $file"); }
    $file = "$workpath/SSDT-2.aml"; if (file_exists($file)) { system_call("rm $file"); }
    $file = "$workpath/SSDT-3.aml"; if (file_exists($file)) { system_call("rm $file"); }
    $file = "$workpath/SSDT-4.aml"; if (file_exists($file)) { system_call("rm $file"); }
    $file = "$workpath/SSDT-5.aml"; if (file_exists($file)) { system_call("rm $file"); }

    $edp->writeToLog("$workpath/build.log", "  Copying COMMON system plists and dsdt.aml from $workpath/model-data/$modelName/common to $workpath<br>");
    $file = "$workpath/model-data/$modelName/common/smbios.plist";             if (file_exists($file)) { system_call("cp -f $file $workpath"); }
    $file = "$workpath/model-data/$modelName/common/org.chameleon.Boot.plist"; if (file_exists($file)) { system_call("cp -f $file $workpath"); }
    $file = "$workpath/model-data/$modelName/common/dsdt.aml";                 if (file_exists($file)) { system_call("cp -f $file $workpath"); }

    $edp->writeToLog("$workpath/build.log", "  Copying OS Specific system plists and dsdt.aml from $workpath/model-data/$modelName/$os to $workpath<br>");		
    $file = "$workpath/model-data/$modelName/$os/smbios.plist";             if (file_exists($file)) { system_call("cp -f $file $workpath"); }
    $file = "$workpath/model-data/$modelName/$os/org.chameleon.Boot.plist"; if (file_exists($file)) { system_call("cp -f $file $workpath"); }
    $file = "$workpath/model-data/$modelName/$os/dsdt.aml";                 if (file_exists($file)) { system_call("cp -f $file $workpath"); }					

    $edp->writeToLog("$workpath/build.log", "  Checking if your model includes SSDT dump files - will copy if any exists..<br>");
    $file = "$workpath/model-data/$modelName/common/SSDT.aml";   if (file_exists($file)) { system_call("cp -f $file $workpath"); }
    $file = "$workpath/model-data/$modelName/common/SSDT-1.aml"; if (file_exists($file)) { system_call("cp -f $file $workpath"); }
    $file = "$workpath/model-data/$modelName/common/SSDT-2.aml"; if (file_exists($file)) { system_call("cp -f $file $workpath"); }
    $file = "$workpath/model-data/$modelName/common/SSDT-3.aml"; if (file_exists($file)) { system_call("cp -f $file $workpath"); }
    $file = "$workpath/model-data/$modelName/common/SSDT-4.aml"; if (file_exists($file)) { system_call("cp -f $file $workpath"); }
    $file = "$workpath/model-data/$modelName/common/SSDT-5.aml"; if (file_exists($file)) { system_call("cp -f $file $workpath"); }			


    //Copy essentials from /Extra/include
    $edp->writeToLog("$workpath/build.log", "  Checking if we have any essential files in /Extra/include that will be used instead...<br>");
    if (is_file("$workpath/include/smbios.plist")) 				{ system_call("cp -f $workpath/include/smbios.plist /Extra"); }
    if (is_file("$workpath/include/org.chameleon.Boot.plist")) 	{ system_call("cp -f $workpath/include/org.chameleon.Boot.plist /Extra"); }
    if (is_file("$workpath/include/dsdt.aml")) 					{ system_call("cp -f $workpath/include/dsdt.aml /Extra"); }
    if (is_file("$workpath/include/SSDT.aml")) 					{ system_call("cp -f $workpath/include/SSDT.aml /Extra"); }
    if (is_file("$workpath/include/SSDT-1.aml")) 				{ system_call("cp -f $workpath/include/SSDT-1.aml /Extra"); }
    if (is_file("$workpath/include/SSDT-2.aml")) 				{ system_call("cp -f $workpath/include/SSDT-2.aml /Extra"); }
    if (is_file("$workpath/include/SSDT-3.aml")) 				{ system_call("cp -f $workpath/include/SSDT-3.aml /Extra"); }    
    if (is_file("$workpath/include/SSDT-4.aml")) 				{ system_call("cp -f $workpath/include/SSDT-4.aml /Extra"); }
    if (is_file("$workpath/include/SSDT-5.aml")) 				{ system_call("cp -f $workpath/include/SSDT-5.aml /Extra"); }    
}



//Copying kexts 
function copyKexts() {
    //Get vars from config.inc.php
    global $workpath, $rootpath, $slepath, $ps2db, $audiodb, $incpath, $wifidb, $modeldb, $modelID, $os, $ee, $batterydb, $landb, $fakesmcdb, $edp;
    
    //Get our class(s)
    global $builder;

    $modelName = $modeldb[$modelID]["name"];

    $edp->writeToLog("$workpath/build.log", "  Start by cleaning up in $ee..<br>");
    system_call("rm -Rf $ee/*.kext");

    //Checking if we need to patch AppleIntelCPUPowerManagement.kext
    $pathCPU = $modeldb[$modelID]["patchCPU"];
    if ($pathCPU == "yes") {
        patchAppleIntelCPUPowerManagement();
    }



    //New function for copying PS2 kexts from kextpacks
    $ps2id = $modeldb[$modelID]['ps2pack'];
    if ($ps2id != "" && $ps2id != "no") {
    	$name = $ps2db[$ps2id]["foldername"];
         
    	//Syncing kextpack to local storage
    	kextpackLoader("$name");

    	//Copying the kextpack to /Extra/Extentions
    	$edp->writeToLog("$workpath/build.log", "  Copying the PS2 controller kexts ($workpath/storage/kpsvn/$name) to $ee<br>");
		if ($name != "") { system_call("cp -R $workpath/storage/kpsvn/$name/. $ee"); }
	}
	
	



    //Copying custom kexts from inside include folder
    $edp->writeToLog("$workpath/build.log", "  Copying custom kexts from $workpath/include/Extensions to $ee/<br>");
    system_call("cp -R $workpath/include/Extensions/* $ee");

    //Copying audio kexts
    $audioid = $modeldb[$modelID]['audiopack'];
    $audiodir = $audiodb[$audioid]["foldername"];
    if ($modeldb[$modelID]['audiopack'] != "" && $modeldb[$modelID]['audiopack'] != "no") {
        $edp->writeToLog("$workpath/build.log", "  Copying the Audio kexts to $ee<br>");
        //Clean up
        if (is_dir("$slepath/HDAEnabler.kext")) {
            system_call("rm -Rf $slepath/HDAEnabler.kext");
        }
        if ($audioid == "buildin") {
        	if (is_dir("$workpath/model-data/$modelName/$os/applehda")) { system_call("cp -R $workpath/model-data/$modelName/$os/applehda/. $ee/"); }
        	else { 
        		if (is_dir("$workpath/model-data/$modelName/common/applehda")) { system_call("cp -R $workpath/model-data/$modelName/common/applehda/. $ee/"); }
        	}
	        
        } else { system_call("cp -R $workpath/storage/kextpacks/$audiodir/. $ee/"); }   
    }

    //Copying FakeSMC kextpack
    $fakesmcid = $modeldb[$modelID]['fakesmc'];
    $fakesmcdir = $fakesmcdb[$fakesmcid]["foldername"];
    if ($modeldb[$modelID]['fakesmc'] != "" && $modeldb[$modelID]['fakesmc'] != "no") {
        $edp->writeToLog("$workpath/build.log", "  Copying the FakeSMC kexts to $ee<br>");
        system_call("cp -R $workpath/storage/kextpacks/$fakesmcdir/. $ee/");
    }

    //Copying ethernet kexts
    if ($modeldb[$modelID]['ethernet'] != "" && $modeldb[$modelID]['ethernet'] != "no") {
        $lanid = $modeldb[$modelID]['ethernet'];
        $lankext = $landb[$lanid]['kextname'];
        $edp->writeToLog("$workpath/build.log", "  Copying the Lan kext to $ee ($lankext - ID: $lanid)<br>");
        if ($lankext != "") {
            system_call("cp -R $workpath/storage/kexts/networking/$lankext $ee/");
        }
    }

    //Copying wifi kexts
    if ($modeldb[$modelID]['wifikext'] != "" && $modeldb[$modelID]['wifikext'] != "no") {
        $wifid = $modeldb[$modelID]['wifikext'];
        $wififolder = $wifidb[$wifid]['foldername'];
        $wifikextname = $wifidb[$wifid]['kextname'];
        if ($wififolder != "") {
            $edp->writeToLog("$workpath/build.log", "  Copying the wifi kext to $ee ($wifikextname)<br>");
            system_call("cp -R $workpath/storage/kextpacks/$wififolder/. $ee/");
        }
    }

    //Checking if we need nullcpu
    if ($modeldb[$modelID]['nullcpu'] == "yes" || $modeldb[$modelID]['nullcpu'] == "y") {
        $edp->writeToLog("$workpath/build.log", "  Copying NullCPUPowerManagement.kext for disabling Apples native power management.. <br>");
        system_call("cp -R $workpath/storage/kexts/NullCPUPowerManagement.kext $ee");
    }

    //Checking if we need to patch AHCI
    if ($modeldb[$modelID]['patchAHCIml'] == "yes" || $modeldb[$modelID]['patchAHCIml'] == "y") {
        $edp->writeToLog("$workpath/build.log", "  Patching IOAHCIFamily.kext for OS: $os... <br>");
        if ($os == "ml") {
            patchAHCI();
        }
    } 
    
    
    //Checking if we need USB Roolback fix
    if ($modeldb[$modelID]['usbRollBack'] == "yes" || $modeldb[$modelID]['usbRollBack'] == "y") {
    	$edp->writeToLog("$workpath/build.log", "  Copying USB Roolback kexts... <br>");
    	system_call("cp -R $workpath/storage/kextpacks/usb_rollback/* $ee");
    } 



    
    //Copy optinal kexts
    $data = $modeldb[$modelID]['optionalpacks'];
    $array 	= explode(',', $data);
    
    foreach($array as $id) {
	    //Getting foldername from ID
        $name = $builder->getKextpackNameFromID("optionalpacks", "$id");
        
    	//Syncing kextpack to local storage
    	kextpackLoader("$name");

    	//Copying the kextpack to /Extra/Extentions
		$edp->writeToLog("$workpath/build.log", "  Copying optional kextpack: $workpath/storage/kpsvn/$name to $ee<br>");
		if ($name != "") { system_call("cp -R $workpath/storage/kpsvn/$name/. $ee"); }
	}



    //Checking if we need Sleepenabler
    if ($modeldb[$modelID]['sleepEnabler'] == "yes" || $modeldb[$modelID]['sleepEnabler'] == "y") {
        $edp->writeToLog("$workpath/build.log", "  Copying SleepEnabler.kext for enabling sleep...<br>");
        system_call("cp -R $workpath/storage/kexts/$os/SleepEnabler.kext $ee");
    }

    if ($modeldb[$modelID]['loadIOATAFamily'] == "yes") {
        $edp->writeToLog("$workpath/build.log", "  Copying IOATAFamily.kext to $ee.. <br>");
        system_call("cp -R $workpath/storage/kexts/IOATAFamily.kext $ee");
    }

    if ($modeldb[$modelID]['loadNatit'] == "yes") {
        $edp->writeToLog("$workpath/build.log", "  Copying Natit.kext to $ee.. <br>");
        system_call("cp -R $workpath/storage/kexts/natit.kext $ee");
    }

    if ($modeldb[$modelID]['tscsync'] == "yes" || $modeldb[$modelID]['tscsync'] == "y") {
        $edp->writeToLog("$workpath/build.log", "  Check if we need VoodooTSCSync.kext for syncing CPU cores...<br>");
        system_call("cp -R $workpath/storage/kexts/VoodooTSCSync.kext $ee");
    }

    if ($modeldb[$modelID]['emulatedST'] == "yes" || $modeldb[$modelID]['emulatedST'] == "y") {
        $edp->writeToLog("$workpath/build.log", "  Check if we are using emulated speedstep via voodoopstate and voodoopstatemenu <br>");
        system_call("cp -R $workpath/storage/kexts/VoodooPState.kext $ee");
        system_call("cp $workpath/storage/LaunchAgents/PStateMenu.plist /Library/LaunchAgents");
    } else {
        system_call("rm -rf /Library/LaunchAgents/PStateMenu.plist");
    }

    //Copy battery kexts
    if ($modeldb[$modelID]['batteryKext'] != "" && $modeldb[$modelID]['batteryKext'] != "no") {
        $battid = $modeldb[$modelID]['batteryKext'];
        $battkext = $batterydb[$battid]["kextname"];
        $edp->writeToLog("$workpath/build.log", "  Copying Battery kext ($battkext) to $ee <br>");
        system_call("cp -R $workpath/storage/kexts/battery/$os/$battkext $ee/");
    }

    //Check if we need a custom version of chameleon
    if ($modeldb[$modelID]['customCham'] == "yes" || $modeldb[$modelID]['customCham'] == "y") {
        $edp->writeToLog("$workpath/build.log", "  Copying custom chameleon to $rootpath.. <br>");
        system_call("rm -f $rootpath/boot");
        system_call("cp $workpath/model-data/$modelName/$os/boot $rootpath");
    }

    //Check if we need a custom made kernel
    if ($modeldb[$modelID]['customKernel'] == "yes" || $modeldb[$modelID]['customKernel'] == "y") {
        $edp->writeToLog("$workpath/build.log", "  Copying custom made kernel to $rootpath.. <br>");
        system_call("rm -f $rootpath/custom_kernel");
        system_call("cp $workpath/model-data/$modelName/$os/custom_kernel $rootpath");
    }
    
    $edp->writeToLog("$workpath/build.log", "  Copying standard kexts to $ee.. <br>");
    system_call("cp -R $workpath/storage/standard/common/Extensions/* $ee");

    $edp->writeToLog("$workpath/build.log", "  Copying $os kexts to $ee.. <br>");
    system_call("cp -R $workpath/storage/standard/$os/Extensions/* $ee");

    $edp->writeToLog("$workpath/build.log", "  Copying common kexts to $ee..<br>");
    $tf = "$workpath/model-data/$modelName/common/Extensions";
    system_call("cp -Rf $tf/* $ee");

    $edp->writeToLog("$workpath/build.log", "  Copying $os kexts to $ee.. <br>");
    $tf = "$workpath/model-data/$modelName/$os/Extensions";
    system_call("cp -Rf $tf/* $ee");

    $edp->writeToLog("$workpath/build.log", "  Applying fixes... <br>");
    AppleACPIfixCheck();
    GMA950brightnessfixCheck();

    $edp->writeToLog("$workpath/build.log", "  Applying custom plists, kexts etc.");
    //Copying kexts
    system_call("cp -R $incpath/Extensions/* $ee");
    //Copying any .AML files to /Extra
    system_call("cp -R $incpath/*.aml $workpath");
    //Copying any plists files to /Extra
    system_call("cp -R $incpath/*.plist $workpath");

    $edp->writeToLog("$workpath/build.log", "  Removing version control of kexts in $ee");
    system_call("rm -Rf `find -f path \"$ee\" -type d -name .svn`");
}

function isEmptyDir($dir) {
    if (($files = @scandir("$dir")) && (count($files) > 2)) {
        return "yes";
    } else {
        return "no";
    }
}
	
?>