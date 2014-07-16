<?php
  
  //Include builder class
  include_once "classes/chamModules.php";
  include_once "classes/edp.php";
  include_once "classes/nvram.php";
  include_once "classes/kexts.php";  
    
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

// Returns Kextpack data from id
	function getKextpackDataFromID($table, $id) {
		if ($table != "" && $id != "") {
			global $edp_db;
			$stmt = $edp_db->query("SELECT * FROM $table where id = '$id'");
			$stmt->execute(); $result = $stmt->fetchAll(); $kprow = $result[0];
			return $kprow;
		}		
	}

// Returns model data from id
	function getModelDataFromID($sysType, $modelid) {
		
			global $edp_db;
			
			switch ($sysType) {
 			  case "Notebook":
 			  case "Ultrabook":
 			  case "Tablet":
 			  	$query = "SELECT * FROM modelsPortable WHERE type = '$sysType' AND id = '$modelid'";
 			  break;
 			  
 			  case "Desktop":
 			  case "Workstation":
 			  case "AllinOnePC":
 			  	$query = "SELECT * FROM  modelsDesk WHERE type = '$sysType' AND id = '$modelid'";
 			  break;
 			}
 	
			$stmt = $edp_db->query($query);
			$stmt->execute(); $result = $stmt->fetchAll(); 
			$mdata = $result[0]; //get first row
			return $mdata;
   }
	
function getSystemTypeValue() {
    global $edp_db;

    $return = '';

	$result = $edp_db->query("SELECT DISTINCT type FROM modelsPortable ORDER BY type");

    foreach($result as $row) {
        $return .= '<option value="' . $row['type'] . '">&nbsp;&nbsp;' . $row['type'] . '</option>';
    }
    
	$result = $edp_db->query("SELECT DISTINCT type FROM modelsDesk ORDER BY type");

    foreach($result as $row) {
        $return .= '<option value="' . $row['type'] . '">&nbsp;&nbsp;' . $row['type'] . '</option>';
    }

    return $return;
}

//--- Get EDP builder Model / Vendor / series values for the user to select from
function builderGetVendorValuebyID($sysType, $modelid) {
    global $edp_db;

	switch ($sysType) {
 			  case "Notebook":
 			  case "Ultrabook":
 			  case "Tablet":
 			  	$query = "SELECT DISTINCT vendor FROM modelsPortable WHERE type = '$sysType' AND id = '$modelid'";
 			  break;
 			  
 			  case "Desktop":
 			  case "Workstation":
 			  case "AllinOnePC":
 			  	$query = "SELECT DISTINCT vendor FROM  modelsDesk WHERE type = '$sysType' AND id = '$modelid'";
 			  break;
 	}
 	
	$stmt = $edp_db->query($query);
	$stmt->execute();
	$bigrow = $stmt->fetchAll(); $mdrow = $bigrow[0];
    return $mdrow[vendor];
}

function builderGetSeriesValuebyID($sysType, $modelid) {
    global $edp_db;

	switch ($sysType) {
 			  case "Notebook":
 			  case "Ultrabook":
 			  case "Tablet":
 			  	$query = "SELECT DISTINCT series FROM modelsPortable WHERE type = '$sysType' AND id = '$modelid'";
 			  break;
 			  
 			  case "Desktop":
 			  case "Workstation":
 			  case "AllinOnePC":
 			  	$query = "SELECT DISTINCT series FROM  modelsDesk WHERE type = '$sysType' AND id = '$modelid'";
 			  break;
 	}
 	
	$stmt = $edp_db->query($query);
	$stmt->execute();
	$bigrow = $stmt->fetchAll(); $mdrow = $bigrow[0];
    return $mdrow[series];
}

function builderGetGenValuebyID($sysType, $modelid) {
    global $edp_db;

	switch ($sysType) {
 			  case "Notebook":
 			  case "Ultrabook":
 			  case "Tablet":
 			  	$query = "SELECT DISTINCT generation FROM modelsPortable WHERE type = '$sysType' AND id = '$modelid'";
 			  break;
 			  
 			  case "Desktop":
 			  case "Workstation":
 			  case "AllinOnePC":
 			  	$query = "SELECT DISTINCT generation FROM  modelsDesk WHERE type = '$sysType' AND id = '$modelid'";
 			  break;
 	}
 	
	$stmt = $edp_db->query($query);
	$stmt->execute();
	$bigrow = $stmt->fetchAll(); $mdrow = $bigrow[0];
    return $mdrow[generation];
}

function builderGetVendorValues($sysType) {
    global $edp_db;

 	switch ($sysType) {
 			  case "Notebook":
 			  case "Ultrabook":
 			  case "Tablet":
 			  	$query = "SELECT DISTINCT vendor FROM modelsPortable WHERE type = '$sysType'";
 			  break;
 			  
 			  case "Desktop":
 			  case "Workstation":
 			  case "AllinOnePC":
 			  	$query = "SELECT DISTINCT vendor FROM  modelsDesk WHERE type = '$sysType'";
 			  break;
 	}
  	
    $result = $edp_db->query($query);
    $return = '';

    foreach($result as $row) {
       $return .= '<option value="' . $row['vendor'] . '">&nbsp;&nbsp;' . $row['vendor'] . '</option>';
    }

    return '<option value="" >&nbsp;&nbsp;Select vendor...</option>' . $return;
}
function builderGetSerieValues($sysType, $vendor) {
    global $edp_db;

	switch ($sysType) {
 			  case "Notebook":
 			  case "Ultrabook":
 			  case "Tablet":
 			  	$query = "SELECT DISTINCT vendor, series FROM modelsPortable WHERE type = '$sysType' AND vendor = '$vendor' ORDER BY series";
 			  break;
 			  
 			  case "Desktop":
 			  case "Workstation":
 			  case "AllinOnePC":
 			  	$query = "SELECT DISTINCT vendor, series FROM  modelsDesk WHERE type = '$sysType' AND vendor = '$vendor' ORDER BY series";
 			  break;
 	}
 	
    $result = $edp_db->query($query);
    $return = '';

    foreach($result as $row) {
        $return .= '<option value="' . $row['series'] . '">&nbsp;&nbsp;' . $row['vendor'] . ' ' . $row['series'] . ' </option>';
    }

    return '<option value="" >&nbsp;&nbsp;Select series...</option>' . $return;
}
function builderGetModelValues($sysType, $vendor, $series, $generation) {
    global $edp_db;

	switch ($sysType) {
 			  case "Notebook":
 			  case "Ultrabook":
 			  case "Tablet":
 			  	$query = "SELECT DISTINCT * FROM modelsPortable WHERE type = '$sysType' AND vendor = '$vendor' AND series = '$series' ORDER BY type";
 			  break;
 			  
 			  case "Desktop":
 			  case "Workstation":
 			  case "AllinOnePC":
 			  	$query = "SELECT DISTINCT * FROM  modelsDesk WHERE type = '$sysType' AND vendor = '$vendor' AND series = '$series' ORDER BY type";
 			  break;
 	}
 	
    $result = $edp_db->query($query);
    $return = '';

    foreach($result as $row) {
    if($row['generation'] != "")
        	$return .= '<option value="' . $row['id'] . '">&nbsp;&nbsp;' . $row[desc] . ' (' . $row['generation'] .')  </option>';
        else
        	$return .= '<option value="' . $row['id'] . '">&nbsp;&nbsp;' . $row[desc] . '  </option>';
    }

    return '<option value="" >&nbsp;&nbsp;Select model...</option>' . $return;
}
//---

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
 * This function will download a kextpack from SVN if requested (or update it if allready exists) 
 */
function kextpackLoader($categ, $fname, $name) {
		global $workpath, $edp, $ee;
    	    	
    	if(!is_dir("$workpath/kpsvn/dload/statFiles"))
			$createStatFile = "mkdir $workpath/kpsvn/dload/statFiles; cd $workpath/kpsvn/dload/statFiles; touch $fname.txt";
    	else		
			$createStatFile = "cd $workpath/kpsvn/dload/statFiles; touch $fname.txt";	
		
		$endStatFile = "cd $workpath/kpsvn/dload/statFiles; rm -rf $fname.txt";
		
		//
		// Download custom Kexts, kernel and AppleHDA from model data
		//
    	if ($categ == "Extensions"  || $categ == "Kernel")
		{
			$categdir = "$workpath/model-data/$name";
			$packdir = "$categdir/$fname";
			$svnpath = "model-data/$name/$fname";
			$copyKextCmd = "cp -a $workpath/model-data/$name/$fname/*.kext $ee/; echo \"Copy : $fname file(s) installed<br>\" >> $workpath/build.log";
			$name = $fname;
		}
		//
		// Download kexts and booloader from kextpacks
		//
		else {
			$categdir = "$workpath/kpsvn/$categ";
			$packdir = "$categdir/$fname";
			$svnpath = "kextpacks/$categ/$fname";
			$copyKextCmd = "cp -a $workpath/kpsvn/$categ/$fname/*.kext $ee/; echo \"Copy : $name file(s) installed<br>\" >> $workpath/build.log";


			// Copy VoodooHDA prefpanes
			if ($name == "AudioSettings")
			{
        		$copyKextCmd = "cp -a $workpath/kpsvn/$categ/$fname/*.kext $ee/; cp -R $workpath/kpsvn/$categ/$fname/VoodooHdaSettingsLoader.app /Applications/; cp $workpath/kpsvn/$categ/$fname/com.restore.voodooHDASettings.plist /Library/LaunchAgents/; cp -R $workpath/kpsvn/$categ/$fname/VoodooHDA.prefPane /Library/PreferencePanes/; echo \"Copy : VoodooHDA file(s) installed<br>\" >> $workpath/build.log";
			}
			
			switch ($fname) {
				// Copy VoodooPState launch agent plist
				case "VoodooPState":
				$copyKextCmd = "cp -a $workpath/kpsvn/$categ/$fname/*.kext $ee/; cp $workpath/kpsvn/PowerMgmt/VoodooPState/PStateMenu.plist /Library/LaunchAgents/; echo \"Copy : $name file(s) installed<br>\" >> $workpath/build.log";
				break;
				
				// Copy VoodooPS2 prefpanes
				case "StandardVooDooPS2":
				$copyKextCmd = "cp -a $workpath/kpsvn/$categ/$fname/*.kext $ee/; cp -R $workpath/kpsvn/$categ/$fname/VoodooPS2.prefpane /Library/PreferencePanes; echo \"Copy : $fname installed to /Library/PreferencePanes<br>\" >> $workpath/build.log";
				break;
				
				case "LatestVoodooPS2":				
				case "VoooDooALPS2":
				$copyKextCmd = "cp -a $workpath/kpsvn/$categ/$fname/*.kext $ee/; cp $workpath/kpsvn/$categ/$fname/VoodooPS2Daemon /usr/bin; cp $workpath/kpsvn/$categ/$fname/org.rehabman.voodoo.driver.Daemon.plist /Library/LaunchDaemons; cp -R $workpath/kpsvn/$categ/$fname/VoodooPS2synapticsPane.prefPane /Library/PreferencePanes; echo \"Copy : $fname file(s) installed<br>\" >> $workpath/build.log";
				break;
				
				default:
				break;
			}
			
			// change to correct bootloader, ethernet and power mgmt kexts folder path
			switch ($categ) {
		
				case "Ethernet":
				$categdir = "$workpath/kpsvn/$categ/$fname"; // Ethernet/RealTek/kextname.kext
				$packdir = "$categdir/$name";
				$svnpath = "kextpacks/$categ/$fname/$name";				
				break;
				
				case "PowerMgmt":
				$copyKextCmd = "cp -a $workpath/kpsvn/$categ/*.kext $ee/; echo \"Copy : $name file(s) installed<br>\" >> $workpath/build.log";
				$categdir = "$workpath/kpsvn/$categ"; // PowerMgmt/kextname.kext
				$packdir = "$categdir/$name";
				$svnpath = "kextpacks/$categ/$name";
				break;
				
				case "Bootloader":
				$copyKextCmd = "cp -f $workpath/kpsvn/$categ/$fname/$name /; echo \"Copy : $fname bootloader installed/updated<br>\" >> $workpath/build.log";
				$categdir = "$workpath/kpsvn/$categ";
				$packdir = "$categdir/$fname";
				$svnpath = "kextpacks/$categ/$fname";
				break;
			}
		}	
			
		if (is_dir("$packdir")) {
			$checkoutCmd = "if svn --non-interactive --username edp --password edp --quiet --force update $packdir; then echo \"Update : $name file(s) finished<br>\" >> $workpath/build.log; $copyKextCmd; fi";

			$edp->writeToLog("$workpath/kpsvn/dload/$fname.sh", "$createStatFile; $checkoutCmd; $endStatFile;");
			system_call("sh $workpath/kpsvn/dload/$fname.sh >> $workpath/build.log &");
			
			// system_call("svn --non-interactive --username edp --password edp --quiet --force update $packdir");
		}
		else {
			$checkoutCmd = "mkdir $categdir; cd $categdir; if svn --non-interactive --username osxlatitude-edp-read-only --quiet --force co http://osxlatitude-edp.googlecode.com/svn/$svnpath; then echo \"Download : $name file(s) finished<br>\" >> $workpath/build.log; $copyKextCmd; fi";

			$edp->writeToLog("$workpath/kpsvn/dload/$fname.sh", "$createStatFile; $checkoutCmd; $endStatFile; ");
			system_call("sh $workpath/kpsvn/dload/$fname.sh >> $workpath/build.log &");
			
			// system_call("mkdir $packdir; cd $packdir; svn --non-interactive --username osxlatitude-edp-read-only --quiet --force co http://osxlatitude-edp.googlecode.com/svn/kextpacks/$pname/ .");
		}
} 

 
/**
 * Function to check if the model is allready checked out
 * if the model is not checked out it will check it out
 */
function loadModelEssentialFiles() {
    global $workpath, $modelNamePath, $os;
	
	//
	// download essential files from common folder
	//
    $modelfolder = "$workpath/model-data/$modelNamePath/common";
    if (is_dir("$modelfolder")) {
        system_call("svn --non-interactive --username edp --password edp --force --quiet update $modelfolder");
    } else {
        system_call("mkdir $modelfolder; cd $modelfolder; svn --non-interactive --username osxlatitude-edp-read-only --force --quiet co http://osxlatitude-edp.googlecode.com/svn/model-data/$modelNamePath/common .");
    }
    
    //
	// download essential files from $os folder
	//
    $modelfolder = "$workpath/model-data/$modelNamePath/$os";
    if (is_dir("$modelfolder")) {
        system_call("svn --non-interactive --username edp --password edp --force --quiet update $modelfolder");
    } else {
        system_call("mkdir $modelfolder; cd $modelfolder; svn --non-interactive --username osxlatitude-edp-read-only --force --quiet co http://osxlatitude-edp.googlecode.com/svn/model-data/$modelNamePath/$os .");
    }
}
	
function svnModeldata($model) {
    global $workpath, $os;
		
	//
	// download essential files from common folder
	//
    $modelfolder = "$workpath/model-data/$model/common";
    if (is_dir("$modelfolder")) {
        system_call("svn --non-interactive --username edp --password edp --force --quiet update $modelfolder");
    } else {
        system_call("mkdir $modelfolder; cd $modelfolder; svn --non-interactive --username osxlatitude-edp-read-only --force --quiet co http://osxlatitude-edp.googlecode.com/svn/model-data/$model/common .");
    }
    
    //
	// download essential files from common $os
	//
    $modelfolder = "$workpath/model-data/$model/$os";
    if (is_dir("$modelfolder")) {
        system_call("svn --non-interactive --username edp --password edp --force --quiet update $modelfolder");
    } else {
        system_call("mkdir $modelfolder; cd $modelfolder; svn --non-interactive --username osxlatitude-edp-read-only --force --quiet co http://osxlatitude-edp.googlecode.com/svn/model-data/$model/common .");
    }
}

/**
 * Function to check if myhack.kext exists in ale, and if it dosent for some weird reason... copy it there...
 */
function myHackCheck() {
    global $workpath, $slepath;

    if (!is_dir("$slepath/myHack.kext")) {
        $myhackkext = "$workpath/bin/myHack/myHack.kext";
        system_call("rm -Rf `find -f path \"$myhackkext\" -type d -name .svn`");
        system_call("cp -R \"$myhackkext\" $slepath");
    }
    if (!is_file("/usr/sbin/")) {
        system_call("cp \"$workpath/bin/myHack/myfix\" /usr/sbin/myfix; chmod 777 /usr/sbin/myfix");
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
 * replace system_call() .. works with LWS also
 */
function system_call($data) {
    passthru("$data");
    echo str_repeat(' ', 254);
    flush();
}

function isEmptyDir($dir) {
    if (($files = @scandir("$dir")) && (count($files) > 2)) {
        return "yes";
    } else {
        return "no";
    }
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
	

function kernelcachefix() {
    global $workpath, $rootpath;
    
    $chkdir = $rootpath . "/System/Library/Caches/com.apple.kext.caches/Startup";
    $kerncachefile = $rootpath . "/System/Library/Caches/com.apple.kext.caches/Startup/kernelcache";

    if (!is_dir("$chkdir") && ($workpath == "/Extra/EDP")) {
        system_call("mkdir $chkdir");
        if (file_exists($kerncachefile)) {
            echo "\n\nWARNING: Falling back to EDP kernelcache generation - myfix was not successfull.. \n\n";
            system_call("kextcache -system-prelinked-kernel");
        }
    }
}

function lastMinFixes() {
		global $workpath; global $edp; global $nvram;
		// Final Chown to SLE (this is due to some issuses with myFix in Mavericks)
		system_call("sudo chown -R root:wheel /System/Library/Extensions/");
		$stat = $nvram->clear();
		$edp->writeToLog("$workpath/build.log", "Clearing boot-args in NVRAM...$stat<br>");
	}
		
/**
 * Patch AHCI
 * @see http://www.insanelymac.com/forum/topic/280062-waiting-for-root-device-when-kernel-cache-used-only-with-some-disks-fix/page__st__60#entry1851722
 */
function patchAHCI() {
	global $workpath,$slepath, $ee;
    system_call("cp -R $slepath/IOAHCIFamily.kext $ee");
    system_call("perl $workpath/bin/fixes/patch-ahci-mlion.pl >> $workpath/build.log");
}

/**
 * Patch VGA and HDMI for Intel HD3000 GPU
 */
function patchAppleIntelSNBGraphicsFB() {

    global $ee, $slepath;
    
    system_call("cp -R $slepath/AppleIntelSNBGraphicsFB.kext $ee/");
    system_call('sudo perl -pi -e \'s|\x01\x02\x04\x00\x10\x07\x00\x00\x10\x07\x00\x00\x05\x03\x00\x00\x02\x00\x00\x00\x30\x00\x00\x00\x02\x05\x00\x00\x00\x04\x00\x00\x07\x00\x00\x00\x03\x04\x00\x00\x00\x04\x00\x00\x09\x00\x00\x00\x04\x06\x00\x00\x00\x04\x00\x00\x09\x00\x00\x00|\x01\x02\x03\x00\x10\x07\x00\x00\x10\x07\x00\x00\x05\x03\x00\x00\x02\x00\x00\x00\x30\x00\x00\x00\x06\x02\x00\x00\x00\x01\x00\x00\x07\x00\x00\x00\x03\x04\x00\x00\x00\x08\x00\x00\x06\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00|g\' /Extra/Extensions/AppleIntelSNBGraphicsFB.kext/Contents/MacOS/AppleIntelSNBGraphicsFB');
    system_call("sudo /usr/libexec/PlistBuddy -c \"add PatchedBy string \"OSXLatitude\"\" $ee/AppleIntelSNBGraphicsFB.kext/Contents/KextPatched.plist");  
}
/**
 * Patch AppleIntelCPUPowerxxx for Native Speedstep and Power managment
 */
function patchAppleIntelCPUPowerManagement() {
    global $ee, $slepath;
    
    if(is_dir("$slepath/AppleIntelCPUPowerManagement.kext")) {
    	system_call("cp -R $slepath/AppleIntelCPUPowerManagement.kext $ee/");
    	system_call('sudo perl -pi -e \'s|\xE2\x00\x00\x00\x0F\x30|\xE2\x00\x00\x00\x90\x90|g\' /Extra/Extensions/AppleIntelCPUPowerManagement.kext/Contents/MacOS/AppleIntelCPUPowerManagement');
		system_call('sudo perl -pi -e \'s|\xE2\x00\x00\x00\x48\x89\xF2\x0F\x30|\xE2\x00\x00\x00\x48\x89\xF2\x90\x90|g\' /Extra/Extensions/AppleIntelCPUPowerManagement.kext/Contents/MacOS/AppleIntelCPUPowerManagement');
        system_call("sudo /usr/libexec/PlistBuddy -c \"add PatchedBy string \"OSXLatitude\"\" $ee/AppleIntelCPUPowerManagement.kext/Contents/KextPatched.plist");  
  }
}
/**
 * Patch WiFI and Bluetooth Kexts
 */
//<-----------------------------------------------------------------------------------------------------------------------------------

/*
 * Patch AirPortAtheros40.kext for the card AR5B95/AR5B195 from Lion onwards
 */
function patchWiFiAR9285AndAR9287() {
	global $ee, $slepath;
	echo "  Applying AR9285/AR9287 WiFi Fix for AR5B195/AR5B95 and AR5B197\n";

	$file = "$slepath/IO80211Family.kext/Contents/PlugIns/AirPortAtheros40.kext/Contents/Info.plist";   if (file_exists($file)) {
	system_call("cp -R $slepath/IO80211Family.kext $ee/");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Atheros\ Wireless\ LAN\ PCI:IONameMatch:0 string \"pci168c,2b\"\" $ee/IO80211Family.kext/Contents/PlugIns/AirPortAtheros40.kext/Contents/Info.plist");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Atheros\ Wireless\ LAN\ PCI:IONameMatch:0 string \"pci168c,2e\"\" $ee/IO80211Family.kext/Contents/PlugIns/AirPortAtheros40.kext/Contents/Info.plist");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add PatchedBy string \"OSXLatitude\"\" $ee/IO80211Family.kext/Contents/PlugIns/AirPortAtheros40.kext/Contents/KextPatched.plist"); 
    }
    else { echo "  AirPortAtheros40.kext not found for patching in System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/\n"; }
}


/*
 * Patch AirPortBrcm4360.kext for the card BCM94352HMB from Mountain Lion 10.8.5 onwards
 */
function patchWiFiBTBCM4352() {
	global $ee, $slepath;
	echo "  Applying BCM4352 WiFi Fix for BCM94352HMB card\n";
	
	$file = "$slepath/IO80211Family.kext/Contents/PlugIns/AirPortBrcm4360.kext/Contents/Info.plist";   if (file_exists($file)) {
	system_call("cp -R $slepath/IO80211Family.kext $ee/");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Broadcom\ 802.11\ PCI:IONameMatch:0 string \"pci14e4,43b1\"\" $ee/IO80211Family.kext/Contents/PlugIns/AirPortBrcm4360.kext/Contents/Info.plist");   
    // Binary patches
    system_call('sudo perl -pi -e \'s|\x01\x58\x54|\x01\x55\x53|g\' /Extra/Extensions/IO80211Family.kext/Contents/PlugIns/AirPortBrcm4360.kext/Contents/MacOS/AirPortBrcm4360'); // region code change to US
    system_call('sudo perl -pi -e \'s|\x6B\x10\x00\x00\x75|\x6B\x10\x00\x00\x74|g\' /Extra/Extensions/IO80211Family.kext/Contents/PlugIns/AirPortBrcm4360.kext/Contents/MacOS/AirPortBrcm4360'); // skipping binary checks of apple device id to work Appple card
    system_call('sudo perl -pi -e \'s|\x6B\x10\x00\x00\x0F\x85|\x6B\x10\x00\x00\x0F\x84|g\' /Extra/Extensions/IO80211Family.kext/Contents/PlugIns/AirPortBrcm4360.kext/Contents/MacOS/AirPortBrcm4360'); // skipping binary checks of apple device id to work Appple card
    
    system_call("sudo /usr/libexec/PlistBuddy -c \"add PatchedBy string \"OSXLatitude\"\" $ee/IO80211Family.kext/Contents/PlugIns/AirPortBrcm4360.kext/Contents/KextPatched.plist");

  }
    else { echo "  AirPortBrcm4360.kext not found for patching in System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/\n"; }    
}

/*
 * Patch AppleAirPortBrcm43224.kext for the card Dell DW1395, DW1397 from Lion onwards
 */
function patchDW13957WiFiBCM43224() {
	global $ee, $slepath;
	echo "  Applying BCM43224 WiFi Fix for Dell DW1395, DW1397 \n";

	$file = "$slepath/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm43224.kext/Contents/Info.plist";   if (file_exists($file)) {
	system_call("cp -R $slepath/IO80211Family.kext $ee/");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Broadcom\ 802.11\ PCI:IONameMatch:0 string \"pci14e4,4315\"\" $ee/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm43224.kext/Contents/Info.plist");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add PatchedBy string \"OSXLatitude\"\" $ee/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm43224.kext/Contents/KextPatched.plist");
    }
     else { echo "  AppleAirPortBrcm43224.kext not found for patching in System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/\n"; }
}

/*
 * Patch AppleAirPortBrcm4311.kext for the card Dell DW1395, DW1397 in Snow Leopard
 */
function patchDW13957WiFiBCM4311() {
	global $ee, $slepath;
	echo "  Applying BCM4311 WiFi Fix for Dell DW1395, DW1397 \n";

	$file = "$slepath/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm4311.kext/Contents/Info.plist";   if (file_exists($file)) {
	system_call("cp -R $slepath/IO80211Family.kext $ee/");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Broadcom\ 802.11\ PCI:IONameMatch:0 string \"pci14e4,4315\"\" $ee/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm4311.kext/Contents/Info.plist");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add PatchedBy string \"OSXLatitude\"\" $ee/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm4311.kext/Contents/KextPatched.plist");
    }
     else { echo "  AppleAirPortBrcm4311.kext not found for patching in System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/\n"; }
}

/*
 * Patch AppleAirPortBrcm43224.kext for the card BCM943224 HMS and BCM943225 HMB from Lion onwards
 */
function patchWiFiBCM43224() {
	global $ee, $slepath;
	echo "  Applying BCM43224 WiFi Fix for BCM943224 HMS and BCM943225 HMB \n";

	$file = "$slepath/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm43224.kext/Contents/Info.plist";   if (file_exists($file)) {
	system_call("cp -R $slepath/IO80211Family.kext $ee/");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Broadcom\ 802.11\ PCI:IONameMatch:0 string \"pci14e4,4353\"\" $ee/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm43224.kext/Contents/Info.plist");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Broadcom\ 802.11\ PCI:IONameMatch:0 string \"pci14e4,4357\"\" $ee/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm43224.kext/Contents/Info.plist");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add PatchedBy string \"OSXLatitude\"\" $ee/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm43224.kext/Contents/KextPatched.plist");
    }
     else { echo "  AppleAirPortBrcm43224.kext not found for patching in System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/\n"; }
}

/*
 * Patch AirPortBrcm4331.kext for the card BCM943224 HMS and BCM943225 HMB from Lion onwards
 */
function patchWiFiBCM4331() {
	global $ee, $slepath;
	echo "  Applying BCM4331 WiFi Fix for BCM943225 HMB \n";

	$file = "$slepath/IO80211Family.kext/Contents/PlugIns/AirPortBrcm4331.kext/Contents/Info.plist";   if (file_exists($file)) {
	system_call("cp -R $slepath/IO80211Family.kext $ee/");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Broadcom\ 802.11\ PCI:IONameMatch:0 string \"pci14e4,4357\"\" $ee/IO80211Family.kext/Contents/PlugIns/AirPortBrcm4331.kext/Contents/Info.plist");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add PatchedBy string \"OSXLatitude\"\" $ee/IO80211Family.kext/Contents/PlugIns/AirPortBrcm4331.kext/Contents/KextPatched.plist");
    }
     else { echo "  AirPortBrcm4331.kext not found for patching in System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/\n"; }
}

//<-----------------------------------------------------------------------------------------------------------------------------------

	
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
    if ($v == "10.9.4") { $r="mav"; $os_string = "OSX Maverick $v"; }
    if ($v == "10.9.5") { $r="mav"; $os_string = "OSX Maverick $v"; }
    if ($v == "10.9.6") { $r="mav"; $os_string = "OSX Maverick $v"; }
    if ($v == "10.10") { $r="yos"; $os_string = "OSX Yosemite $v"; }    
    if ($v == "10.10.0") { $r="yos"; $os_string = "OSX Yosemite $v"; }    
    if ($v == "10.10.1") { $r="yos"; $os_string = "OSX Yosemite $v"; }    
    if ($v == "10.10.2") { $r="yos"; $os_string = "OSX Yosemite $v"; }    
    if ($v == "10.10.3") { $r="yos"; $os_string = "OSX Yosemite $v"; }
    if ($v == "10.10.4") { $r="yos"; $os_string = "OSX Yosemite $v"; }
    if ($v == "10.10.5") { $r="yos"; $os_string = "OSX Yosemite $v"; } 
    if ($v == "10.10.6") { $r="yos"; $os_string = "OSX Yosemite $v"; }                            			
    return $r;
}
	
function getMacOSXVersion() {
		$path = "/System/Library/CoreServices/SystemVersion";
    	$ver = exec("defaults read $path ProductVersion");
    	return $ver;
}

/**
 * Essentials like dsdt, ssdt and plists
 */
function copyEssentials() {
    global $workpath, $incpath, $os; global $edp;
	global $modeldb, $modelRowID;
    global $modelNamePath;

	$extrapath = "/Extra";
    $edp->writeToLog("$workpath/build.log", " Checking for DSDT, SSDT and System Plist files...<br>");
    
    // use EDP SMBIos?
    if($modeldb[$modelRowID]["useEDPSMBIOS"] == "on")
    {
    	$file1 = "$workpath/model-data/$modelNamePath/common/SMBios.plist"; 
    	$file2 = "$workpath/model-data/$modelNamePath/$os/SMBios.plist"; 

   		$edp->writeToLog("$workpath/build.log", " SMBios.plist found, Copying to $extrapath<br>");
    	//Remove existing file from /Extra
    	if (file_exists("$extrapath/SMBios.plist")) { system_call("rm $extrapath/SMBios.plist"); }
    	//Copy file from common folder if exists
    	if(file_exists($file1))
    	system_call("cp -f $file1 $extrapath"); 
    	//Copy file from os folder if exists
    	if(file_exists($file2))
    	system_call("cp -f $file2 $extrapath");
    	
    } else {
    	$edp->writeToLog("$workpath/build.log", " Skipping SMBios.plist file from EDP on user request<br>");
    }
    
    // use EDP org.chameleon.Boot.plist?
    if($modeldb[$modelRowID]["useEDPCHAM"] == "on")
    {
    	$file1 = "$workpath/model-data/$modelNamePath/common/org.chameleon.Boot.plist"; 
    	$file2 = "$workpath/model-data/$modelNamePath/$os/org.chameleon.Boot.plist"; 

   	    $edp->writeToLog("$workpath/build.log", " org.chameleon.Boot.plist found, Copying to $extrapath<br>");
    	//Remove existing file from /Extra
    	if (file_exists("$extrapath/org.chameleon.Boot.plist")) { system_call("rm $extrapath/org.chameleon.Boot.plist"); }
    	//Copy file from common folder if exists
    	if(file_exists($file1))
    	system_call("cp -f $file1 $extrapath"); 
    	//Copy file from os folder if exists
    	if(file_exists($file2))
    	system_call("cp -f $file2 $extrapath");
   	 	
    } else {
    	$edp->writeToLog("$workpath/build.log", " Skipping org.chameleon.Boot.plist file from EDP on user request<br>");
    }
    
    // use EDP DSDT?
    if($modeldb[$modelRowID]["useEDPDSDT"] == "on")
    {
    	$file1 = "$workpath/model-data/$modelNamePath/common/dsdt.aml"; 
    	$file2 = "$workpath/model-data/$modelNamePath/$os/dsdt.aml"; 

    	$edp->writeToLog("$workpath/build.log", " dsdt found, Copying to $extrapath<br>");
    	//Remove existing file from /Extra
    	if (file_exists("$extrapath/dsdt.aml")) { system_call("rm $extrapath/dsdt.aml"); }
    	//Copy file from common folder if exists
    	if(file_exists($file1))
    	system_call("cp -f $file1 $extrapath"); 
    	//Copy file from os folder if exists
    	if(file_exists($file2))
    	system_call("cp -f $file2 $extrapath");
    	
    } else {
    	$edp->writeToLog("$workpath/build.log", " Skipping DSDT file from EDP on user request<br>");
    }
	
    // If its mavericks then copy the files from ml folder temporarilyfor now
    if($os == "mav" && !is_dir("$workpath/model-data/$modelNamePath/$os") && is_dir("$workpath/model-data/$modelNamePath/ml")) {
    $edp->writeToLog("$workpath/build.log", "  mavericks directory is not found, Copying dsdt and plist files from ml folder<br>");
    if($modeldb[$modelRowID]["useEDPDSDT"] == "on") {
    	$file = "$workpath/model-data/$modelNamePath/ml/dsdt.aml";                 if (file_exists($file)) { system_call("cp -f $file $extrapath"); }	
    	}
    if($modeldb[$modelRowID]["useEDPSMBIOS"] == "on") {
    	$file = "$workpath/model-data/$modelNamePath/ml/SMBios.plist";             if (file_exists($file)) { system_call("cp -f $file $extrapath"); }
    	}
    if($modeldb[$modelRowID]["useEDPCHAM"] == "on") {
    	$file = "$workpath/model-data/$modelNamePath/ml/org.chameleon.Boot.plist";  if (file_exists($file)) { system_call("cp -f $file $extrapath"); }	
    	}
    }						

	// use EDP org.chameleon.Boot.plist?
	if($modeldb[$modelRowID]["useEDPCHAM"] == "on") {
		// set UseKernelCache to Yes from org.chameleon.Boot.plist
		system("sudo /usr/libexec/PlistBuddy -c \"set UseKernelCache Yes\" $extrapath/org.chameleon.Boot.plist");
	}
	
	// use EDP SSDT?
    if($modeldb[$modelRowID]["useEDPSSDT"] == "on")
    {
    
      if (file_exists("$extrapath/SSDT.aml")) { system_call("rm $extrapath/SSDT.aml"); }
      $file = "$workpath/model-data/$modelNamePath/common/SSDT.aml";
  	  if (file_exists($file)) 
       { 
    		$edp->writeToLog("$workpath/build.log", " SSDT files found, Copying to $extrapath<br>");
    		system_call("cp -f $file $extrapath");
    		// set DropSSDT to Yes from org.chameleon.Boot.plist
			system("sudo /usr/libexec/PlistBuddy -c \"set DropSSDT Yes\" $extrapath/org.chameleon.Boot.plist"); 
   		}
   		
    	if (file_exists("$extrapath/SSDT-1.aml")) { system_call("rm $extrapath/SSDT-1.aml"); }
   		$file = "$workpath/model-data/$modelNamePath/common/SSDT-1.aml"; if (file_exists($file)) { 
   			system_call("cp -f $file $extrapath"); 
    	}
    	
    	if (file_exists("$extrapath/SSDT-2.aml")) { system_call("rm $extrapath/SSDT-2.aml"); }
    	$file = "$workpath/model-data/$modelNamePath/common/SSDT-2.aml"; if (file_exists($file)) { 
    		system_call("cp -f $file $extrapath"); 
    	}
    	
    	if (file_exists("$extrapath/SSDT-3.aml")) { system_call("rm $extrapath/SSDT-3.aml"); }
    	$file = "$workpath/model-data/$modelNamePath/common/SSDT-3.aml"; if (file_exists($file)) { 
    		system_call("cp -f $file $extrapath"); 
    	}

    	if (file_exists("$extrapath/SSDT-4.aml")) { system_call("rm $extrapath/SSDT-4.aml"); } 
    	$file = "$workpath/model-data/$modelNamePath/common/SSDT-4.aml"; if (file_exists($file)) {
    		system_call("cp -f $file $extrapath"); 
    	}
    	
    	if (file_exists("$extrapath/SSDT-5.aml")) { system_call("rm $extrapath/SSDT-5.aml"); } 
    	$file = "$workpath/model-data/$modelNamePath/common/SSDT-5.aml"; if (file_exists($file)) {
    		system_call("cp -f $file $extrapath"); 
    	}	
    }  
    else {
    	$edp->writeToLog("$workpath/build.log", " Skipping SSDT files from EDP on user request<br>");
    }
    
    //
    // Copy essentials from /Extra/include if user has
    //

    if (is_file("$incpath/smbios.plist") && $modeldb[$modelRowID]["useIncSMBIOS"] == "on") 				{ 
    	$edp->writeToLog("$workpath/build.log", " Custom smbios.plist found, Copying from $incpath to $extrapath<br>");
    	system_call("cp -f $incpath/smbios.plist /Extra"); 
    }
    if (is_file("$incpath/org.chameleon.Boot.plist") && $modeldb[$modelRowID]["useIncCHAM"] == "on") 	{ 
    	$edp->writeToLog("$workpath/build.log", " Custom org.chameleon.Boot.plist found, Copying from $incpath to $extrapath<br>");
    	system_call("cp -f $incpath/org.chameleon.Boot.plist /Extra"); 
    }
    if (is_file("$incpath/dsdt.aml") && $modeldb[$modelRowID]["useIncDSDT"] == "on") 					{ 
    	$edp->writeToLog("$workpath/build.log", " Custom dsdt file found, Copying from $incpath to $extrapath<br>");
    	system_call("cp -f $incpath/dsdt.aml /Extra"); 
    }
    if($modeldb[$modelRowID]["useIncSSDT"] == "on")
    {
    	if (is_file("$incpath/SSDT.aml")) 					{ 
    		$edp->writeToLog("$workpath/build.log", " Custom SSDT files found, Copying from $incpath to $extrapath<br>");
    		system_call("cp -f $incpath/SSDT.aml /Extra"); 
    	}
    	if (is_file("$incpath/SSDT-1.aml")) 				{ system_call("cp -f $incpath/SSDT-1.aml /Extra"); }
    	if (is_file("$incpath/SSDT-2.aml")) 				{ system_call("cp -f $incpath/SSDT-2.aml /Extra"); }
    	if (is_file("$incpath/SSDT-3.aml")) 				{ system_call("cp -f $incpath/SSDT-3.aml /Extra"); }    
    	if (is_file("$incpath/SSDT-4.aml")) 				{ system_call("cp -f $incpath/SSDT-4.aml /Extra"); }
    	if (is_file("$incpath/SSDT-5.aml")) 				{ system_call("cp -f $incpath/SSDT-5.aml /Extra"); }  
    }
    
     //
    // Check if we need a custom version of chameleon from essential common and $os folders
    //
    if ($modeldb[$modelRowID]['customCham'] == "on") {
        $edp->writeToLog("$workpath/build.log", "  Copying custom chameleon to $rootpath if exists... <br>");
        
        $cboot = "$workpath/model-data/$modelNamePath/common/boot";
        $osboot = "$workpath/model-data/$modelNamePath/$os/boot";
        
        if(is_file("$cboot") || is_file("$osboot"))
        {
        	system_call("rm -f $rootpath/boot");
        	system_call("cp $workpath/model-data/$modelNamePath/common/boot $rootpath");
        	system_call("cp $workpath/model-data/$modelNamePath/$os/boot $rootpath");
        }
    }
    
    //
    // Copy Custom Themes folder to Extra
    //
    $edp->writeToLog("$workpath/build.log", "  Copying Themes folder to /Extra...<br>");
    if (!is_dir("/Extra/Themes")) {
        system_call("mkdir /Extra/Themes");
     }
     
    if(is_dir("$workpath/model-data/$modelNamePath/common/Themes")) {
		system_call("cp -a $workpath/model-data/$modelNamePath/common/Themes/. /Extra/Themes");
    }
    else {
    	system_call("cp -a $workpath/Themes/. /Extra/Themes");
    }
}


/**
 * Kexts loading for build
 */
 function copyEDPKexts()
 {
 	//Get vars from config.inc.php
    global $workpath, $rootpath, $slepath, $ps2db, $audiodb, $incpath, $wifidb, $modeldb, $modelRowID, $os, $ee, $batterydb, $landb, $fakesmcdb, $edp;
    global $cpufixdb;
    
    //Get our class(s)
    global $builder;
    global $modelNamePath;
	
	// kextpack svn path
	$kpsvn = "$workpath/kpsvn";    
    
    // Use EDP Kexts?
    if($modeldb[$modelRowID]['useEDPExtensions'] == "on")
    {
    	//
    	// copying PS2 kexts from kextpacks
    	//
    	$ps2id = $modeldb[$modelRowID]['ps2pack'];
    
    	if ($ps2id != "" && $ps2id != "no")
        {
    		$fname = $ps2db[$ps2id]["foldername"];
    		$name = $ps2db[$ps2id]['kextname'];
    		
    		// remove voodooPS2 files if its installed before
    		if($ps2id != "2" && $ps2id != "6")
    		{
        		if(is_dir("/Library/PreferencePanes/VoodooPS2.prefpane")) {system_call("rm -rf /Library/PreferencePanes/VoodooPS2.prefpane");}
        		if(file_exists("/usr/bin/VoodooPS2Daemon")) {system_call("rm -rf /usr/bin/VoodooPS2Daemon");}
        		if(file_exists("/Library/LaunchDaemons/org.rehabman.voodoo.driver.Daemon.plist")) {system_call("rm -rf /Library/LaunchDaemons/org.rehabman.voodoo.driver.Daemon.plist");}
        		if(is_dir("/Library/PreferencePanes/VoodooPS2synapticsPane.prefPane")) {system_call("rm -rf /Library/PreferencePanes/VoodooPS2synapticsPane.prefPane");}
    		}
    	
        	if ($fname != "") { 
        	    $edp->writeToLog("$workpath/build.log", "  Downloading Touchpad kext $fname<br>");
        	    
        	    if(!is_dir("$kpsvn/PS2Touchpad"))
    				system_call("mkdir $kpsvn/PS2Touchpad");
    				
    			kextpackLoader("PS2Touchpad", "$fname", "$name");
    		}
		 } 
		// Reset vars
		$name = "";
		$fname = "";
		
		
		//
    	// copying Wifi/BT kexts from kextpacks / patch WiFi/BT Kexts
    	//
    	if ($modeldb[$modelRowID]['wifipack'] != "" && $modeldb[$modelRowID]['wifipack'] != "no") 
    	{
        	$wifid = $modeldb[$modelRowID]['wifipack'];
        	$name = $wifidb[$wifid]['kextname'];
        	$fname = $wifidb[$wifid]['foldername'];
        	
        	if ($name != "") {
        	
    		$edp->writeToLog("$workpath/build.log", "  Patching WiFi kext $name<br>");
    		
    		if($wifid == "0" || $wifid == "1")
    			patchWiFiAR9285AndAR9287();
    		else if($wifid == "2") {
    			if(getMacOSXVersion() >= "10.8.5")
    				patchWiFiBTBCM4352();
    			else
    				$edp->writeToLog("$workpath/build.log", "  OSX version is not supported for WiFi, need OSX 10.8.5 or later<br>");
    		}
    			
    		else if($wifid == "3")
    			patchDW13957WiFiBCM43224();
    		else if($wifid == "4")
    			patchDW13957WiFiBCM4311();
    		else if($wifid == "5")
    			patchWiFiBCM43224();
    		else if($wifid == "6")
    			patchWiFiBCM4331();
    		else if($wifid == "7")
    			{
    				$edp->writeToLog("$workpath/build.log", "  Downloading WiFi kext $fname<br>");
    					
    				if(!is_dir("$kpsvn/Wireless"))
    					system_call("mkdir $kpsvn/Wireless");
    				
    				kextpackLoader("Wireless", "$fname", "$name");
    			}
    			
    		// Load Bluetooth kext for AR3011 and BCM4352
    		if($wifid < "3")
    			{
    				$edp->writeToLog("$workpath/build.log", "  Downloading Bluetooth kext $fname<br>");
        	    
        	    	 if(!is_dir("$kpsvn/Wireless"))
    					system_call("mkdir $kpsvn/Wireless");
    					
    				 kextpackLoader("Wireless", "BluetoothFWUploader", "BluetoothFWUploader.kext");
    			}
    		}
		}
		// Reset vars
		$name = "";
		$fname = "";    

		 //
   		 // copying fakesmc kexts from kextpacks
   		 //
    	
    	if ($modeldb[$modelRowID]['fakesmc'] != "" && $modeldb[$modelRowID]['fakesmc'] != "no")
    	 {   
    		$fakesmcid = $modeldb[$modelRowID]['fakesmc'];
    		$fname = $fakesmcdb[$fakesmcid]['foldername'];
    		$name = $fakesmcdb[$fakesmcid]['name']; 
    		
    		if ($fname != "") {
    			$edp->writeToLog("$workpath/build.log", "  Downloading FakeSMC kext $fname<br>");
        	    
        	    if(!is_dir("$kpsvn/FakeSMC"))
    				system_call("mkdir $kpsvn/FakeSMC");
    					
    			kextpackLoader("FakeSMC", "$fname", "$name");
    		}
     	}
		// Reset vars
		$name = "";
		$fname = "";	
    	
    	//
    	// copying audio kexts
    	//
    	if ($modeldb[$modelRowID]['audiopack'] != "" && $modeldb[$modelRowID]['audiopack'] != "no")
        {
        
        	$audioid = $modeldb[$modelRowID]['audiopack'];
    		$fname = $audiodb[$audioid]['foldername']; 
    		$name = $audiodb[$audioid]['name']; 
    
    		// remove voodooHDA related files if installed before
    		if($audioid == "no" || $audioid == "builtin") {
        	 	if(is_dir("/Applications/VoodooHdaSettingsLoader.app")) {system_call("rm -rf /Applications/VoodooHdaSettingsLoader.app");}
        	 	if(file_exists("/Library/LaunchAgents/com.restore.voodooHDASettings.plist")) {system_call("rm -rf /Library/LaunchAgents/com.restore.voodooHDASettings.plist");}
        	 	if(is_dir("/Library/PreferencePanes/VoodooHDA.prefPane")) {system_call("rm -rf /Library/PreferencePanes/VoodooHDA.prefPane");}
   			 }
        	if (is_dir("$slepath/HDAEnabler.kext")) { system_call("rm -Rf $slepath/HDAEnabler.kext"); }
        
        	if ($audioid == "builtin")
        	{
        		$edp->writeToLog("$workpath/build.log", " Downloading Audio kext patched AppleHDA<br>");

				if(!is_dir("$workpath/model-data/$modelNamePath/applehda"))
    				system_call("mkdir $workpath/model-data/$modelNamePath/applehda");
    				
        		kextpackLoader("Extensions", "audiocommon", "$modelNamePath/applehda");
				kextpackLoader("Extensions", "audio$os", "$modelNamePath/applehda");
				
				//
				// Copy AppleHDA kexts from common and $os folders (used in old db structure, have to remove this when model moved to new db)
				//
				if(is_dir("$workpath/model-data/$modelNamePath/common/applehda"))
    				{
    				$edp->writeToLog("$workpath/build.log", "  Copying AppleHDA kexts from model common folder to $ee<br>");
    				$tf = "$workpath/model-data/$modelNamePath/common/applehda";
    				system_call("cp -a $tf/. $ee/");
   				 }
   				 
				if(is_dir("$workpath/model-data/$modelNamePath/$os/applehda"))
    				{
    				$edp->writeToLog("$workpath/build.log", "  Copying AppleHDA kexts from model $os folder to $ee<br>");
    				$tf = "$workpath/model-data/$modelNamePath/$os/applehda";
    				system_call("cp -a $tf/. $ee/");
   				 }
        	}
        	else if ($fname != "") {
    			        
    		    $edp->writeToLog("$workpath/build.log", "  Downloading Audio kext $fname<br>");

    			if(!is_dir("$kpsvn/Audio"))
    				system_call("mkdir $kpsvn/Audio");
    					    
        		kextpackLoader("Audio", "$fname", "$name");
        		
        		// Copy Prefpane and Settings loader
        		kextpackLoader("Audio", "Settings", "AudioSettings");
        	} 
   	 	}
   	 	// Reset vars
		$name = "";
		$fname = "";
	
		//
		// copying ethernet kexts from kextpacks
		//
    	if ($modeldb[$modelRowID]['ethernet'] != "" && $modeldb[$modelRowID]['ethernet'] != "no")
    	 {
        	$lanid = $modeldb[$modelRowID]['ethernet'];
        	$name = $landb[$lanid]['name'];
        	$fname = $landb[$lanid]['foldername'];
        	
        	if ($fname != "") {
        		
        		$edp->writeToLog("$workpath/build.log", "  Downloading Ethernet kext $name<br>");
        	    
    			if(!is_dir("$kpsvn/Ethernet"))
    				system_call("mkdir $kpsvn/Ethernet");
    			
    			// Category folder
    			if(!is_dir("$kpsvn/Ethernet/$fname"))
    				system_call("mkdir $kpsvn/Ethernet/$fname");
    		
    			kextpackLoader("Ethernet", "$fname", "$name");   
     	  	}	
		}
		// Reset vars
		$name = "";
		$fname = "";
		
	 //	
	 // copying battery kexts from kextpacks
	 //
   	 if ($modeldb[$modelRowID]['batterypack'] != "" && $modeldb[$modelRowID]['batterypack'] != "no") 
   	 {
        $battid = $modeldb[$modelRowID]['batterypack'];
        $fname = $batterydb[$battid]['foldername'];
        $name = $batterydb[$battid]['name'];
        
        if ($fname != "") {
        		$edp->writeToLog("$workpath/build.log", "  Downloading Battery kext $name<br>");
        	    
        	    if(!is_dir("$kpsvn/Battery"))
    				system_call("mkdir $kpsvn/Battery");
    				
    			kextpackLoader("Battery", "$fname", "$name");  
    		}
	   }
		// Reset vars
		$name = "";
		$fname = "";
    } 
    else {
    	$edp->writeToLog("$workpath/build.log", " Skipping Standard Kexts from EDP on user request<br>");
    }
    
    //
    // Copy selected optional kexts
    //
    $data = $modeldb[$modelRowID]['optionalpacks'];
    $array 	= explode(',', $data);
    
    foreach($array as $id) {
    	$opdata = getKextpackDataFromID("optionalpacks", $id);
        $categ = $opdata[category];
        $fname = $opdata[foldername];
        $name = $opdata[name];
        
         if ($fname != "") { 
    		if(!is_dir("$kpsvn/$categ"))
    			system_call("mkdir $kpsvn/$categ");
    		
    		if($id == "5") {
			//Choose new version 
    		if(getMacOSXVersion() >= "10.8.5")
    			kextpackLoader("$categ/GenericXHCIUSB3_New");
    		//chooose old version
    		else if(getMacOSXVersion() < "10.8.5")
    			kextpackLoader("$categ", "$fname", "$name");
    		}
    		else	
    			kextpackLoader("$categ", "$fname", "$name");
    	 }
      }
    	// Reset vars
		$name = "";
		$fname = "";
		
	$edp->writeToLog("$workpath/build.log", "  Downloading Standard kexts... <br>");

	//
    // Standard kexts
    //
    if(!is_dir("$workpath/kpsvn/Standard"));
    	system_call("mkdir $workpath/kpsvn/Standard");
    	
    kextpackLoader("Standard", "common", "Standard common");

    kextpackLoader("Standard", "$os", "Standard $os");
    
    $edp->writeToLog("$workpath/build.log", "  Downloading Model specific kexts... <br>");

    //
	// From Model data (Extensions folder)
	//
	kextpackLoader("Extensions", "kextscommon", "$modelNamePath/Extensions");
	kextpackLoader("Extensions", "kexts$os", "$modelNamePath/Extensions");
	
    // From Model data (Common and $os folder used before, have to remove this when all the models updated to new Extensions folder)
    if(is_dir("$workpath/model-data/$modelNamePath/common/Extensions"))
    {
    	$edp->writeToLog("$workpath/build.log", "  Copying kexts from model common folder to $ee<br>");
    	$tf = "$workpath/model-data/$modelNamePath/common/Extensions";
    	system_call("cp -a $tf/. $ee/");
    }
    if(is_dir("$workpath/model-data/$modelNamePath/$os/Extensions"))
    {
    	$edp->writeToLog("$workpath/build.log", "  Copying kexts from model $os folder to $ee<br>");
    	$tf = "$workpath/model-data/$modelNamePath/$os/Extensions";
    	system_call("cp -a $tf/. $ee/");
    }
    
    //
    // Download custom kernel from EDP
    //
    	  	
    kextpackLoader("Kernel", "kernel$os", "$modelNamePath/Kernel");
    
    //
    // Create a script file if we need to copy kexts from Extra/include/Extensions
    //
    if($modeldb[$modelRowID]["useIncExtensions"] == "on")
    {
    	$edp->writeToLog("$workpath/kpsvn/dload/CopyCustomKexts.sh", "");
    } 
 }
 
 /*
  * Fixes
  */
function applyFixes() {
	//Get vars from config.inc.php
    global $workpath, $rootpath, $slepath, $os, $ee, $edp;
    global $cpufixdb;
    global $modelNamePath, $sysType, $modeldb, $modelRowID, $modelID;
	
	//kextpack svn path
	$kpsvn = "$workpath/kpsvn";
	
    $edp->writeToLog("$workpath/build.log", "  Applying fixes and patches...... <br>");
	
	//
	// Apply power management related fixes 
	//
    $mdata = getModelDataFromID($sysType, $modelID);
    $array 	= explode(',', $mdata['pmfixes']);
    
    $i = 0; // iterating through all the id's
	while ($cpufixdb[$i] != "") {
	    // Getting kextname from ID
        $cpufixdata = getKextpackDataFromID("pmfixes", "$i");
        $kxtname = $cpufixdata[kextname];
        $name = $cpufixdata[edpid];
        
        // Checking if we need to patch AppleIntelCPUPowerManagement.kext
        if(($modeldb[$modelRowID]['applecpupwr'] == "on") && $i == "1") {
        	$edp->writeToLog("$workpath/build.log", "  Patching AppleIntelCPUPowerManagement.kext<br>");
        	patchAppleIntelCPUPowerManagement();
        }
        else if(($modeldb[$modelRowID]['emupstates'] == "on") && $i == "3") {
        	
        	kextpackLoader("PowerMgmt", "VoodooPState", "$kxtname"); 
        }
        else if ($kxtname != "" && $modeldb[$modelRowID][$cpufixdata[edpid]] == "on") { 

    		if(!is_dir("$kpsvn/PowerMgmt"))
    			system_call("mkdir $kpsvn/PowerMgmt");
    		
    		kextpackLoader("PowerMgmt", "$name", "$kxtname");
    		
    		 //remove PStateMenu if installed before
    		 if (file_exists("/Library/LaunchAgents/PStateMenu.plist")) { system_call("rm -rf /Library/LaunchAgents/PStateMenu.plist"); }
    	}
    	$i++;
	}

	// Reset vars
	$name = "";
	$fname = "";
		
	//
 	// Apply Generic xes
 	//
    $data = $modeldb[$modelRowID]['fixes'];
    $array 	= explode(',', $data);
    
    foreach($array as $id) {
	    //Getting names from ID
	    $fixdata = getKextpackDataFromID("genfixes", "$id");
        $categ = $fixdata[category];
        $fname = $fixdata[foldername];
        $name = $fixdata[name];
        
       if($id == "2") {
       		$edp->writeToLog("$workpath/build.log", "  Patching AHCI.kext to waiting for root device problem in ML<br>");
       		patchAHCI();
       }
       else if($id == "8") {
        	$edp->writeToLog("$workpath/build.log", "  Patching AppleIntelSNBGraphicsFB.kext for VGA and HDMI in Intel HD3000<br>");
        	patchAppleIntelSNBGraphicsFB();
        }
       else if ($fname != "") { 

			if($id == "1") {
       			$edp->writeToLog("$workpath/build.log", "  Applying ACPI fix for Battery read and Coolbook...<br>");
       		}
       		else if($id == "5") {
       			$edp->writeToLog("$workpath/build.log", "  Downloading patched IOATAFamily fix for IDE disks...<br>");
       		}
       		
    		if(!is_dir("$kpsvn/$categ"))
    			system_call("mkdir $kpsvn/$categ");
    		
    		kextpackLoader("$categ", "$fname", "$name");
    	}
	}
    	
    // Reset vars
	$name = "";
	$fname = "";
} 
 
/*
 * Copying custom kexts 
 */
function copyCustomFiles() {
    //Get vars from config.inc.php
    global $workpath, $rootpath, $slepath, $incpath, $os, $ee, $edp;
    global $modelNamePath;
	
	$edp->writeToLog("$workpath/build.log", "  Checking for Custom files from EDP model path and $incpath... <br>");

	//
    // Check if we need a custom made kernel from EDP model kernel folder
    //
    
    if(is_dir("$workpath/model-data/$modelNamePath/kernel/kernel$os")) {
        $edp->writeToLog("$workpath/build.log", "  Copying custom kernel to $rootpath if exists... <br>");
        	
        $ckernel = "$workpath/model-data/$modelNamePath/kernel/kernel$os/custom_kernel";
        if(is_file("$ckernel"))
        {
        	$edp->writeToLog("$workpath/build.log", "  custom_kernel found, copied to $rootpath <br>");
        	system_call("rm -f $rootpath/custom_kernel");
       		system_call("cp $workpath/model-data/$modelNamePath/kernel/kernel$os//custom_kernel $rootpath");
        }
        $kernelos = "$workpath/model-data/$modelNamePath/kernel/kernel$os/mach_kernel";
        if(is_file("$kernelos"))
        {
        	$edp->writeToLog("$workpath/build.log", "  mach_kernel found, copied to $rootpath <br>");
        	system_call("rm -f $rootpath/mach_kernel");
       		system_call("cp $workpath/model-data/$modelNamePath/kernel/kernel$os/mach_kernel $rootpath");
        }
    }


 	//
    // Copy Custom Themes folder from $incpatch to /Extra
    //
    if (is_dir("$incpath/Themes")) {
        $edp->writeToLog("$workpath/build.log", "  Copying Custom themes folder to /Extra...<br>");
        system_call("rm -rf /Extra/Themes");
        system_call("mkdir /Extra/Themes");
		system_call("cp -a $incpath/Themes/. /Extra/Themes/");
     }	
     
	//
    // Copying Custom kexts from include if CopyCustomKexts file exists
    //
    if(is_file("$workpath/kpsvn/dload/CopyCustomKexts.sh") && shell_exec("cd $incpath/Extensions; ls | wc -l") > 0)
    {
    	$edp->writeToLog("$workpath/build.log", "  Copying custom kexts from $incpath to /Extra<br>");
    	system_call("cp -a $incpath/Extensions/. $ee/");
    	
    	//If AppleHDA is found in Extra/include then remove VoodooHDA from ee
    	if(file_exists("$incpath/Extensions/AppleHDA.kext")) {
    			if(is_dir("/Applications/VoodooHdaSettingsLoader.app")) {system_call("rm -rf /Applications/VoodooHdaSettingsLoader.app");}
        	 	if(file_exists("/Library/LaunchAgents/com.restore.voodooHDASettings.plist")) {system_call("rm -rf /Library/LaunchAgents/com.restore.voodooHDASettings.plist");}
        	 	if(is_dir("/Library/PreferencePanes/VoodooHDA.prefPane")) {system_call("rm -rf /Library/PreferencePanes/VoodooHDA.prefPane");}
    			system_call("rm -rf $ee/VoodooHDA.kext");
    			system_call("rm -rf $ee/AppleHDADisabler.kext");
    			$edp->writeToLog("$workpath/build.log", "  found AppleHDA from $incpath, VoodooHDA removed<br>");
   		 }
    } 
}

	
?>